<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Task;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Schedule;
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

    public function getActiveStudents()
    {
        $students = Student::where('status', 1)->orderBy('last_name', 'asc')->get();

        return response()->json($students, Response::HTTP_OK);
    }


    public function getAcademicHistory($id)
    {
        $student = Student::with([
            'enrollments.course.tasks.grades',
            'enrollments.course.attendances',
            'enrollments.course.courseSchedules.shift'
        ])->find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $history = [];

        foreach ($student->enrollments as $enrollment) {
            $course = $enrollment->course;
            $schedules = $course->courseSchedules->map(function ($schedule) {
                return [
                    'day' => $schedule->day ?? 'Sin día asignado',
                    'start_time' => $schedule->shift->start_time ?? 'Sin hora de inicio',
                    'end_time' => $schedule->shift->end_time ?? 'Sin hora de fin',
                ];
            })->toArray();

            $grades = $course->tasks->map(function ($task) use ($student) {
                $grade = $task->grades->where('student_id', $student->id)->first();
                return [
                    'task' => $task->title,
                    'grade' => $grade->grade ?? 0,
                ];
            })->toArray();

            $attendanceCounts = [
                'PRESENTE' => $course->attendances->where('student_id', $student->id)->where('status', 'PRESENTE')->count(),
                'AUSENTE' => $course->attendances->where('student_id', $student->id)->where('status', 'AUSENTE')->count(),
                'LICENCIA' => $course->attendances->where('student_id', $student->id)->where('status', 'LICENCIA')->count(),
            ];

            $totalPayments = $enrollment->payments->sum('amount');

            $history[] = [
                'course' => $course->name,
                'start_date' => $course->start_date,
                'end_date' => $course->end_date,
                'schedules' => $schedules,
                'grades' => $grades,
                'attendance' => $attendanceCounts,
                'payments' => $totalPayments,
            ];
        }

        return response()->json($history, Response::HTTP_OK);
    }

    public function disableStudent(Request $request, $id)
    {
        $student = Student::find($id);

        if (!$student) {
            return response()->json(['message' => 'Estudiante no encontrado.'], Response::HTTP_NOT_FOUND);
        }

        $student->status = !$student->status;
        $student->save();

        return response()->json([
            'message' => 'Estado del estudiante actualizado con éxito.',
            'status' => $student->status
        ], Response::HTTP_OK);
    }
}
