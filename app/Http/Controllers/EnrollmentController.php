<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Enrollment;
use App\Models\CourseSchedule;
use Illuminate\Support\Facades\Storage;

class EnrollmentController extends Controller
{
    public function getEnrollments(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');
        $enrollments = Enrollment::with(['student', 'course'])
            ->when($query, function ($q) use ($query) {
                $q->whereHas('student', function ($q) use ($query) {
                    $q->where('name', 'like', "%$query%");
                });
            })->paginate($perPage);
        return response()->json($enrollments, Response::HTTP_OK);
    }

    public function registerEnrollment(Request $request)
    {
        $data = $request->only([
            'student_id',
            'course_id',
            'discount_id',
            'enrollment_date'
        ]);
        if (!isset($data['course_id'])) {
            return response()->json(['error' => 'El campo course_id es obligatorio.'], 400);
        }
        $selectedCourseSchedules = CourseSchedule::where('course_id', $data['course_id'])->get();
        $existingEnrollments = Enrollment::where('student_id', $data['student_id'])->pluck('course_id');
        $existingCourseSchedules = CourseSchedule::whereIn('course_id', $existingEnrollments)->get();
        foreach ($selectedCourseSchedules as $selectedSchedule) {
            foreach ($existingCourseSchedules as $existingSchedule) {
                if ($selectedSchedule->day === $existingSchedule->day) {
                    if ($this->hasTimeConflict($selectedSchedule, $existingSchedule)) {
                        return response()->json([
                            'error' => "Conflicto de horario detectado: el curso {$selectedSchedule->course->name} choca con otro curso en el día {$selectedSchedule->day} de {$selectedSchedule->shift->start_time} a {$selectedSchedule->shift->end_time}"
                        ], 409);
                    }
                }
            }
        }

        if ($request->hasFile('document_1')) {
            $document1 = $request->file('document_1')->store('documents', 'public');
            $data['document_1'] = $document1;
        }

        if ($request->hasFile('document_2')) {
            $document2 = $request->file('document_2')->store('documents', 'public');
            $data['document_2'] = $document2;
        }

        $enrollment = Enrollment::create($data);

        return response()->json([
            'message' => 'Inscripción registrada con éxito.',
            'enrollment' => $enrollment
        ], Response::HTTP_CREATED);
    }

    private function hasTimeConflict($schedule1, $schedule2)
    {
        $start1 = strtotime($schedule1->shift->start_time);
        $end1 = strtotime($schedule1->shift->end_time);
        $start2 = strtotime($schedule2->shift->start_time);
        $end2 = strtotime($schedule2->shift->end_time);
        return ($start1 < $end2 && $start2 < $end1);
    }

    public function getEnrollmentById($id)
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return response()->json(['message' => 'Enrollment not found.'], Response::HTTP_NOT_FOUND);
        }
        return response()->json($enrollment, Response::HTTP_OK);
    }

    public function editEnrollment(Request $request, $id)
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return response()->json(['message' => 'Enrollment not found.'], Response::HTTP_NOT_FOUND);
        }
        $enrollment->update([
            'student_id' => $request->input('student_id', $enrollment->student_id),
            'course_id' => $request->input('course_id', $enrollment->course_id),
            'discount_id' => $request->input('discount_id', $enrollment->discount_id),
            'enrollment_date' => $request->input('enrollment_date', $enrollment->enrollment_date),
        ]);
        if ($request->hasFile('document_1')) {
            Storage::disk('public')->delete($enrollment->document_1);
            $document1 = $request->file('document_1')->store('documents', 'public');
            $enrollment->document_1 = $document1;
        }
        if ($request->hasFile('document_2')) {
            Storage::disk('public')->delete($enrollment->document_2);
            $document2 = $request->file('document_2')->store('documents', 'public');
            $enrollment->document_2 = $document2;
        }
        $enrollment->save();
        return response()->json([
            'message' => 'Enrollment updated successfully.',
            'enrollment' => $enrollment
        ], Response::HTTP_OK);
    }

    public function deleteEnrollment($id)
    {
        $enrollment = Enrollment::find($id);
        if (!$enrollment) {
            return response()->json(['message' => 'Enrollment not found.'], Response::HTTP_NOT_FOUND);
        }
        if ($enrollment->document_1) {
            Storage::disk('public')->delete($enrollment->document_1);
        }
        if ($enrollment->document_2) {
            Storage::disk('public')->delete($enrollment->document_2);
        }
        $enrollment->delete();
        return response()->json([
            'message' => 'Enrollment deleted successfully.'
        ], Response::HTTP_OK);
    }
}
