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

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas. Vuelva a ingresar correo electrónico y/o contraseña.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'message' => 'Debes verificar tu correo electrónico antes de iniciar sesión.'
            ], Response::HTTP_FORBIDDEN);
        }

        $rememberMe = $request->has('remember') && $request->remember == true;
        $token = JWTAuth::attempt($request->only('email', 'password'), $rememberMe);

        if (!$token) {
            return response()->json([
                'message' => 'Credenciales incorrectas.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $permissions = $user->getAllPermissions()->pluck('name');
        $cookieExpirationTime = $rememberMe ? 60 * 24 * 7 : 60 * 24;
        $cookie = cookie('jwt', $token, $cookieExpirationTime);

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'token' => $token,
            'permissions' => $permissions,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
            ]
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

    public function updateUserProfile(Request $request)
    {
        try {
            $user = auth()->user();

            if ($request->has('name') && !empty($request->input('name'))) {
                $user->name = $request->input('name');
            }

            if ($request->has('currentPassword') && $request->has('newPassword')) {
                if (!Hash::check($request->input('currentPassword'), $user->password)) {
                    return response()->json([
                        'message' => 'La contraseña actual no es correcta.'
                    ], Response::HTTP_UNAUTHORIZED);
                }

                $newPassword = $request->input('newPassword');
                $passwordValidationRules = [
                    'password' => [
                        'required',
                        'string',
                        'min:8',
                        'regex:/^(?=.*[A-Z])(?=.*[\W_]).+$/',
                    ],
                ];

                $validator = \Validator::make(['password' => $newPassword], $passwordValidationRules);

                if ($validator->fails()) {
                    return response()->json([
                        'message' => 'La nueva contraseña no cumple con los requisitos.',
                        'errors' => $validator->errors()
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $user->password = Hash::make($newPassword);
            }

            $user->save();

            return response()->json(['message' => 'Perfil actualizado con éxito.', 'user' => $user], Response::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error inesperado.',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
