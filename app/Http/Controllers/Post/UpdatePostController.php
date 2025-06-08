<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class UpdatePostController extends Controller
{
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $post = Post::findOrFail($id);
            $post->update([
                'title' => $request->title,
                'summary' => $request->summary,
                'content' => $request->content,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Bài viết được cập nhật thành công',
                'data' => $post
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cập nhật bài viết thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}