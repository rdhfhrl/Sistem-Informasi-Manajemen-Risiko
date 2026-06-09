<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display user profile
     */
    public function show()
    {
        $user = Auth::user();
        
        // Get user statistics
        $stats = $this->getUserStats($user);
        
        // Get recent activity
        $recentActivity = $this->getRecentActivity($user);
        
        return view('profile.show', compact('user', 'stats', 'recentActivity'));
    }
    
    /**
     * Show edit profile form
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }
    
    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ], [
            'current_password.required_with' => 'Password saat ini diperlukan untuk mengubah password.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password tidak cocok.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar yang didukung: jpeg, png, jpg, gif.',
            'avatar.max' => 'Ukuran gambar maksimal 2MB.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Start database transaction
            \DB::beginTransaction();
            
            // Update basic info
            $user->name = $request->name;
            $user->email = $request->email;
            
            // Update additional fields if they exist in database
            if ($request->filled('phone') && in_array('phone', $user->getFillable())) {
                $user->phone = $request->phone;
            }
            
            if ($request->filled('address') && in_array('address', $user->getFillable())) {
                $user->address = $request->address;
            }
            
            // Handle password change
            if ($request->filled('new_password')) {
                if (!Hash::check($request->current_password, $user->password)) {
                    return redirect()->back()
                        ->withErrors(['current_password' => 'Password saat ini salah.'])
                        ->withInput();
                }
                $user->password = Hash::make($request->new_password);
            }
            
            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                // Store new avatar
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
            }
            
            // Handle avatar removal
            if ($request->has('remove_avatar') && $user->avatar) {
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $user->avatar = null;
            }
            
            $user->save();
            
            \DB::commit();
            
            return redirect()->route('profile.show')
                ->with('success', 'Profil berhasil diperbarui.');
                
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Profile update error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat memperbarui profil.'])
                ->withInput();
        }
    }
    
    /**
     * Get user statistics
     */
    private function getUserStats($user)
    {
        $stats = [];
        
        try {
            // Risk statistics
            if (class_exists(\App\Models\Risk::class)) {
                $stats['total_risks'] = \App\Models\Risk::where('risk_user_id', $user->id)->count();
                $stats['high_risks'] = \App\Models\Risk::where('risk_user_id', $user->id)
                    ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                    ->count();
            }
            
            // Mitigation statistics
            if (class_exists(\App\Models\RiskMitigation::class)) {
                $stats['active_mitigations'] = \App\Models\RiskMitigation::where('responsible_party', $user->id)
                    ->where('status', 'in_progress')
                    ->count();
                
                $stats['completed_mitigations'] = \App\Models\RiskMitigation::where('responsible_party', $user->id)
                    ->where('status', 'completed')
                    ->count();
            }
            
            // Report statistics
            if (class_exists(\App\Models\Report::class)) {
                $stats['generated_reports'] = \App\Models\Report::where('generated_by', $user->id)->count();
                $stats['approved_reports'] = \App\Models\Report::where('approved_by', $user->id)->count();
            }
            
            // Schedule statistics
            if (class_exists(\App\Models\ReportSchedule::class)) {
                $stats['created_schedules'] = \App\Models\ReportSchedule::where('created_by', $user->id)->count();
                $stats['active_schedules'] = \App\Models\ReportSchedule::where('created_by', $user->id)
                    ->where('is_active', true)
                    ->count();
            }
            
        } catch (\Exception $e) {
            \Log::error('User stats error: ' . $e->getMessage());
        }
        
        return $stats;
    }
    
    /**
     * Get recent activity for user
     */
    private function getRecentActivity($user)
    {
        $activities = [];
        
        try {
            // Recent risks
            if (class_exists(\App\Models\Risk::class)) {
                $recentRisks = \App\Models\Risk::where('risk_user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get()
                    ->map(function($risk) {
                        return [
                            'type' => 'risk',
                            'title' => 'Membuat risiko baru',
                            'description' => $risk->risk_name,
                            'icon' => 'alert-triangle',
                            'icon_color' => 'text-orange-600',
                            'url' => route('risks.show', $risk->risk_id),
                            'date' => $risk->created_at,
                        ];
                    });
                
                $activities = array_merge($activities, $recentRisks->toArray());
            }
            
            // Recent mitigations
            if (class_exists(\App\Models\RiskMitigation::class)) {
                $recentMitigations = \App\Models\RiskMitigation::where('responsible_party', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get()
                    ->map(function($mitigation) {
                        return [
                            'type' => 'mitigation',
                            'title' => 'Melakukan mitigasi',
                            'description' => $mitigation->mitigation_plan,
                            'icon' => 'shield',
                            'icon_color' => 'text-blue-600',
                            'url' => route('risk-mitigations.show', [
                                'risk' => $mitigation->risk_mitigation_risk_id,
                                'riskMitigation' => $mitigation->risk_mitigation_id
                            ]),
                            'date' => $mitigation->created_at,
                        ];
                    });
                
                $activities = array_merge($activities, $recentMitigations->toArray());
            }
            
            // Recent reports
            if (class_exists(\App\Models\Report::class)) {
                $recentReports = \App\Models\Report::where('generated_by', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get()
                    ->map(function($report) {
                        return [
                            'type' => 'report',
                            'title' => 'Membuat laporan',
                            'description' => $report->title,
                            'icon' => 'file-text',
                            'icon_color' => 'text-green-600',
                            'url' => route('reports.show', $report->report_id),
                            'date' => $report->created_at,
                        ];
                    });
                
                $activities = array_merge($activities, $recentReports->toArray());
            }
            
            // Sort activities by date
            usort($activities, function($a, $b) {
                return $b['date'] <=> $a['date'];
            });
            
            // Limit to 10 most recent activities
            $activities = array_slice($activities, 0, 10);
            
        } catch (\Exception $e) {
            \Log::error('Recent activity error: ' . $e->getMessage());
        }
        
        return $activities;
    }
    
    /**
     * Update user preferences
     */
    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'theme' => 'nullable|in:light,dark,system',
            'language' => 'nullable|in:id,en',
            'notifications_email' => 'nullable|boolean',
            'notifications_push' => 'nullable|boolean',
            'timezone' => 'nullable|timezone',
            'date_format' => 'nullable|string',
            'time_format' => 'nullable|in:12,24',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Store preferences in user's metadata or separate table
            // For now, we'll store in a JSON column if exists
            
            if (in_array('preferences', $user->getFillable())) {
                $preferences = $user->preferences ?? [];
                
                $preferences['theme'] = $request->input('theme', $preferences['theme'] ?? 'system');
                $preferences['language'] = $request->input('language', $preferences['language'] ?? 'id');
                $preferences['notifications'] = [
                    'email' => $request->boolean('notifications_email', $preferences['notifications']['email'] ?? true),
                    'push' => $request->boolean('notifications_push', $preferences['notifications']['push'] ?? true),
                ];
                $preferences['timezone'] = $request->input('timezone', $preferences['timezone'] ?? config('app.timezone'));
                $preferences['date_format'] = $request->input('date_format', $preferences['date_format'] ?? 'd/m/Y');
                $preferences['time_format'] = $request->input('time_format', $preferences['time_format'] ?? '24');
                
                $user->preferences = $preferences;
                $user->save();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Preferensi berhasil diperbarui.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Preferences update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui preferensi.'
            ], 500);
        }
    }
    
    /**
     * Get user preferences
     */
    public function getPreferences()
    {
        $user = Auth::user();
        
        $defaults = [
            'theme' => 'system',
            'language' => 'id',
            'notifications' => [
                'email' => true,
                'push' => true,
            ],
            'timezone' => config('app.timezone'),
            'date_format' => 'd/m/Y',
            'time_format' => '24',
        ];
        
        if (isset($user->preferences) && is_array($user->preferences)) {
            $preferences = array_merge($defaults, $user->preferences);
        } else {
            $preferences = $defaults;
        }
        
        return response()->json($preferences);
    }
    
    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'password' => 'required|current_password',
            'confirmation' => 'required|in:DELETE',
        ], [
            'password.required' => 'Password diperlukan untuk menghapus akun.',
            'password.current_password' => 'Password salah.',
            'confirmation.required' => 'Konfirmasi diperlukan.',
            'confirmation.in' => 'Harap ketik DELETE untuk konfirmasi.',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        try {
            // Soft delete user
            $user->delete();
            
            // Logout user
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('success', 'Akun Anda telah berhasil dihapus.');
                
        } catch (\Exception $e) {
            \Log::error('Account deletion error: ' . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat menghapus akun.'])
                ->withInput();
        }
    }
    
    /**
     * Upload avatar via AJAX
     */
    public function uploadAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $user = Auth::user();
            
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            
            // Store new avatar
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
            $user->save();
            
            return response()->json([
                'success' => true,
                'avatar_url' => Storage::url($path),
                'message' => 'Avatar berhasil diperbarui.'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Avatar upload error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengupload avatar.'
            ], 500);
        }
    }
}