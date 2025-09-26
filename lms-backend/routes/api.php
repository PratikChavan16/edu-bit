<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // User profile and management routes
    Route::get('/users/me', [UserController::class, 'me']);
    Route::apiResource('users', UserController::class);
    
    // Subject routes
    Route::get('/subjects', [\App\Http\Controllers\Api\SubjectController::class, 'index']);
    Route::get('/subjects/{subject}', [\App\Http\Controllers\Api\SubjectController::class, 'show']);
    
    // Content Management Routes
    Route::prefix('subjects/{subject}')->group(function () {
        // Notes management
        Route::get('/notes', [\App\Http\Controllers\Api\ContentController::class, 'getNotes']);
        Route::post('/notes/upload-url', [\App\Http\Controllers\Api\ContentController::class, 'generateNoteUploadUrl']);
        Route::post('/notes/confirm-upload', [\App\Http\Controllers\Api\ContentController::class, 'confirmNoteUpload']);
        
        // Videos management
        Route::get('/videos', [\App\Http\Controllers\Api\ContentController::class, 'getVideos']);
        Route::post('/videos/upload-url', [\App\Http\Controllers\Api\ContentController::class, 'generateVideoUploadUrl']);
        Route::post('/videos/confirm-upload', [\App\Http\Controllers\Api\ContentController::class, 'confirmVideoUpload']);
    });
    
    // Individual content item routes
    Route::get('/notes/{note}/download', [\App\Http\Controllers\Api\ContentController::class, 'downloadNote']);
    Route::delete('/notes/{note}', [\App\Http\Controllers\Api\ContentController::class, 'deleteNote']);
    Route::get('/videos/{video}/stream', [\App\Http\Controllers\Api\ContentController::class, 'streamVideo']);
    Route::delete('/videos/{video}', [\App\Http\Controllers\Api\ContentController::class, 'deleteVideo']);
    
    // TODO: Implement these controllers
    /*
    // Course management routes
    Route::apiResource('courses', \App\Http\Controllers\Api\CourseController::class);
    Route::apiResource('subjects', \App\Http\Controllers\Api\SubjectController::class);
    
    // Content routes
    Route::apiResource('notes', \App\Http\Controllers\Api\NoteController::class);
    Route::apiResource('videos', \App\Http\Controllers\Api\VideoController::class);
    
    // Assessment routes
    Route::apiResource('assessments', \App\Http\Controllers\Api\AssessmentController::class);
    Route::apiResource('questions', \App\Http\Controllers\Api\QuestionController::class);
    Route::apiResource('submissions', \App\Http\Controllers\Api\SubmissionController::class);
    
    // Department routes
    Route::apiResource('departments', \App\Http\Controllers\Api\DepartmentController::class);
    Route::apiResource('roles', \App\Http\Controllers\Api\RoleController::class);
    */
});