<?php

use App\Http\Controllers\Api\FileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FolderController;

Route::prefix('folders')->controller(FolderController::class)->group(function () {
    Route::post('/', 'store');
    Route::get('/{folder?}', 'browse');
    Route::delete('/{folder}', 'destroy');
});

Route::prefix('files')->controller(FileController::class)->group(function () {
    Route::post('/', 'store');
    Route::delete('/{file}', 'destroy');
    Route::get('/search', 'search');
    Route::get('/suggestions', 'suggestions');
});