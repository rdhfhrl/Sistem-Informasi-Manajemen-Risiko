<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login action
     */
    public function login(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Coba login
        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Redirect berdasarkan role (sesuai skripsi)
            return $this->redirectBasedOnRole();
        }

        // Jika gagal login
        throw ValidationException::withMessages([
            'email' => trans('auth.failed')
        ]);
    }

    /**
     * Show register form (TIDAK AKTIF - sesuai web.php)
     * Hanya untuk development/testing
     */
    public function register()
    {
        // Hanya tampilkan di development
        if (app()->environment('local')) {
            return view('auth.register');
        }
        
        abort(404);
    }

    /**
     * Handle registration (TIDAK AKTIF - sesuai web.php)
     */
    public function registerSave(Request $request)
    {
        // Hanya aktif di development
        if (!app()->environment('local')) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
            'role' => 'required|in:admin,unit_pemilik_risiko,auditor'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Buat user baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'organization_id' => $request->organization_id ?? null
        ]);

        // Login otomatis setelah register
        Auth::login($user);

        return $this->redirectBasedOnRole();
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }

    /**
     * Redirect user based on their role
     * Sesuai dengan use case diagram di skripsi
     */
    private function redirectBasedOnRole()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        // Flash message berdasarkan role
        $welcomeMessage = match($user->role) {
            'admin' => 'Selamat datang, Administrator!',
            'unit_pemilik_risiko' => 'Selamat datang, Unit Pemilik Risiko!',
            'auditor' => 'Selamat datang, Auditor!',
            default => 'Selamat datang!'
        };

        // Redirect ke dashboard dengan pesan
        return redirect()->route('dashboard')
            ->with('success', $welcomeMessage)
            ->with('role', $user->role);
    }

    /**
     * Show profile page (pindah ke ProfileController)
     * Method ini hanya untuk backward compatibility
     */
    public function profile()
    {
        // Redirect ke ProfileController
        return redirect()->route('profile.show');
    }

    /**
     * Demo login untuk testing (opsional)
     */
    public function demoLogin(Request $request, $role)
    {
        // Hanya aktif di development
        if (!app()->environment(['local', 'testing'])) {
            abort(404);
        }

        $demoAccounts = [
            'superadmin' => ['email' => 'superadmin@simr.test', 'password' => 'password123'],
            'admin' => ['email' => 'admin@dpupr.test', 'password' => 'password123'],
            'upr' => ['email' => 'riskmanager@uptd.test', 'password' => 'password123'],
            'auditor' => ['email' => 'auditor@simr.test', 'password' => 'password123'],
        ];

        if (!array_key_exists($role, $demoAccounts)) {
            return redirect()->route('login')
                ->with('error', 'Role demo tidak tersedia');
        }

        $account = $demoAccounts[$role];

        if (Auth::attempt(['email' => $account['email'], 'password' => $account['password']])) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole();
        }

        return redirect()->route('login')
            ->with('error', 'Gagal login dengan akun demo');
    }
}