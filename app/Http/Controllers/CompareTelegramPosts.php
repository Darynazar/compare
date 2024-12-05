<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Storage;
use App\Models\TelegramPost;

class CompareTelegramPosts extends Command
{
    protected $signature = 'telegram:compare';
    protected $description = 'Fetch and store posts from two Telegram channels created today';

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    public function handle()
    {
        $channel1 = $this->ask('Enter the username of the first channel (e.g., @channel1):');
        $channel2 = $this->ask('Enter the username of the second channel (e.g., @channel2):');

        $today = now()->startOfDay();
        $this->info("Fetching posts created today...");

        // Fetch posts for both channels
        $posts1 = $this->fetchAndFilterPosts($channel1, $today);
        $posts2 = $this->fetchAndFilterPosts($channel2, $today);

        // Save posts to JSON
        $allPosts = [
            'channel_1' => $posts1,
            'channel_2' => $posts2,
        ];
        Storage::put('telegram_posts.json', json_encode($allPosts, JSON_PRETTY_PRINT));
        $this->info('Posts saved to telegram_posts.json.');

        // Save posts to database
        $this->saveToDatabase($posts1, $channel1);
        $this->saveToDatabase($posts2, $channel2);

        $this->info('Posts saved to the database.');
    }

    private function fetchAndFilterPosts($channel, $today)
    {
        $posts = $this->telegramService->fetchChannelPosts($channel);
        return collect($posts)->filter(function ($post) use ($today) {
            $postDate = isset($post['date']) ? \Carbon\Carbon::createFromTimestamp($post['date']) : null;
            return $postDate && $postDate->greaterThanOrEqualTo($today);
        })->toArray();
    }

    private function saveToDatabase($posts, $channel)
    {
        dd($posts);
        foreach ($posts as $post) {
            TelegramPost::create([
                'channel' => $channel,
                'message' => $post['message'] ?? '',
                'posted_at' => \Carbon\Carbon::createFromTimestamp($post['date'] ?? time()),
            ]);
        }
    }
}
