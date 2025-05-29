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
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Feedback;
use App\Models\Report;


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

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'contact.verified'])->name('dashboard');

Route::get('admin/dashboard', [DashboardAdminController::class, 'index'])
    ->middleware(['auth', 'contact.verified', 'role:admin||verifier'])
    ->name('dashboard.admin');

Route::get('/verifikasi', function () {
    $feedbacks = Feedback::with('user')->where('kategori', 'Verifikasi')->latest()->take(10)->get();
    return Inertia::render('verifikasi', [
        'feedbacks' => $feedbacks
    ]);
});

Route::get('/LaporMap', [LaporMapController::class, 'index'])->name('LaporMap');

Route::get('/CariLaporan', [ReportController::class, 'search'])->name('CariLaporan');


Route::get('/tentang-kami', function () {
    return Inertia::render('TentangKami');
})->name('tentang-kami');

Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');

Route::middleware(['auth', 'contact.verified'])->group(function () {
    Route::resource('feedback', FeedbackController::class)->except(['index', 'show']);
});

Route::middleware('auth','contact.verified')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
});

Route::middleware('auth','contact.verified')->group(function () {
    Route::middleware('role:admin||verifier')->group(function () {
        Route::get('/admin/pelaporan', [ReportManagementController::class, 'index'])->name('laporan.index');
        Route::patch('/pelaporan/{report}/terima', [ReportManagementController::class, 'accept'])->name('laporan.terima');
        Route::patch('/pelaporan/{report}/tolak', [ReportManagementController::class, 'reject'])->name('laporan.tolak');
        Route::patch('/pelaporan/{report}/publikasikan', [ReportManagementController::class, 'publish'])->name('laporan.publikasikan');
        Route::delete('/pelaporan/{report}/hapus', [ReportManagementController::class, 'destroy'])->name('laporan.hapus');
        Route::get('/pelaporan/{report}/flags', [ReportManagementController::class, 'getFlags'])->name('laporan.flags');
        Route::patch('/pelaporan/{report}/selesai', [ReportManagementController::class, 'solved'])->name('laporan.solved');
        Route::patch('/pelaporan/{report}/batalkan-publikasi', [ReportManagementController::class, 'unpublish'])->name('laporan.unpublish');
    });

    Route::post('/pelaporan/create', [ReportController::class, 'store'])->name('laporan.store');
    Route::post('/laporan/flag', [ReportController::class, 'flagReport'])->name('laporan.flag');

});

Route::get('/pelaporan', [ReportController::class, 'create'])->name('laporan.create');

Route::middleware(['auth','contact.verified'])->group(function () {
    Route::get('/laporan-saya', [UserHistoryReportController::class, 'allHistory'])->name('history');
});

Route::middleware(['auth','contact.verified'])->group(function () {
    Route::get('/top-ups/history', [TopUpController::class, 'index'])->name('top-ups.index');     
    Route::get('/top-ups', [TopUpController::class, 'create'])->name('top-ups.create');; 
    Route::post('/top-ups/create', [TopUpController::class, 'store'])->name('top-ups.store');       
});

Route::middleware(['auth', 'contact.verified', 'role:admin|verifier'])->group(function () {
    Route::get('/admin/top-ups', [TopUpController::class, 'adminIndex'])->name('admin.topups.index');

    Route::post('/admin/top-ups/{topUp}/verify', [TopUpController::class, 'verify'])
        ->middleware('can:verify,topUp')
        ->name('admin.topups.verify');

    Route::post('/admin/top-ups/{topUp}/reject', [TopUpController::class, 'reject'])
        ->middleware('can:reject,topUp')
        ->name('admin.topups.reject');

    Route::get('/admin/top-ups/export-logs', [TopUpController::class, 'exportTopUpLogsToCsv'])
        ->name('admin.topups.exportLogs');
});


Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.delete');
    Route::post('/users/{user}/assign-role', [UserManagementController::class, 'assignRole'])->name('users.assignRole');
    Route::post('/roles', [UserManagementController::class, 'storeRole'])->name('roles.store');
    Route::delete('/roles/{roles}', [UserManagementController::class, 'destroyRole'])->name('roles.destroy');
    Route::get('/users/{user}/permissions', [UserManagementController::class, 'editPermissions']);
    Route::patch('/users/{user}/permissions', [UserManagementController::class, 'updatePermissions']);

});

require __DIR__.'/auth.php';
