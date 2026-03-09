<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class LineService
{
    protected $httpClient;
    protected $channelAccessToken;
    protected $channelAccessToken2;
    protected $channelSecret2;
    protected $lineNotifyToken;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->channelAccessToken = env('LINE_CHANNEL_ACCESS_TOKEN');
        $this->channelAccessToken2 = env('LINE_CHANNEL_ACCESS_TOKEN_2');
        $this->channelSecret2 = env('LINE_CHANNEL_SECRET_2');
        $this->lineNotifyToken = env('LINE_NOTIFY_TOKEN');
    }

    /**
     * Send a simple text message to a specific user
     */
    public function sendTextMessage($lineUserId, $text)
    {
        if (!$lineUserId || !$this->channelAccessToken) {
            return false;
        }

        $this->incrementQuota(1);

        return $this->sendWithRetry('https://api.line.me/v2/bot/message/push', [
            'to' => $lineUserId,
            'messages' => [['type' => 'text', 'text' => $text]],
        ], 'LINE Send Text Message Error');
    }

    /**
     * Send a Flex Message to a specific user
     */
    public function sendFlexMessage($lineUserId, $altText, $flexContents)
    {
        if (!$lineUserId || !$this->channelAccessToken) {
            return false;
        }

        $this->incrementQuota(1);

        return $this->sendWithRetry('https://api.line.me/v2/bot/message/push', [
            'to' => $lineUserId,
            'messages' => [['type' => 'flex', 'altText' => $altText, 'contents' => $flexContents]],
        ], 'LINE Send Flex Message Error');
    }

    /**
     * Send a text message to multiple users using multicast API (counts as 1 push per recipient).
     * Use this instead of looping sendTextMessage to keep rate limits low.
     *
     * @param  array  $lineUserIds  Array of LINE user IDs (max 500 per call)
     * @param  string $text
     * @return bool
     */
    public function multicastTextMessage(array $lineUserIds, string $text): bool
    {
        return $this->multicast($lineUserIds, [['type' => 'text', 'text' => $text]]);
    }

    /**
     * Send a Flex Message to multiple users using multicast API (1 API call for N users,
     * but still counts as N messages against the monthly quota).
     *
     * @param  array  $lineUserIds
     * @param  string $altText
     * @param  array  $flexContents
     * @return bool
     */
    public function multicastFlexMessage(array $lineUserIds, string $altText, array $flexContents): bool
    {
        return $this->multicast($lineUserIds, [
            ['type' => 'flex', 'altText' => $altText, 'contents' => $flexContents],
        ]);
    }

    /**
     * Core multicast sender — sends messages to up to 500 recipients in a single API call.
     * Splits automatically into chunks if more than 500 IDs are supplied.
     */
    protected function multicast(array $lineUserIds, array $messages): bool
    {
        if (empty($lineUserIds) || !$this->channelAccessToken) {
            return false;
        }

        $chunks = array_chunk($lineUserIds, 500);
        $success = true;

        foreach ($chunks as $chunk) {
            $this->incrementQuota(count($chunk));
            $result = $this->sendWithRetry('https://api.line.me/v2/bot/message/multicast', [
                'to' => $chunk,
                'messages' => $messages,
            ], 'LINE Multicast Error');

            if (!$result) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Send a text message to a LINE group via push API using Bot 2 (for daily reports).
     * Uses LINE_CHANNEL_ACCESS_TOKEN_2 and LINE_GROUP_ID_2.
     */
    public function sendGroupTextMessage2(string $text): bool
    {
        $groupId = env('LINE_GROUP_ID_2');

        if (!$groupId || !$this->channelAccessToken2) {
            Log::warning('LINE Bot 2: GROUP_ID or ACCESS_TOKEN not configured.');
            return false;
        }

        $this->addRateLimitDelay();

        try {
            $response = $this->httpClient->post('https://api.line.me/v2/bot/message/push', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $this->channelAccessToken2,
                ],
                'json' => [
                    'to'       => $groupId,
                    'messages' => [['type' => 'text', 'text' => $text]],
                ],
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            Log::error('LINE Bot 2 Send Group Text Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a Flex Message to a LINE group via push API using Bot 2 (for daily reports).
     * Uses LINE_CHANNEL_ACCESS_TOKEN_2 and LINE_GROUP_ID_2.
     */
    public function sendGroupFlexMessage2(string $altText, array $flexContents): bool
    {
        $groupId = env('LINE_GROUP_ID_2');

        if (!$groupId || !$this->channelAccessToken2) {
            Log::warning('LINE Bot 2: GROUP_ID or ACCESS_TOKEN not configured.');
            return false;
        }

        $this->addRateLimitDelay();

        try {
            $response = $this->httpClient->post('https://api.line.me/v2/bot/message/push', [
                'headers' => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Bearer ' . $this->channelAccessToken2,
                ],
                'json' => [
                    'to'       => $groupId,
                    'messages' => [['type' => 'flex', 'altText' => $altText, 'contents' => $flexContents]],
                ],
            ]);

            return $response->getStatusCode() === 200;
        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            $body = $e->getResponse()->getBody()->getContents();
            Log::error("LINE Bot 2 Send Group Flex Error ({$statusCode}): {$body}");
            return false;
        } catch (\Exception $e) {
            Log::error('LINE Bot 2 Send Group Flex Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a text message to a LINE group via push API.
     * NOTE: For group/broadcast messages, prefer sendGroupNotify() which uses LINE Notify
     * (free, unlimited) instead of consuming the 500 push quota.
     */
    public function sendGroupTextMessage($text)
    {
        $groupId = env('LINE_GROUP_ID');

        if (!$groupId || !$this->channelAccessToken) {
            Log::warning('LINE Group ID or Channel Access Token not configured.');
            return false;
        }

        $this->addRateLimitDelay();
        $this->incrementQuota(1);

        return $this->sendWithRetry('https://api.line.me/v2/bot/message/push', [
            'to' => $groupId,
            'messages' => [['type' => 'text', 'text' => $text]],
        ], 'LINE Send Group Text Message Error');
    }

    /**
     * Send a Flex Message to a LINE group via push API.
     * NOTE: LINE Notify does not support Flex Messages, so this still uses push.
     * Use sparingly to stay within the 500/month free tier limit.
     */
    public function sendGroupFlexMessage($altText, $flexContents)
    {
        $groupId = env('LINE_GROUP_ID');

        if (!$groupId || !$this->channelAccessToken) {
            Log::warning('LINE Group ID or Channel Access Token not configured.');
            return false;
        }

        $this->addRateLimitDelay();
        $this->incrementQuota(1);

        return $this->sendWithRetry('https://api.line.me/v2/bot/message/push', [
            'to' => $groupId,
            'messages' => [['type' => 'flex', 'altText' => $altText, 'contents' => $flexContents]],
        ], 'LINE Send Group Flex Message Error');
    }

    /**
     * Send a plain-text notification to a LINE Notify token (group or personal).
     * LINE Notify is FREE and has NO monthly message quota — use this for group
     * broadcasts and daily summaries instead of push API.
     *
     * Requires LINE_NOTIFY_TOKEN in .env
     */
    public function sendGroupNotify(string $message): bool
    {
        if (!$this->lineNotifyToken) {
            Log::warning('LINE_NOTIFY_TOKEN not configured; falling back to push group message.');
            return $this->sendGroupTextMessage($message);
        }

        try {
            $response = $this->httpClient->post('https://notify-api.line.me/api/notify', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->lineNotifyToken,
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'message' => $message,
                ],
            ]);

            $statusCode = $response->getStatusCode();
            if ($statusCode === 200) {
                Log::info('LINE Notify sent successfully');
                return true;
            }

            Log::warning('LINE Notify unexpected status: ' . $statusCode);
            return false;

        } catch (ClientException $e) {
            $statusCode = $e->getResponse()->getStatusCode();
            Log::error('LINE Notify Error (' . $statusCode . '): ' . $e->getMessage());

            if ($statusCode === 401) {
                Log::error('LINE Notify token is invalid or revoked. Please re-issue a token at https://notify-bot.line.me/');
            }

            return false;
        } catch (\Exception $e) {
            Log::error('LINE Notify Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check remaining push message quota for the current month.
     * Returns -1 if quota is unlimited (paid plan), or the remaining count.
     */
    public function getRemainingQuota(): int
    {
        if (!$this->channelAccessToken) {
            return 0;
        }

        try {
            $response = $this->httpClient->get('https://api.line.me/v2/bot/message/quota/consumption', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->channelAccessToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);
            $totalQuota = $data['value'] ?? 500;

            $limitResponse = $this->httpClient->get('https://api.line.me/v2/bot/message/quota', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->channelAccessToken,
                ],
            ]);

            $limitData = json_decode($limitResponse->getBody(), true);

            if (($limitData['type'] ?? '') === 'unlimited') {
                return -1;
            }

            $limit = $limitData['value'] ?? 500;
            return max(0, $limit - $totalQuota);

        } catch (\Exception $e) {
            Log::warning('LINE quota check failed: ' . $e->getMessage());
            return -1;
        }
    }

    /**
     * Send LINE API request with retry on 429 Too Many Requests
     * Version: 3.0 - With Retry-After header support
     */
    protected function sendWithRetry(string $url, array $payload, string $errorPrefix, int $maxRetries = 3): bool
    {
        $attempt = 0;
        $delay = 10; // seconds

        while ($attempt <= $maxRetries) {
            try {
                $response = $this->httpClient->post($url, [
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'Authorization' => 'Bearer ' . $this->channelAccessToken,
                    ],
                    'json' => $payload,
                ]);

                return $response->getStatusCode() === 200;

            } catch (ClientException $e) {
                $statusCode = $e->getResponse()->getStatusCode();

                if ($statusCode === 429 && $attempt < $maxRetries) {
                    $attempt++;
                    $retryAfter = (int) $e->getResponse()->getHeaderLine('Retry-After');
                    $waitSeconds = $retryAfter > 0 ? $retryAfter : $delay;
                    Log::warning("{$errorPrefix}: 429 Too Many Requests, retrying in {$waitSeconds}s (attempt {$attempt}/{$maxRetries})");
                    sleep($waitSeconds);
                    $delay *= 2; // exponential backoff: 10s, 20s, 40s
                    continue;
                }

                Log::error("{$errorPrefix}: " . $e->getMessage());
                return false;

            } catch (\Exception $e) {
                Log::error("{$errorPrefix}: " . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    /**
     * Add delay between messages to avoid rate limiting
     */
    protected function addRateLimitDelay()
    {
        $lastSent = cache()->get('line_last_sent_time', 0);
        $now = time();
        $minInterval = 2; // seconds between messages

        if ($now - $lastSent < $minInterval) {
            $waitTime = $minInterval - ($now - $lastSent);
            Log::info("LINE rate limiting: waiting {$waitTime}s");
            sleep($waitTime);
        }

        cache()->put('line_last_sent_time', time(), 60);
    }

    /**
     * Track push message quota usage in cache for monitoring.
     * Resets at the start of each calendar month.
     */
    protected function incrementQuota(int $count = 1): void
    {
        $key = 'line_push_quota_' . now()->format('Y_m');
        $current = (int) cache()->get($key, 0);
        $newTotal = $current + $count;

        // TTL: keep until end of current month + 1 day buffer
        $ttl = now()->endOfMonth()->addDay();
        cache()->put($key, $newTotal, $ttl);

        $limit = (int) env('LINE_MONTHLY_QUOTA', 500);

        if ($newTotal >= $limit) {
            Log::critical("LINE push quota EXHAUSTED: {$newTotal}/{$limit} this month. Switch to LINE Notify for group messages.");
        } elseif ($newTotal >= (int) ($limit * 0.8)) {
            Log::warning("LINE push quota WARNING: {$newTotal}/{$limit} used this month (80%+ consumed).");
        }
    }
}
