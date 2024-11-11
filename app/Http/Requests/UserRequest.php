<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => [
            'required',
            'string',
            'max:20',
            'regex:/^[a-zA-Z\s]+$/'
        ],
        "email" => [
            'required',
            'email',
            'regex:/^[a-zA-Z0-9._%+-]+@(gmail|hotmail|yahoo|outlook)\.(com|es|bo)$/',
            'not_regex:/^[-!#$%&\'*+\\/=?^_`{|}~.]/',
            'not_regex:/[-!#$%&\'*+\\/=?^_`{|}~.]$/',
            'unique:users,email'
        ],
        "password" => [
            'required',
            'string',
            'min:8',
            'regex:/^(?=.*[A-Z])(?=.*[\W_]).+$/',
        ],
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
