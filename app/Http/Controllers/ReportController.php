<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\Enrollment;
use App\Models\Task;
use App\Models\Grade;

class ReportController extends Controller
{
    public function exportAttendanceReport()
    {
        $courseId = request('course_id');
        $startDate = request('start_date');
        $endDate = request('end_date');

        $course = Course::find($courseId);
        $attendances = Attendance::where('course_id', $courseId)->whereBetween('date', [$startDate, $endDate])->with('student')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Nro.');
        $sheet->setCellValue('B1', 'Apellidos y Nombres');

        $dates = collect($attendances->pluck('date'))->unique()->sort()->values();
        $columnIndex = 3;
        foreach ($dates as $date) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex++) . '1', $date);
        }

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex++) . '1', 'Asistencias');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex++) . '1', 'Faltas');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex) . '1', 'Licencias');
        $students = $attendances->groupBy('student_id');
        $rowIndex = 2;

        foreach ($students as $studentId => $studentAttendances) {
            $fullName = $studentAttendances->first()->student->last_name . ' ' . $studentAttendances->first()->student->second_last_name . ' ' . $studentAttendances->first()->student->name;
            $sheet->setCellValue('A' . $rowIndex, $rowIndex - 1);
            $sheet->setCellValue('B' . $rowIndex, $fullName);
            $attendanceCounts = ['PRESENTE' => 0, 'AUSENTE' => 0, 'LICENCIA' => 0];
            foreach ($dates as $key => $date) {
                $attendance = $studentAttendances->firstWhere('date', $date);
                $status = $attendance ? $attendance->status : '';
                $sheet->setCellValue(Coordinate::stringFromColumnIndex(3 + $key) . $rowIndex, $status);
                if ($status) {
                    $attendanceCounts[$status]++;
                }
            }
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex - 2) . $rowIndex, $attendanceCounts['PRESENTE']);
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex - 1) . $rowIndex, $attendanceCounts['AUSENTE']);
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex) . $rowIndex, $attendanceCounts['LICENCIA']);
            $rowIndex++;
        }

        $formattedStartDate = date('Y-m-d', strtotime($startDate));
        $formattedEndDate = date('Y-m-d', strtotime($endDate));
        $fileName = "reporte_asistencias_del_{$formattedStartDate}_al_{$formattedEndDate}.xlsx";
        $writer = new Xlsx($spreadsheet);
        $path = storage_path($fileName);
        $writer->save($path);
        return response()->download($path, $fileName)->deleteFileAfterSend();
    }

    public function exportEnrollmentReport()
    {
        $courseId = request('course_id');

        $course = Course::find($courseId);
        if (!$course) {
            return response()->json(['message' => 'Curso no encontrado'], 404);
        }

        $enrollments = Enrollment::where('course_id', $courseId)->with('student', 'discount')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Nro.');
        $sheet->setCellValue('B1', 'Apellidos y Nombres');
        $sheet->setCellValue('C1', 'Carnet de Identidad');
        $sheet->setCellValue('D1', 'Descuento');
        $sheet->setCellValue('E1', 'Tipo de Pago');
        $sheet->setCellValue('F1', 'Pagos Totales');

        $rowIndex = 2;

        foreach ($enrollments as $index => $enrollment) {
            if (!$enrollment->student) {
                continue;
            }
            $sheet->setCellValue('A' . $rowIndex, $index + 1);
            $sheet->setCellValue('B' . $rowIndex, $enrollment->student->full_name);
            $sheet->setCellValue('C' . $rowIndex, $enrollment->student->ci);
            $sheet->setCellValue('D' . $rowIndex, $enrollment->discount->name ?? 'Sin descuento');
            $sheet->setCellValue('E' . $rowIndex, $enrollment->payment_type);
            $sheet->setCellValue('F' . $rowIndex, $enrollment->totalPayments());
            $rowIndex++;
        }

        $courseNameSlug = str_replace(' ', '-', strtolower($course->name));
        $fileName = "reporte_inscripciones_curso_{$courseNameSlug}.xlsx";
        $writer = new Xlsx($spreadsheet);
        $temp = storage_path($fileName);
        $writer->save($temp);

        return response()->download($temp, $fileName)->deleteFileAfterSend();
    }

    public function exportGradesReport()
    {
        $courseId = request('course_id');
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json(['message' => 'Curso no encontrado'], 404);
        }

        $tasks = $course->tasks()->get();

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No hay tareas asociadas a este curso'], 404);
        }

        $students = $course->enrollments()
            ->with('student')
            ->get()
            ->pluck('student');

        if ($students->isEmpty()) {
            return response()->json(['message' => 'No hay estudiantes inscritos en este curso'], 404);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Nro.');
        $sheet->setCellValue('B1', 'Apellidos y Nombres');
        $sheet->setCellValue('C1', 'Carnet de Identidad');
    
        $columnIndex = 4;
        foreach ($tasks as $task) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex) . '1', $task->title);
            $columnIndex++;
        }

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex) . '1', 'Promedio');
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($columnIndex + 1) . '1', 'Nota Final');

        $rowIndex = 2;
        foreach ($students as $index => $student) {
            $sheet->setCellValue('A' . $rowIndex, $index + 1);
            $sheet->setCellValue('B' . $rowIndex, $student->full_name);
            $sheet->setCellValue('C' . $rowIndex, $student->ci);

            $totalGrades = 0;
            $taskCount = $tasks->count();

            $currentColumn = 4;
            foreach ($tasks as $task) {
                $grade = Grade::where('task_id', $task->id)->where('student_id', $student->id)->value('grade') ?? 0;
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . $rowIndex, $grade);
                $totalGrades += $grade;
                $currentColumn++;
            }

            $average = $taskCount > 0 ? $totalGrades / $taskCount : 0;
            $finalGrade = round($average, 2);
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn) . $rowIndex, $average);
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($currentColumn + 1) . $rowIndex, $finalGrade);
            $rowIndex++;
        }

        $courseNameSlug = str_replace(' ', '-', strtolower($course->name));
        $fileName = "reporte_notas_curso_{$courseNameSlug}.xlsx";
        $writer = new Xlsx($spreadsheet);
        $path = storage_path($fileName);
        $writer->save($path);
        return response()->download($path)->deleteFileAfterSend();
    }
}
