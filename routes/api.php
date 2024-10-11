<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RolesPermissionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ModalityController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

//Authentication
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    
    Route::get('dashboard/counts', [DashboardController::class, 'getCounts']);
    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    // Users management (restricted to users with 'Gestión de Usuarios' permission)
    Route::group(['middleware' => ['permission:Gestión de Usuarios']], function () {
        Route::get('users', [AdminController::class, 'getUsers']);
        Route::get('users/{id}', [AdminController::class, 'getUserById']);
        Route::get('/users/{id}/permissions', [RolesPermissionsController::class, 'getUserPermissions']);
        Route::post('/users/{id}/permissions/save', [RolesPermissionsController::class, 'savePermissions']);
        Route::get('roles', [RolesPermissionsController::class, 'getAllRoles']);
        Route::get('user/{id}/role', [RolesPermissionsController::class, 'getUserRole']);
        Route::post('user/{id}/role', [RolesPermissionsController::class, 'updateUserRole']);
    });

    Route::group(['middleware' => ['permission:Consultar Estudiantes']], function () {
        Route::get('students', [StudentController::class, 'getStudents']);
        Route::get('students/{id}', [StudentController::class, 'getStudentById']);
        Route::post('students', [StudentController::class, 'registerStudent']);
        Route::put('students/{id}', [StudentController::class, 'editStudent']);
        Route::delete('students/{id}', [StudentController::class, 'deleteStudent']);
    });

    Route::group(['middleware' => ['permission:Gestión de Cursos']], function () {
        //Teachers
        Route::get('teachers', [TeacherController::class, 'getTeachers']);
        Route::get('teachers/{id}', [TeacherController::class, 'getTeacherById']);
        Route::post('teachers', [TeacherController::class, 'registerTeacher']);
        Route::put('teachers/{id}', [TeacherController::class, 'editTeacher']);
        Route::delete('teachers/{id}', [TeacherController::class, 'deleteTeacher']);

        //Modalities
        Route::get('modalities', [ModalityController::class, 'getModalities']);
        Route::get('modalities/{id}', [ModalityController::class, 'getModalityById']);
        Route::post('modalities', [ModalityController::class, 'registerModality']);
        Route::put('modalities/{id}', [ModalityController::class, 'editModality']);
        Route::delete('modalities/{id}', [ModalityController::class, 'deleteModality']);

        //Courses
        Route::get('courses', [CourseController::class, 'getCourses']);
        Route::get('courses/{id}', [CourseController::class, 'getCourseById']);
        Route::post('courses', [CourseController::class, 'registerCourse']);
        Route::put('courses/{id}', [CourseController::class, 'editCourse']);
        Route::delete('courses/{id}', [CourseController::class, 'deleteCourse']);
    });

    Route::group(['middleware' => ['permission:Inscripciones']], function () {
        
    });
});