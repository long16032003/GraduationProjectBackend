<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class IndexTableController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tables = Table::filter($request->all())
            ->with(['creator'])
            ->get();

        return new JsonResponse([
            'success' => true,
            'data' => $tables
        ], JsonResponse::HTTP_OK);
    }
}
