<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramPostsTable extends Migration
{
    public function up()
    {
        Schema::create('telegram_posts', function (Blueprint $table) {
            $table->id();
            $table->string('channel'); // Channel username
            $table->text('message'); // Message content
            $table->time('posted_at'); // Time of the post (h:m:s)
            $table->unsignedBigInteger('views')->nullable(); // Number of views
            $table->unsignedBigInteger('forwards')->nullable(); // Number of forwards
            $table->timestamps(); // Created at and Updated at timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('telegram_posts');
    }
}
