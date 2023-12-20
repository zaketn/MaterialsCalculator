<?php

use App\Http\Controllers\Moonshine\ComponentController;
use Illuminate\Support\Facades\Route;

Route::post('/component', ComponentController::class)->name('component.save');
