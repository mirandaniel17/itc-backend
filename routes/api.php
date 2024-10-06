<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RolesPermissionsController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

//Authentication
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('dashboard/counts', [DashboardController::class, 'getCounts']);
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);
    
    //Users
    Route::get('users', [AdminController::class, 'getUsers']);
    Route::get('users/{id}', [AdminController::class, 'getUserById']);
    
    //Students
    Route::post('students', [AdminController::class, 'registerStudent']);
    Route::get('students', [AdminController::class, 'getStudents']);
    Route::get('students/{id}', [AdminController::class, 'getStudentById']);
    Route::put('students/{id}', [AdminController::class, 'editStudent']);
    Route::delete('students/{id}', [AdminController::class, 'deleteStudent']);

    //Permissions
    Route::get('/users/{id}/permissions', [RolesPermissionsController::class, 'getUserPermissions']);
    Route::post('/users/{id}/permissions/save', [RolesPermissionsController::class, 'savePermissions']);
});