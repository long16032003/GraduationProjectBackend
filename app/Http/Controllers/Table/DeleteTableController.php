<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DeleteTableController extends Controller
{
    public function delete(Request $request, $id): JsonResponse
    {
        $table = Table::findOrFail($id);
        $table->delete();

        return new JsonResponse([
            'success' => true,
            'message' => 'Bàn được xóa thành công'
        ], JsonResponse::HTTP_OK);
    }
}
