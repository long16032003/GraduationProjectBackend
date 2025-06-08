<?php

namespace App\Http\Controllers\Media;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;

class MediaController extends Controller
{
    /**
     * Upload một file và lưu vào storage
     */
    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
            'type' => 'required|in:image,document,other',
            'folder' => 'nullable|string',
        ]);

        $file = $request->file('file');
        $folder = $request->input('folder', 'uploads');

        // Tạo tên file duy nhất với timestamp
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Lưu file vào storage/app/public/{folder}
        $path = $file->storeAs($folder, $fileName, 'public');

        // Trả về URL đầy đủ cho frontend
        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::url($path),
            'name' => $fileName,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    /**
     * Xóa file từ storage
     */
    public function delete(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $path = $request->input('path');

        // Kiểm tra xem file có tồn tại không
        if (!Storage::disk('public')->exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File không tồn tại',
            ], 404);
        }

        // Xóa file
        Storage::disk('public')->delete($path);

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa file thành công',
        ]);
    }

    /**
     * Lấy danh sách file trong một thư mục
     */
    public function list(Request $request): JsonResponse
    {
        $folder = $request->input('folder', 'uploads');
        $files = Storage::disk('public')->files($folder);

        $fileList = [];
        foreach ($files as $file) {
            $fileList[] = [
                'path' => $file,
                'url' => Storage::url($file),
                'name' => basename($file),
                'size' => Storage::disk('public')->size($file),
                'last_modified' => Storage::disk('public')->lastModified($file),
            ];
        }

        return response()->json([
            'success' => true,
            'files' => $fileList,
        ]);
    }
}
