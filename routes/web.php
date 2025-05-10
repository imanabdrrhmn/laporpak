<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Feedback\FeedbackController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserHistoryReportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Feedback;






Route::get('/', function () {
    $feedbacks = Feedback::with('user')->latest()->take(10)->get();
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'feedbacks' => $feedbacks
    ]);
});

Route::get('/dashboard', function (){
    return Inertia::render('Dashboard');
})->middleware(['auth', 'contact.verified'])->name('dashboard');

Route::get('/verifikasi', function () {
    $feedbacks = Feedback::with('user')->where('kategori', 'Verifikasi')->latest()->take(10)->get();
    return Inertia::render('verifikasi', [
        'feedbacks' => $feedbacks
    ]);
});

 Route::get('/LaporMap', function (){
     return Inertia::render('LaporMap');
 })->name('LaporMap');

  Route::get('/CariLaporan', function (){
    $feedbacks = Feedback::where('kategori', 'Cari Laporan')->latest()->take(10)->get();
     return Inertia::render('Pelaporan/CariLaporan', [
        'feedbacks' => $feedbacks
     ]);
 })->name('CariLaporan');

Route::get('/tentang-kami', function () {
    return Inertia::render('TentangKami');
})->name('tentang-kami');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
});

Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');

Route::middleware(['auth', 'contact.verified'])->group(function () {
    Route::resource('feedback', FeedbackController::class)->except(['index', 'show']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users.index');
});

Route::middleware('auth')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('/pelaporan/index', [ReportController::class, 'index'])->name('laporan.index');
        Route::patch('/pelaporan/{report}/terima', [ReportController::class, 'accept'])->name('laporan.terima');
        Route::patch('/pelaporan/{report}/tolak', [ReportController::class, 'reject'])->name('laporan.tolak');
        Route::patch('/pelaporan/{report}/publikasikan', [ReportController::class, 'publish'])->name('laporan.publikasikan');
        Route::delete('/pelaporan/{report}/hapus', [ReportController::class, 'destroy'])->name('laporan.hapus');
    });

    Route::post('/pelaporan/create', [ReportController::class, 'store'])->name('laporan.store');
    Route::get('/Cari-laporan', [ReportController::class, 'search'])->name('laporan.cari');

});

    Route::get('/pelaporan', [ReportController::class, 'create'])->name('laporan.create');

Route::middleware(['auth'])->group(function () {
    Route::get('/laporan-saya', [UserHistoryReportController::class, 'reportHistory'])->name('history.reports');
    // Route::get('/laporan-saya', [UserHistoryController::class, 'verificationHistory'])->name('history.verifications');
});


require __DIR__.'/auth.php';
