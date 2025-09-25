<?php

namespace Database\Seeders;

use App\Models\Parish;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $parish = Parish::first();
        if (!$parish) {
            $parish = Parish::factory()->create(['name' => 'Paroisse Démo']);
        }

        User::updateOrCreate(
            ['email' => 'admin@paro.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'parish_id' => $parish->id,
            ]
        );

        User::updateOrCreate(
            ['email' => 'user@paro.com'],
            [
                'name' => 'Utilisateur',
                'password' => Hash::make('password'),
                'parish_id' => $parish->id,
            ]
        );
    }
}




