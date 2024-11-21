<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Payment;
use App\Models\Enrollment;
use App\Http\Requests\PaymentRequest;

class PaymentController extends Controller
{
    public function getPayments(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query', null);
        if ($query) {
            $payments = Payment::search($query)
                ->query(fn ($builder) => $builder->with(['enrollment.student', 'enrollment.course']))
                ->paginate($perPage);
        } else {
            $payments = Payment::with(['enrollment.student', 'enrollment.course'])
                ->orderBy('payment_date', 'desc')
                ->paginate($perPage);
        }
        return response()->json($payments, Response::HTTP_OK);
    }

    public function registerPayment(PaymentRequest $request)
    {
        $payment = Payment::create($request->all());

        return response()->json([
            'message' => 'Pago registrado con éxito.',
            'payment' => $payment,
        ], Response::HTTP_CREATED);
    }

    public function getPaymentById($id)
    {
        $payment = Payment::with(['enrollment.student', 'enrollment.course'])->findOrFail($id);

        return response()->json($payment, Response::HTTP_OK);
    }

    public function editPayment(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->update($request->all());

        return response()->json([
            'message' => 'Pago actualizado con éxito.',
            'payment' => $payment,
        ], Response::HTTP_OK);
    }

    public function deletePayment($id)
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();

        return response()->json([
            'message' => 'Pago eliminado con éxito.',
        ], Response::HTTP_OK);
    }

    public function getStudentDetails($studentId)
    {
        $enrollments = Enrollment::with(['student', 'course', 'payments'])
            ->where('student_id', $studentId)
            ->get();

        if ($enrollments->isEmpty()) {
            return response()->json([
                'message' => 'No se encontraron inscripciones para este estudiante.'
            ], Response::HTTP_NOT_FOUND);
        }

        $details = $enrollments->map(function ($enrollment) {
            $totalPayments = $enrollment->payments->sum('amount');
            $balance = max($enrollment->course->cost - $totalPayments, 0);
            return [
                'enrollment_id' => $enrollment->id,
                'student' => [
                    'id' => $enrollment->student->id,
                    'name' => $enrollment->student->name,
                    'last_name' => $enrollment->student->last_name,
                    'second_last_name' => $enrollment->student->second_last_name,
                ],
                'course' => [
                    'id' => $enrollment->course->id,
                    'name' => $enrollment->course->name,
                    'cost' => $enrollment->course->cost,
                ],
                'total_payments' => $totalPayments,
                'saldo_pendiente' => $balance,
            ];
        });

        return response()->json($details, Response::HTTP_OK);
    }
}
