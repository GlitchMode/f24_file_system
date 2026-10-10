<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFileRequest;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function store(StoreFileRequest $request): JsonResponse {
        $file = File::create($request->validated());

        return response()->json($file, 201);
    }
}
