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

        $users = User::factory()
            ->count(50)
            ->create();

        $companies = Company::factory()
        ->count(40)
        ->create();

        $affiliate_links = AffiliateLink::factory()
            ->count(30)
            ->create();

        $offers = Offer::factory()
            ->count(70)
            ->create();

        $user_matches = UserMatch::factory()
            ->count(25)
            ->create();

        $applications = Application::factory()
            ->count(40)
            ->create();

        $ratings = Rating::factory()
            ->count(15)
            ->create();



        // // Erstelle Affiliate Links für jedes Unternehmen
        // $affiliateLinks = [];
        // foreach ($companies as $company) {
        //     $affiliateLinks[] = AffiliateLink::factory()->create([
        //         'company_id' => $company->id,
        //         'admin_status' => 'active'
        //     ]);
        // }

        // // Erstelle Angebote mit den neuen Status-Werten und stelle sicher, dass jedes Angebot eine gültige Company-ID hat
        // $offers = [];
        // for ($i = 0; $i < 30; $i++) {
        //     $company = $companies->random();
        //     $offers[] = Offer::factory()->create([
        //         'user_id' => $users->random()->id,
        //         'company_id' => $company->id,
        //         'status' => fake()->randomElement(['draft', 'live', 'hidden', 'matched', 'deleted']),
        //         'admin_status' => 'active'
        //     ]);
        // }

        // // Wir erzeugen für 20 zufällige Offers passende UserMatches
        // $userMatches = [];
        // $randomOffers = collect($offers)->random(20);
        // foreach ($randomOffers as $offer) {
        //     // Stelle sicher, dass die company_id gültig ist
        //     if (!$offer->company_id || !Company::find($offer->company_id)) {
        //         $company = $companies->random();
        //         $offer->update(['company_id' => $company->id]);
        //     }

        //     $affiliateLink = AffiliateLink::where('company_id', $offer->company_id)->first();

        //     $userMatches[] = UserMatch::factory()->create([
        //         'offer_id' => $offer->id,
        //         'user_referrer_id' => $offer->user_id,
        //         'user_referred_id' => $users->where('id', '!=', $offer->user_id)->random()->id,
        //         'affiliate_link_id' => $affiliateLink ? $affiliateLink->id : null,
        //         'status' => 'opened',
        //         'success_status' => 'pending'
        //     ]);
        // }

        // // Erstelle Bewerbungen für einige Angebote
        // $applications = [];
        // $applicationOffers = collect($offers)->random(15);
        // foreach ($applicationOffers as $offer) {
        //     // Stelle sicher, dass die company_id gültig ist
        //     if (!$offer->company_id || !Company::find($offer->company_id)) {
        //         $company = $companies->random();
        //         $offer->update(['company_id' => $company->id]);
        //     }

        //     // Wähle 1-3 zufällige Bewerber aus
        //     $applicantsCount = rand(1, 3);
        //     $applicants = $users->where('id', '!=', $offer->user_id)->random($applicantsCount);

        //     foreach ($applicants as $applicant) {
        //         $applications[] = Application::factory()->create([
        //             'offer_id' => $offer->id,
        //             'applicant_id' => $applicant->id,
        //             'offer_owner_id' => $offer->user_id,
        //         ]);
        //     }
        // }



        $this->call([
            FirstUsersSeeder::class
        ]);
    }
}
