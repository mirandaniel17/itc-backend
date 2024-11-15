<?php

use App\Http\Controllers\AuthController;
use App\Models\User;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RolesPermissionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ModalityController;
use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;

Route::get('email/verify', [EmailVerificationController::class, 'showNotice'])->name('verification.notice');
Route::get('email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);
    if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
        return response()->json(['message' => 'El hash no coincide con el correo.'], 403);
    }
    if ($user->email_verified_at) {
        return response()->json(['message' => 'El correo ya ha sido verificado.'], 200);
    }
    $user->email_verified_at = now();
    $user->save();
    return redirect('http://localhost:5173/login?verified=1');
})->middleware('signed')->name('verification.verify');
Route::post('email/resend', [EmailVerificationController::class, 'resendVerificationEmail'])->middleware(['auth:api', 'throttle:6,1'])->name('verification.resend');
Route::post('forgot-password', [PasswordResetController::class, 'sendResetLinkEmail']);
Route::get('reset-password/{token}', function ($token) {
    return redirect("http://localhost:5173/reset-password/{$token}");
})->name('password.reset');
Route::post('reset-password', [PasswordResetController::class, 'resetPassword']);
Route::get('reset-password/email/{token}', [PasswordResetController::class, 'getEmailByToken']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::get('dashboard/counts', [DashboardController::class, 'getCounts']);
    Route::get('user', [AuthController::class, 'user']);
    Route::put('user/update', [AuthController::class, 'updateUserProfile']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::group(['middleware' => ['permission:Gestión de Usuarios']], function () {
        Route::get('users', [AdminController::class, 'getUsers']);
        Route::get('users/{id}', [AdminController::class, 'getUserById']);
        Route::get('users/{id}/permissions', [RolesPermissionsController::class, 'getUserPermissions']);
        Route::post('users/{id}/permissions/save', [RolesPermissionsController::class, 'savePermissions']);
        Route::get('roles', [RolesPermissionsController::class, 'getAllRoles']);
        Route::get('user/{id}/role', [RolesPermissionsController::class, 'getUserRole']);
        Route::post('user/{id}/role', [RolesPermissionsController::class, 'updateUserRole']);
        Route::get('roles/{roleName}/permissions', [RolesPermissionsController::class, 'getRolePermissions']);
        
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
        Route::middleware('auth:sanctum')->get('/notifications/unread', [NotificationController::class, 'unreadCount']);
    });

    Route::group(['middleware' => ['permission:Consultar Estudiantes']], function () {
        Route::get('students', [StudentController::class, 'getStudents']);
        Route::get('students/{id}', [StudentController::class, 'getStudentById']);
        Route::post('students', [StudentController::class, 'registerStudent']);
        Route::put('students/{id}', [StudentController::class, 'editStudent']);
        Route::delete('students/{id}', [StudentController::class, 'deleteStudent']);
        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
        Route::middleware('auth:sanctum')->get('/notifications/unread', [NotificationController::class, 'unreadCount']);
    });

    Route::group(['middleware' => ['permission:Gestión de Cursos']], function () {
        Route::get('teachers', [TeacherController::class, 'getTeachers']);
        Route::get('teachers/{id}', [TeacherController::class, 'getTeacherById']);
        Route::post('teachers', [TeacherController::class, 'registerTeacher']);
        Route::put('teachers/{id}', [TeacherController::class, 'editTeacher']);
        Route::delete('teachers/{id}', [TeacherController::class, 'deleteTeacher']);

        Route::get('modalities', [ModalityController::class, 'getModalities']);
        Route::get('modalities/{id}', [ModalityController::class, 'getModalityById']);
        Route::post('modalities', [ModalityController::class, 'registerModality']);
        Route::put('modalities/{id}', [ModalityController::class, 'editModality']);
        Route::delete('modalities/{id}', [ModalityController::class, 'deleteModality']);

        Route::get('courses', [CourseController::class, 'getCourses']);
        Route::get('courses/{id}', [CourseController::class, 'getCourseById']);
        Route::post('courses', [CourseController::class, 'registerCourse']);
        Route::put('courses/{id}', [CourseController::class, 'editCourse']);
        Route::delete('courses/{id}', [CourseController::class, 'deleteCourse']);

        Route::get('shifts', [ShiftController::class, 'index']);
        Route::get('shifts/{id}', [ShiftController::class, 'show']);
        Route::post('shifts', [ShiftController::class, 'store']);
        Route::put('shifts/{id}', [ShiftController::class, 'update']);
        Route::delete('shifts/{id}', [ShiftController::class, 'destroy']);

        Route::get('discounts', [DiscountController::class, 'index']);
        Route::get('discounts/{id}', [DiscountController::class, 'show']);
        Route::post('discounts', [DiscountController::class, 'store']);
        Route::put('discounts/{id}', [DiscountController::class, 'update']);
        Route::delete('discounts/{id}', [DiscountController::class, 'destroy']);

        Route::get('rooms', [RoomController::class, 'index']);
        Route::post('rooms', [RoomController::class, 'store']);
        Route::get('rooms/{id}', [RoomController::class, 'show']);
        Route::put('rooms/{id}', [RoomController::class, 'update']);
        Route::delete('rooms/{id}', [RoomController::class, 'destroy']);

        Route::get('courses-attendance', [AttendanceController::class, 'index']);
        Route::get('courses/{course_id}/attendance-dates', [AttendanceController::class, 'getAttendanceDates']);
        Route::get('courses/{course_id}/students', [AttendanceController::class, 'getStudentsForAttendance']);
        Route::post('attendance', [AttendanceController::class, 'storeAttendance']);
        Route::get('attendance-dates/{course_id}', [AttendanceController::class, 'getAttendanceDates']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
        Route::middleware('auth:sanctum')->get('notifications/unread', [NotificationController::class, 'unreadCount']);
    });

    Route::group(['middleware' => ['permission:Inscripciones']], function () {
        Route::get('enrollments', [EnrollmentController::class, 'getEnrollments']);
        Route::get('enrollments/{id}', [EnrollmentController::class, 'getEnrollmentById']);
        Route::post('enrollments', [EnrollmentController::class, 'registerEnrollment']);
        Route::put('enrollments/{id}', [EnrollmentController::class, 'editEnrollment']);
        Route::delete('enrollments/{id}', [EnrollmentController::class, 'deleteEnrollment']);

        Route::get('schedules', [ScheduleController::class, 'getCourseSchedules']);
        Route::get('schedules/{id}', [ScheduleController::class, 'show']);
        Route::post('schedules', [ScheduleController::class, 'store']);
        Route::put('schedules/{id}', [ScheduleController::class, 'update']);

        Route::get('notifications', [NotificationController::class, 'index']);
        Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
        Route::get('notifications/unread', [NotificationController::class, 'unreadCount'])->middleware('auth:api');
    });
});
