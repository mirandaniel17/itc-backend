<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Permission;

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
        $user->givePermissionTo(Permission::all());
        return response()->json([
            'message' => 'Usuario creado y rol asignado.',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'El correo electrónico ingresado no existe.'
            ], Response::HTTP_NOT_FOUND);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'La contraseña es incorrecta.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = JWTAuth::attempt($request->only('email', 'password'));

        if (!$token) {
            return response()->json([
                'message' => 'Las credenciales no son válidas.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        $rememberMe = $request->has('remember') && $request->remember == true;

        $cookieExpirationTime = $rememberMe ? 60 * 24 * 7 : 60 * 24;

        $cookie = cookie('jwt', $token, $cookieExpirationTime);

        return response()->json([
            'message' => 'Inicio de sesión exitoso.',
            'token' => $token
        ])->withCookie($cookie);
    }


    public function user()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        $cookie = Cookie::forget('jwt');
        return response()->json([
            'message' => 'Cierre de sesión exitoso.'
        ])->withCookie($cookie);
    }
}
