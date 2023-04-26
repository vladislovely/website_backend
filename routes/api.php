<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPermissionsController;
use App\Http\Controllers\VacancyController;
use App\Models\User;
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

Route::group(['middleware' => ['auth:sanctum']], static function () {
    Route::get('/vacancies', [VacancyController::class, 'index'])
         ->name('vacancies');

    Route::get('/vacancies/{id}', [VacancyController::class, 'show'])
         ->name('vacancy.show');

    Route::post('/vacancies', [VacancyController::class, 'store'])
        ->middleware(['ability:create-vacancy'])
        ->name('create-vacancy');

    Route::patch('/vacancies/{id}', [VacancyController::class, 'update'])
        ->middleware(['ability:update-vacancy'])
        ->name('update-vacancy');

    Route::post('/vacancies/{id}/delete', [VacancyController::class, 'delete'])
        ->middleware(['ability:delete-vacancy'])
        ->name('delete-vacancy');

    Route::post('/vacancies/{id}/recovery', [VacancyController::class, 'restore'])
        ->middleware(['ability:recovery-vacancy'])
        ->name('recovery-vacancy');

    Route::delete('/vacancies/{id}', [VacancyController::class, 'destroy'])
        ->middleware(['ability:permanently-delete-vacancy'])
        ->name('permanently-delete-vacancy');

    Route::get('/users', [UserController::class, 'index'])
         ->name('view-users');

    Route::patch('/users/{id}', [UserController::class, 'update'])
         ->middleware(['ability:update-user'])
         ->name('update-user');

    Route::post('/users/{id}/delete', [UserController::class, 'delete'])
         ->middleware(['ability:delete-user'])
         ->name('delete-user');

    Route::post('/users/{id}/recovery', [UserController::class, 'restore'])
         ->middleware(['ability:recovery-user'])
         ->name('recovery-user');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])
         ->middleware(['ability:permanently-delete-user'])
         ->name('permanently-delete-user');

    Route::get('/users/permissions', [UserPermissionsController::class, 'index'])
        ->name('view-users-permissions');

    Route::get('/users/{id}/permissions', [UserPermissionsController::class, 'show'])
         ->name('view-user-permissions');

    Route::patch('/users/{id}/permissions', [UserPermissionsController::class, 'update'])
         ->middleware(['ability:update-user-permissions'])
         ->name('update-user-permissions');

    Route::get('/permissions', [UserPermissionsController::class, 'permissionsList'])
         ->name('view-permissions');
});

Route::post('/tokens/create', static function (Request $request) {
    $token = $request->user()->createToken('apiToken', $request->user()->abilities()->toArray());

    return ['token' => $token->plainTextToken];
});
