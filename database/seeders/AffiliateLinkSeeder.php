<?php

namespace Database\Seeders;

use App\Models\AffiliateLink;
use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AffiliateLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Erstelle für jede Company mindestens einen AffiliateLink
        $companies = Company::all();
        
        foreach ($companies as $company) {
            // Erstelle 1-3 AffiliateLinks pro Company
            $count = rand(1, 3);
            
            for ($i = 0; $i < $count; $i++) {
                AffiliateLink::factory()->create([
                    'company_id' => $company->id,
                    'url' => $company->referral_program_url ?? $this->faker->url(),
                    'admin_status' => $company->is_active ? 'active' : 'inactive',
                ]);
            }
        }
    }
}