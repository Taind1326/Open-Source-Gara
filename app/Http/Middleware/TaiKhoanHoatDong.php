<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Nếu admin khoá tài khoản khi người dùng đang đăng nhập → đá ra ngay request kế tiếp.
 */
class TaiKhoanHoatDong
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && !$user->dangHoatDong()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['SoDienThoai' => 'Tài khoản của bạn đã bị khóa.']);
        }

        return $next($request);
    }
}
