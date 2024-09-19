<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function users()
    {
        $user = Auth::user();
        if ($user->hasRole('Admin')) {
            $users = User::all();
            return response()->json($users);
        }
    }

    public function registerStudent(Request $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $user->assignRole('Student');

        $student = Student::create([
            'last_name' => $request->input('last_name'),
            'second_last_name' => $request->input('second_last_name'),
            'first_name' => $request->input('first_name'),
            'second_name' => $request->input('second_name'),
            'dateofbirth' => $request->input('dateofbirth'),
            'placeofbirth' => $request->input('placeofbirth'),
            'phone' => $request->input('phone'),
            'gender' => $request->input('gender'),
            'status' => $request->input('status', true),
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Student registered successfully.',
            'student' => $student
        ], 201);
    }

    public function registerTeacher(Request $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $user->assignRole('Teacher');

        $teacher = Teacher::create([
            'last_name' =>  $request->input('last_name'),
            'second_last_name' => $request->input('second_last_name'),
            'first_name' => $request->input('first_name'),
            'second_name' => $request->input('second_name'),
            'dateofbirth' => $request->input('dateofbirth'),
            'placeofbirth' => $request->input('placeofbirth'),
            'phone' => $request->input('phone'),
            'gender' => $request->input('gender'),
            'specialty' => $request->input('specialty'),
            'user_id' => $user->id,
        ]);

        return response()->json([
            'message' => 'Teacher registered successfully.',
            'teacher' => $teacher
        ], 201);
    }
}
