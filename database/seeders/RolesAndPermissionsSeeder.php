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
       $permissions = [
            'FullAccess',
            'ManageCourses',
            'ManageUsers',
            'Enrollments',
            'StudentInquiries',
            'ViewSchedules',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $managerRole = Role::create(['name' => 'Gerente']);
        $administrativeRole = Role::create(['name' => 'Administrativo']);
        $secretaryRole = Role::create(['name' => 'Secretaria']);

        $managerRole->syncPermissions(['FullAccess', 'ManageCourses', 'ManageUsers', 'Enrollments', 'StudentInquiries']);
        $administrativeRole->syncPermissions(['ManageCourses', 'StudentInquiries', 'ViewSchedules']);
        $secretaryRole->syncPermissions(['Enrollments', 'StudentInquiries', 'ViewSchedules']);
    }
}
