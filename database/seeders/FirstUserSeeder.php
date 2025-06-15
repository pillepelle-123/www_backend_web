<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FirstUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        // Admin User (Rolle: Admin)
        User::factory()->create([
            'name' => 'pillepelle',
            'email' => 'pillepelle@freenet.de',
            'email_verified_at' => now(),
            'password' => bcrypt('djembe32'),
            'role' => 'admin',
            'average_rating' => 0.00,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Admin User (Rolle: Admin)
        User::factory()->create([
            'name' => 'pillepelle',
            'email' => 'pillepelle@freenet.de',
            'email_verified_at' => now(),
            'password' => bcrypt('djembe32'),
            'role' => 'admin',
            'average_rating' => 0.00,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Test User (Rolle: User)
        User::factory()->create([
            'name' => 'user',
            'email' => 'user@user.de',
            'password' => bcrypt('djembe32'),
            'role' => 'user'
        ]);
    }
}
