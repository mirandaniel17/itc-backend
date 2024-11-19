<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->firstOrFail();

        if ($user) {
            $token = app('auth.password.broker')->createToken($user);
            $resetLink = "http://localhost:5173/reset-password/{$token}";
            Mail::to($request->email)->send(new ResetPasswordMail($resetLink));
            return response()->json(['message' => 'Enlace de restablecimiento de contraseña enviado a tu correo.'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'No se pudo enviar el correo.'], Response::HTTP_BAD_REQUEST);
    }

    public function resetPassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|email',
                'token' => 'required',
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'regex:/^(?=.*[A-Z])(?=.*[\W_]).+$/',
                    'confirmed',
                ],
            ], [
                'password.regex' => 'La contraseña debe tener al menos una letra mayúscula y un carácter especial.',
                'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            ]);

            $user = User::where('email', $request->email)->firstOrFail();
            $user->password = Hash::make($validated['password']);
            $user->save();

            return response()->json(['message' => 'Contraseña actualizada con éxito.'], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' => $e->errors(),
            ], 422);
        }
    }

    public function getEmailByToken($token)
    {
        $record = DB::table('password_reset_tokens')->get();
        foreach ($record as $entry) {
            if (Hash::check($token, $entry->token)) {
                return response()->json(['email' => $entry->email], 200);
            }
        }
        return response()->json(['message' => 'Token inválido.'], 404);
    }
}
