<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Jika user role ada di daftar roles yang diizinkan
        if (in_array($user->role, $roles)) {
            return $next($request);
        }

        // Redirect berdasarkan role
        switch ($user->role) {
            case 'admin':
                return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
            case 'unit_pemilik_risiko':
                return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
            case 'auditor':
                return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
            default:
                return redirect()->route('login');
        }
    }
}
