<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AssignAdminRole extends Command
{
    protected $signature = 'user:assign-admin {email}';

    protected $description = 'Assign admin role to a user by email';

    public function handle()
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return 1;
        }

        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->error("Admin role not found. Please run the RolePermissionSeeder first.");
            return 1;
        }

        $user->assignRole($adminRole);

        $this->info("Successfully assigned admin role to user: {$user->name} ({$user->email})");
        return 0;
    }
}
