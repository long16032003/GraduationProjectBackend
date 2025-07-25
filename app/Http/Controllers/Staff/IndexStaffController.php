<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexStaffController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            // $staff = User::filter($request->all())->get();
            $staff = User::query()
            ->when($request->has('search'), function ($query) use ($request) {
                $search = $request->search;
                return $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%")
                      ->orWhere('phone', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('email', 'LIKE', "%{$search}%");
                      });
                });
            })
            ->when($request->has('role'), function ($query) use ($request) {
                return $query->where('role', $request->role);
            })
            ->when($request->has('sort'), function ($query) use ($request) {
                $sortField = $request->input('sort', 'created_at');
                $sortOrder = $request->input('order', 'desc');
                return $query->orderBy($sortField, $sortOrder);
            })
            ->get();
            $staff->load('roles');
            return new JsonResponse([
                'success' => true,
                'data' => $staff,
                'message' => 'Lấy danh sách nhân viên thành công'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}