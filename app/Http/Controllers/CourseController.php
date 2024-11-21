<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Course;
use App\Models\User;
use App\Http\Requests\CourseRequest;
use App\Notifications\CourseCompletionNotification;
use Carbon\Carbon;

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

    public function notifyCourseCompletion()
    {
        $today = Carbon::today();
        $courses = Course::whereDate('end_date', $today)->get();

        if ($courses->isEmpty()) {
            return response()->json(['message' => 'No hay cursos que finalicen hoy.'], Response::HTTP_OK);
        }

        foreach ($courses as $course) {
            $users = User::all();
            foreach ($users as $user) {
                $user->notify(new CourseCompletionNotification($course));
            }
        }

        return response()->json(['message' => 'Notificaciones de finalización de cursos enviadas.'], Response::HTTP_OK);
    }
}
