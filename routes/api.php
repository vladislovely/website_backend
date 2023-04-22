<?php

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

Route::prefix('api')->middleware(['auth:sanctum'])->group(static function () {
    Route::get('/vacancies', [VacancyController::class, 'index'])
         ->middleware(['ability:view-vacancies'])
         ->name('vacancies');

    Route::get('/vacancies/{id}', [VacancyController::class, 'show'])
         ->middleware(['ability:view-vacancy'])
         ->name('vacancy');

    Route::post('/vacancies', [VacancyController::class, 'store'])
         ->middleware(['ability:create-vacancy'])
         ->name('create-vacancy');

    Route::put('/vacancies/{id}', [VacancyController::class, 'edit'])
         ->middleware(['ability:update-vacancy'])
         ->name('update-vacancy');

    Route::patch('/vacancies/{id}', [VacancyController::class, 'update'])
        ->middleware(['ability:update-vacancy'])
        ->name('update-vacancy');

    Route::post('/vacancies/{id}/delete', [VacancyController::class, 'delete'])
         ->middleware(['ability:delete-vacancy'])
         ->name('delete-vacancy');

    Route::post('/vacancies/{id}/recovery', [VacancyController::class, 'restore'])
         ->middleware(['ability:recovery-vacancy'])
         ->name('restore-vacancy');

    Route::delete('/vacancies/{id}', [VacancyController::class, 'destroy'])
         ->middleware(['ability:permanently-delete-vacancy'])
         ->name('permanently-delete-vacancy');
});
