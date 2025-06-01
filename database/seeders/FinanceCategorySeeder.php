<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FinanceCategory;

class FinanceCategorySeeder extends Seeder
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
