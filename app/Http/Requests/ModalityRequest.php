<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ModalityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/u',
            ],
            'duration_in_months' => [
                'required',
                'integer',
                'min:6',
                'max:24', 
            ],
        ];
    }

    public function messages()
    {
        return [
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'duration_in_months.min' => 'La duración en meses debe ser de al menos 6 meses.',
            'duration_in_months.max' => 'La duración en meses no puede ser mayor a 24 meses.',
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
