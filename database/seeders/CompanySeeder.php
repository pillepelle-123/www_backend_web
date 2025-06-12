<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'N26',
                'domain' => 'n26.com',
                'referral_program_url' => 'n26.com/de-de/referral',
                'description' => 'Digitale Bank mit Kunden werben Kunden Programm',
                'logo_path' => 'company_logos/n26.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Trade Republic',
                'domain' => 'traderepublic.com',
                'referral_program_url' => 'traderepublic.com/de/referral',
                'description' => 'Neobroker mit Kunden werben Kunden Programm',
                'logo_path' => 'company_logos/traderepublic.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Scalable Capital',
                'domain' => 'scalable.capital',
                'referral_program_url' => 'scalable.capital/de/referral',
                'description' => 'Robo-Advisor und Neobroker mit Kunden werben Kunden Programm',
                'logo_path' => 'company_logos/scalable.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Vivid Money',
                'domain' => 'vivid.money',
                'referral_program_url' => 'vivid.money/de/referral',
                'description' => 'Digitale Bank mit Kunden werben Kunden Programm',
                'logo_path' => 'company_logos/vivid.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Bitpanda',
                'domain' => 'bitpanda.com',
                'referral_program_url' => 'bitpanda.com/de/referral',
                'description' => 'Kryptobörse mit Kunden werben Kunden Programm',
                'logo_path' => 'company_logos/bitpanda.svg',
                'is_active' => true,
            ],
            [
                'name' => 'Apple',
                'domain' => 'apple.com',
                'referral_program_url' => 'apple.com/referral',
                'description' => 'US-amerikanisches Technologieunternehmen',
                'logo_path' => 'company_logos/apple.svg',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $company) {
            Company::create($company);
        }
    }
}
