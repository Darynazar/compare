<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $apiId;
    protected $apiHash;
    protected $phoneNumber; // Your Telegram account phone number
    protected $authFilePath = 'telegram_auth.json';

    public function __construct()
    {
        $this->apiId = env('TELEGRAM_API_ID');
        $this->apiHash = env('TELEGRAM_API_HASH');
        $this->phoneNumber = env('TELEGRAM_PHONE');
    }

    // Authenticate and save session
    public function authenticate()
    {
        $url = "https://api.telegram.org/auth/sendCode";
        $response = Http::post($url, [
            'api_id' => $this->apiId,
            'api_hash' => $this->apiHash,
            'phone_number' => $this->phoneNumber,
        ]);

        $result = $response->json();

        if (isset($result['error'])) {
            throw new \Exception("Authentication failed: " . $result['error_message']);
        }

        // Save session data for reuse
        file_put_contents($this->authFilePath, json_encode($result));

        return $result;
    }

    // Get channel posts
    public function fetchChannelPosts($channelUsername, $limit = 50)
    {
        $authData = $this->getAuthData();

        $url = "https://api.telegram.org/messages.getHistory";
        $response = Http::post($url, [
            'peer' => $channelUsername,
            'limit' => $limit,
            'access_hash' => $authData['access_hash'],
            'user_id' => $authData['user_id'],
        ]);

        $result = $response->json();

        if (isset($result['error'])) {
            throw new \Exception("Failed to fetch messages: " . $result['error_message']);
        }

        return $result['messages'] ?? [];
    }

    // Retrieve authentication data from file
    protected function getAuthData()
    {
        if (!file_exists($this->authFilePath)) {
            throw new \Exception("Authentication data not found. Please authenticate first.");
        }

        return json_decode(file_get_contents($this->authFilePath), true);
    }
}
