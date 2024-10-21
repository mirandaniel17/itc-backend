<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Password;
use App\Mail\VerifyEmailMail;

class AuthController extends Controller
{
    public function register(UserRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $user->assignRole('Gerente');

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        Mail::to($user->email)->send(new VerifyEmailMail($verificationUrl));

        return response()->json(['message' => 'Registro exitoso. Por favor, verifica tu correo electrónico.'], 201);
    }


    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'El correo electrónico ingresado no existe.'
            ], Response::HTTP_NOT_FOUND);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Debes verificar tu correo electrónico antes de iniciar sesión.'
            ], Response::HTTP_FORBIDDEN);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'La contraseña es incorrecta.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $rememberMe = $request->has('remember') && $request->remember == true;

        $token = JWTAuth::attempt($request->only('email', 'password'), $rememberMe);

        if (!$token) {
            return response()->json([
                'message' => 'Las credenciales no son válidas.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $cookieExpirationTime = $rememberMe ? 60 * 24 * 7 : 60 * 24;

        $cookie = cookie('jwt', $token, $cookieExpirationTime);

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'token' => $token
        ])->withCookie($cookie);
    }


    public function user()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no autenticado.'
            ], Response::HTTP_UNAUTHORIZED);
        }
        $roles = $user->getRoleNames();
        $permissions = $user->getAllPermissions()->pluck('name');
        $userAgent = request()->header('User-Agent');
        return response()->json([
            'user' => $user,
            'roles' => $roles,
            'permissions' => $permissions,
            'userAgent' => $userAgent
        ], Response::HTTP_OK);
    }


    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        $cookie = Cookie::forget('jwt');
        return response()->json([
            'message' => 'Cierre de sesión exitoso.'
        ], Response::HTTP_OK)->withCookie($cookie);
    }

    

}
