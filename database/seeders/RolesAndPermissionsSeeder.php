<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $managerRole = Role::create(['name' => 'Gerente']);
        $administrativeRole = Role::create(['name' => 'Administrativo']);
        $secretaryRole = Role::create(['name' => 'Secretaria']);
    }
}
