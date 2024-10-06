<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Permission;

class RolesPermissionsController extends Controller
{
    public function getUserPermissions($userId)
    {
        $user = User::findOrFail($userId);
        $allPermissions = Permission::all()->pluck('name');
        $assignedPermissions = $user->permissions->pluck('name');
        return response()->json([
            'user' => $user,
            'all_permissions' => $allPermissions,
            'assigned_permissions' => $assignedPermissions
        ]);
    }

    public function savePermissions(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $permissions = $request->input('permissions', []);

        $user->syncPermissions($permissions);

        return response()->json(['message' => 'Permisos actualizados con éxito.']);
    }
}
