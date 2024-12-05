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

    public function fetchChannelPosts($channelUsername, $limit = 50)
    {
        $this->authenticate();

        // Get channel messages
        return $this->madelineProto->messages->getHistory([
            'peer' => $channelUsername,
            'limit' => $limit,
        ])['messages'];
    }
}
