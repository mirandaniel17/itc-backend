<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Teacher;
use App\Http\Requests\TeacherRequest;

class TeacherController extends Controller
{
    public function getTeachers(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');
    
        if ($query) {
            $teachers = Teacher::search($query)->orderBy('last_name', 'asc')->paginate($perPage);
        } else {
            $teachers = Teacher::orderBy('last_name', 'asc')->paginate($perPage);
        }
    
        return response()->json($teachers, Response::HTTP_OK);
    }
    

     public function registerTeacher(TeacherRequest $request)
    {
        $teacher = Teacher::create($request->all());
        return response()->json($teacher, Response::HTTP_CREATED);
    }

    public function getTeacherById($id)
    {
        $teacher = Teacher::findOrFail($id);
        return response()->json($teacher, Response::HTTP_OK);
    }

    public function editTeacher(TeacherRequest $request, $id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->update($request->all());
        return response()->json($teacher, Response::HTTP_OK);
    }

    public function deleteTeacher($id)
    {
        $teacher = Teacher::findOrFail($id);
        $teacher->delete();
        return response()->json(Response::HTTP_OK);
    }
}
