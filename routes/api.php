<?php

use App\Http\Controllers\Api\FileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FolderController;

// Folder
Route::post('/folders', [FolderController::class, 'store']);
Route::get('/folders/{folder?}', [FolderController::class, 'browse']);
Route::delete('/folders/{folder}', [FolderController::class, 'destroy']);

// File
Route::post('/files', [FileController::class, 'store']);
Route::delete('/files/{file}', [FileController::class, 'destroy']);
