<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class UserController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $newUsersMonth = User::where('created_at', '>=', now()->subMonth())->count();
        
        $roleDistribution = User::select('role', DB::raw('COUNT(*) as count'))
            ->groupBy('role')
            ->get();
            
        $users = User::with('organization')->latest()->paginate(10);
        $recentActivities = $this->getRecentUserActivities();

        return view('users.index', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'newUsersMonth',
            'roleDistribution',
            'users',
            'recentActivities'
        ));
    }

    public function create()
    {
        $roles = [
            'admin' => 'Administrator',
            'unit_pemilik_risiko' => 'Unit Pemilik Risiko',
            'auditor' => 'Auditor',
        ];

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => 'required|in:admin,unit_pemilik_risiko,auditor',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'organization_id' => 'nullable|exists:organizations,id',
        ]);

        try {
            $validatedData['password'] = Hash::make($validatedData['password']);
            $validatedData['is_active'] = true;

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $validatedData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            User::create($validatedData);

            return redirect()
                ->route('users.index')
                ->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function show(User $user)
    {
        $roles = [
            'admin' => 'Administrator',
            'unit_pemilik_risiko' => 'Unit Pemilik Risiko',
            'auditor' => 'Auditor',
        ];
        return view('users.show', compact('user', 'roles'));
    }

    public function edit(User $user)
    {
        $roles = [
            'admin' => 'Administrator',
            'unit_pemilik_risiko' => 'Unit Pemilik Risiko',
            'auditor' => 'Auditor',
        ];
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => 'required|in:admin,unit_pemilik_risiko,auditor',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'organization_id' => 'nullable|exists:organizations,id',
            'is_active' => 'boolean',
        ]);

        try {
            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $validatedData['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user->update($validatedData);

            return redirect()
                ->route('users.index')
                ->with('success', 'User berhasil diperbarui');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun sendiri']);
        }

        try {
            // Delete avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $userName = $user->name;
            $user->delete();

            return redirect()
                ->route('users.index')
                ->with('success', "User '{$userName}' berhasil dihapus");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun sendiri']);
        }

        try {
            $user->is_active = !$user->is_active;
            $user->save();

            $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()
                ->route('users.index')
                ->with('success', "User '{$user->name}' berhasil {$status}");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Helper method untuk aktivitas terbaru (TANPA last_login_at)
    private function getRecentUserActivities()
    {
        $activities = collect();
        
        // Recent user registrations
        $recentUsers = User::latest()->limit(5)->get();
        
        foreach ($recentUsers as $user) {
            $activities->push([
                'title' => 'User Baru: ' . $user->name,
                'description' => 'Role: ' . ($user->role === 'admin' ? 'Administrator' : 
                                  ($user->role === 'unit_pemilik_risiko' ? 'Unit Pemilik Risiko' : 'Auditor')),
                'time' => $user->created_at->diffForHumans(),
                'bg_color' => 'bg-blue-50',
                'text_color' => 'text-blue-600',
                'icon' => 'user-plus'
            ]);
        }

        // Recent profile updates (gunakan updated_at)
        $recentUpdates = User::where('updated_at', '!=', 'created_at')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($recentUpdates as $user) {
            if ($user->updated_at->gt($user->created_at)) {
                $activities->push([
                    'title' => 'Update Profil: ' . $user->name,
                    'description' => 'Data user diperbarui',
                    'time' => $user->updated_at->diffForHumans(),
                    'bg_color' => 'bg-purple-50',
                    'text_color' => 'text-purple-600',
                    'icon' => 'edit'
                ]);
            }
        }

        // Recent user status changes
        $recentStatusChanges = User::where('is_active', false)
            ->orderBy('updated_at', 'desc')
            ->limit(3)
            ->get();
            
        foreach ($recentStatusChanges as $user) {
            $activities->push([
                'title' => 'Status Nonaktif: ' . $user->name,
                'description' => 'User dinonaktifkan',
                'time' => $user->updated_at->diffForHumans(),
                'bg_color' => 'bg-yellow-50',
                'text_color' => 'text-yellow-600',
                'icon' => 'user-x'
            ]);
        }

        return $activities->sortByDesc(function($activity) {
                // Gunakan timestamp dari created_at jika ada
                return isset($activity['created_at']) ? $activity['created_at'] : now();
            })
            ->take(5)
            ->values();
    }
}   