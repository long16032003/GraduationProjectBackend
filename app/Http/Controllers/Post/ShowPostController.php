<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShowPostController extends Controller
{
    public function show(Request $request, $id): JsonResponse
    {
        try {
            $post = Post::filter($request->all())
                ->with(['creator'])
                ->findOrFail($id);

            return new JsonResponse([
                'success' => true,
                'message' => 'Lấy bài viết thành công',
                'data' => $post,
            ], JsonResponse::HTTP_OK);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Không tìm thấy bài viết'
            ], JsonResponse::HTTP_NOT_FOUND);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}