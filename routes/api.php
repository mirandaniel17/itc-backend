<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('students', [AdminController::class, 'getStudents']);
Route::post('register-student', [AdminController::class, 'registerStudent']);

Route::middleware('auth:sanctum')->group(function(){
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users', [AdminController::class, 'users']);
    Route::post('register-teacher', [AdminController::class, 'registerTeacher']);
});