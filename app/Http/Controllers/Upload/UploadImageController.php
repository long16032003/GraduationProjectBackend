<?php

namespace App\Http\Controllers\Upload;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadImageController extends Controller
{
    public function upload(Request $request): JsonResponse
    {
        try {
            // Validate request
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            if (!$request->hasFile('image')) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'Không tìm thấy file ảnh'
                ], JsonResponse::HTTP_BAD_REQUEST);
            }

            // Upload file
            $file = $request->file('image');
            $path = Storage::disk('public')->put('images', $file);

            return new JsonResponse([
                'success' => true,
                'message' => 'Upload ảnh thành công',
                'data' => [
                    'path' => $path,
                    'url' => Storage::url($path)
                ]
            ], JsonResponse::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Upload ảnh thất bại: ' . $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}