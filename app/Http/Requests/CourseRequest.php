<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|before:end_date', 
            'end_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $startDate = Carbon::parse($this->start_date);
                    $endDate = Carbon::parse($value);
                    if ($startDate->diffInMonths($endDate) < 5) {
                        $fail('La diferencia entre la fecha de inicio y la fecha final debe ser mayor a 5 meses.');
                    }
                },
            ],
            'teacher_id' => 'required|exists:teachers,id',
            'modality_id' => 'required|exists:modalities,id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422));
    }
}
