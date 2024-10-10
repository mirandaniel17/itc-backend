<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
     public function run(): void
    {
        Permission::create(['name' => 'Gestión de Cursos', 'guard_name' => 'api']);
        Permission::create(['name' => 'Gestión de Usuarios', 'guard_name' => 'api']);
        Permission::create(['name' => 'Inscripciones', 'guard_name' => 'api']);
        Permission::create(['name' => 'Consultar Estudiantes', 'guard_name' => 'api']);
        Permission::create(['name' => 'Ver Horarios', 'guard_name' => 'api']);

        $managerRole = Role::create(['name' => 'Gerente', 'guard_name' => 'api']);
        $managerRole->givePermissionTo([
            'Gestión de Cursos',
            'Gestión de Usuarios'
        ]);

        $administrativeRole = Role::create(['name' => 'Administrativo', 'guard_name' => 'api']);
        $administrativeRole->givePermissionTo([
            'Gestión de Cursos',
            'Consultar Estudiantes',
            'Ver Horarios'
        ]);

        $secretaryRole = Role::create(['name' => 'Secretaria', 'guard_name' => 'api']);
        $secretaryRole->givePermissionTo([
            'Inscripciones',
            'Consultar Estudiantes',
            'Ver Horarios'
        ]);
    }
}
