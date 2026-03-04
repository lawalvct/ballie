<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_types', function (Blueprint $table) {
            if (!Schema::hasColumn('business_types', 'business_category')) {
                $table->string('business_category', 20)
                    ->default('hybrid')
                    ->after('category')
                    ->index()
                    ->comment('trading, manufacturing, service, hybrid');
            }
        });

        // Assign business_category based on existing category values
        $categoryMapping = [
            'Retail, Commerce & Sales'                 => 'trading',
            'Professional & Service-Based'             => 'service',
            'Food & Hospitality'                       => 'hybrid',
            'Industrial, Manufacturing & Construction' => 'manufacturing',
            'Agriculture, Agro & Natural Resources'    => 'manufacturing',
            'Transport, Logistics & Mobility'          => 'service',
            'Finance, Technology & Innovation'         => 'service',
            'Nonprofit, Government & Social Services'  => 'service',
            'Entertainment, Media & Arts'              => 'service',
            'Personal & Miscellaneous Services'        => 'service',
            'Other / Mixed Business'                   => 'hybrid',
        ];

        foreach ($categoryMapping as $existingCategory => $businessCategory) {
            \DB::table('business_types')
                ->where('category', $existingCategory)
                ->update(['business_category' => $businessCategory]);
        }

        // Individual overrides for types that don't match their category default
        $overrides = [
            'Marketplace Platform'                     => 'hybrid',
            'Vehicle Parts Sales'                      => 'trading',
            'Auto Repair Workshop'                     => 'hybrid',
            'Interior Design & Renovation'             => 'hybrid',
            'Beauty Salon / Barbershop'                => 'hybrid',
            'Tailoring & Fashion Design'               => 'hybrid',
            'Home Maintenance / Repair'                => 'hybrid',
            'Rental Services'                          => 'hybrid',
            'Agricultural Equipment & Supplies'        => 'hybrid',
            'Printing & Stationery'                    => 'hybrid',
            'Cryptocurrency / Blockchain Business'     => 'hybrid',
            'Import & Export'                          => 'hybrid',
            'Forestry & Logging'                       => 'hybrid',
            'Maritime / Shipping'                      => 'hybrid',
            'Aviation & Airline Services'              => 'hybrid',
            'Performing Arts / Theatre'                => 'hybrid',
            'Gaming & eSports'                         => 'hybrid',
        ];

        foreach ($overrides as $typeName => $businessCategory) {
            \DB::table('business_types')
                ->where('name', $typeName)
                ->update(['business_category' => $businessCategory]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_types', function (Blueprint $table) {
            if (Schema::hasColumn('business_types', 'business_category')) {
                $table->dropColumn('business_category');
            }
        });
    }
};
