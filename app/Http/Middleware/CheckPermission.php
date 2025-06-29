<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next, string ...$permissions): ResponseAlias
    {
        $user = $request->user();

        // Nếu không có user (chưa đăng nhập)
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để tiếp tục'
            ], Response::HTTP_UNAUTHORIZED);
        }

        // Nếu là superadmin thì có tất cả quyền
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Kiểm tra quyền
        if (!$user->hasPermission($permissions)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có quyền thực hiện hành động này',
                'required_permissions' => $permissions
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
