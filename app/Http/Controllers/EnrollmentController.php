<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Enrollment;
use App\Models\CourseSchedule;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\EnrollmentRequest;

class EnrollmentController extends Controller
{
    public function getEnrollments(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');

        $enrollments = Enrollment::with(['student', 'course', 'payments'])
            ->when($query, function ($q) use ($query) {
                $q->whereHas('student', function ($q) use ($query) {
                    $q->where('name', 'like', "%$query%");
                });
            })->paginate($perPage);

        $enrollments->getCollection()->transform(function ($enrollment) {
            $totalPaid = $enrollment->payments->sum('amount');
            $totalCost = $enrollment->course->cost;
            if ($enrollment->discount) {
                $totalCost -= ($totalCost * $enrollment->discount->percentage) / 100;
            }
            $enrollment->payment_status = $totalPaid >= $totalCost ? 'Pagado' : 'Pendiente';
            return $enrollment;
        });

        return response()->json($enrollments, Response::HTTP_OK);
    }

    public function registerEnrollment(EnrollmentRequest $request)
    {
        $data = $request->only([
            'student_id',
            'discount_id',
            'enrollment_date',
            'payment_type',
            'amount',
        ]);

        $courseIds = $request->input('course_ids', []);

        if ($request->hasFile('document_1')) {
            $data['document_1'] = $request->file('document_1')->store('documents', 'public');
        }

        if ($request->hasFile('document_2')) {
            $data['document_2'] = $request->file('document_2')->store('documents', 'public');
        }

        foreach ($courseIds as $courseId) {
            $data['course_id'] = $courseId;
            $enrollment = Enrollment::create($data);
            $courseCost = $enrollment->course->cost;

            if (!empty($data['discount_id'])) {
                $discount = $enrollment->discount->percentage ?? 0;
                $courseCost -= ($courseCost * $discount) / 100;
            }

            $initialAmount = $request->input('amount', 0);

            if ($data['payment_type'] === 'MENSUAL' && $initialAmount <= 0) {
                return response()->json([
                    'message' => 'El monto inicial es requerido y debe ser mayor a 0 para pagos mensuales.',
                ], Response::HTTP_BAD_REQUEST);
            }

            Payment::create([
                'enrollment_id' => $enrollment->id,
                'amount' => $initialAmount,
                'payment_date' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Inscripción registrada con éxito.',
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
            return response()->json(['message' => 'Inscripción no encontrada.'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($enrollment, Response::HTTP_OK);
    }

    public function editEnrollment(Request $request, $id)
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json(['message' => 'Inscripción no encontrada.'], Response::HTTP_NOT_FOUND);
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
            'message' => 'Inscripción actualizada exitosamente.',
            'enrollment' => $enrollment
        ], Response::HTTP_OK);
    }

    public function deleteEnrollment($id)
    {
        $enrollment = Enrollment::find($id);

        if (!$enrollment) {
            return response()->json(['message' => 'Inscripción no encontrada.'], Response::HTTP_NOT_FOUND);
        }
        
        if ($enrollment->document_1) {
            Storage::disk('public')->delete($enrollment->document_1);
        }

        if ($enrollment->document_2) {
            Storage::disk('public')->delete($enrollment->document_2);
        }

        $enrollment->delete();
        
        return response()->json([
            'message' => 'Inscripción eliminada exitosamente.'
        ], Response::HTTP_OK);
    }
}
