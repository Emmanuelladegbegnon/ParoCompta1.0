<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Entry;
use App\Models\User;

class UpdateEntriesWithUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assigner toutes les entries existantes au premier utilisateur de chaque paroisse
        $entries = Entry::whereNull('user_id')->get();

        foreach ($entries as $entry) {
            // Trouver le premier utilisateur de cette paroisse
            $user = User::where('parish_id', $entry->parish_id)->first();

            if ($user) {
                $entry->update(['user_id' => $user->id]);
                $this->command->info("Entry {$entry->id} assignée à l'utilisateur {$user->name}");
            } else {
                $this->command->warn("Aucun utilisateur trouvé pour la paroisse {$entry->parish_id}");
            }
        }

        $this->command->info('Mise à jour des entries terminée.');
    }
}
