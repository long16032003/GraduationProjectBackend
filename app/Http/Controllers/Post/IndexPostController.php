<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexPostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $posts = Post::filter($request->all())
                ->with(['creator'])
                ->get();

            return new JsonResponse([
                'success' => true,
                'message' => 'Lấy danh sách bài viết thành công',
                'data' => $posts
            ], JsonResponse::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}