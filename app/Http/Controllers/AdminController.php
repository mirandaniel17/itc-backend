<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    public function getUsers(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $query = $request->input('query');
        if ($query) {
            $users = User::where('name', 'like', "%{$query}%")->with('roles')->paginate($perPage);
        } else {
            $users = User::with('roles')->paginate($perPage);
        }
        return response()->json($users);
    }

    public function getUserById($id)
    {
        $user = User::with(['roles', 'permissions'])->find($id);
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
        return response()->json($user);
    }
}
