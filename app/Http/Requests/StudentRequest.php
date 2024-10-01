<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StudentRequest extends FormRequest
{
   public function authorize():bool
   {
    return true;
   }
   public function rules():array
   {
    return [
        'last_name' => 'required|string|max:255|regex:/^[A-Z]/',
        'second_last_name' => 'required|string|max:255|regex:/^[A-Z]/',
        'name' => 'required|string|max:255|regex:/^[A-Z]/',
        'ci' => 'required|digits:8|regex:/^[0-9]{8}$/',
        'program_type' => 'required|in:MODULAR,CARRERA',
        'shift' => 'required|in:MAÑANA,TARDE',
        'phone' => 'required|digits_between:8,15|regex:/^[76][0-9]{7}$/',
        'gender' => 'required|in:MASCULINO,FEMENINO',
        'status' => 'required|boolean'
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