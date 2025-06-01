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
            'Cricket',
            'Football',
            'Hockey',
            'Tennis',
            'Badminton',
            'Table Tennis',
            'Chess',
            'Music',
            'Art',
            'Dance',
            'Singing',
            'Drama',
            'Debating',
            'Public Speaking',
            'Leadership',
            'Coding',
            'Team Work',
            'Cooking',
            'Gardening',
            'Painting',
            'Photography',
            'Gardening',
            'Painting',
            'Photography',
        ];

        foreach ($skills as $skill) {
            Skill::create(['name' => $skill]);
        }
    }
}
