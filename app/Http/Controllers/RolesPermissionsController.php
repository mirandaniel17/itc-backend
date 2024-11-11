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
use Spatie\Permission\Models\Role;

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
        return response()->json(['message' => 'Permissions updated successfully.']);
    }

    public function getUserRole($userId)
    {
        $user = User::findOrFail($userId);
        $role = $user->roles->pluck('name')->first();
        return response()->json([
            'user' => $user,
            'role' => $role
        ]);
    }

    public function updateUserRole(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $role = $request->input('role');

        if (is_null($role)) {
            $user->syncRoles([]);
            $user->syncPermissions([]);
            return response()->json(['message' => 'Role and permissions removed successfully.']);
        }

        if (!Role::where('name', $role)->exists()) {
            return response()->json(['error' => 'Role does not exist.'], Response::HTTP_BAD_REQUEST);
        }

        $user->syncRoles([$role]);
        return response()->json(['message' => 'Role updated successfully.']);
    }

    public function getAllRoles()
    {
        $allRoles = Role::all()->pluck('name');
        return response()->json([
            'roles' => $allRoles
        ]);
    }

    public function getRolePermissions($roleName)
    {
        $role = Role::where('name', $roleName)->firstOrFail();
        return response()->json([
            'permissions' => $role->permissions->pluck('name'),
        ]);
    }

}
