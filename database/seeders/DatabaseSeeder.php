<?php

namespace Database\Seeders;

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
        // User::factory(10)->withPersonalTeam()->create();
        
        
        // admin
        User::factory()->withPersonalTeam()->create([
            'name' => 'Admin',
            'email' => 'biosetservice@gmail.com',
            'password'=> static::$password ??= Hash::make('11111111'),
        ]);
        // client
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'bibovaldez2002@gmail.com',
            'password'=> static::$password ??= Hash::make('11111111'),
        ]);
    }
}
