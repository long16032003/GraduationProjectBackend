<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateTableController extends Controller
{
    public function update(Request $request, Table $table): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer|min:1',
            'area' => ['sometimes', Rule::in(Table::AREA_LIST)],
            'status' => ['sometimes', Rule::in(Table::STATUS_LIST)],
        ]);

        $table->update($validated);

        return new JsonResponse([
            'success' => true,
            'message' => 'Bàn được cập nhật thành công',
            'data' => $table->fresh()
        ], JsonResponse::HTTP_OK);
    }
}
