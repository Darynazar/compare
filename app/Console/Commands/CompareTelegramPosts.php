<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use App\Models\TelegramPost;

class CompareTelegramPosts extends Command
{
    protected $signature = 'telegram:compare-posts';
    protected $description = 'Compare Telegram channel posts for similarity';
    protected $telegramService;

    // Define static channel usernames as an array
    protected $channels = [
        '@Tasnimnews',
        '@Farsna',
        '@Isna94',
    ];

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;

        // Set the default timezone to Iran
        date_default_timezone_set('Asia/Tehran');
    }

    public function handle()
    {
        try {
            foreach ($this->channels as $channel) {
                // Fetch posts for each channel
                $posts = $this->telegramService->fetchChannelPosts($channel);

                // Save posts in the database
                $this->savePostsToDatabase($posts);

                $this->info("Posts from {$channel} have been fetched and stored in the database.");
            }

            // Uncomment the similarity comparison logic if needed
            // foreach ($posts1 as $post1) {
            //     foreach ($posts2 as $post2) {
            //         $similarity = $this->calculateSimilarity($post1['message'], $post2['message']);
            //         if ($similarity >= 80) {
            //             $this->info("Similar Post Found:\n{$post1['message']}");
            //         }
            //     }
            // }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    private function savePostsToDatabase($posts)
    {
        foreach ($posts as $post) {
            TelegramPost::create($post);
        }
    }

    private function calculateSimilarity($text1, $text2)
    {
        similar_text($text1, $text2, $percent);
        return $percent;
    }
}
