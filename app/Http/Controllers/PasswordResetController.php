<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;

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
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'La contraseña ha sido restablecida.'], Response::HTTP_OK);
        }

        return response()->json(['message' => 'Error al restablecer la contraseña.'], Response::HTTP_BAD_REQUEST);
    }
}
