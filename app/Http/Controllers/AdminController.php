<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StudentRequest;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function registerStudent(StudentRequest $request)
    {
        $data = $request->only([    
            'last_name',
            'second_last_name',
            'name',
            'ci',
            'program_type',
            'school_cycle',
            'shift',
            'parallel',
            'dateofbirth',
            'placeofbirth',
            'phone',
            'gender',
            'status'
        ]);

        if ($request->hasFile('image')) 
        {
            $image = $request->file('image')->store('students', 'public');
            $data['image'] = $image;
        }

        $student = Student::create($data);

        return response()->json([
            'message' => 'Student registered successfully.',
            'student' => $student
        ], 201);
    }
    
    public function getStudents(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');
        if ($query) {
            $students = Student::search($query)->paginate($perPage);
        } else {
            $students = Student::paginate($perPage);
        }
        return response()->json($students);
    }


    public function getStudentById($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Student not found.'], 404);
        }
        return response()->json($student);
    }


    public function editStudent(Request $request, $id)
    {
        $student = Student::find($id);
        $student->update([
            'last_name' => $request->input('last_name', $student->last_name),
            'second_last_name' => $request->input('second_last_name', $student->second_last_name),
            'name' => $request->input('name', $student->name),
            'ci' => $request->input('ci', $student->ci),
            'image' => $request->input('image', $student->image),
            'program_type' => $request->input('program_type', $student->program_type),
            'school_cycle' => $request->input('school_cycle', $student->school_cycle),
            'shift' => $request->input('shift', $student->shift),
            'parallel' => $request->input('parallel', $student->parallel),
            'dateofbirth' => $request->input('dateofbirth', $student->dateofbirth),
            'placeofbirth' => $request->input('placeofbirth', $student->placeofbirth),
            'phone' => $request->input('phone', $student->phone),
            'gender' => $request->input('gender', $student->gender),
            'status' => $request->input('status', $student->status),
        ]);
        return response()->json([
            'message' => 'Student updated successfully.',
            'student' => $student
        ], 200);
    }


    public function deleteStudent($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'message' => 'Student not found.'
            ], 404);
        }

        if ($student->image) {
            Storage::disk('public')->delete($student->image);
        }

        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.'
        ], 200);
    }
}
