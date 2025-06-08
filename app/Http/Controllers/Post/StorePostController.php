<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StorePostController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // // Validate dữ liệu đầu vào
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'summary' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $auth = Auth::user();
            // Tạo bản ghi mới
            $post = Post::create([
                'creator_id' => $auth->id,
                'title' => $request->title,
                'content' => $request->content,
                'summary' => $request->summary,
            ]);

            return new JsonResponse($post, JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo bài viết thất bại',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}