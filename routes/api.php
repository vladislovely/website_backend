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
         ->middleware(['auth:sanctum', 'ability:view-vacancies'])
         ->name('vacancies');

    Route::get('/vacancies/{id}', [VacancyController::class, 'show'])
         ->middleware(['auth:sanctum', 'ability:view-vacancy'])
         ->name('vacancy');

    Route::post('/vacancies', [VacancyController::class, 'store'])
         ->middleware(['auth:sanctum', 'ability:create-vacancy'])
         ->name('create-vacancy');

    Route::match(['put', 'patch'], '/vacancies/{id}', [VacancyController::class, 'update'])
        ->middleware(['auth:sanctum', 'ability:update-vacancy'])
        ->name('update-vacancy');

    Route::post('/vacancies/{id}/delete', [VacancyController::class, 'delete'])
         ->middleware(['auth:sanctum', 'ability:delete-vacancy'])
         ->name('delete-vacancy');

    Route::post('/vacancies/{id}/restore', [VacancyController::class, 'restore'])
         ->middleware(['auth:sanctum', 'ability:restore-vacancy'])
         ->name('restore-vacancy');

    Route::delete('/vacancies/{id}', [VacancyController::class, 'destroy'])
         ->middleware(['auth:sanctum', 'ability:permanently-delete-vacancy'])
         ->name('permanently-delete-vacancy');
});
