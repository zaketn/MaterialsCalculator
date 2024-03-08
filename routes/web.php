<?php

use App\Http\Controllers\Bitrix24\Bitrix24Controller;
use App\Http\Middleware\VerifyCsrfToken;
use App\Livewire\Calculator;
use App\Livewire\Catalog;
use Illuminate\Support\Facades\Route;

Route::get('/', Calculator::class)
    ->name('index');

Route::get('/catalog', Catalog::class)
    ->name('catalog');

Route::post('/', Calculator::class)
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('index.post');

Route::post('/bitrix-install', Bitrix24Controller::class)
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('bitrix.install');
