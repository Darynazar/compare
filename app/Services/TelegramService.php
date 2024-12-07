<?php

namespace App\Services;

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;

class TelegramService
{
    protected $madelineProto;
    protected $authFilePath = 'telegram_auth_session.madeline';

    public function __construct()
    {
        // Create settings for MadelineProto
        $settings = new Settings([
            'app_info' => new AppInfo([
                'api_id' => (int) env('TELEGRAM_API_ID'),       // Your API ID
                'api_hash' => env('TELEGRAM_API_HASH'),        // Your API Hash
                'device_model' => 'PHP Application',          // Optional device info
                'system_version' => '1.0.0',                  // Optional system info
            ]),
        ]);

        // Instantiate MadelineProto with proper settings
        $this->madelineProto = new API($this->authFilePath, $settings);
    }

    public function authenticate()
    {
        // Check if the session is authenticated
        $authorizationState = $this->madelineProto->getSelf();
        if (empty($authorizationState)) {
            // If not logged in, log in
            $this->madelineProto->phoneLogin(env('TELEGRAM_PHONE'));
            $code = readline("Enter the code you received: ");
            $this->madelineProto->completePhoneLogin($code);
        }
    }

    public function fetchChannelPosts($channelUsername)
    {
        $this->authenticate();

        // Get channel messages without a limit
        $messages = $this->madelineProto->messages->getHistory([
            'peer' => $channelUsername,
            'limit' => 100, // You can set a higher limit if needed
        ])['messages'];

        // Get today's date
        $today = new \DateTime();
        $today->setTime(0, 0, 0); // Set to the start of the day
        $todayTimestamp = $today->getTimestamp();

        // Create a DateTimeZone object for Iran
        $iranTimezone = new \DateTimeZone('Asia/Tehran');

        // Extract relevant fields and filter by today's date
        $posts = [];
        foreach ($messages as $message) {
            // Only process if the message has content
            if (isset($message['message'])) {
                $messageDate = new \DateTime();
                $messageDate->setTimestamp($message['date']);
                $messageDate->setTimezone($iranTimezone); // Set the timezone to Iran

                // Check if the message was created today
                if ($messageDate->getTimestamp() >= $todayTimestamp) {
                    $posts[] = [
                        'channel' => $channelUsername,
                        'message' => $message['message'],
                        'posted_at' => $messageDate->format('H:i:s'), // Convert timestamp to time format in Iran timezone
                        'views' => $message['views'] ?? 0,
                        'forwards' => $message['forwards'] ?? 0,
                    ];
                }
            }
        }

        return $posts;
    }
}
