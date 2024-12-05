<?php

namespace App\Http\Controllers;

use App\Services\MadelineProtoService;

class TelegramController extends Controller
{
    protected $madelineProtoService;

    public function __construct(MadelineProtoService $madelineProtoService)
    {
        $this->madelineProtoService = $madelineProtoService;
    }

    public function compareChannelsPosts()
    {
        $channel1 = 't.me/Tasnimnews'; // Replace with actual channel username
        $channel2 = 't.me/farsna'; // Replace with actual channel username

        // Fetch posts from both channels
        $posts1 = $this->madelineProtoService->getChannelPosts($channel1);
        $posts2 = $this->madelineProtoService->getChannelPosts($channel2);

        // Compare posts
        $similarPosts = [];
        foreach ($posts1 as $post1) {
            foreach ($posts2 as $post2) {
                if ($this->madelineProtoService->comparePosts($post1, $post2)) {
                    $similarPosts[] = [
                        'channel_1_post' => $post1['message'],
                        'channel_2_post' => $post2['message'],
                    ];
                }
            }
        }

        // Return or display similar posts
        return view('similar_posts', compact('similarPosts'));
    }
}
