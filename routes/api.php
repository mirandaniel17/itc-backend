<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

//Authentication
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function(){
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('users', [AdminController::class, 'users']);
});

//Students
Route::post('students', [AdminController::class, 'registerStudent']);
Route::get('students', [AdminController::class, 'getStudents']);
Route::get('students/{id}', [AdminController::class, 'getStudentById']);
Route::put('students/{id}', [AdminController::class, 'editStudent']);
Route::delete('students/{id}', [AdminController::class, 'deleteStudent']);

//Teachers
Route::post('teachers', [AdminController::class, 'registerTeacher']);