<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Course;

class CourseController extends Controller
{
    public function getCourses()
    {
        $courses = Course::all();
        return response()->json($courses, Response::HTTP_OK);
    }

    public function registerCourse(Request $request)
    {
        $course = Course::create($request->all());
        return response()->json($course, Response::HTTP_CREATED);
    }

    public function getCourseById($id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course, Response::HTTP_OK);
    }

    public function editCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->update($request->all());
        return response()->json($course, Response::HTTP_OK);
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
