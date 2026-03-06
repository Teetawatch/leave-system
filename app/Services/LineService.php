<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class LineService
{
    protected $httpClient;
    protected $channelAccessToken;

    public function __construct()
    {
        $this->httpClient = new Client();
        $this->channelAccessToken = env('LINE_CHANNEL_ACCESS_TOKEN');
    }

    /**
     * Send a simple text message to a specific user
     */
    public function sendTextMessage($lineUserId, $text)
    {
        if (!$lineUserId || !$this->channelAccessToken) {
            return false;
        }

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

        return $this->sendWithRetry('https://api.line.me/v2/bot/message/push', [
            'to' => $lineUserId,
            'messages' => [['type' => 'flex', 'altText' => $altText, 'contents' => $flexContents]],
        ], 'LINE Send Flex Message Error');
    }

    /**
     * Send LINE API request with retry on 429 Too Many Requests
     */
    protected function sendWithRetry(string $url, array $payload, string $errorPrefix, int $maxRetries = 3): bool
    {
        $attempt = 0;
        $delay = 5; // seconds

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
                    Log::warning("{$errorPrefix}: 429 Too Many Requests, retrying in {$delay}s (attempt {$attempt}/{$maxRetries})");
                    sleep($delay);
                    $delay *= 2; // exponential backoff: 5s, 10s, 20s
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
     * Send a text message to a LINE group
     */
    public function sendGroupTextMessage($text)
    {
        $groupId = env('LINE_GROUP_ID');

        if (!$groupId || !$this->channelAccessToken) {
            Log::warning('LINE Group ID or Channel Access Token not configured.');
            return false;
        }

        return $this->sendTextMessage($groupId, $text);
    }

    /**
     * Send a Flex Message to a LINE group
     */
    public function sendGroupFlexMessage($altText, $flexContents)
    {
        $groupId = env('LINE_GROUP_ID');

        if (!$groupId || !$this->channelAccessToken) {
            Log::warning('LINE Group ID or Channel Access Token not configured.');
            return false;
        }

        return $this->sendFlexMessage($groupId, $altText, $flexContents);
    }
}
