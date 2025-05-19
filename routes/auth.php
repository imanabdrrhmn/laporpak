<?php
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\PhoneVerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureContactVerified;
use Inertia\Inertia;
use Illuminate\Http\Request;


Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register/email', [RegisteredUserController::class, 'storeWithEmail'])->name('register.email');
    Route::post('register/phone', [RegisteredUserController::class, 'storeWithNoHp'])->name('register.no_hp');

    Route::post('login', [AuthenticatedSessionController::class, 'store'])->name('login.submit')->middleware('throttle:5,1');

    Route::get('login/google', [AuthenticatedSessionController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('login/google/callback', [AuthenticatedSessionController::class, 'handleGoogleCallback']);

    Route::inertia('/forgot-password', 'ForgotPassword')->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:1,1');

    Route::get('/reset-password', function (Request $request) {
        return Inertia::render('ResetPassword', [
            'token' => $request->query('token'),
            'email' => $request->query('email'),
            'no_hp' => $request->query('no_hp')
        ]);
    })->name('password.reset');

    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    });

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/verify-phone', function () {
    return Inertia::render('VerifyPhone');})->name('verification.phone.notice');

    Route::post('/phone/send-verification', [PhoneVerificationController::class, 'send'])->name('phone.send');
    Route::post('/phone/verify', [PhoneVerificationController::class, 'verify'])->name('phone.verify');

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
