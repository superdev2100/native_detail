<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $skills = [
            'Reading',
            'Writing',
            'Mathematics',
            'Science',
            'Sports',
            'Music',
            'Art',
            'Dance',
            'Coding',
            'Public Speaking',
            'Leadership',
            'Team Work',
        ];

        foreach ($skills as $skill) {
            Skill::create(['name' => $skill]);
        }
    }
}
