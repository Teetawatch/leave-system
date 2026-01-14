<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FCMService
{
    protected $projectId;
    protected $credentialsPath;

    public function __construct()
    {
        $this->credentialsPath = storage_path('app/firebase-credentials.json');
        
        // Read project_id from credentials file
        if (file_exists($this->credentialsPath)) {
            $credentials = json_decode(file_get_contents($this->credentialsPath), true);
            $this->projectId = $credentials['project_id'] ?? null;
        }
    }

    /**
     * Get OAuth 2.0 Access Token using Service Account
     */
    private function getAccessToken()
    {
        if (!file_exists($this->credentialsPath)) {
            Log::error('FCM: firebase-credentials.json not found at ' . $this->credentialsPath);
            return null;
        }

        $client = new GoogleClient();
        $client->setAuthConfig($this->credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        
        $token = $client->fetchAccessTokenWithAssertion();
        
        return $token['access_token'] ?? null;
    }

    /**
     * Send notification using FCM HTTP v1 API
     */
    public function sendNotification($token, $title, $body, $data = [])
    {
        if (!$token) {
            Log::warning('FCM: No device token provided');
            return false;
        }

        if (!$this->projectId) {
            Log::error('FCM: Project ID not found. Check firebase-credentials.json');
            return false;
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            Log::error('FCM: Failed to get access token');
            return false;
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        $message = [
            'message' => [
                'token' => $token,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'data' => array_map('strval', $data), // FCM v1 requires string values
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'high_importance_channel',
                    ],
                ],
            ],
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($url, $message);

            if ($response->successful()) {
                Log::info('FCM v1: Notification sent successfully to ' . substr($token, 0, 20) . '...');
                return true;
            } else {
                Log::error('FCM v1 Error: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('FCM v1 Exception: ' . $e->getMessage());
            return false;
        }
    }
}
