<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dùng: ->middleware('role:ADMIN') hoặc ->middleware('role:ADMIN,TECHNICIAN')
 */
class KiemTraVaiTro
{
    public function handle(Request $request, Closure $next, string ...$vaiTro): Response
    {
        $user = $request->user();

        if (!$user || !$user->coVaiTro(...$vaiTro)) {
            abort(403, 'Bạn không có quyền truy cập chức năng này.');
        }

        return $next($request);
    }
}
