<?php

namespace App\Console\Commands;

use App\Models\FinanceCategory;
use App\Models\FinanceTransaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SeedFinanceData extends Command
{
    protected $signature = 'finance:seed-sample-data';

    protected $description = 'Seed sample data for finance categories and transactions';

    public function handle()
    {
        $this->info('Seeding finance categories...');

        // Income Categories
        $incomeCategories = [
            [
                'name' => 'Government Grants',
                'type' => 'income',
                'description' => 'Funds received from government schemes and programs',
            ],
            [
                'name' => 'Donations',
                'type' => 'income',
                'description' => 'Contributions from individuals and organizations',
            ],
            [
                'name' => 'Membership Fees',
                'type' => 'income',
                'description' => 'Annual membership fees from village members',
            ],
            [
                'name' => 'Event Revenue',
                'type' => 'income',
                'description' => 'Income from village events and functions',
            ],
        ];

        // Expense Categories
        $expenseCategories = [
            [
                'name' => 'Infrastructure',
                'type' => 'expense',
                'description' => 'Expenses for village infrastructure development',
            ],
            [
                'name' => 'Education',
                'type' => 'expense',
                'description' => 'Educational programs and scholarships',
            ],
            [
                'name' => 'Healthcare',
                'type' => 'expense',
                'description' => 'Medical camps and healthcare initiatives',
            ],
            [
                'name' => 'Events',
                'type' => 'expense',
                'description' => 'Village events and celebrations',
            ],
            [
                'name' => 'Maintenance',
                'type' => 'expense',
                'description' => 'Regular maintenance of village facilities',
            ],
        ];

        // Create categories and collect them
        $categories = new Collection();
        foreach (array_merge($incomeCategories, $expenseCategories) as $category) {
            $categories->push(FinanceCategory::create($category));
        }

        $this->info('Created ' . $categories->count() . ' finance categories');

        $this->info('Seeding sample transactions...');

        // Get a user to associate with transactions
        $user = User::first();
        if (!$user) {
            $this->error('No users found. Please create a user first.');
            return 1;
        }

        // Create sample transactions
        $transactions = [];
        $paymentMethods = ['cash', 'bank_transfer', 'cheque', 'upi'];

        // Income transactions
        foreach ($categories->where('type', 'income') as $category) {
            for ($i = 0; $i < 3; $i++) {
                $transactions[] = FinanceTransaction::create([
                    'date' => Carbon::now()->subDays(rand(1, 30)),
                    'amount' => rand(10000, 50000),
                    'type' => 'income',
                    'category_id' => $category->id,
                    'description' => "Sample income transaction for {$category->name}",
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'reference_number' => 'REF-' . strtoupper(uniqid()),
                    'user_id' => $user->id,
                ]);
            }
        }

        // Expense transactions
        foreach ($categories->where('type', 'expense') as $category) {
            for ($i = 0; $i < 3; $i++) {
                $transactions[] = FinanceTransaction::create([
                    'date' => Carbon::now()->subDays(rand(1, 30)),
                    'amount' => rand(5000, 25000),
                    'type' => 'expense',
                    'category_id' => $category->id,
                    'description' => "Sample expense transaction for {$category->name}",
                    'payment_method' => $paymentMethods[array_rand($paymentMethods)],
                    'reference_number' => 'REF-' . strtoupper(uniqid()),
                    'user_id' => $user->id,
                ]);
            }
        }

        $this->info('Created ' . count($transactions) . ' sample transactions');
        $this->info('Sample data seeding completed successfully!');

        return 0;
    }
}
