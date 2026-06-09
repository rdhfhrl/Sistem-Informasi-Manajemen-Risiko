<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditorMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (auth()->user()->role !== 'auditor') {
            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Hanya Auditor yang dapat mengakses halaman ini.');
        }

        return $next($request);
    }
}