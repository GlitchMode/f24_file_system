<?php

use App\Http\Controllers\Api\FileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FolderController;

Route::post('/folders', [FolderController::class, 'store']);
Route::get('/folders/{folder?}', [FolderController::class, 'browse']);

Route::post('/files', [FileController::class, 'store']);
