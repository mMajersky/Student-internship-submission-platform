<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AnnouncementController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Public route for getting published announcements
Route::get('/announcements/published', [AnnouncementController::class, 'published']);

// Protected routes for Garant and Admin only
Route::middleware(['auth:api', 'role:admin,garant'])->group(function () {
    Route::apiResource('announcements', AnnouncementController::class);
});
