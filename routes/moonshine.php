<?php

use App\Http\Controllers\Moonshine\ComponentController;
use Illuminate\Support\Facades\Route;

Route::post('/component/store', [ComponentController::class, 'store'])->name('component.store');

Route::post('/component', [ComponentController::class, 'save'])->name('component.save');
