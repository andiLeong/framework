<?php

use Andileong\Framework\Core\Facades\Route;
use App\Controller\AboutController;
use App\Controller\ContactController;

Route::get('', fn() => 'welcome home');
Route::get('about/', [AboutController::class, 'index']);
Route::get('/user/{id}/post/{postId}', [ContactController::class, 'index']);
