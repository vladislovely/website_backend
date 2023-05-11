<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\StorageController;
use App\Http\Controllers\SuccessStoryController;
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
    // Vacancies
    Route::get('/vacancies', [VacancyController::class, 'index'])
         ->name('vacancies');

    Route::get('/vacancies/{id}', [VacancyController::class, 'show'])
         ->name('vacancy-show');

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

    // Users
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

    // Blog
    Route::get('/articles', [BlogController::class, 'index'])
         ->name('articles');

    Route::get('/articles/{id}', [BlogController::class, 'show'])
         ->name('article-show');

    Route::post('/articles', [BlogController::class, 'store'])
         ->middleware(['ability:create-article'])
         ->name('create-article');

    Route::patch('/articles/{id}', [BlogController::class, 'update'])
         ->middleware(['ability:update-article'])
         ->name('update-article');

    Route::post('/articles/{id}/delete', [BlogController::class, 'delete'])
         ->middleware(['ability:delete-article'])
         ->name('delete-article');

    Route::post('/articles/{id}/recovery', [BlogController::class, 'restore'])
         ->middleware(['ability:recovery-article'])
         ->name('recovery-article');

    Route::delete('/articles/{id}', [BlogController::class, 'destroy'])
         ->middleware(['ability:permanently-delete-article'])
         ->name('permanently-delete-article');

    // Success stories
    Route::get('/success-stories', [SuccessStoryController::class, 'index'])
         ->name('success-stories');

    Route::get('/success-stories/{id}', [SuccessStoryController::class, 'show'])
         ->name('success-stories-show');

    Route::post('/success-stories', [SuccessStoryController::class, 'store'])
         ->middleware(['ability:create-success-stories'])
         ->name('create-success-stories');

    Route::patch('/success-stories/{id}', [SuccessStoryController::class, 'update'])
         ->middleware(['ability:update-success-stories'])
         ->name('update-success-stories');

    Route::post('/success-stories/{id}/delete', [SuccessStoryController::class, 'delete'])
         ->middleware(['ability:delete-success-stories'])
         ->name('delete-success-stories');

    Route::post('/success-stories/{id}/recovery', [SuccessStoryController::class, 'restore'])
         ->middleware(['ability:recovery-success-stories'])
         ->name('recovery-success-stories');

    Route::delete('/success-stories/{id}', [SuccessStoryController::class, 'destroy'])
         ->middleware(['ability:permanently-delete-success-stories'])
         ->name('permanently-delete-success-stories');

    // AWS s3
    Route::post('/storage/upload-file', [StorageController::class, 'store'])
         ->name('upload-file');

    Route::post('/storage/delete-file', [StorageController::class, 'destroy'])
         ->name('delete-file');
});

Route::post('/tokens/create', static function (Request $request) {
    if (!$request->user()) {
        return response()->json(['message' => 'Session is expired', 'status' => 'session_expired']);
    }
    $token = $request->user()->createToken('apiToken', $request->user()->abilities()->toArray());

    return response()->json(['token' => $token->plainTextToken]);
});
