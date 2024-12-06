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

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
        date_default_timezone_set('Asia/Tehran');
    }

    public function handle()
    {
        $channel1 = $this->ask('Enter the username of the first channel (e.g., @channel1)');
       // $channel2 = $this->ask('Enter the username of the second channel (e.g., @channel2)');

        try {
            // Fetch posts for both channels
            $posts1 = $this->telegramService->fetchChannelPosts($channel1);
          //  $posts2 = $this->telegramService->fetchChannelPosts($channel2);

            // Save posts in the database
            $this->savePostsToDatabase($posts1);
           // $this->savePostsToDatabase($posts2);

            $this->info('Posts have been fetched and stored in the database.');

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
