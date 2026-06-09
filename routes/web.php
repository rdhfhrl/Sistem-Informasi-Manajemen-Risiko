<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    OrganizationController,
    StrategicObjectiveController,
    BusinessProcessController,
    ProjectController,
    RiskCategoryController,
    RiskController,
    RiskIdentificationController,
    RiskAnalysisController,
    RiskEvaluationController,
    RiskIndicatorController,
    RiskMitigationController,
    RiskMonitoringController,
    ReportController,
    ReportScheduleController,
    AuditController,
    NotificationController,
    SearchController,
    AuthController,
    UserController,
    ProfileController
};

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
if (app()->environment('local')) {
        Route::get('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/register', [AuthController::class, 'registerSave']);
    }
if (app()->environment(['local', 'testing'])) {
        Route::get('/demo-login/{role}', [AuthController::class, 'demoLogin'])->name('demo.login');
    }
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Dashboard routes
Route::middleware(['auth'])->group(function () {
    // Main dashboard route
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Dashboard API routes for AJAX
    Route::get('/dashboard/data', [DashboardController::class, 'getDashboardData'])->name('dashboard.data');
    Route::get('/dashboard/alerts', [DashboardController::class, 'getAlerts'])->name('dashboard.alerts');
    Route::get('/dashboard/activities', [DashboardController::class, 'getActivities'])->name('dashboard.activities');
    
    // Role-based dashboard routes (optional direct access)
    Route::get('/dashboard/admin', [DashboardController::class, 'adminDashboard'])->name('dashboard.admin')
        ->middleware('role:admin');
    
    Route::get('/dashboard/upr', [DashboardController::class, 'uprDashboard'])->name('dashboard.upr')
        ->middleware('role:unit_pemilik_risiko');
    
    Route::get('/dashboard/auditor', [DashboardController::class, 'auditorDashboard'])->name('dashboard.auditor')
        ->middleware('role:auditor');
});

// Profile Routes
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
});

Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user}', [UserController::class, 'show'])->name('show');
    Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
});

// Organizations
Route::resource('organizations', OrganizationController::class);
Route::get('organizations/{organization}/risks', [OrganizationController::class, 'risks'])->name('organizations.risks');

// Strategic Objectives
Route::resource('strategic-objectives', StrategicObjectiveController::class);

// Business Processes
Route::resource('business-processes', BusinessProcessController::class);

// Projects
Route::resource('projects', ProjectController::class);
Route::get('/export/pdf', [ProjectController::class, 'exportPDF'])->name('export.pdf');
Route::post('projects/{id}/update-status', [ProjectController::class, 'updateStatus'])->name('projects.update-status');
Route::get('projects/statistics', [ProjectController::class, 'getStatistics'])->name('projects.statistics');

// Risk Categories
Route::resource('risk-categories', RiskCategoryController::class);
Route::get('risk-categories/statistics', [RiskCategoryController::class, 'getStatistics'])->name('risk-categories.statistics');

// Risks 
Route::prefix('risks')->name('risks.')->group(function () {
    Route::get('/', [RiskController::class, 'index'])->name('index');
    Route::get('/create', [RiskController::class, 'create'])->name('create');
    Route::post('/', [RiskController::class, 'store'])->name('store');
    Route::get('/{risk}', [RiskController::class, 'show'])->name('show');
    Route::get('/{risk}/edit', [RiskController::class, 'edit'])->name('edit');
    Route::put('/{risk}', [RiskController::class, 'update'])->name('update');
    Route::delete('/{risk}', [RiskController::class, 'destroy'])->name('destroy');
    Route::get('/matrix', [RiskController::class, 'getRiskMatrix'])->name('matrix');
    Route::get('/high-risks', [RiskController::class, 'getHighRisks'])->name('high-risks');
    
    // Tambahan routes untuk method lain di controller
    Route::post('/{risk}/identification', [RiskController::class, 'storeIdentification'])->name('identification.store');
    Route::post('/{risk}/analysis', [RiskController::class, 'storeAnalysis'])->name('analysis.store');
    Route::post('/{risk}/evaluation', [RiskController::class, 'storeEvaluation'])->name('evaluation.store');
    Route::post('/{risk}/indicator', [RiskController::class, 'storeIndicator'])->name('indicator.store');
    Route::post('/{risk}/mitigation', [RiskController::class, 'storeMitigation'])->name('mitigation.store');
    Route::post('/{risk}/monitoring', [RiskController::class, 'storeMonitoring'])->name('monitoring.store');
});

