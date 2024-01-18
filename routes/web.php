<?php

use App\Http\Controllers\Bitrix24\Bitrix24Controller;
use App\Http\Middleware\VerifyCsrfToken;
use App\Livewire\Calculator;
use Illuminate\Support\Facades\Route;

Route::get('/', Calculator::class)
    ->name('index');

Route::post('/', Calculator::class)
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('index.post');

Route::post('/bitrix-install', Bitrix24Controller::class)
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('bitrix.install');
