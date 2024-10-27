<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Storage;

class EnrollmentController extends Controller
{
    public function getEnrollments(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');

        $enrollments = Enrollment::with(['student', 'course', 'shift'])
            ->when($query, function ($q) use ($query) {
                $q->whereHas('student', function ($q) use ($query) {
                    $q->where('name', 'like', "%$query%");
                });
            })
            ->paginate($perPage);

        return response()->json($enrollments, Response::HTTP_OK);
    }

    public function registerEnrollment(Request $request)
    {
        $data = $request->only([
            'student_id',
            'shift_id',
            'course_id',
            'discount_id',
            'enrollment_date'
        ]);

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
            'message' => 'Enrollment registered successfully.',
            'enrollment' => $enrollment
        ], Response::HTTP_CREATED);
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
            'shift_id' => $request->input('shift_id', $enrollment->shift_id),
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
