<?php

use Andileong\Framework\Core\Facades\Route;
use App\Controller\AboutController;
use App\Controller\ContactController;
use App\Controller\MeController;
use App\Controller\UserController;

Route::get('', fn() => 'welcome home')->middleware(['one']);
Route::get('about/', [AboutController::class, 'index']);
Route::get('user/{id}', [UserController::class, 'show']);
Route::get('/user', [UserController::class, 'index']);
Route::get('/user/{id}/post/{postId}', [ContactController::class, 'index']);

Route::middleware('one')->get('/foo', [AboutController::class, 'index']);
Route::get('/baz', [AboutController::class, 'index']);

Route::middleware('auth')->group(function () {
    Route::get('/me', [MeController::class, 'index']);
});
