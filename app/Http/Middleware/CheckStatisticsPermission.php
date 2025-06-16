<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CheckStatisticsPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Kiểm tra user đã đăng nhập
        if (!$request->user()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn cần đăng nhập để truy cập chức năng này',
            ], 401);
        }

        // Kiểm tra quyền truy cập thống kê
        $user = $request->user();

        // Giả sử có method hasRole, nếu không có thì có thể check bằng cách khác
        if (!$this->hasStatisticsPermission($user)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Bạn không có quyền truy cập chức năng thống kê',
            ], 403);
        }

        return $next($request);
    }

    /**
     * Kiểm tra user có quyền truy cập thống kê không
     */
    private function hasStatisticsPermission($user): bool
    {
        // Có thể customize logic này theo hệ thống phân quyền của bạn

        // Nếu có relationship với role
        if (method_exists($user, 'roles')) {
            return $user->roles()->whereIn('name', ['manager', 'admin'])->exists();
        }

        // Nếu có method hasRole
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('manager') || $user->hasRole('admin');
        }

        // Nếu có field role_id hoặc role
        if (isset($user->role)) {
            return in_array($user->role, ['manager', 'admin']);
        }

        // Mặc định cho phép admin (có thể thay đổi theo business logic)
        return $user->id == 1; // Giả sử user có id = 1 là admin
    }
}
