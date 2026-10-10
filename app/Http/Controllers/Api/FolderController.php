<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFolderRequest;
use App\Models\Folder;
use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function store(StoreFolderRequest $request): JsonResponse{
        $folder = Folder::create($request->validated());

        return response()->json($folder, 201);
    }

    public function browse(?Folder $folder = null) : JsonResponse {
        if($folder === null){
            return response()->json([
                'folder' => null,
                'folders' => Folder::whereNull('parent_id')->get(),
                'files' => File::whereNull('folder_id')->get(),
            ]);
        }

        return response()->json([
            'folder' => $folder,
            'folders' => $folder->folders()->get(),
            'files' => $folder->files()->get(),
        ]);
    }
}
