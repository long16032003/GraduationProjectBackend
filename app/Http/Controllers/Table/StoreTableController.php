<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreTableController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'area' => ['required', Rule::in(Table::AREA_LIST)],
            'status' => ['required', Rule::in(Table::STATUS_LIST)],
        ]);

        try {
            $auth = Auth::user();
            // Tạo bản ghi mới
            $table = Table::create([
                'creator_id' => $auth->id,
                'name' => $request->name,
                'capacity' => $request->capacity,
                'area' => $request->area,
                'status' => $request->status,
            ]);

            return new JsonResponse([
                'success' => true,
                'message' => 'Bàn được tạo thành công',
                'data' => $table
            ], JsonResponse::HTTP_CREATED);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tạo bài viết thất bại',
                'error' => $e->getMessage()
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
