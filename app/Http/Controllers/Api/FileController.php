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

    public function destroy(File $file): JsonResponse {
        $file->delete();

        return response()->json(null, 204);
    }

    public function search(Request $request): JsonResponse {
        $request->validate([
            'name' => ['required', 'string'],
            'folder_id' => ['nullable', 'integer', 'exists:folders,id'],
        ]);

        $query = File::query()
            ->where('name', $request->query('name'));

        if($request->has('folder_id')){
            $query->where('folder_id', $request->query('folder_id'));
        }

        return response()->json(
            $query->get()
        );
    }

    public function suggestions(Request $request): JsonResponse {
        $request->validate([
            'q' => ['required', 'string'],
        ]);

        $files = File::query()
            ->where('name', 'like', $request->query('q') . '%')
            ->orderBy('name')
            ->limit(10)
            ->get();

        return response()->json($files);
    }
}
