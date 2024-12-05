<?php

use App\Http\Controllers\TelegramController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/madeline-init', function () {
    return response()->json(['status' => 'MadelineProto initialized.']);
});
Route::get('/compare-posts', [TelegramController::class, 'compareChannelsPosts']);


