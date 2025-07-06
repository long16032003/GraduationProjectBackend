<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserPermissionController extends Controller
{
    /**
     * Lấy tất cả permissions của user hiện tại
     */
    public function getUserPermissions(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $user->load('roles');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'superadmin' => $user->isSuperAdmin(),
                ],
                'roles' => $user->roles->map(function ($role) {
                    return [
                        'id' => $role->id,
                        'key' => $role->key,
                        'name' => $role->name,
                        'level' => $role->level,
                        'permissions' => $role->permissions
                    ];
                }),
                'permissions' => $user->permissions()->toArray(),
                'flat_permissions' => $user->permissions()->keys()->toArray()
            ]
        ]);
    }

    /**
     * Kiểm tra user có quyền cụ thể không
     */
    public function checkPermission(Request $request): JsonResponse
    {
        $request->validate([
            'permission' => 'required|string'
        ]);

        $user = $request->user();
        $permission = $request->permission;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $hasPermission = $user->hasPermission($permission);

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'permission' => $permission,
                'has_permission' => $hasPermission,
                'is_superadmin' => $user->isSuperAdmin()
            ]
        ]);
    }

    /**
     * Kiểm tra user có bất kỳ quyền nào trong danh sách không
     */
    public function checkAnyPermissions(Request $request): JsonResponse
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'required|string'
        ]);

        $user = $request->user();
        $permissions = $request->permissions;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $hasAnyPermission = $user->hasPermission($permissions, true); // true = hasAny

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'permissions' => $permissions,
                'has_any_permission' => $hasAnyPermission,
                'is_superadmin' => $user->isSuperAdmin(),
                'detailed_check' => collect($permissions)->mapWithKeys(function ($permission) use ($user) {
                    return [$permission => $user->hasPermission($permission)];
                })
            ]
        ]);
    }

    /**
     * Kiểm tra user có tất cả quyền trong danh sách không
     */
    public function checkAllPermissions(Request $request): JsonResponse
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'required|string'
        ]);

        $user = $request->user();
        $permissions = $request->permissions;

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $hasAllPermissions = $user->hasAllPermission($permissions);

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => $user->id,
                'permissions' => $permissions,
                'has_all_permissions' => $hasAllPermissions,
                'is_superadmin' => $user->isSuperAdmin(),
                'detailed_check' => collect($permissions)->mapWithKeys(function ($permission) use ($user) {
                    return [$permission => $user->hasPermission($permission)];
                })
            ]
        ]);
    }
}
