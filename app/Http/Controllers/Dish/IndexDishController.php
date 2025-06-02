<?php

namespace App\Http\Controllers\Dish;

use App\Http\Controllers\Controller;
use App\Models\Dish;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexDishController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $dishes = Dish::filter($request->all())
            ->with([
                'dishCategories',
                'creator',
            ])
            ->get();

            return new JsonResponse([
                'data' => $dishes,
                'message' => 'Dishes fetched successfully',
                'status' => 'success'
            ], JsonResponse::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}