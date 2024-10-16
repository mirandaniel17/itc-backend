<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Carbon\Carbon;

class TeacherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teacherId = $this->route('id');

        return [
            'last_name' => 'required|string|max:255|regex:/^[A-Z]/',
            'second_last_name' => 'required|string|max:255|regex:/^[A-Z]/',
            'name' => 'required|string|max:255|regex:/^[A-Z]/',
            'ci' => [
                'required',
                'digits:8',
                'regex:/^[0-9]{8}$/',
                Rule::unique('teachers', 'ci')->ignore($teacherId), 
            ],
            'dateofbirth' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $birthDate = Carbon::parse($value);
                    $today = Carbon::now();
                    if ($birthDate->diffInYears($today) < 18) {
                        $fail('El docente debe tener al menos 18 años.');
                    }
                },
            ],
            'placeofbirth' => 'required|string|max:255',
            'phone' => 'required|digits_between:8,15|regex:/^[76][0-9]{7}$/',
            'gender' => 'required|in:MASCULINO,FEMENINO,OTRO',
            'specialty' => 'nullable|string|max:255',
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
