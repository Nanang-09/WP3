<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForemanMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->isForeman()) {
            return $next($request);
        }

        if (auth()->user()->isAdmin()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('error', 'Kelola semua pesanan dan mandor melalui panel admin.');
        }

        return redirect()
            ->route('order.index')
            ->with('error', 'Panel mandor hanya untuk akun mandor lapangan.');
    }
}
