<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InternshipPdfController;

Route::get('/vykaz-generate/{id}', [InternshipPdfController::class, 'generate']);
Route::get('/vykaz-generate-empty', [InternshipPdfController::class, 'generateEmpty']);


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('/auth/login', [AuthController::class, 'login']);
