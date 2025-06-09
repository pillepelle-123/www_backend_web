<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Offer;
use App\Models\UserMatch;
use App\Models\Rating;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        $companies = Company::factory()
        ->count(40)
        ->create();

        $users = User::factory()
            ->count(50)
            ->create();

        $offers = Offer::factory()
            ->count(30)
            ->create([
            'user_id' => function() use ($users) {
                return $users->random()->id;
            },
            'company_id' => function() use ($companies) {
                return $companies->random()->id;
            }
            ]);

        // Wir erzeugen für 20 zufällige Offers passende UserMatches
        $userMatches = Offer::inRandomOrder()
            ->take(20)
            ->get()
            ->map(function ($offer) use ($users) {
                return UserMatch::factory()->create([
                    'offer_id' => $offer->id,
                    'user_referrer_id' => $offer->user_id,
                    'user_referred_id' => $users
                        ->where('id', '!=', $offer->user_id)
                        ->random()
                        ->id
                ]);
            });

        // $userMatches = UserMatch::factory()
        //     ->count(20)
        //     ->create([
        //         'offer_id' => function() {
        //             return Offer::all()->random()->id;
        //         },
        //         'user_referrer_id' => function() {
        //             return User::all()->random()->id;
        //         },
        //         'user_referred_id' => function(array $attributes) {
        //             // Hole alle User-IDs außer der bereits gewählten referrer_id
        //             return User::where('id', '!=', $attributes['user_referrer_id'])
        //                 ->inRandomOrder()
        //                 ->first()
        //                 ->id;
        //         }
        //     ]);

        $ratings = Rating::factory()
            ->count(15)
            ->create([
                'user_match_id' => function() use ($userMatches) {
                    return UserMatch::all()->random()->id;
                },
            ]);
    }
}
