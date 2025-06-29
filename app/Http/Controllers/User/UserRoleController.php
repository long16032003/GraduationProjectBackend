<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRoleController extends Controller
{
    /**
     * Lấy danh sách roles của user
     */
    public function index(User $user): JsonResponse
    {
        $roles = $user->roles()->with(['users' => function($query) {
            $query->select('id', 'name', 'email');
        }])->get();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'superadmin' => $user->isSuperAdmin()
                ],
                'roles' => $roles,
                'permissions' => $user->permissions()
            ]
        ]);
    }

    /**
     * Gán roles cho user
     */
    public function store(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role_ids' => 'required|array',
            'role_ids.*' => 'required|integer|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Tìm user bằng ID
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhân viên'
            ], 404);
        }

        // Sync roles - tự động xóa cũ và thêm mới
        $user->roles()->sync($request->role_ids);

        return response()->json([
            'success' => true,
            'message' => 'Gán quyền cho nhân viên thành công',
            'data' => [
                'user_id' => $user->id,
                'roles' => $user->roles()->get(['id', 'name']),
                'permissions' => $user->permissions()
            ]
        ]);
    }

    /**
     * Thêm một role cho user
     */
    public function attach(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::find($request->role_id);

        // Kiểm tra user đã có role này chưa
        if ($user->roles()->where('role_id', $role->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Nhân viên đã có quyền này rồi'
            ], 400);
        }

        $user->roles()->attach($role->id);

        return response()->json([
            'success' => true,
            'message' => 'Thêm quyền cho nhân viên thành công',
            'data' => [
                'role' => $role,
                'permissions' => $user->permissions()
            ]
        ]);
    }

    /**
     * Xóa một role khỏi user
     */
    public function detach(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required|integer|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $role = Role::find($request->role_id);

        // Kiểm tra user có role này không
        if (!$user->roles()->where('role_id', $role->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Nhân viên không có quyền này'
            ], 400);
        }

        $user->roles()->detach($role->id);

        return response()->json([
            'success' => true,
            'message' => 'Xóa quyền khỏi nhân viên thành công',
            'data' => [
                'removed_role' => $role,
                'remaining_permissions' => $user->permissions()
            ]
        ]);
    }

    /**
     * Kiểm tra user có quyền cụ thể không
     */
    public function checkPermission(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'permission' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $hasPermission = $user->hasPermission($request->permission);

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'permission' => $request->permission,
                'has_permission' => $hasPermission,
                'is_superadmin' => $user->isSuperAdmin()
            ]
        ]);
    }
}
