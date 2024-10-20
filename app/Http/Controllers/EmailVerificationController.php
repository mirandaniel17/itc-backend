<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Response;

class EmailVerificationController extends Controller
{
    public function showNotice()
    {
        return response()->json(['message' => 'Por favor verifica tu correo electrónico.'], 200);
    }

    public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
            return response()->json(['message' => 'El hash no coincide con el correo.'], 403);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'El correo ya ha sido verificado.'], 200);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json(['message' => 'Correo verificado exitosamente.'], 200);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->user()->sendEmailVerificationNotification();

        return response()->json(['message' => 'Se ha reenviado el correo de verificación.'], 200);
    }
}
