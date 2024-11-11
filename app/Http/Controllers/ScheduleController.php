<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CourseSchedule;
use App\Models\Shift;

class ScheduleController extends Controller
{
    public function index()
    {
        $courses = CourseSchedule::with(['course', 'shift'])->get();
        \Log::info($courses);
        return response()->json($courses, Response::HTTP_OK);
    }


    public function store(Request $request)
    {
        $course_id = $request->input('course_id');
        $schedules = $request->input('schedules');

        if (!is_array($schedules)) {
            return response()->json(['error' => 'El campo schedules debe ser un array.'], 400);
        }

        foreach ($schedules as $schedule) {
            CourseSchedule::updateOrCreate(
                [
                    'course_id' => $course_id,
                    'day' => $schedule['day'],
                    'shift_id' => $schedule['shift_id'],
                ]
            );
        }
        
        return response()->json(['message' => 'Horario guardado correctamente.']);
    }

    public function getCourseSchedules(Request $request)
    {
        $courseIds = $request->query('course_ids');
        $query = CourseSchedule::with('course', 'shift');

        if ($courseIds) {
            $courseIdsArray = explode(',', $courseIds);
            $query->whereIn('course_id', $courseIdsArray);
        }

        $schedules = $query->get()->map(function ($schedule) {
            return [
                'course_name' => $schedule->course->name,
                'parallel' => $schedule->course->parallel,
                'day' => $schedule->day,
                'shift_name' => $schedule->shift->name,
                'start_time' => $schedule->shift->start_time,
                'end_time' => $schedule->shift->end_time,
                'start_date' => $schedule->course->start_date, 
                'end_date' => $schedule->course->end_date,   
            ];
        });

        \Log::info($schedules);

        return response()->json(['schedules' => $schedules]);
    }
    public function show($id)
    {
        $schedule = CourseSchedule::with(['course', 'shift'])->findOrFail($id);

        return response()->json([
            'id' => $schedule->id,
            'course_name' => $schedule->course->name,
            'parallel' => $schedule->course->parallel,
            'day' => $schedule->day,
            'shift_id' => $schedule->shift->id,
            'shift_name' => $schedule->shift->name,
            'start_time' => $schedule->shift->start_time,
            'end_time' => $schedule->shift->end_time,
            'start_date' => $schedule->course->start_date,
            'end_date' => $schedule->course->end_date,
        ]);
    }

    public function update(Request $request, $id)
    {
        $schedule = CourseSchedule::findOrFail($id);

        $request->validate([
            'shift_id' => 'required|exists:shifts,id',
        ]);

        $schedule->update([
            'shift_id' => $request->input('shift_id'),
        ]);

        return response()->json(['message' => 'Horario actualizado correctamente']);
    }



}
