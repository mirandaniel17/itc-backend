<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Task;
use App\Models\Grade;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    public function getCourses()
    {
        $courses = Course::with('teacher')->get();

        return response()->json($courses, Response::HTTP_OK);
    }

    public function getTasks($courseId)
    {
        $tasks = Task::where('course_id', $courseId)->get();

        return response()->json($tasks, Response::HTTP_OK);
    }

    public function createTask(Request $request)
    {
        $task = Task::create($request->all());

        return response()->json($task, Response::HTTP_CREATED);
    }

    public function getStudentsWithGrades($taskId)
    {
        $task = Task::with('grades.student')->findOrFail($taskId);

        $students = Student::whereHas('enrollments', function ($query) use ($task) {
            $query->where('course_id', $task->course_id);
        })->get();

        $grades = $task->grades->keyBy('student_id');

        return response()->json([
            'students' => $students,
            'grades' => $grades,
        ], Response::HTTP_OK);
    }

    public function saveGrades(Request $request, $taskId)
    {
        foreach ($request->grades as $gradeData) {
            Grade::updateOrCreate(
                ['task_id' => $taskId, 'student_id' => $gradeData['student_id']],
                ['grade' => $gradeData['grade'], 'delivered' => $gradeData['delivered']]
            );
        }

        return response()->json(['message' => 'Notas guardadas con éxito'], Response::HTTP_OK);
    }

    public function deleteTask($taskId)
    {
        $task = Task::find($taskId);

        if (!$task) {
            return response()->json(['message' => 'Tarea no encontrada'], Response::HTTP_NOT_FOUND);
        }

        $task->delete();

        return response()->json(['message' => 'Tarea eliminada correctamente'], Response::HTTP_OK);
    }

}
