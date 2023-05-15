<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
Route::prefix('auth')->group(static function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])
         ->middleware('guest')
         ->name('register');

    Route::post('/login', [AuthenticatedSessionController::class, 'login'])
         ->middleware('guest')
         ->name('login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
         ->middleware('guest')
         ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
         ->middleware('guest')
         ->name('password.store');

    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
         ->middleware(['auth', 'signed', 'throttle:6,1'])
         ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
         ->middleware(['auth', 'throttle:6,1'])
         ->name('verification.send');

    Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])
         ->middleware('auth')
         ->name('logout');

    Route::post('/prolongation-session', [AuthenticatedSessionController::class, 'prolongationSession'])
         ->middleware('auth')
         ->name('prolongation.session');

    // 2FA
    Route::get('/prepare-two-factor', [AuthenticatedSessionController::class, 'prepareTwoFactor'])
         ->middleware('auth')
         ->name('prepare-two-factor');

    Route::post('/confirm-two-factor', [AuthenticatedSessionController::class, 'confirmTwoFactor'])
         ->middleware('auth')
         ->name('confirm-two-factor');

    Route::post('/validate-code', [AuthenticatedSessionController::class, 'validateTwoFactor'])
         ->middleware('guest')
         ->name('validate-two-factor');
});
