<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\MadelineProtoService;
use Illuminate\Http\Request;

class TelegramController extends Controller
{
    protected $madelineProtoService;

    public function __construct(MadelineProtoService $madelineProtoService)
    {
        $this->madelineProtoService = $madelineProtoService;
    }

    public function compareChannels(Request $request)
    {
        // Validate incoming request
        $validated = $request->validate([
            'channel_1' => 'required|string',
            'channel_2' => 'required|string',
        ]);

        $channel1 = $validated['channel_1'];
        $channel2 = $validated['channel_2'];

        try {
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

            return response()->json([
                'status' => 'success',
                'data' => $similarPosts,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
