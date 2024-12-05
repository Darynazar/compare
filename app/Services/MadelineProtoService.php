<?php

namespace App\Services;

use danog\MadelineProto\API;

class MadelineProtoService
{
    protected $madelineProto;

    public function __construct()
    {
        // Initialize MadelineProto
        $this->madelineProto = new API('session.madeline', ['ipcDisabled' => true]);

//        $this->madelineProto = new API('session.madeline');
        $this->madelineProto->start();
    }

    public function getChannelPosts($channelUsername)
    {
        $channel = $this->madelineProto->getInfo('@' . $channelUsername);
        $messages = $this->madelineProto->messages->getHistory([
            'peer' => $channel['username'],
            'limit' => 100, // Fetch the last 100 posts, or adjust as needed
        ]);

        return $messages['messages'];
    }

    public function comparePosts($post1, $post2)
    {
        // Get the text content of both posts
        $text1 = $post1['message'];
        $text2 = $post2['message'];

        // Calculate the similarity between the two texts (e.g., using a simple character comparison)
        similar_text($text1, $text2, $percent);

        return $percent >= 80;
    }
}
