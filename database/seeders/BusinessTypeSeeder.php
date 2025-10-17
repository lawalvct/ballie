<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $businessTypes = [
            // 🛍️ Retail, Commerce & Sales
            [
                'category' => 'Retail, Commerce & Sales',
                'icon' => '🛍️',
                'types' => [
                    ['name' => 'Retail Store', 'description' => 'Physical retail shop selling various products'],
                    ['name' => 'E-commerce / Online Store', 'description' => 'Online business selling products through website or app'],
                    ['name' => 'Wholesale & Distribution', 'description' => 'Bulk sales and product distribution'],
                    ['name' => 'Supermarket / Grocery Store', 'description' => 'Food and household items retail'],
                    ['name' => 'Boutique / Fashion Store', 'description' => 'Clothing, accessories and fashion items'],
                    ['name' => 'Electronics & Appliances Store', 'description' => 'Electronic devices and home appliances'],
                    ['name' => 'Furniture & Home Decor', 'description' => 'Furniture, fixtures and home decoration items'],
                    ['name' => 'Automobile Sales', 'description' => 'Vehicle sales and dealership'],
                    ['name' => 'Marketplace Platform', 'description' => 'Online marketplace connecting buyers and sellers'],
                ]
            ],

            // 💼 Professional & Service-Based
            [
                'category' => 'Professional & Service-Based',
                'icon' => '💼',
                'types' => [
                    ['name' => 'Consulting & Advisory Services', 'description' => 'Professional consulting and business advisory'],
                    ['name' => 'Marketing & Advertising Agency', 'description' => 'Marketing, advertising and brand promotion'],
                    ['name' => 'Legal Services', 'description' => 'Law firm and legal consulting'],
                    ['name' => 'Accounting & Financial Consulting', 'description' => 'Accounting, bookkeeping and financial advisory'],
                    ['name' => 'Human Resource / Recruitment Agency', 'description' => 'HR services and talent recruitment'],
                    ['name' => 'IT Services & Support', 'description' => 'Information technology services and support'],
                    ['name' => 'Graphic Design / Branding Agency', 'description' => 'Design, branding and creative services'],
                    ['name' => 'Photography & Videography', 'description' => 'Professional photography and video production'],
                    ['name' => 'Printing & Publishing', 'description' => 'Printing services and publishing'],
                    ['name' => 'Education & Training', 'description' => 'Tutoring, coaching and e-learning services'],
                    ['name' => 'Healthcare & Wellness Services', 'description' => 'Clinic, spa, gym, pharmacy and wellness'],
                    ['name' => 'Real Estate Agency', 'description' => 'Property sales, rental and real estate management'],
                    ['name' => 'Architecture / Engineering Services', 'description' => 'Architectural and engineering consulting'],
                ]
            ],

            // 🍴 Food & Hospitality
            [
                'category' => 'Food & Hospitality',
                'icon' => '🍴',
                'types' => [
                    ['name' => 'Restaurant / Eatery', 'description' => 'Food service and dining establishment'],
                    ['name' => 'Catering Services', 'description' => 'Event catering and food delivery'],
                    ['name' => 'Bakery / Confectionery', 'description' => 'Bread, pastries and sweet treats'],
                    ['name' => 'Bar / Lounge / Nightclub', 'description' => 'Beverages and entertainment venue'],
                    ['name' => 'Food Processing & Packaging', 'description' => 'Food production and packaging'],
                    ['name' => 'Hotel / Guesthouse / Airbnb', 'description' => 'Accommodation and lodging services'],
                    ['name' => 'Event Planning & Decoration', 'description' => 'Event planning, decoration and management'],
                    ['name' => 'Travel & Tourism Agency', 'description' => 'Travel booking and tourism services'],
                ]
            ],

            // 🏭 Industrial, Manufacturing & Construction
            [
                'category' => 'Industrial, Manufacturing & Construction',
                'icon' => '🏭',
                'types' => [
                    ['name' => 'Manufacturing / Production', 'description' => 'General manufacturing and production'],
                    ['name' => 'Fabrication / Assembly', 'description' => 'Metal fabrication and product assembly'],
                    ['name' => 'Construction Company', 'description' => 'Building construction and development'],
                    ['name' => 'Civil Engineering / Building Contractor', 'description' => 'Civil engineering and contracting'],
                    ['name' => 'Interior Design & Renovation', 'description' => 'Interior design and building renovation'],
                    ['name' => 'Mining & Quarrying', 'description' => 'Mining operations and quarrying'],
                    ['name' => 'Chemical & Paint Production', 'description' => 'Chemical and paint manufacturing'],
                    ['name' => 'Plastic, Paper, or Rubber Production', 'description' => 'Plastic, paper and rubber products'],
                    ['name' => 'Packaging & Labelling', 'description' => 'Product packaging and labelling services'],
                ]
            ],

            // 🌾 Agriculture, Agro & Natural Resources
            [
                'category' => 'Agriculture, Agro & Natural Resources',
                'icon' => '🌾',
                'types' => [
                    ['name' => 'Crop Farming', 'description' => 'Maize, rice, cassava and other crop farming'],
                    ['name' => 'Livestock Farming', 'description' => 'Poultry, cattle, fishery and livestock'],
                    ['name' => 'Agro-Processing & Packaging', 'description' => 'Agricultural product processing'],
                    ['name' => 'Agricultural Equipment & Supplies', 'description' => 'Farm equipment and supplies'],
                    ['name' => 'Forestry & Logging', 'description' => 'Forestry operations and timber'],
                    ['name' => 'Oil & Gas', 'description' => 'Upstream and downstream oil and gas'],
                    ['name' => 'Renewable Energy', 'description' => 'Solar, wind and biomass energy'],
                    ['name' => 'Water & Irrigation Services', 'description' => 'Water supply and irrigation systems'],
                ]
            ],

            // 🚗 Transport, Logistics & Mobility
            [
                'category' => 'Transport, Logistics & Mobility',
                'icon' => '🚗',
                'types' => [
                    ['name' => 'Logistics & Delivery Services', 'description' => 'Courier and delivery services'],
                    ['name' => 'Haulage & Trucking', 'description' => 'Heavy goods transportation'],
                    ['name' => 'Ride-hailing / Taxi Service', 'description' => 'Passenger transport services'],
                    ['name' => 'Car Rentals & Leasing', 'description' => 'Vehicle rental and leasing'],
                    ['name' => 'Freight Forwarding & Clearing', 'description' => 'Import/export freight services'],
                    ['name' => 'Auto Repair Workshop', 'description' => 'Vehicle maintenance and repair'],
                    ['name' => 'Vehicle Parts Sales', 'description' => 'Automobile spare parts'],
                    ['name' => 'Maritime / Shipping', 'description' => 'Sea freight and shipping'],
                    ['name' => 'Aviation & Airline Services', 'description' => 'Air transport and aviation'],
                ]
            ],

            // 💰 Finance, Technology & Innovation
            [
                'category' => 'Finance, Technology & Innovation',
                'icon' => '💰',
                'types' => [
                    ['name' => 'Banking / Microfinance', 'description' => 'Banking and microfinance services'],
                    ['name' => 'Cooperative / Credit Union', 'description' => 'Cooperative financial services'],
                    ['name' => 'Investment / Asset Management', 'description' => 'Investment and wealth management'],
                    ['name' => 'Insurance Services', 'description' => 'Insurance and risk management'],
                    ['name' => 'Financial Technology (Fintech)', 'description' => 'Digital financial services'],
                    ['name' => 'Cryptocurrency / Blockchain Business', 'description' => 'Crypto and blockchain services'],
                    ['name' => 'Software Development', 'description' => 'Custom software development'],
                    ['name' => 'Web & App Development', 'description' => 'Website and mobile app development'],
                    ['name' => 'SaaS / Cloud Services', 'description' => 'Software as a Service platforms'],
                    ['name' => 'Data Analytics / AI Solutions', 'description' => 'Data analysis and AI services'],
                    ['name' => 'Cybersecurity Services', 'description' => 'Information security services'],
                    ['name' => 'Telecommunications / ISP', 'description' => 'Telecom and internet services'],
                ]
            ],

            // 🏘️ Nonprofit, Government & Social Services
            [
                'category' => 'Nonprofit, Government & Social Services',
                'icon' => '🏘️',
                'types' => [
                    ['name' => 'NGO / Nonprofit Organization', 'description' => 'Non-governmental organization'],
                    ['name' => 'Charity Foundation', 'description' => 'Charitable foundation'],
                    ['name' => 'Religious / Faith-based Organization', 'description' => 'Religious organization'],
                    ['name' => 'Community Development Project', 'description' => 'Community development initiatives'],
                    ['name' => 'Cooperative Society', 'description' => 'Member-owned cooperative'],
                    ['name' => 'Educational Institution', 'description' => 'School, college or university'],
                    ['name' => 'Government Department / Agency', 'description' => 'Government organization'],
                ]
            ],

            // 🎭 Entertainment, Media & Arts
            [
                'category' => 'Entertainment, Media & Arts',
                'icon' => '🎭',
                'types' => [
                    ['name' => 'Music Production / Record Label', 'description' => 'Music production and recording'],
                    ['name' => 'Film / Video Production', 'description' => 'Film and video content creation'],
                    ['name' => 'Event Promotion & Management', 'description' => 'Event promotion and management'],
                    ['name' => 'Performing Arts / Theatre', 'description' => 'Theatre and performing arts'],
                    ['name' => 'Gaming & eSports', 'description' => 'Gaming and esports business'],
                    ['name' => 'Content Creation / Influencer', 'description' => 'Digital content creation'],
                    ['name' => 'Media / Broadcasting', 'description' => 'TV, radio and podcast broadcasting'],
                    ['name' => 'Advertising / PR Agency', 'description' => 'Advertising and public relations'],
                ]
            ],

            // 🧾 Personal & Miscellaneous Services
            [
                'category' => 'Personal & Miscellaneous Services',
                'icon' => '🧾',
                'types' => [
                    ['name' => 'Laundry & Cleaning Services', 'description' => 'Laundry and cleaning services'],
                    ['name' => 'Beauty Salon / Barbershop', 'description' => 'Hair and beauty services'],
                    ['name' => 'Tailoring & Fashion Design', 'description' => 'Tailoring and fashion design'],
                    ['name' => 'Home Maintenance / Repair', 'description' => 'Home repair and maintenance'],
                    ['name' => 'Security Services', 'description' => 'Security and guard services'],
                    ['name' => 'Printing & Stationery', 'description' => 'Printing and stationery supplies'],
                    ['name' => 'Rental Services', 'description' => 'Equipment, halls and item rentals'],
                    ['name' => 'Pet Care & Grooming', 'description' => 'Pet care and grooming services'],
                    ['name' => 'Funeral Services', 'description' => 'Funeral and burial services'],
                ]
            ],

            // ⚙️ Other / Mixed Business
            [
                'category' => 'Other / Mixed Business',
                'icon' => '⚙️',
                'types' => [
                    ['name' => 'Mixed or Multi-sector Business', 'description' => 'Business spanning multiple sectors'],
                    ['name' => 'Import & Export', 'description' => 'International trade and import/export'],
                    ['name' => 'Start-up / Holding Company', 'description' => 'Start-up or holding company'],
                    ['name' => 'Cooperative Enterprise', 'description' => 'Cooperative business enterprise'],
                    ['name' => 'Other', 'description' => 'Other business types not listed'],
                ]
            ],
        ];

        $sortOrder = 1;
        foreach ($businessTypes as $categoryData) {
            foreach ($categoryData['types'] as $type) {
                BusinessType::create([
                    'name' => $type['name'],
                    'slug' => Str::slug($type['name']),
                    'category' => $categoryData['category'],
                    'icon' => $categoryData['icon'],
                    'description' => $type['description'],
                    'sort_order' => $sortOrder++,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('Business types seeded successfully!');
        $this->command->info('Total business types created: ' . BusinessType::count());
    }
}
