<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->isAdmin()) {
            return $next($request);
        }

        if (auth()->user()->isForeman()) {
            return redirect()
                ->route('foreman.dashboard')
                ->with('error', 'Panel admin hanya untuk administrator. Mandor dapat mengelola pesanan yang ditugaskan di panel mandor.');
        }

        return redirect()
            ->route('order.index')
            ->with('error', 'Halaman admin hanya dapat diakses oleh administrator.');
    }
}
