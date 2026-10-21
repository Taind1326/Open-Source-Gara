<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => CheckRole::class,
        ]);

        $middleware->redirectGuestsTo(
            fn (Request $request) => route('login')
        );

        $middleware->redirectUsersTo(function (Request $request) {
            return route(
                match ($request->user()?->VaiTro) {
                    'ADMIN' => 'admin.dashboard',
                    'TECHNICIAN' => 'ktv.lichhen.index',
                    default => 'public.home',
                }
            );
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['MatKhau', 'MatKhau_confirmation', 'MatKhauHienTai']);

        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*')
                || $request->expectsJson(),
        );
    })
    ->create();