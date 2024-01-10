<?php

use App\Http\Controllers\Moonshine\ComponentController;
use App\Http\Controllers\Moonshine\ParameterController;
use Illuminate\Support\Facades\Route;

Route::post('/component/store', [ComponentController::class, 'store'])->name('component.store');
Route::post('/component', [ComponentController::class, 'save'])->name('component.save');

Route::delete('/parameter/{id}', [ParameterController::class, 'delete'])->name('parameter.delete');
