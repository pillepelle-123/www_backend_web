<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Company;
use App\Models\Offer;
use App\Models\UserMatch;
use App\Models\Rating;
use App\Models\AffiliateLink;
use App\Models\Application;
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

        // Erstelle Affiliate Links für jedes Unternehmen
        $affiliateLinks = [];
        foreach ($companies as $company) {
            $affiliateLinks[] = AffiliateLink::factory()->create([
                'company_id' => $company->id,
                'admin_status' => 'active'
            ]);
        }

        // Erstelle Angebote mit den neuen Status-Werten
        $offers = [];
        for ($i = 0; $i < 30; $i++) {
            $offers[] = Offer::factory()->create([
                'user_id' => $users->random()->id,
                'company_id' => $companies->random()->id,
                'status' => fake()->randomElement(['draft', 'live', 'hidden', 'matched', 'deleted']),
                'admin_status' => 'active'
            ]);
        }

        // Wir erzeugen für 20 zufällige Offers passende UserMatches
        $userMatches = [];
        $randomOffers = collect($offers)->random(20);
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

        // Erstelle Bewerbungen für einige Angebote
        $applications = [];
        $applicationOffers = collect($offers)->random(15);
        foreach ($applicationOffers as $offer) {
            // Wähle 1-3 zufällige Bewerber aus
            $applicantsCount = rand(1, 3);
            $applicants = $users->where('id', '!=', $offer->user_id)->random($applicantsCount);

            foreach ($applicants as $applicant) {
                $applications[] = Application::factory()->create([
                    'offer_id' => $offer->id,
                    'applicant_id' => $applicant->id,
                    'offer_owner_id' => $offer->user_id,
                ]);
            }
        }

        $ratings = Rating::factory()
            ->count(15)
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
