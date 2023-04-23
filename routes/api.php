<?php

use App\Http\Controllers\Auth\AuthenticatedUserController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\VacancyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(static function () {
    Route::post('/register', [RegisteredUserController::class, 'store'])
         ->name('register');

    Route::post('/login', [AuthenticatedUserController::class, 'login'])
         ->name('login');

    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
         ->name('password.email');

    Route::post('/reset-password', [NewPasswordController::class, 'store'])
         ->name('password.store');

    Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
         ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])
         ->name('verification.verify');

    Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
         ->middleware(['auth:sanctum', 'throttle:6,1'])
         ->name('verification.send');

    Route::post('/logout', [AuthenticatedUserController::class, 'logout'])
         ->middleware('auth:sanctum')
         ->name('logout');
});

Route::group(['middleware' => ['auth:sanctum']], static function () {
    Route::get('/vacancies', [VacancyController::class, 'index'])
         ->name('vacancies');

    Route::get('/vacancies/{id}', [VacancyController::class, 'show'])
         ->name('vacancy');

    Route::post('/vacancies', [VacancyController::class, 'store'])
         ->name('create-vacancy');

    Route::put('/vacancies/{id}', [VacancyController::class, 'edit'])
         ->name('update-vacancy');

    Route::patch('/vacancies/{id}', [VacancyController::class, 'update'])
         ->name('update-vacancy');

    Route::post('/vacancies/{id}/delete', [VacancyController::class, 'delete'])
         ->name('delete-vacancy');

    Route::post('/vacancies/{id}/recovery', [VacancyController::class, 'restore'])
         ->name('restore-vacancy');

    Route::delete('/vacancies/{id}', [VacancyController::class, 'destroy'])
         ->name('permanently-delete-vacancy');

});
