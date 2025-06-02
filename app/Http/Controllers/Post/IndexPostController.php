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
            $query = Post::query()
                ->when($request->has('search'), function ($query) use ($request) {
                    $search = $request->search;
                    return $query->where('title', 'LIKE', "%{$search}%");
                })
                ->when($request->has('sort'), function ($query) use ($request) {
                    return $query->orderBy($request->sort, $request->order ?? 'asc');
                });

            $posts = $query->get();

            return new JsonResponse($posts, JsonResponse::HTTP_OK);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}