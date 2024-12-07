<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramPost extends Model
{
    protected $fillable = [
        'channel',   // Channel username
        'message',   // Message content
        'posted_at', // Time of the post (h:m:s)
        'views',     // Number of views
        'forwards',  // Number of forwards
    ];
}
