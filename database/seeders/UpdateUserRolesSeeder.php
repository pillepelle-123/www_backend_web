<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UpdateUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // ADMINs setzen (für bestimmte Namen)
        User::whereIn('name', ['admin', 'pillepelle'])
            ->update(['role' => 'admin']);

        // Alle anderen auf 'user' setzen
        User::whereNotIn('name', ['admin', 'pillepelle'])
            ->whereNull('role') // Optional: Nur wenn role leer ist
            ->update(['role' => 'user']);
    }
}
