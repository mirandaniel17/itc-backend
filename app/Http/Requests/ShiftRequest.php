<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ShiftRequest extends FormRequest
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
                'max:100',
                'regex:/^[a-zA-ZÀ-ÿ\s]+$/u',
            ],
            'start_time' => 'required|date_format:H:i',
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                function ($attribute, $value, $fail) {
                    $startTime = \Carbon\Carbon::createFromFormat('H:i', $this->input('start_time'))->setDate(2024, 1, 1);
                    $endTime = \Carbon\Carbon::createFromFormat('H:i', $value)->setDate(2024, 1, 1);

                    if ($endTime->lt($startTime)) {
                        $endTime->addDay();
                    }

                    $diffInMinutes = $startTime->diffInMinutes($endTime);

                    \Log::info('Diferencia en minutos ajustada: ' . $diffInMinutes);

                    if ($diffInMinutes < 60) {
                        $fail('La diferencia entre la hora inicial y la final debe ser de al menos 1 hora.');
                    }
                },
            ],

            'room_id' => 'required|exists:rooms,id',
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
