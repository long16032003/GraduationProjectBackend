<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use EloquentFilter\Filterable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IndexBillController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $bills = Bill::filter($request->all())->get();
        return new JsonResponse([
            'success' => true,
            'message' => 'Lấy danh sách hóa đơn thành công',
            'data' => $bills
        ], JsonResponse::HTTP_OK);
    }
}