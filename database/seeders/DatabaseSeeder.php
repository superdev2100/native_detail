<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create or update admin user
        User::updateOrCreate(
            ['email' => 'admin@mcc.vktools.in'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('vj@12345'),
                'email_verified_at' => now(),
            ]
        );

        // Run seeders in correct order
        $this->call([
            RolePermissionSeeder::class,
            MonthlySavingSchemeSeeder::class,
            // VillagePeopleSeeder::class,
            SkillSeeder::class,
        ]);
    }
}
