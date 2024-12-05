<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use Illuminate\Support\Str;

class CompareTelegramPosts extends Command
{
    protected $signature = 'telegram:compare';
    protected $description = 'Compare posts from two Telegram channels';

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    public function handle()
    {
        $channel1 = 'CHANNEL_USERNAME_1';
        $channel2 = 'CHANNEL_USERNAME_2';

        // Fetch posts
        $posts1 = $this->telegramService->fetchChannelPosts($channel1);
        $posts2 = $this->telegramService->fetchChannelPosts($channel2);

        // Compare posts
        foreach ($posts1 as $post1) {
            foreach ($posts2 as $post2) {
                $similarity = $this->calculateSimilarity($post1['message'], $post2['message']);
                if ($similarity >= 80) {
                    $this->info("Similar Post Found:\n{$post1['message']}");
                }
            }
        }
    }

    private function calculateSimilarity($text1, $text2)
    {
        similar_text($text1, $text2, $percent);
        return $percent;
    }
}