// Risk Identifications
Route::prefix('risk-identifications')->name('risk-identifications.')->group(function () {
    Route::get('/', [RiskIdentificationController::class, 'index'])->name('index');
    Route::get('/create', [RiskIdentificationController::class, 'create'])->name('create');
    Route::post('/', [RiskIdentificationController::class, 'store'])->name('store');
    Route::get('/edit', [RiskIdentificationController::class, 'edit'])->name('edit');
    Route::put('/', [RiskIdentificationController::class, 'update'])->name('update');
    Route::delete('/', [RiskIdentificationController::class, 'destroy'])->name('destroy');
    Route::get('/statistics', [RiskIdentificationController::class, 'getStatistics'])->name('statistics');
});

// Risk Analyses
Route::prefix('risk-analyses')->name('risk-analyses.')->group(function () {
    Route::get('/', [RiskAnalysisController::class, 'index'])->name('index');
    Route::get('/all', [RiskAnalysisController::class, 'allAnalyses'])->name('all');
    
    Route::prefix('risk/{riskId}')->group(function () {
        Route::get('/', [RiskAnalysisController::class, 'byRisk'])->name('by-risk');
        Route::get('/create', [RiskAnalysisController::class, 'create'])->name('create');
        Route::get('/{analysisId}', [RiskAnalysisController::class, 'show'])->name('show');
        Route::post('/', [RiskAnalysisController::class, 'store'])->name('store');
        Route::get('/{analysisId}/edit', [RiskAnalysisController::class, 'edit'])->name('edit');
        Route::put('/{analysisId}', [RiskAnalysisController::class, 'update'])->name('update');
        Route::delete('/{analysisId}', [RiskAnalysisController::class, 'destroy'])->name('destroy');
    });
});

// Risk Evaluations
Route::prefix('risk-evaluations')->name('risk-evaluations.')->group(function () {
    Route::get('/', [RiskEvaluationController::class, 'index'])->name('index');
    Route::get('/all', [RiskEvaluationController::class, 'all'])->name('all');
    
    Route::prefix('risk/{riskId}')->group(function () {
        Route::get('/', [RiskEvaluationController::class, 'byRisk'])->name('by-risk');
        Route::get('/create', [RiskEvaluationController::class, 'create'])->name('create');
        Route::post('/', [RiskEvaluationController::class, 'store'])->name('store');
        Route::get('/{evaluationId}', [RiskEvaluationController::class, 'show'])->name('show');
        Route::get('/{evaluationId}/edit', [RiskEvaluationController::class, 'edit'])->name('edit');
        Route::put('/{evaluationId}', [RiskEvaluationController::class, 'update'])->name('update');
        Route::delete('/{evaluationId}', [RiskEvaluationController::class, 'destroy'])->name('destroy');
    });
});

// Risk Indicators
Route::prefix('risk-indicators')->name('risk-indicators.')->group(function () {
    Route::get('/', [RiskIndicatorController::class, 'index'])->name('index');
    Route::get('/all', [RiskIndicatorController::class, 'all'])->name('all');
    Route::get('/exceeded-thresholds', [RiskIndicatorController::class, 'all'])->where('filter', 'exceeded')->name('exceeded-thresholds');
    
    Route::prefix('risk/{riskId}')->group(function () {
        Route::get('/', [RiskIndicatorController::class, 'byRisk'])->name('by-risk');
        Route::get('/create', [RiskIndicatorController::class, 'create'])->name('create');
        Route::post('/', [RiskIndicatorController::class, 'store'])->name('store');
        Route::get('/{indicatorId}', [RiskIndicatorController::class, 'show'])->name('show');
        Route::get('/{indicatorId}/edit', [RiskIndicatorController::class, 'edit'])->name('edit');
        Route::put('/{indicatorId}', [RiskIndicatorController::class, 'update'])->name('update');
        Route::delete('/{indicatorId}', [RiskIndicatorController::class, 'destroy'])->name('destroy');
    });
    
    // API routes
    Route::post('/{indicatorId}/update-value', [RiskIndicatorController::class, 'updateValue'])->name('update-value');
});

