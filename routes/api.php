<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostingController;
use App\Http\Controllers\ApplicantController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;

// Auth Routes (public)
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Protected Routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Specific routes must come first
    Route::get('/applicants/my-applications', [ApplicantController::class, 'myApplications']);
    Route::post('/users/{id}/change-password', [UserController::class, 'changePassword']);
    
    // Resource routes
    Route::apiResource('job-postings', JobPostingController::class);
    Route::apiResource('applicants', ApplicantController::class);
    Route::apiResource('notifications', NotificationController::class);
    Route::apiResource('users', UserController::class);
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus']);
});