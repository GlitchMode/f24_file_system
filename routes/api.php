<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FolderController;

Route::post('/folders', [FolderController::class, 'store']);
