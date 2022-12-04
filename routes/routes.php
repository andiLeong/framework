<?php

use Andileong\Framework\Core\Facades\Route;
use App\Controller\AboutController;
use App\Controller\ContactController;
use App\Controller\UserController;

Route::get('', fn() => 'welcome home')->middleware(['one']);
Route::get('about/', [AboutController::class, 'index']);
Route::get('user/{id}', [UserController::class, 'show']);
Route::get('/user', [UserController::class, 'index']);
Route::get('/user/{id}/post/{postId}', [ContactController::class, 'index']);
