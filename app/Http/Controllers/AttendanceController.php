<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Notifications\StudentAbsenceAlert;

class AttendanceController extends Controller
{
    public function index()
    {
        $courses = Course::with('teacher', 'modality')->get();
        return response()->json($courses, Response::HTTP_OK);
    }

    public function getAttendanceDates($course_id)
    {
        $attendanceDates = Attendance::where('course_id', $course_id)
            ->select('date')
            ->distinct()
            ->get();
        return response()->json($attendanceDates, Response::HTTP_OK);
    }

    public function getStudentsForAttendance($course_id, Request $request)
    {
        $date = $request->query('date');

        $students = Student::whereHas('enrollments', function ($query) use ($course_id) {
            $query->where('course_id', $course_id);
        })->orderBy('last_name', 'asc')->get();

        $attendances = [];
        foreach ($students as $student) {
            $attendance = Attendance::where('student_id', $student->id)
                ->where('course_id', $course_id)
                ->whereDate('date', $date)
                ->first();

            $attendances[$student->id] = $attendance ? $attendance->status : 'AUSENTE';
        }

        return response()->json([
            'students' => $students,
            'attendances' => $attendances,
        ], Response::HTTP_OK);
    }

    public function storeAttendance(Request $request)
    {
        $courseId = $request->input('course_id');
        $date = $request->input('date');
        $attendances = $request->input('attendances');
        foreach ($attendances as $attendance) {
            $attendanceModel = Attendance::updateOrCreate(
                [
                    'student_id' => $attendance['student_id'],
                    'course_id' => $courseId,
                    'date' => $date,
                ],
                ['status' => $attendance['status']]
            );
            if ($attendanceModel->status == 'AUSENTE') {
                $student = Student::find($attendance['student_id']);
                $absencesCount = Attendance::where('student_id', $student->id)
                    ->where('status', 'AUSENTE')
                    ->count();

                if ($absencesCount >= 5) {
                    $users = User::all();
                    foreach ($users as $user) {
                        $user->notify(new StudentAbsenceAlert($student));
                    }
                }
            }
        }
        return response()->json(['message' => 'Attendance saved successfully'], Response::HTTP_CREATED);
    }
}