// Risk Mitigations
Route::prefix('risk-mitigations')->name('risk-mitigations.')->group(function () {
    Route::get('/', [RiskMitigationController::class, 'index'])->name('index');
    Route::get('/all', [RiskMitigationController::class, 'all'])->name('all');
    
    Route::prefix('risk/{riskId}')->group(function () {
        Route::get('/', [RiskMitigationController::class, 'byRisk'])->name('by-risk');
        Route::get('/create', [RiskMitigationController::class, 'create'])->name('create');
        Route::post('/', [RiskMitigationController::class, 'store'])->name('store');
        Route::get('/{mitigationId}', [RiskMitigationController::class, 'show'])->name('show');
        Route::get('/{mitigationId}/edit', [RiskMitigationController::class, 'edit'])->name('edit');
        Route::put('/{mitigationId}', [RiskMitigationController::class, 'update'])->name('update');
        Route::delete('/{mitigationId}', [RiskMitigationController::class, 'destroy'])->name('destroy');
    });
});

// Risk Monitorings
Route::prefix('monitorings')->name('risk-monitorings.')->group(function () {
    Route::get('/', [RiskMonitoringController::class, 'index'])->name('index');
    Route::get('/all', [RiskMonitoringController::class, 'all'])->name('all');
    
    Route::prefix('risk/{riskId}')->group(function () {
        Route::get('/', [RiskMonitoringController::class, 'byRisk'])->name('by-risk');
        Route::get('/create', [RiskMonitoringController::class, 'create'])->name('create');
        Route::post('/', [RiskMonitoringController::class, 'store'])->name('store');
        Route::get('/{monitoringId}', [RiskMonitoringController::class, 'show'])->name('show');
        Route::get('/{monitoringId}/edit', [RiskMonitoringController::class, 'edit'])->name('edit');
        Route::put('/{monitoringId}', [RiskMonitoringController::class, 'update'])->name('update');
        Route::delete('/{monitoringId}', [RiskMonitoringController::class, 'destroy'])->name('destroy');
    });
});

// REPORTS ROUTES - FIXED
Route::prefix('reports')->name('reports.')->group(function () {
    // CRUD Routes
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('/create', [ReportController::class, 'create'])->name('create');
    Route::post('/', [ReportController::class, 'store'])->name('store');
    Route::get('/{report}', [ReportController::class, 'show'])->name('show');
    Route::get('/{report}/edit', [ReportController::class, 'edit'])->name('edit');
    Route::put('/{report}', [ReportController::class, 'update'])->name('update');
    Route::delete('/{report}', [ReportController::class, 'destroy'])->name('destroy');
    
    // Approval and PDF
    Route::post('/{report}/approve', [ReportController::class, 'approve'])->name('approve');
    Route::get('/{report}/generate-pdf', [ReportController::class, 'generatePDF'])->name('generate.pdf');
    Route::get('/{report}/download', [ReportController::class, 'downloadFile'])->name('download');
    
    // Schedule-related routes
    Route::post('/generate-from-schedule/{schedule}', [ReportController::class, 'generateFromSchedule'])->name('generate-from-schedule');
    Route::post('/bulk-generate', [ReportController::class, 'bulkGenerateFromSchedule'])->name('bulk.generate');
    
    // Specific report generators
    Route::post('/generate/monitoring', [ReportController::class, 'generateMonitoringReport'])->name('generate.monitoring');
    Route::post('/generate/risk-profile', [ReportController::class, 'generateRiskProfileReport'])->name('generate.risk-profile');
    Route::post('/generate/executive-summary', [ReportController::class, 'generateExecutiveSummary'])->name('generate.executive-summary');
    Route::post('/generate/mitigation-effectiveness', [ReportController::class, 'generateMitigationEffectivenessReport'])->name('generate.mitigation-effectiveness');
    Route::post('/generate/dashboard', [ReportController::class, 'generateDashboardReport'])->name('generate.dashboard');
});

