<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StudentRequest;

class StudentController extends Controller
{
    public function getStudents(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query', null);

        if ($query) {
            $students = Student::search($query)->orderBy('last_name', 'asc')->paginate($perPage);
        } else {
            $students = Student::orderBy('last_name', 'asc')->paginate($perPage);
        }

        return response()->json($students, Response::HTTP_OK);
    }


    public function registerStudent(StudentRequest $request)
    {
        $data = $request->only([
            'last_name',
            'second_last_name',
            'name',
            'ci',
            'dateofbirth',
            'placeofbirth',
            'phone',
            'gender',
            'status'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('students', 'public');
            $data['image'] = $image;
        }

        $student = Student::create($data);

        return response()->json([
            'message' => 'Estudiante registrado exitosamente.',
            'student' => $student
        ], Response::HTTP_CREATED);
    }

    public function getStudentById($id)
    {
        $student = Student::find($id);
        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado.'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($student, Response::HTTP_OK);
    }

    public function editStudent(StudentRequest $request, $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $data = $request->all();

        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('students', 'public');
            $data['image'] = $image;
            if ($student->image) {
                Storage::disk('public')->delete($student->image);
            }
        }

        $student->update($data);

        return response()->json([
            'message' => 'Estudiante actualizado exitosamente.',
            'student' => $student
        ], Response::HTTP_OK);
    }


    public function deleteStudent($id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json([
                'message' => 'Estudiante no encontrado.'
            ], Response::HTTP_NOT_FOUND);
        }

        if ($student->image) {
            Storage::disk('public')->delete($student->image);
        }

        $student->delete();

        return response()->json([
            'message' => 'Estudiante eliminado exitosamente.'
        ], Response::HTTP_OK);
    }

    public function getAllStudents()
    {
        $students = Student::orderBy('last_name', 'asc')->get();

        return response()->json($students, Response::HTTP_OK);
    }
}
