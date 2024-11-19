<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Course;
use App\Http\Requests\CourseRequest;

class CourseController extends Controller
{
    public function getCourses(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');

        if ($query) {
            $courses = Course::search($query)->query(function ($builder) {
                $builder->with(['teacher', 'modality']);
            })->paginate($perPage);
        } else {
            $courses = Course::with(['teacher', 'modality'])->paginate($perPage);
        }

        return response()->json($courses, Response::HTTP_OK);
    }

    public function registerCourse(CourseRequest $request)
    {
        $course = Course::create($request->all());
        return response()->json($course, Response::HTTP_CREATED);
    }

    public function getCourseById($id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course, Response::HTTP_OK);
    }

    public function editCourse(CourseRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $course->update($request->all());
        return response()->json($course, Response::HTTP_OK);
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return response()->json($course, Response::HTTP_OK);
    }
}
