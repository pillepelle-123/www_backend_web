<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FirstTwoUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'pillepelle',
            'email' => 'pillepelle@freenet.de',
            'password' => bcrypt('djembe32'),
            'role' => 'admin'
        ]);
        User::factory()->create([
            'name' => 'user',
            'email' => 'user@user.de',
            'password' => bcrypt('djembe32'),
            'role' => 'user'
        ]);
    }
}
