<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;
use Illuminate\Support\Str;

class StoreMediaController extends Controller
{
    public function store(Request $request): JsonResponse {
        try {
            $request->validate([
                'folder' => 'required',
                'file' => 'required|file|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if (!$request->hasFile('file')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy file upload'
                ], 400);
            }

            $file = $request->file('file');

            // Unique filename
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

            // Save to storage/app/public/dishes
            $path = $file->storeAs($request->folder, $fileName, 'public');

            // Create media record
            $media = Media::create([
                'title' => $file->getClientOriginalName(),
                'path' => $path,
                'type' => Media::TYPE_IMAGE,
                'size' => $file->getSize(),
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Upload ảnh thành công',
                'data' => $media
            ], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Upload ảnh thất bại: ' . $e->getMessage()
            ], 500);
        }
    }
}
