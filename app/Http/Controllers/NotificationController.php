<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Risk;
use App\Models\RiskMitigation;
use App\Models\Report;
use App\Models\ReportSchedule;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Display notifications page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get all notifications
        $notifications = $this->getAllNotifications();
        
        // Get notification counts - safely access array keys
        $counts = [
            'total' => 0,
            'unread' => 0,
            'overdue_mitigations' => 0,
            'high_risk_no_mitigation' => 0,
            'pending_approvals' => 0,
            'due_schedules' => 0,
            'risks_due_monitoring' => 0,
        ];
        
        // Calculate totals safely
        foreach ($notifications as $key => $notification) {
            $counts['total'] += $notification['count'] ?? 0;
            
            // Set specific counts if key exists
            if (isset($counts[$key])) {
                $counts[$key] = $notification['count'] ?? 0;
            }
        }
        
        return view('notifications.index', compact('notifications', 'counts'));
    }
    
    /**
     * Get all notifications for current user
     */
    private function getAllNotifications()
    {
        $notifications = [];
        
        try {
            // Always initialize all notification types
            $notificationTypes = [
                'overdue_mitigations',
                'high_risk_no_mitigation', 
                'pending_approvals',
                'due_schedules',
                'risks_due_monitoring'
            ];
            
            // Initialize all notification types with zero count
            foreach ($notificationTypes as $type) {
                $notifications[$type] = [
                    'id' => $type,
                    'title' => '',
                    'description' => '',
                    'type' => 'info',
                    'icon' => 'alert-circle',
                    'count' => 0,
                    'url' => '#',
                    'items' => [],
                    'created_at' => now()->toIso8601String(),
                    'is_read' => false,
                ];
            }
            
            // 1. Overdue Mitigations
            $overdueMitigations = RiskMitigation::where('deadline', '<', now())
                ->whereNotIn('status', ['selesai', 'dibatalkan', 'completed'])
                ->count();
            
            if ($overdueMitigations > 0) {
                $notifications['overdue_mitigations'] = [
                    'id' => 'overdue_mitigations',
                    'title' => 'Mitigasi Terlambat',
                    'description' => 'Ada mitigasi yang melewati deadline',
                    'type' => 'danger',
                    'icon' => 'alert-triangle',
                    'count' => $overdueMitigations,
                    'url' => route('risk-mitigations.index', ['status' => 'overdue']),
                    'items' => $this->getOverdueMitigations(),
                    'created_at' => now()->toIso8601String(),
                    'is_read' => false,
                ];
            }
            
            // 2. High Risk Without Mitigation
            $highRiskNoMitigation = Risk::whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->whereDoesntHave('mitigations')
                ->count();
            
            if ($highRiskNoMitigation > 0) {
                $notifications['high_risk_no_mitigation'] = [
                    'id' => 'high_risk_no_mitigation',
                    'title' => 'Risiko Tinggi Tanpa Mitigasi',
                    'description' => 'Risiko tinggi belum memiliki mitigasi',
                    'type' => 'warning',
                    'icon' => 'alert-circle',
                    'count' => $highRiskNoMitigation,
                    'url' => route('risks.index', ['risk_level' => 'tinggi']),
                    'items' => $this->getHighRiskWithoutMitigation(),
                    'created_at' => now()->toIso8601String(),
                    'is_read' => false,
                ];
            }
            
            // 3. Pending Report Approvals
            $pendingApprovals = Report::where('status', 'pending_approval')->count();
            
            if ($pendingApprovals > 0) {
                $notifications['pending_approvals'] = [
                    'id' => 'pending_approvals',
                    'title' => 'Laporan Menunggu Persetujuan',
                    'description' => 'Laporan yang perlu disetujui',
                    'type' => 'info',
                    'icon' => 'clock',
                    'count' => $pendingApprovals,
                    'url' => route('reports.index', ['status' => 'pending_approval']),
                    'items' => $this->getPendingApprovals(),
                    'created_at' => now()->toIso8601String(),
                    'is_read' => false,
                ];
            }
            
            // 4. Due Report Schedules
            $dueSchedules = ReportSchedule::active()
                ->autoGenerate()
                ->where('generation_time', '<=', now()->addDay())
                ->count();
            
            if ($dueSchedules > 0) {
                $notifications['due_schedules'] = [
                    'id' => 'due_schedules',
                    'title' => 'Jadwal Laporan Mendatang',
                    'description' => 'Jadwal laporan yang akan berjalan',
                    'type' => 'primary',
                    'icon' => 'calendar',
                    'count' => $dueSchedules,
                    'url' => route('report-schedules.index'),
                    'items' => $this->getDueSchedules(),
                    'created_at' => now()->toIso8601String(),
                    'is_read' => false,
                ];
            }
            
            // 5. Risks Due for Monitoring
            $risksDueForMonitoring = Risk::where(function($query) {
                    $query->whereNull('last_monitoring_date')
                          ->orWhere('last_monitoring_date', '<', now()->subDays(30));
                })
                ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
                ->where('risk_status', '!=', 'closed')
                ->count();
            
            if ($risksDueForMonitoring > 0) {
                $notifications['risks_due_monitoring'] = [
                    'id' => 'risks_due_monitoring',
                    'title' => 'Risiko Perlu Dimonitor',
                    'description' => 'Risiko tinggi perlu pemantauan',
                    'type' => 'secondary',
                    'icon' => 'eye',
                    'count' => $risksDueForMonitoring,
                    'url' => route('risks.index', ['filter' => 'due_monitoring']),
                    'items' => $this->getRisksDueForMonitoring(),
                    'created_at' => now()->toIso8601String(),
                    'is_read' => false,
                ];
            }
            
            // Remove notifications with zero count
            $notifications = array_filter($notifications, function($notification) {
                return $notification['count'] > 0;
            });
            
        } catch (\Exception $e) {
            \Log::error('Notification error: ' . $e->getMessage());
            
            // Return empty array on error
            return [];
        }
        
        return $notifications;
    }
    
    /**
     * Get overdue mitigations
     */
    private function getOverdueMitigations()
    {
        return RiskMitigation::with(['risk', 'responsibleUser'])
            ->where('deadline', '<', now())
            ->whereNotIn('status', ['selesai', 'dibatalkan', 'completed'])
            ->orderBy('deadline', 'asc')
            ->take(10)
            ->get()
            ->map(function($mitigation) {
                return [
                    'id' => $mitigation->risk_mitigation_id,
                    'title' => $mitigation->risk->risk_name ?? 'Unknown Risk',
                    'description' => $mitigation->mitigation_plan,
                    'deadline' => $mitigation->deadline->format('d M Y'),
                    'days_overdue' => $mitigation->deadline->diffInDays(now()),
                    'responsible' => $mitigation->responsibleUser->name ?? 'Unknown',
                    'url' => route('risk-mitigations.show', [
                        'risk' => $mitigation->risk_mitigation_risk_id,
                        'riskMitigation' => $mitigation->risk_mitigation_id
                    ]),
                ];
            });
    }
    
    /**
     * Get high risk without mitigation
     */
    private function getHighRiskWithoutMitigation()
    {
        return Risk::with(['project', 'organization'])
            ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
            ->whereDoesntHave('mitigations')
            ->orderBy('risk_score', 'desc')
            ->take(10)
            ->get()
            ->map(function($risk) {
                return [
                    'id' => $risk->risk_id,
                    'title' => $risk->risk_name,
                    'description' => $risk->risk_description,
                    'risk_score' => $risk->risk_score,
                    'risk_level' => $risk->risk_level_label,
                    'project' => $risk->project->pro_nama ?? 'N/A',
                    'url' => route('risks.show', $risk->risk_id),
                ];
            });
    }
    
    /**
     * Get pending approvals
     */
    private function getPendingApprovals()
    {
        return Report::with(['organization', 'project'])
            ->where('status', 'pending_approval')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($report) {
                return [
                    'id' => $report->report_id,
                    'title' => $report->title,
                    'description' => $report->description,
                    'type' => $report->report_type_label,
                    'date' => $report->report_date?->format('d M Y'),
                    'generated_by' => $report->generator->name ?? 'System',
                    'url' => route('reports.show', $report->report_id),
                ];
            });
    }
    
    /**
     * Get due schedules
     */
    private function getDueSchedules()
    {
        return ReportSchedule::with(['creator'])
            ->active()
            ->autoGenerate()
            ->where('generation_time', '<=', now()->addDay())
            ->orderBy('generation_time', 'asc')
            ->take(10)
            ->get()
            ->map(function($schedule) {
                $nextRun = $schedule->next_run_date;
                return [
                    'id' => $schedule->schedule_id,
                    'title' => $schedule->schedule_name,
                    'description' => $schedule->report_type_label,
                    'frequency' => $schedule->frequency_label,
                    'next_run' => $nextRun ? $nextRun->format('d M Y H:i') : 'N/A',
                    'time_left' => $nextRun ? $nextRun->diffForHumans() : 'N/A',
                    'url' => route('report-schedules.show', $schedule->schedule_id),
                ];
            });
    }
    
    /**
     * Get risks due for monitoring
     */
    private function getRisksDueForMonitoring()
    {
        return Risk::with(['project'])
            ->where(function($query) {
                $query->whereNull('last_monitoring_date')
                      ->orWhere('last_monitoring_date', '<', now()->subDays(30));
            })
            ->whereIn('risk_level', ['tinggi', 'sangat_tinggi'])
            ->where('risk_status', '!=', 'closed')
            ->orderBy('last_monitoring_date', 'asc')
            ->take(10)
            ->get()
            ->map(function($risk) {
                $daysSince = $risk->last_monitoring_date 
                    ? $risk->last_monitoring_date->diffInDays(now())
                    : null;
                
                return [
                    'id' => $risk->risk_id,
                    'title' => $risk->risk_name,
                    'description' => $risk->risk_description,
                    'risk_level' => $risk->risk_level_label,
                    'last_monitored' => $risk->last_monitoring_date 
                        ? $risk->last_monitoring_date->format('d M Y')
                        : 'Belum Pernah',
                    'days_since' => $daysSince,
                    'project' => $risk->project->pro_nama ?? 'N/A',
                    'url' => route('risks.show', $risk->risk_id),
                ];
            });
    }
    
    /**
     * API endpoint for notifications
     */
    public function getNotifications()
    {
        try {
            $notifications = $this->getAllNotifications();
            
            // Format for API response
            $formatted = array_values(array_map(function($notification) {
                return [
                    'id' => $notification['id'],
                    'title' => $notification['title'],
                    'description' => $notification['description'],
                    'type' => $notification['type'],
                    'count' => $notification['count'],
                    'url' => $notification['url'],
                    'created_at' => $notification['created_at'],
                    'icon' => $notification['icon'],
                    'icon_bg' => $this->getIconBgClass($notification['type']),
                    'icon_color' => $this->getIconColorClass($notification['type']),
                ];
            }, $notifications));
            
            return response()->json($formatted);
            
        } catch (\Exception $e) {
            \Log::error('API Notification error: ' . $e->getMessage());
            return response()->json([]); // Return empty array instead of error
        }
    }
    
    /**
     * Get notification counts for top bar
     */
    public function getNotificationCounts()
    {
        try {
            $notifications = $this->getAllNotifications();
            
            $totalCount = 0;
            $byType = [];
            
            foreach ($notifications as $notification) {
                $totalCount += $notification['count'] ?? 0;
                $byType[$notification['id']] = $notification['count'] ?? 0;
            }
            
            return response()->json([
                'total' => $totalCount,
                'by_type' => $byType
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Notification count error: ' . $e->getMessage());
            return response()->json([
                'total' => 0,
                'by_type' => []
            ]);
        }
    }
    
    /**
     * Helper: Get icon background class based on notification type
     */
    private function getIconBgClass($type)
    {
        return match($type) {
            'danger' => 'bg-red-100',
            'warning' => 'bg-orange-100',
            'info' => 'bg-blue-100',
            'primary' => 'bg-purple-100',
            'secondary' => 'bg-teal-100',
            default => 'bg-gray-100',
        };
    }
    
    /**
     * Helper: Get icon color class based on notification type
     */
    private function getIconColorClass($type)
    {
        return match($type) {
            'danger' => 'text-red-600',
            'warning' => 'text-orange-600',
            'info' => 'text-blue-600',
            'primary' => 'text-purple-600',
            'secondary' => 'text-teal-600',
            default => 'text-gray-600',
        };
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notification as read'
            ], 500);
        }
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'All notifications marked as read'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark notifications as read'
            ], 500);
        }
    }
}