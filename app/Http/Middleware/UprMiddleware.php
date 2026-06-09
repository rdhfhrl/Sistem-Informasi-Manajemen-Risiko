<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UprMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== 'unit_pemilik_risiko') {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Hanya Unit Pemilik Risiko yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}