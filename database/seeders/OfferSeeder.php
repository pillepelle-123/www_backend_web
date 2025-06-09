<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Offer;
use App\Models\UserMatch;
use App\Models\Rating;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OfferSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $offers = Offer::factory()
            ->count(30)
            ->create([
            'user_id' => function() {
                return User::inRandomOrder()->first()->id;
            },
            'company_id' => function() {
                return Company::inRandomOrder()->first()->id;
            }
            ]);
    }
}
