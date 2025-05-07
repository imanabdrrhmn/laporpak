<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Feedback\FeedbackController;
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

Route::get('/pelaporan', function (){
    return Inertia::render('pelaporan');
})->name('pelaporan');

 Route::get('/verifikasi', function (){
     return Inertia::render('verifikasi');
 })->name('verifikasi');

 Route::get('/LaporMap', function (){
     return Inertia::render('LaporMap');
 })->name('LaporMap');

  Route::get('/CariLaporan', function (){
     return Inertia::render('CariLaporan');
 })->name('CariLaporan');

// // Pelaporan Route
// Route::get('/pelaporan', function () {
//     return Inertia::render('pelaporan', [
//         'user' => auth()->user(), 
//     ]);
// })->name('pelaporan');


// Tentang Kami Route
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


require __DIR__.'/auth.php';
