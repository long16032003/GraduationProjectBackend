<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class DeletePostController extends Controller
{
    public function delete(Request $request, $id): JsonResponse
    {
        try {
            $post = Post::findOrFail($id);
            $post->delete();

            return new JsonResponse([
                'success' => true,
                'message' => 'Bài viết được xóa thành công',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Xóa bài viết thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}