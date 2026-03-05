<?php

namespace App\Services;

use GuzzleHttp\Client;
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

        try {
            $response = $this->httpClient->post('https://api.line.me/v2/bot/message/push', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->channelAccessToken,
                ],
                'json' => [
                    'to' => $lineUserId,
                    'messages' => [
                        [
                            'type' => 'text',
                            'text' => $text,
                        ]
                    ],
                ],
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            Log::error('LINE Send Text Message Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send a Flex Message to a specific user
     */
    public function sendFlexMessage($lineUserId, $altText, $flexContents)
    {
        if (!$lineUserId || !$this->channelAccessToken) {
            return false;
        }

        try {
            $response = $this->httpClient->post('https://api.line.me/v2/bot/message/push', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->channelAccessToken,
                ],
                'json' => [
                    'to' => $lineUserId,
                    'messages' => [
                        [
                            'type' => 'flex',
                            'altText' => $altText,
                            'contents' => $flexContents,
                        ]
                    ],
                ],
            ]);

            return $response->getStatusCode() === 200;
        } catch (\Exception $e) {
            Log::error('LINE Send Flex Message Error: ' . $e->getMessage());
            return false;
        }
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
