<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Mobile\AuthController as MobileAuthController;
use App\Http\Controllers\Mobile\UserController as MobileUserController;
use App\Http\Controllers\Mobile\UserController as MobileApplicantController;



/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| These routes do NOT require login.
*/
Route::get('/test', function () {
    return response()->json([
        'status' => 'connected'
    ]);
});

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/jobs', [JobPostingController::class, 'index']);
Route::get('/jobs/{id}', [JobPostingController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Protected Routes
|--------------------------------------------------------------------------
| These routes require a valid Sanctum token.
*/
Route::middleware('auth:sanctum')->group(function () {

    // Applicant routes
    Route::get('/applicants/my-applications', [ApplicantController::class, 'myApplications']);

    // User routes
    Route::post('/users/{id}/change-password', [UserController::class, 'changePassword']);
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);

    // Resource routes
    Route::apiResource('job-postings', JobPostingController::class);
    Route::apiResource('applicants', ApplicantController::class);
    Route::apiResource('notifications', NotificationController::class);
    Route::apiResource('users', UserController::class);
});



Route::post('/mobile-register', [MobileAuthController::class, 'register']);
Route::post('/mobile-login', [MobileAuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mobile-user', [MobileUserController::class, 'getName']);

    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/mobile/applicant', [ApplicantController::class, 'Applications']);
    Route::post('/mobile/applicant', [ApplicantController::class, 'store']);
});
});