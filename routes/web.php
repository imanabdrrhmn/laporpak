<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Feedback\FeedbackController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;


Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
});

Route::get('/dashboard', function (){
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('/pelaporan', function (){
    return Inertia::render('Pelaporan');
})->name('pelaporan');

// Route::get('/verifikasi', function (){
//     return Inertia::render('Verifikasi');
// })->name('verifikasi');


// Pelaporan Route
Route::get('/pelaporan', function () {
    return Inertia::render('Pelaporan', [
        'user' => auth()->user(), 
    ]);
})->name('pelaporan');



// Tentang Kami Route
Route::get('/tentang-kami', function () {
    return Inertia::render('TentangKami');
})->name('tentang-kami');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');

Route::middleware(['auth', 'verified',])->group(function () {
    Route::resource('feedback', FeedbackController::class)->except(['index', 'show']);
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
});


require __DIR__.'/auth.php';
