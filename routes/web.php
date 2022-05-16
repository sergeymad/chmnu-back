<?php

use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;
use Illuminate\Support\Facades\Http;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
$updates = Telegram::getWebhookUpdates();


Route::get('/', function () {
    return redirect('/nova');
});

Route::post('/tg', [App\Http\Controllers\TgBotController::class, 'handler']);
Route::get('/chatList', [App\Http\Controllers\TgBotController::class, 'getChats']);
Route::get('/getMessages/{id}', [App\Http\Controllers\TgBotController::class, 'getMessages']);
Route::get('/sendMessage/{id}', [App\Http\Controllers\TgBotController::class, 'sendMessage']);
