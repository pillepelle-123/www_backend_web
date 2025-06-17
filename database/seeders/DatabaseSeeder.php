<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Offer;
use App\Models\UserMatch;
use App\Models\Rating;
use App\Models\AffiliateLink;
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
        ->count(50)
        ->create();

        $users = User::factory()
            ->count(60)
            ->create();

        // Erstelle Affiliate Links für jedes Unternehmen
        $affiliateLinks = [];
        foreach ($companies as $company) {
            $affiliateLinks[] = AffiliateLink::factory()->create([
                'company_id' => $company->id,
                'admin_status' => 'active'
            ]);
        }

        $offers = Offer::factory()
            ->count(60)
            ->create([
                'user_id' => $users->random()->id,
                'company_id' => $companies->random()->id,
                'status' => fake()->randomElement(['draft', 'live', 'hidden', 'matched', 'deleted']),
                'admin_status' => 'active'
            ]);

        // Erstelle Angebote mit den neuen Status-Werten
        // $offers = [];
        // for ($i = 0; $i < 30; $i++) {
        //     $offers[] = Offer::factory()->create([
        //         'user_id' => $users->random()->id,
        //         'company_id' => $companies->random()->id,
        //         'status' => fake()->randomElement(['draft', 'live', 'hidden', 'matched', 'deleted']),
        //         'admin_status' => 'active'
        //     ]);
        // }

        // Wir erzeugen für 20 zufällige Offers passende UserMatches
        $userMatches = [];
        $randomOffers = collect($offers)->random(25);
        foreach ($randomOffers as $offer) {
            $affiliateLink = AffiliateLink::where('company_id', $offer->company_id)->first();

            $userMatches[] = UserMatch::factory()->create([
                'offer_id' => $offer->id,
                'user_referrer_id' => $offer->user_id,
                'user_referred_id' => $users->where('id', '!=', $offer->user_id)->random()->id,
                'affiliate_link_id' => $affiliateLink ? $affiliateLink->id : null,
                'status' => 'opened',
                'success_status' => 'pending'
            ]);
        }

        $ratings = Rating::factory()
            ->count(30)
            ->create([
                'user_match_id' => function() use ($userMatches) {
                    return collect($userMatches)->random()->id;
                },
            ]);

        $this->call([
            FirstUsersSeeder::class
        ]);
    }
}
