<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\Offer;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        // Hole alle Angebote
        $offers = Offer::all();
        
        // Für jedes Angebot erstellen wir 0-3 Bewerbungen
        foreach ($offers as $offer) {
            $applicationsCount = rand(0, 3);
            
            // Hole potenzielle Bewerber (alle außer dem Angebotseigentümer)
            $potentialApplicants = User::where('id', '!=', $offer->user_id)->inRandomOrder()->take($applicationsCount)->get();
            
            foreach ($potentialApplicants as $applicant) {
                Application::factory()->create([
                    'offer_id' => $offer->id,
                    'applicant_id' => $applicant->id,
                    'offer_owner_id' => $offer->user_id,
                ]);
            }
        }
    }
}