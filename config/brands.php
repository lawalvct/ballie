<?php

return [
    'default' => env('BRAND', 'ballie'),

    'brands' => [
        'ballie' => [
            'key' => 'ballie',
            'name' => 'Ballie',
            'company' => 'Ballie Technology Company',
            'tagline' => 'Nigerian Business Management Software',
            'description' => 'Comprehensive business management software built specifically for Nigerian businesses.',
            'support_email' => 'support@ballie.co',
            'whatsapp' => '2348132712715',
            'show_pricing' => true,           // Show pricing page link
            'allow_registration' => true,      // Allow public registration (Get Started)
            'show_demo' => true,               // Show demo link
            'show_affiliate' => true,          // Show affiliate program
            'show_subscription' => true,       // Show subscription menu in tenant sidebar
            'domains' => [
                'ballie.co',
                'www.ballie.co',
                'ballie.test',        // Local testing
                'localhost',
                '127.0.0.1',
            ],
            'theme' => [
                'primary' => '#4F46E5',
                'secondary' => '#7C3AED',
                'gradient_from' => '#3c2c64',
                'gradient_via' => '#4a3570',
                'gradient_to' => '#614c80',
            ],
            'logo' => 'brands/ballie/logo.png',
            'logo_fallback' => 'images/ballie_logo.png',
            'favicon' => 'brands/ballie/favicon.ico',
            'landing_view' => 'welcome',
            'meta' => [
                'keywords' => 'business management software, accounting software Nigeria, inventory management, invoicing, Nigerian business, ERP software',
            ],
        ],
        'budlite' => [
            'key' => 'budlite',
            'name' => 'Budlite',
            'company' => 'Budlite Group of Companies',
            'tagline' => 'Business Management Suite',
            'description' => 'Budlite helps fast-growing teams manage accounting, sales, inventory, and operations in one place.',
            'support_email' => 'support@budlite.ng',
            'whatsapp' => null,
            'show_pricing' => false,          // No public pricing page
            'allow_registration' => false,    // Companies created via super admin
            'show_demo' => false,             // No demo link
            'show_affiliate' => false,        // No affiliate program
            'show_subscription' => false,     // No subscription menu for Budlite
            'domains' => [
                'budlite.ng',
                'www.budlite.ng',
                'budlite.test',       // Local testing
            ],
            'theme' => [
                'primary' => '#0F766E',
                'secondary' => '#10B981',
                'gradient_from' => '#0F766E',
                'gradient_via' => '#059669',
                'gradient_to' => '#10B981',
            ],
            'logo' => 'brands/budlite/logo.png',
            'logo_fallback' => 'images/ballie_logo.png',
            'favicon' => 'brands/budlite/favicon.ico',
            'landing_view' => 'brands.budlite.welcome',
            'meta' => [
                'keywords' => 'business management software, accounting software, inventory management, invoicing, ERP software, enterprise solutions',
            ],
        ],
    ],
];
