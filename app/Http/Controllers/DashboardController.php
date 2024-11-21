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
}