// REPORT SCHEDULES ROUTES - DIPERBAIKI
Route::prefix('report-schedules')->name('report-schedules.')->group(function () {
    // CRUD Routes
    Route::get('/', [ReportScheduleController::class, 'index'])->name('index');
    Route::get('/create', [ReportScheduleController::class, 'create'])->name('create');
    Route::post('/', [ReportScheduleController::class, 'store'])->name('store');
    Route::get('/{reportSchedule}', [ReportScheduleController::class, 'show'])->name('show');
    Route::get('/{reportSchedule}/edit', [ReportScheduleController::class, 'edit'])->name('edit');
    Route::put('/{reportSchedule}', [ReportScheduleController::class, 'update'])->name('update');
    Route::delete('/{reportSchedule}', [ReportScheduleController::class, 'destroy'])->name('destroy');
    
    // Additional Actions
    Route::post('/{reportSchedule}/run-now', [ReportScheduleController::class, 'runNow'])->name('run-now');
    Route::post('/{reportSchedule}/duplicate', [ReportScheduleController::class, 'duplicate'])->name('duplicate');
    Route::patch('/{reportSchedule}/toggle-active', [ReportScheduleController::class, 'toggleActive'])->name('toggle-active');
    Route::patch('/{reportSchedule}/activate', [ReportScheduleController::class, 'activate'])->name('activate');
    Route::patch('/{reportSchedule}/deactivate', [ReportScheduleController::class, 'deactivate'])->name('deactivate');
    Route::get('/{reportSchedule}/logs', [ReportScheduleController::class, 'logs'])->name('logs');
    
    // Manual Generation
    Route::post('/{reportSchedule}/generate-report', [ReportScheduleController::class, 'generateReport'])->name('generate-report');
});

// Audits
Route::resource('audits', AuditController::class);
Route::get('audits/{riskId}/by-risk', [AuditController::class, 'getRiskAudits']);
Route::get('audits/statistics', [AuditController::class, 'getStatistics']);

//Profile Routes
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    Route::post('/preferences', [ProfileController::class, 'updatePreferences'])->name('update-preferences');
    Route::get('/preferences', [ProfileController::class, 'getPreferences'])->name('get-preferences');
    Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('upload-avatar');
});

// Search Routes
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/advanced', [SearchController::class, 'advancedSearch'])->name('search.advanced');
Route::post('/search/advanced', [SearchController::class, 'performAdvancedSearch'])->name('search.perform');
Route::get('/api/search', [SearchController::class, 'apiSearch'])->name('api.search');
Route::get('/api/search/stats', [SearchController::class, 'getSearchStats'])->name('api.search.stats');

// Notification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/api/notifications', [NotificationController::class, 'getNotifications'])->name('notifications.api');
    Route::get('/api/notification-counts', [NotificationController::class, 'getNotificationCounts'])->name('notifications.counts');
    Route::post('/api/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/api/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});

// API Routes
Route::prefix('api')->middleware(['auth:sanctum'])->group(function () {
    // Search API
    Route::get('/search', [SearchController::class, 'apiSearch'])->name('api.search');
    
    // Notifications API
    Route::get('/notifications', [NotificationController::class, 'getNotifications'])->name('api.notifications');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('api.notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('api.notifications.mark-all-read');
    
    // Due Schedules API
    Route::get('/due-schedules', [ReportScheduleController::class, 'getDueSchedules'])->name('api.due-schedules');
    
    // Risk Indicators
    Route::post('/risk-indicators/{indicatorId}/update-value', [RiskIndicatorController::class, 'updateValue'])->name('api.risk-indicators.update-value');
    Route::get('/risk-indicators/{indicatorId}/measurements', [RiskIndicatorController::class, 'getIndicatorMeasurements'])->name('api.risk-indicators.measurements');
    Route::get('/risk-indicators/exceeded-thresholds', [RiskIndicatorController::class, 'getExceededThresholds'])->name('api.risk-indicators.exceeded-thresholds');
    
    // Reports
    Route::get('/reports/statistics', [ReportController::class, 'getReportStatistics'])->name('api.reports.statistics');
    Route::get('/reports/trend-data', [ReportController::class, 'getTrendData'])->name('api.reports.trend-data');
    Route::get('/reports/scheduled-stats', [ReportController::class, 'getScheduledReportsStats'])->name('api.reports.scheduled-stats');
    
    // Dashboard Data
    Route::get('/dashboard/matrix', [DashboardController::class, 'getDashboardData'])->name('api.dashboard.matrix');
    Route::get('/dashboard/trend', [DashboardController::class, 'getDashboardTrend'])->name('api.dashboard.trend');
    Route::get('/dashboard/project-status', [DashboardController::class, 'getProjectStatus'])->name('api.dashboard.project-status');
    Route::get('/dashboard/risk-by-category', [DashboardController::class, 'getRiskByCategory'])->name('api.dashboard.risk-by-category');
});

// Fallback for undefined routes
Route::fallback(function () {
    return redirect()->route('dashboard');
});