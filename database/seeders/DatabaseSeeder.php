<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    protected static ?string $password;
    public function run(): void
    {
        // Check if the admin user already exists
        if (!User::where('email', 'biosetservice@gmail.com')->exists()) {
            User::factory()->withPersonalTeam()->create([
                'name' => 'Admin',
                'email' => 'biosetservice@gmail.com',
                'password' => static::$password ??= Hash::make('11111111'),
                'role' => 'admin',
                'current_team_id' => 1,
            ]);
        }

        // Check if the first test user already exists
        if (!User::where('email', 'bibovaldez2002@gmail.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'bibovaldez2002@gmail.com',
                'password' => static::$password ??= Hash::make('11111111'),
                'current_team_id' => 1,

            ]);
        }

        // Check if the second test user already exists
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => static::$password ??= Hash::make('11111111'),
                'current_team_id' => 1,

            ]);
        }
        // team_id = 1
        Team::factory()->create([
            'user_id' => 1,
            'name' => 'Admin Team',
            'personal_team' => 1,
            'address' => 'Admin Team Address',
            'description' => 'Admin Team Description',
        ]);
    }
}
