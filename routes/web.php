<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\Admin\ReportManagementController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\LaporMapController;
use App\Http\Controllers\UserHistoryReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TopUpController;
// use App\Http\Controllers\VerificationController;
use App\Http\Controllers\GisController; 
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Feedback;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home Page
Route::get('/', function () {
    $feedbacks = Feedback::with('user')->latest()->take(10)->get();
    $verifiedReports = Report::whereIn('status', ['published', 'approved'])->count();
    $totalReports = Report::count();
    $fraudReports = Report::where('service', 'Penipuan')->count();

    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'feedbacks' => $feedbacks,
        'verifiedReports' => $verifiedReports,
        'totalReports' => $totalReports,
        'fraudReports' => $fraudReports,
        'flash' => [
            'status' => session('status'),
            'error' => session('error'),
        ],
    ]);
});

// Static Pages
Route::get('/verifikasi', function () {
    $feedbacks = Feedback::with('user')->where('kategori', 'Verifikasi')->latest()->take(10)->get();
    return Inertia::render('verifikasi', [
        'feedbacks' => $feedbacks
    ]);
});

Route::get('/tentang-kami', function () {
    return Inertia::render('TentangKami');
})->name('tentang-kami');

// Public Report Pages
Route::get('/LaporMap', [LaporMapController::class, 'index'])->name('LaporMap');
Route::get('/CariLaporan', [ReportController::class, 'index'])->name('CariLaporan');
Route::get('/pelaporan', [ReportController::class, 'create'])->name('laporan.create');

// Public Feedback
Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');

//Public Populasi
Route::get('/Populasi', function () {
    return Inertia::render('Populasi/CekPopulasi');
})->name('populasi');




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::get('/api/laporan/search', [ReportController::class, 'search'])->name('laporan.search');

Route::get('/api/dashboard-data', [DashboardAdminController::class, 'getDashboardData'])
    ->middleware(['auth', 'contact.verified', 'role:admin||verifier'])
    ->name('admin.api.dashboard.data');

Route::post('api/population', [GisController::class, 'getPopulationStats']); 
Route::get('api/population', [GisController::class, 'getPopulationStats']); 

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'contact.verified'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    
    // Feedback Management
    Route::resource('feedback', FeedbackController::class)->except(['index', 'show']);
    
    // Report Management
    Route::post('/pelaporan/create', [ReportController::class, 'store'])->name('laporan.store');
    Route::post('/laporan/flag', [ReportController::class, 'flagReport'])->name('laporan.flag');
    Route::get('/laporan-saya', [UserHistoryReportController::class, 'allHistory'])->name('history');
    
    // Top-Up Management
    Route::prefix('top-ups')->name('top-ups.')->group(function () {
        Route::get('/history', [TopUpController::class, 'index'])->name('index');
        Route::get('/create', [TopUpController::class, 'create'])->name('create');
        Route::post('/create', [TopUpController::class, 'store'])->name('store');
    });
    
    // Verification
    Route::post('/verify/{featureType}', [VerificationController::class, 'handleVerification']);
});

/*
|--------------------------------------------------------------------------
| Admin & Verifier Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'contact.verified', 'role:admin|verifier'])->group(function () {
    
    // Admin Dashboard
    Route::get('admin/dashboard', [DashboardAdminController::class, 'index'])
        ->name('dashboard.admin.page');
    
    // Admin Report Management Page
    Route::get('/admin/pelaporan', function () {
        $user = Auth::user();
        $permissions = [];

        if ($user) {
            $permissions = [
                'verifyReports' => $user->can('verify_reports'),
                'viewPenipuan' => $user->can('view_reports_penipuan'),
                'viewInfrastructure' => $user->can('view_reports_infrastruktur'),
                'view_reports_by_region' => $user->can('view_reports_by_region'),
            ];
        }
        
        return Inertia::render('Admin/Pelaporan/Index', [
            'can' => $permissions,
        ]);
    })->name('admin.reports.page');
    
    // Admin Top-Up Management
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/top-ups', [TopUpController::class, 'adminIndex'])->name('topups.index');
        Route::get('/top-ups/export-logs', [TopUpController::class, 'exportTopUpLogsToCsv'])->name('topups.exportLogs');
        
        // Top-Up API Routes
        Route::prefix('api/topups')->name('api.topups.')->group(function () {
            Route::get('/', [TopUpController::class, 'getTopUpData'])->name('data');
            Route::post('/{topUp}/verify', [TopUpController::class, 'verify'])
                ->middleware('can:verify,topUp')
                ->name('verify');
            Route::post('/{topUp}/reject', [TopUpController::class, 'reject'])
                ->middleware('can:reject,topUp')
                ->name('reject');
        });
    });
    
    // Report Management API
    Route::prefix('data/reports')->name('admin.data.reports.')->group(function () {
        Route::get('/', [ReportManagementController::class, 'index'])->name('index');
        Route::get('/{report}/flags', [ReportManagementController::class, 'getFlags'])->name('flags.index');
        Route::patch('/{report}/accept', [ReportManagementController::class, 'accept'])->name('accept');
        Route::patch('/{report}/rejected', [ReportManagementController::class, 'reject'])->name('reject');
        Route::patch('/{report}/publish', [ReportManagementController::class, 'publish'])->name('publish');
        Route::patch('/{report}/unpublish', [ReportManagementController::class, 'unpublish'])->name('unpublish');
        Route::patch('/{report}/solved', [ReportManagementController::class, 'solved'])->name('solved');
        Route::delete('/{report}', [ReportManagementController::class, 'destroy'])->name('destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Only Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->group(function () {
    
    // User Management
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.delete');
        Route::post('/users/{user}/assign-role', [UserManagementController::class, 'assignRole'])->name('users.assignRole');
        Route::get('/users/{user}/permissions', [UserManagementController::class, 'editPermissions']);
        Route::patch('/users/{user}/permissions', [UserManagementController::class, 'updatePermissions']);
        
        // Role Management
        Route::post('/roles', [UserManagementController::class, 'storeRole'])->name('roles.store');
        Route::delete('/roles/{roles}', [UserManagementController::class, 'destroyRole'])->name('roles.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';