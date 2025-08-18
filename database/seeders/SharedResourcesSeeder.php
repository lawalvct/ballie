<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SharedResourcesSeeder extends Seeder
{
    public function run(): void
    {
        // Nigerian States
        $states = [
            ['name' => 'Abia', 'code' => 'AB', 'capital' => 'Umuahia'],
            ['name' => 'Adamawa', 'code' => 'AD', 'capital' => 'Yola'],
            ['name' => 'Akwa Ibom', 'code' => 'AK', 'capital' => 'Uyo'],
            ['name' => 'Anambra', 'code' => 'AN', 'capital' => 'Awka'],
            ['name' => 'Bauchi', 'code' => 'BA', 'capital' => 'Bauchi'],
            ['name' => 'Bayelsa', 'code' => 'BY', 'capital' => 'Yenagoa'],
            ['name' => 'Benue', 'code' => 'BE', 'capital' => 'Makurdi'],
            ['name' => 'Borno', 'code' => 'BO', 'capital' => 'Maiduguri'],
            ['name' => 'Cross River', 'code' => 'CR', 'capital' => 'Calabar'],
            ['name' => 'Delta', 'code' => 'DE', 'capital' => 'Asaba'],
            ['name' => 'Ebonyi', 'code' => 'EB', 'capital' => 'Abakaliki'],
            ['name' => 'Edo', 'code' => 'ED', 'capital' => 'Benin City'],
            ['name' => 'Ekiti', 'code' => 'EK', 'capital' => 'Ado Ekiti'],
            ['name' => 'Enugu', 'code' => 'EN', 'capital' => 'Enugu'],
            ['name' => 'FCT', 'code' => 'FC', 'capital' => 'Abuja'],
            ['name' => 'Gombe', 'code' => 'GO', 'capital' => 'Gombe'],
            ['name' => 'Imo', 'code' => 'IM', 'capital' => 'Owerri'],
            ['name' => 'Jigawa', 'code' => 'JI', 'capital' => 'Dutse'],
            ['name' => 'Kaduna', 'code' => 'KD', 'capital' => 'Kaduna'],
            ['name' => 'Kano', 'code' => 'KN', 'capital' => 'Kano'],
            ['name' => 'Katsina', 'code' => 'KT', 'capital' => 'Katsina'],
            ['name' => 'Kebbi', 'code' => 'KE', 'capital' => 'Birnin Kebbi'],
            ['name' => 'Kogi', 'code' => 'KO', 'capital' => 'Lokoja'],
            ['name' => 'Kwara', 'code' => 'KW', 'capital' => 'Ilorin'],
            ['name' => 'Lagos', 'code' => 'LA', 'capital' => 'Ikeja'],
            ['name' => 'Nasarawa', 'code' => 'NA', 'capital' => 'Lafia'],
            ['name' => 'Niger', 'code' => 'NI', 'capital' => 'Minna'],
            ['name' => 'Ogun', 'code' => 'OG', 'capital' => 'Abeokuta'],
            ['name' => 'Ondo', 'code' => 'ON', 'capital' => 'Akure'],
            ['name' => 'Osun', 'code' => 'OS', 'capital' => 'Osogbo'],
            ['name' => 'Oyo', 'code' => 'OY', 'capital' => 'Ibadan'],
            ['name' => 'Plateau', 'code' => 'PL', 'capital' => 'Jos'],
            ['name' => 'Rivers', 'code' => 'RI', 'capital' => 'Port Harcourt'],
            ['name' => 'Sokoto', 'code' => 'SO', 'capital' => 'Sokoto'],
            ['name' => 'Taraba', 'code' => 'TA', 'capital' => 'Jalingo'],
            ['name' => 'Yobe', 'code' => 'YO', 'capital' => 'Damaturu'],
            ['name' => 'Zamfara', 'code' => 'ZA', 'capital' => 'Gusau'],
        ];

        DB::table('nigerian_states')->insert($states);

        // Business Types
        $businessTypes = [
            ['name' => 'Sole Proprietorship', 'description' => 'Single owner business'],
            ['name' => 'Partnership', 'description' => 'Business owned by two or more partners'],
            ['name' => 'Limited Liability Company (LLC)', 'description' => 'Private limited company'],
            ['name' => 'Public Limited Company (PLC)', 'description' => 'Public limited company'],
            ['name' => 'Non-Profit Organization', 'description' => 'Non-profit organization'],
            ['name' => 'Cooperative Society', 'description' => 'Cooperative business'],
            ['name' => 'Franchise', 'description' => 'Franchise business'],
            ['name' => 'Joint Venture', 'description' => 'Joint venture business'],
        ];

        DB::table('business_types')->insert($businessTypes);

        // Tax Rates (Nigerian VAT)
        $taxRates = [
            [
                'country' => 'NG',
                'state' => null,
                'tax_type' => 'VAT',
                'rate' => 7.50,
                'effective_from' => '2020-02-01',
                'effective_to' => null,
                'is_active' => true,
            ],
        ];

        DB::table('tax_rates')->insert($taxRates);

        // Currency Rates (Sample - you'll need to update these regularly)
        $currencyRates = [
            [
                'from_currency' => 'USD',
                'to_currency' => 'NGN',
                'rate' => 750.00,
                'date' => now()->toDateString(),
            ],
            [
                'from_currency' => 'EUR',
                'to_currency' => 'NGN',
                'rate' => 820.00,
                'date' => now()->toDateString(),
            ],
            [
                'from_currency' => 'GBP',
                'to_currency' => 'NGN',
                'rate' => 950.00,
                'date' => now()->toDateString(),
            ],
        ];

        DB::table('currency_rates')->insert($currencyRates);
    }
}
