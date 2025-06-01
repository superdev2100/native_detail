<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Education;
use App\Models\Occupation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class VillagePeopleSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing users except admin
        User::where('id', '!=', 1)->delete();

        // Generate unique identifiers
        $usedEmails = [];
        $usedAadharNumbers = [];
        $usedPhoneNumbers = [];
        $usedVoterIds = [];
        $usedRationCardNumbers = [];

        // Helper function to generate unique value
        $generateUniqueValue = function(&$usedValues, $prefix, $length) {
            do {
                $value = $prefix . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
            } while (in_array($value, $usedValues));
            $usedValues[] = $value;
            return $value;
        };

        // Create 20 village people
        for ($i = 1; $i <= 20; $i++) {
            $email = $generateUniqueValue($usedEmails, 'person', $i) . '@example.com';
            $aadharNumber = $generateUniqueValue($usedAadharNumbers, '', 12);
            $phoneNumber = $generateUniqueValue($usedPhoneNumbers, '9', 10);
            $voterId = $generateUniqueValue($usedVoterIds, 'VOT', 6);
            $rationCardNumber = $generateUniqueValue($usedRationCardNumbers, 'RAT', 6);

            $gender = rand(0, 1) ? 'male' : 'female';
            $age = rand(18, 80);
            $isStudent = $age < 25 && rand(0, 1);
            $isEmployed = $age >= 25 && rand(0, 1);

            User::create([
                'name' => fake()->name($gender),
                'email' => $email,
                'password' => Hash::make('password'),
                'gender' => $gender,
                'date_of_birth' => now()->subYears($age),
                'age' => $age,
                'door_number' => rand(1, 100) . chr(rand(65, 90)),
                'aadhar_number' => $aadharNumber,
                'phone_number' => $phoneNumber,
                'is_student' => $isStudent,
                'is_employed' => $isEmployed,
                'marital_status' => $age >= 25 ? (rand(0, 1) ? 'married' : 'single') : 'single',
                'blood_group' => fake()->randomElement(['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-']),
                'voter_id' => $voterId,
                'ration_card_number' => $rationCardNumber,
            ]);
        }

        // Assign roles to some users
        $managerRole = \App\Models\Role::where('name', 'manager')->first();
        $userRole = \App\Models\Role::where('name', 'user')->first();

        // Assign manager role to 3 random users
        User::where('id', '!=', 1)
            ->inRandomOrder()
            ->limit(3)
            ->get()
            ->each(fn($user) => $user->assignRole($managerRole));

        // Assign user role to remaining users
        User::where('id', '!=', 1)
            ->whereDoesntHave('roles')
            ->get()
            ->each(fn($user) => $user->assignRole($userRole));

        // Create a family of 5
        $father = User::create([
            'name' => 'Rajesh Kumar',
            'email' => 'rajesh.kumar@example.com',
            'password' => Hash::make('admin@123'),
            'gender' => 'male',
            'date_of_birth' => '1970-05-15',
            'age' => 53,
            'door_number' => '12A',
            'aadhar_number' => '123456789012',
            'phone_number' => '9876543210',
            'marital_status' => 'married',
            'blood_group' => 'B+',
            'voter_id' => 'VOT123456',
            'ration_card_number' => 'RAT123456',
        ]);

        $mother = User::create([
            'name' => 'Priya Kumar',
            'email' => 'priya.kumar@example.com',
            'password' => Hash::make('admin@123'),
            'gender' => 'female',
            'date_of_birth' => '1975-08-20',
            'age' => 48,
            'door_number' => '12A',
            'aadhar_number' => '234567890123',
            'phone_number' => '9876543211',
            'marital_status' => 'married',
            'blood_group' => 'A+',
            'voter_id' => 'VOT234567',
            'ration_card_number' => 'RAT234567',
        ]);

        // Create their children
        $son = User::create([
            'name' => 'Rahul Kumar',
            'email' => 'rahul.kumar@example.com',
            'password' => Hash::make('admin@123'),
            'gender' => 'male',
            'date_of_birth' => '2000-03-10',
            'age' => 23,
            'door_number' => '12A',
            'aadhar_number' => '345678901234',
            'phone_number' => '9876543212',
            'marital_status' => 'single',
            'blood_group' => 'B+',
            'father_id' => $father->id,
            'mother_id' => $mother->id,
            'is_student' => true,
        ]);

        $daughter = User::create([
            'name' => 'Priyanka Kumar',
            'email' => 'priyanka.kumar@example.com',
            'password' => Hash::make('admin@123'),
            'gender' => 'female',
            'date_of_birth' => '2002-07-25',
            'age' => 21,
            'door_number' => '12A',
            'aadhar_number' => '456789012345',
            'phone_number' => '9876543213',
            'marital_status' => 'single',
            'blood_group' => 'A+',
            'father_id' => $father->id,
            'mother_id' => $mother->id,
            'is_student' => true,
        ]);

        // Create another family
        $grandfather = User::create([
            'name' => 'Ram Singh',
            'email' => 'ram.singh@example.com',
            'password' => Hash::make('admin@123'),
            'gender' => 'male',
            'date_of_birth' => '1950-01-01',
            'age' => 73,
            'door_number' => '15B',
            'aadhar_number' => '567890123456',
            'phone_number' => '9876543214',
            'marital_status' => 'widowed',
            'blood_group' => 'O+',
            'voter_id' => 'VOT345678',
            'ration_card_number' => 'RAT345678',
        ]);

        // Create education records
        Education::create([
            'user_id' => $son->id,
            'education_level' => 'graduate',
            'college_name' => 'State University',
            'course_name' => 'Computer Science',
            'year_of_passing' => 2022,
            'percentage' => 85.5,
            'is_currently_studying' => false,
        ]);

        Education::create([
            'user_id' => $daughter->id,
            'education_level' => 'higher_secondary',
            'school_name' => 'City High School',
            'current_class' => '12th',
            'is_currently_studying' => true,
            'scholarship_status' => 'merit',
        ]);

        // Create occupation records
        Occupation::create([
            'user_id' => $father->id,
            'occupation_type' => 'government',
            'company_name' => 'State Government',
            'job_title' => 'Senior Clerk',
            'monthly_income' => 45000,
            'work_experience' => 25,
            'pension_status' => 'active',
        ]);

        Occupation::create([
            'user_id' => $mother->id,
            'occupation_type' => 'self_employed',
            'business_type' => 'Tailoring',
            'business_address' => 'Home based',
            'monthly_income' => 15000,
            'work_experience' => 15,
        ]);

        // Create some more random people
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'name' => fake()->name(),
                'email' => fake()->unique()->safeEmail(),
                'password' => Hash::make('admin@123'),
                'gender' => fake()->randomElement(['male', 'female']),
                'date_of_birth' => fake()->date(),
                'age' => fake()->numberBetween(18, 80),
                'door_number' => fake()->bothify('##?'),
                'aadhar_number' => fake()->numerify('############'),
                'phone_number' => fake()->numerify('##########'),
                'marital_status' => fake()->randomElement(['single', 'married', 'divorced', 'widowed']),
                'blood_group' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
                'is_student' => fake()->boolean(30),
                'is_employed' => fake()->boolean(70),
            ]);

            if ($user->is_student) {
                Education::create([
                    'user_id' => $user->id,
                    'education_level' => fake()->randomElement(['primary', 'secondary', 'higher_secondary', 'graduate', 'post_graduate']),
                    'school_name' => fake()->company(),
                    'college_name' => fake()->company(),
                    'course_name' => fake()->jobTitle(),
                    'year_of_passing' => fake()->year(),
                    'percentage' => fake()->randomFloat(2, 50, 100),
                    'is_currently_studying' => fake()->boolean(),
                ]);
            }

            if ($user->is_employed) {
                Occupation::create([
                    'user_id' => $user->id,
                    'occupation_type' => fake()->randomElement(['government', 'private', 'self_employed', 'unemployed']),
                    'company_name' => fake()->company(),
                    'job_title' => fake()->jobTitle(),
                    'monthly_income' => fake()->numberBetween(10000, 100000),
                    'work_experience' => fake()->numberBetween(1, 30),
                    'skills' => fake()->words(3, true),
                ]);
            }
        }
    }
}
