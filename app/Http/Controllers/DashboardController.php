<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Modality;
use App\Models\Room;
use App\Models\Shift;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getCounts()
    {
        $totalUsers = User::count();
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalCourses = Course::count();
        $totalEnrollments = Enrollment::count();
        $totalModalities = Modality::count();
        $totalRooms = Room::count();
        $totalShifts = Shift::count();
        $totalPayments = Payment::count();
        return response()->json([
            'totalUsers' => $totalUsers,
            'totalStudents' => $totalStudents,
            'totalTeachers' => $totalTeachers,
            'totalCourses' => $totalCourses,
            'totalEnrollments' => $totalEnrollments,
            'totalModalities' => $totalModalities,
            'totalRooms' => $totalRooms,
            'totalShifts' => $totalShifts,
            'totalPayments' => $totalPayments,
        ]);
    }

    public function getEnrollmentsByDate()
    {
        $data = Enrollment::select('created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m');
            })
            ->map(function ($items) {
                return $items->count();
            });

        $result = $data->map(function ($count, $date) {
            return [
                'date' => $date,
                'count' => $count,
            ];
        });

        return response()->json($result);
    }

    public function getPaymentsByCourse()
    {
        $data = Payment::with('enrollment.course')
            ->get()
            ->groupBy(function ($payment) {
                return $payment->enrollment->course->name;
            })
            ->map(function ($payments) {
                return $payments->count();
            });

        $result = $data->map(function ($count, $course) {
            return [
                'course' => $course,
                'payments_count' => $count,
            ];
        });

        return response()->json($result);
    }


    public function getPaymentsByModality()
    {
        $data = Payment::with('enrollment.course.modality')
            ->get()
            ->groupBy(function ($payment) {
                return $payment->enrollment->course->modality->name;
            })
            ->map(function ($payments) {
                return $payments->count();
            });

        $result = $data->map(function ($count, $modality) {
            return [
                'modality' => $modality,
                'payments_count' => $count,
            ];
        });

        return response()->json($result);
    }

    public function getStudentRegistrationsByDate()
    {
        $data = Student::select('created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('Y-m-d');
            })
            ->map(function ($items) {
                return [
                    'count' => $items->count(),
                    'date' => $items->first()->created_at->format('Y-m-d')
                ];
            });

        return response()->json($data);
    }

    public function studentsByCourse(Request $request)
    {
        $enrollments = Enrollment::with('course')->get();
        $studentsByCourse = $enrollments->groupBy(function ($enrollment) {
            return $enrollment->course->name;
        })->map(function ($group) {
            return $group->count();
        });

        return response()->json($studentsByCourse);
    }
}
