<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UpdateFamilyRelationships extends Command
{
    protected $signature = 'village:update-family-relationships';

    protected $description = 'Update existing records to establish father and mother relationships';

    public function handle()
    {
        $this->info('Updating family relationships...');

        // Get all users except admin
        $users = User::where('id', '!=', 1)->get();

        // Group users by age
        $adults = $users->where('age', '>=', 25)->where('age', '<=', 60);
        $children = $users->where('age', '<', 25);

        $this->info('Found ' . $adults->count() . ' potential parents and ' . $children->count() . ' potential children');

        // Update children with parents
        foreach ($children as $child) {
            // Find potential parents (married couples)
            $potentialFathers = User::where('id', '!=', 1)
                ->where('age', '>=', 25)
                ->where('age', '<=', 60)
                ->where('gender', 'male')
                ->where('marital_status', 'married')
                ->whereDoesntHave('children')
                ->get();

            $potentialMothers = User::where('id', '!=', 1)
                ->where('age', '>=', 25)
                ->where('age', '<=', 60)
                ->where('gender', 'female')
                ->where('marital_status', 'married')
                ->whereDoesntHave('children')
                ->get();

            if ($potentialFathers->isNotEmpty() && $potentialMothers->isNotEmpty()) {
                $father = $potentialFathers->random();
                $mother = $potentialMothers->random();

                $child->update([
                    'father_id' => $father->id,
                    'mother_id' => $mother->id,
                ]);

                $this->info("Updated {$child->name} with parents: {$father->name} and {$mother->name}");
            }
        }

        // Update some adults as parents of other adults (for demonstration)
        $youngAdults = $users->where('age', '>=', 25)->where('age', '<=', 40);
        $olderAdults = User::where('id', '!=', 1)
            ->where('age', '>', 40)
            ->where('age', '<=', 60)
            ->get();

        foreach ($youngAdults as $youngAdult) {
            $potentialFather = $olderAdults->where('gender', 'male')
                ->where('marital_status', 'married')
                ->whereDoesntHave('children')
                ->first();

            $potentialMother = $olderAdults->where('gender', 'female')
                ->where('marital_status', 'married')
                ->whereDoesntHave('children')
                ->first();

            if ($potentialFather && $potentialMother) {
                $youngAdult->update([
                    'father_id' => $potentialFather->id,
                    'mother_id' => $potentialMother->id,
                ]);

                $this->info("Updated {$youngAdult->name} with parents: {$potentialFather->name} and {$potentialMother->name}");
            }
        }

        $this->info('Family relationships updated successfully!');
    }
}
