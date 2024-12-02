<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RolesPermissionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\ModalityController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\EnrollmentController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Models\User;

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
    Route::get('dashboard/enrollments-by-date', [DashboardController::class, 'getEnrollmentsByDate']);
    Route::get('dashboard/payments-by-course', [DashboardController::class, 'getPaymentsByCourse']);
    Route::get('dashboard/payments-by-modality', [DashboardController::class, 'getPaymentsByModality']);
    Route::get('dashboard/student-registrations-by-date', [DashboardController::class, 'getStudentRegistrationsByDate']);
    Route::get('dashboard/students-by-course', [DashboardController::class, 'studentsByCourse']);

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
    });

    Route::group(['middleware' => ['permission:Consultar Estudiantes']], function () {
        Route::get('students/active', [StudentController::class, 'getActiveStudents']);
        Route::get('students/all', [StudentController::class, 'getAllStudents']);
        Route::get('students', [StudentController::class, 'getStudents']);
        Route::get('students/{id}', [StudentController::class, 'getStudentById']);
        Route::post('students', [StudentController::class, 'registerStudent']);
        Route::put('students/{id}', [StudentController::class, 'editStudent']);
        Route::delete('students/{id}', [StudentController::class, 'deleteStudent']);
        Route::get('students/{id}/academic-history', [StudentController::class, 'getAcademicHistory']);
        Route::patch('students/{id}/disable', [StudentController::class, 'disableStudent']);
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

        Route::get('shifts', [ShiftController::class, 'getShifts']);
        Route::post('shifts', [ShiftController::class, 'registerShift']);
        Route::get('shifts/{id}', [ShiftController::class, 'getShiftById']);
        Route::put('shifts/{id}', [ShiftController::class, 'editShift']);
        Route::delete('shifts/{id}', [ShiftController::class, 'deleteShift']);

        Route::get('discounts', [DiscountController::class, 'getDiscounts']);
        Route::post('discounts', [DiscountController::class, 'registerDiscounts']);
        Route::get('discounts/{id}', [DiscountController::class, 'getDiscountById']);
        Route::put('discounts/{id}', [DiscountController::class, 'editDiscount']);
        Route::delete('discounts/{id}', [DiscountController::class, 'deleteDiscount']);

        Route::get('rooms', [RoomController::class, 'getRooms']);
        Route::post('rooms', [RoomController::class, 'registerRoom']);
        Route::get('rooms/{id}', [RoomController::class, 'getRoomById']);
        Route::put('rooms/{id}', [RoomController::class, 'editRoom']);
        Route::delete('rooms/{id}', [RoomController::class, 'deleteRoom']);

        Route::get('courses-attendance', [AttendanceController::class, 'getCoursesByEveryAttendance']);
        Route::get('courses/{course_id}/attendance-dates', [AttendanceController::class, 'getAttendanceDates']);
        Route::get('courses/{course_id}/students', [AttendanceController::class, 'getStudentsForAttendance']);
        Route::post('attendance', [AttendanceController::class, 'storeAttendance']);
        Route::get('attendance-dates/{course_id}', [AttendanceController::class, 'getAttendanceDates']);

        Route::get('tasks/courses', [TaskController::class, 'getCourses']);
        Route::get('tasks/course/{courseId}/list', [TaskController::class, 'getTasks']);
        Route::post('tasks/create', [TaskController::class, 'createTask']);
        Route::get('tasks/{taskId}/students', [TaskController::class, 'getStudentsWithGrades']);
        Route::post('tasks/{taskId}/grades/save', [TaskController::class, 'saveGrades']);
        Route::delete('/tasks/{taskId}', [TaskController::class, 'deleteTask']);

        Route::get('payments', [PaymentController::class, 'getPayments']);
        Route::post('payments', [PaymentController::class, 'registerPayment']);
        Route::get('payments/{id}', [PaymentController::class, 'getPaymentById']);
        Route::put('payments/{id}', [PaymentController::class, 'editPayment']);
        Route::delete('payments/{id}', [PaymentController::class, 'deletePayment']);
        Route::get('payments/student-details/{studentId}', [PaymentController::class, 'getStudentDetails']);
    });

    Route::group(['middleware' => ['permission:Inscripciones']], function () {
        Route::get('enrollments', [EnrollmentController::class, 'getEnrollments']);
        Route::get('enrollments/{id}', [EnrollmentController::class, 'getEnrollmentById']);
        Route::post('enrollments', [EnrollmentController::class, 'registerEnrollment']);
        Route::put('enrollments/{id}', [EnrollmentController::class, 'editEnrollment']);
        Route::delete('enrollments/{id}', [EnrollmentController::class, 'deleteEnrollment']);
    });

    Route::group(['middleware' => ['permission:Ver Horarios']], function () {
        Route::get('schedules', [ScheduleController::class, 'getCourseSchedules']);
        Route::post('schedules', [ScheduleController::class, 'registerSchedule']);
        Route::get('schedules/{id}', [ScheduleController::class, 'getScheduleById']);
        Route::put('schedules/{id}', [ScheduleController::class, 'editSchedule']);
        Route::delete('schedules/{id}', [ScheduleController::class, 'deleteScheduleById']);
    });

    Route::get('notifications', [NotificationController::class, 'getNotification']);
    Route::get('notifications/all', [NotificationController::class, 'getNotifications']);
    Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy']);
    Route::get('notifications/unread', [NotificationController::class, 'unreadCount'])->middleware('auth:api');
    Route::post('courses/notify-completion', [CourseController::class, 'notifyCourseCompletion']);

    Route::post('export-attendance-report', [ReportController::class, 'exportAttendanceReport']);
    Route::post('export-enrollment-report', [ReportController::class, 'exportEnrollmentReport']);
    Route::post('export-grades-report', [ReportController::class, 'exportGradesReport']);
});
