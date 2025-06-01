<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view_people' => 'View people list',
            'create_people' => 'Create new people',
            'edit_people' => 'Edit people',
            'delete_people' => 'Delete people',
            'manage_roles' => 'Manage roles and permissions',
        ];

        foreach ($permissions as $name => $description) {
            Permission::updateOrCreate(
                ['name' => $name],
                ['description' => $description]
            );
        }

        // Create roles
        $roles = [
            'admin' => [
                'description' => 'Full access to all features',
                'permissions' => ['view_people', 'create_people', 'edit_people', 'delete_people', 'manage_roles'],
            ],
            'manager' => [
                'description' => 'Can manage people but not roles',
                'permissions' => ['view_people', 'create_people', 'edit_people', 'delete_people'],
            ],
            'user' => [
                'description' => 'Can only view people',
                'permissions' => ['view_people'],
            ],
        ];

        foreach ($roles as $name => $data) {
            $role = Role::updateOrCreate(
                ['name' => $name],
                ['description' => $data['description']]
            );

            foreach ($data['permissions'] as $permission) {
                $role->permissions()->syncWithoutDetaching(
                    Permission::where('name', $permission)->first()
                );
            }
        }

        // Assign admin role to the admin user
        $adminUser = User::where('email', 'admin@nativedetail.com')->first();
        $adminRole = Role::where('name', 'admin')->first();
        $adminUser->assignRole($adminRole);
    }
}
