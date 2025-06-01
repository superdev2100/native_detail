<?php

namespace Database\Seeders;

use App\Models\FinanceCategory;
use Illuminate\Database\Seeder;

class MonthlySavingSchemeSeeder extends Seeder
{
    public function run(): void
    {
        FinanceCategory::create([
            'name' => 'Monthly Saving Scheme',
            'type' => 'income',
            'description' => 'Monthly contributions from scheme members',
        ]);
    }
}
