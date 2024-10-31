<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CourseSchedule;
use App\Models\Shift;

class ScheduleController extends Controller
{
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
        $courseIdsArray = explode(',', $courseIds);

        $schedules = CourseSchedule::whereIn('course_id', $courseIdsArray)
            ->with('course')
            ->get()
            ->map(function ($schedule) {
                return [
                    'course_name' => $schedule->course->name,
                    'day' => $schedule->day,
                    'start_time' => $schedule->shift->start_time,
                    'end_time' => $schedule->shift->end_time,
                ];
            });

        return response()->json(['schedules' => $schedules]);
    }
}
