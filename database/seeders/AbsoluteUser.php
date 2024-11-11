<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class AbsoluteUser extends Seeder
{
    public function run()
    {
        $user = User::firstOrCreate([
            'email' => 'admin@test.com' 
        ], [
            'name' => 'Administrador',
            'password' => Hash::make('qwerty123'),
            'email_verified_at' => now(),
        ]);
        $managerRole = Role::firstOrCreate(['name' => 'Gerente', 'guard_name' => 'api']);
        $permissions = Permission::all();
        $managerRole->syncPermissions($permissions);
        $user->assignRole($managerRole);
        $user->syncPermissions($permissions);
        $this->command->info('Usuario absoluto creado con éxito.');
    }
}
