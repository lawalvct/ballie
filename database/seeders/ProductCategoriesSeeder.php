<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductCategory;
use App\Models\Tenant;

class ProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all tenants
        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $this->seedCategoriesForTenant($tenant->id);
        }
    }

    /**
     * Seed product categories for a specific tenant.
     */
    private function seedCategoriesForTenant(int $tenantId): void
    {
        // Electronics Category
        $electronics = ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Electronics',
            'slug' => 'electronics',
            'description' => 'Electronic devices and accessories',
            'sort_order' => 1,
            'meta_title' => 'Electronics - Electronic Devices and Accessories',
            'meta_description' => 'Browse our wide range of electronic devices and accessories',
        ]);

        // Electronics Subcategories
        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Computers & Laptops',
            'slug' => 'computers-laptops',
            'description' => 'Desktop computers, laptops, and accessories',
            'parent_id' => $electronics->id,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Mobile Phones',
            'slug' => 'mobile-phones',
            'description' => 'Smartphones and mobile accessories',
            'parent_id' => $electronics->id,
            'sort_order' => 2,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Audio & Video',
            'slug' => 'audio-video',
            'description' => 'Speakers, headphones, cameras, and video equipment',
            'parent_id' => $electronics->id,
            'sort_order' => 3,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Home Appliances',
            'slug' => 'home-appliances',
            'description' => 'Kitchen appliances, air conditioners, and home electronics',
            'parent_id' => $electronics->id,
            'sort_order' => 4,
        ]);

        // Clothing & Fashion Category
        $clothing = ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Clothing & Fashion',
            'slug' => 'clothing-fashion',
            'description' => 'Clothing, shoes, and fashion accessories',
            'sort_order' => 2,
            'meta_title' => 'Clothing & Fashion - Apparel and Accessories',
            'meta_description' => 'Discover the latest fashion trends and clothing styles',
        ]);

        // Clothing Subcategories
        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Men\'s Clothing',
            'slug' => 'mens-clothing',
            'description' => 'Men\'s shirts, pants, suits, and casual wear',
            'parent_id' => $clothing->id,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Women\'s Clothing',
            'slug' => 'womens-clothing',
            'description' => 'Women\'s dresses, tops, pants, and formal wear',
            'parent_id' => $clothing->id,
            'sort_order' => 2,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Children\'s Clothing',
            'slug' => 'childrens-clothing',
            'description' => 'Kids and baby clothing',
            'parent_id' => $clothing->id,
            'sort_order' => 3,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Shoes & Footwear',
            'slug' => 'shoes-footwear',
            'description' => 'Shoes, sandals, boots, and footwear accessories',
            'parent_id' => $clothing->id,
            'sort_order' => 4,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Accessories',
            'slug' => 'fashion-accessories',
            'description' => 'Bags, jewelry, watches, and fashion accessories',
            'parent_id' => $clothing->id,
            'sort_order' => 5,
        ]);

        // Home & Garden Category
        $homeGarden = ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Home & Garden',
            'slug' => 'home-garden',
            'description' => 'Home improvement, furniture, and garden supplies',
            'sort_order' => 3,
            'meta_title' => 'Home & Garden - Furniture and Home Improvement',
            'meta_description' => 'Everything you need for your home and garden',
        ]);

        // Home & Garden Subcategories
        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Furniture',
            'slug' => 'furniture',
            'description' => 'Living room, bedroom, office, and outdoor furniture',
            'parent_id' => $homeGarden->id,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Home Decor',
            'slug' => 'home-decor',
            'description' => 'Decorative items, artwork, and home accessories',
            'parent_id' => $homeGarden->id,
            'sort_order' => 2,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Kitchen & Dining',
            'slug' => 'kitchen-dining',
            'description' => 'Cookware, dinnerware, and kitchen accessories',
            'parent_id' => $homeGarden->id,
            'sort_order' => 3,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Garden & Outdoor',
            'slug' => 'garden-outdoor',
            'description' => 'Garden tools, plants, and outdoor equipment',
            'parent_id' => $homeGarden->id,
            'sort_order' => 4,
        ]);

        // Sports & Recreation Category
        $sports = ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Sports & Recreation',
            'slug' => 'sports-recreation',
            'description' => 'Sports equipment, fitness gear, and recreational items',
            'sort_order' => 4,
            'meta_title' => 'Sports & Recreation - Sports Equipment and Fitness Gear',
            'meta_description' => 'Find all your sports and fitness equipment needs',
        ]);

        // Sports Subcategories
        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Fitness Equipment',
            'slug' => 'fitness-equipment',
            'description' => 'Gym equipment, weights, and fitness accessories',
            'parent_id' => $sports->id,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Team Sports',
            'slug' => 'team-sports',
            'description' => 'Football, basketball, soccer, and team sports equipment',
            'parent_id' => $sports->id,
            'sort_order' => 2,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Individual Sports',
            'slug' => 'individual-sports',
            'description' => 'Tennis, golf, swimming, and individual sports gear',
            'parent_id' => $sports->id,
            'sort_order' => 3,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Outdoor Recreation',
            'slug' => 'outdoor-recreation',
            'description' => 'Camping, hiking, and outdoor adventure gear',
            'parent_id' => $sports->id,
            'sort_order' => 4,
        ]);

        // Books & Media Category
        $books = ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Books & Media',
            'slug' => 'books-media',
            'description' => 'Books, magazines, movies, and digital media',
            'sort_order' => 5,
            'meta_title' => 'Books & Media - Books, Movies, and Digital Content',
            'meta_description' => 'Explore our collection of books, movies, and media',
        ]);

        // Books Subcategories
        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Fiction Books',
            'slug' => 'fiction-books',
            'description' => 'Novels, short stories, and fictional literature',
            'parent_id' => $books->id,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Non-Fiction Books',
            'slug' => 'non-fiction-books',
            'description' => 'Educational, biographical, and informational books',
            'parent_id' => $books->id,
            'sort_order' => 2,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Movies & TV',
            'slug' => 'movies-tv',
            'description' => 'DVDs, Blu-rays, and digital movies',
            'parent_id' => $books->id,
            'sort_order' => 3,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Music',
            'slug' => 'music',
            'description' => 'CDs, vinyl records, and digital music',
            'parent_id' => $books->id,
            'sort_order' => 4,
        ]);

        // Health & Beauty Category
        $healthBeauty = ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Health & Beauty',
            'slug' => 'health-beauty',
            'description' => 'Health products, cosmetics, and personal care items',
            'sort_order' => 6,
            'meta_title' => 'Health & Beauty - Personal Care and Cosmetics',
            'meta_description' => 'Take care of your health and beauty needs',
        ]);

        // Health & Beauty Subcategories
        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Skincare',
            'slug' => 'skincare',
            'description' => 'Facial cleansers, moisturizers, and skincare products',
            'parent_id' => $healthBeauty->id,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Makeup & Cosmetics',
            'slug' => 'makeup-cosmetics',
            'description' => 'Foundation, lipstick, eyeshadow, and makeup products',
            'parent_id' => $healthBeauty->id,
            'sort_order' => 2,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Hair Care',
            'slug' => 'hair-care',
            'description' => 'Shampoo, conditioner, and hair styling products',
            'parent_id' => $healthBeauty->id,
            'sort_order' => 3,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Personal Care',
            'slug' => 'personal-care',
            'description' => 'Toothpaste, deodorant, and personal hygiene products',
            'parent_id' => $healthBeauty->id,
            'sort_order' => 4,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Health Supplements',
            'slug' => 'health-supplements',
            'description' => 'Vitamins, minerals, and dietary supplements',
            'parent_id' => $healthBeauty->id,
            'sort_order' => 5,
        ]);

        // Automotive Category
        $automotive = ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Automotive',
            'slug' => 'automotive',
            'description' => 'Car parts, accessories, and automotive supplies',
            'sort_order' => 7,
            'meta_title' => 'Automotive - Car Parts and Accessories',
            'meta_description' => 'Find automotive parts and accessories for your vehicle',
        ]);

        // Automotive Subcategories
        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Car Parts',
            'slug' => 'car-parts',
            'description' => 'Engine parts, brakes, filters, and replacement parts',
            'parent_id' => $automotive->id,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Car Accessories',
            'slug' => 'car-accessories',
            'description' => 'Car mats, seat covers, and interior accessories',
            'parent_id' => $automotive->id,
            'sort_order' => 2,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Car Care',
            'slug' => 'car-care',
            'description' => 'Car wash products, wax, and cleaning supplies',
            'parent_id' => $automotive->id,
            'sort_order' => 3,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Tools & Equipment',
            'slug' => 'automotive-tools-equipment',
            'description' => 'Automotive tools, diagnostic equipment, and garage supplies',
            'parent_id' => $automotive->id,
            'sort_order' => 4,
        ]);

        // Food & Beverages Category
        $foodBeverages = ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Food & Beverages',
            'slug' => 'food-beverages',
            'description' => 'Food items, snacks, and beverages',
            'sort_order' => 8,
            'meta_title' => 'Food & Beverages - Fresh Food and Drinks',
            'meta_description' => 'Quality food products and refreshing beverages',
        ]);

        // Food & Beverages Subcategories
        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Fresh Produce',
            'slug' => 'fresh-produce',
            'description' => 'Fresh fruits, vegetables, and organic produce',
            'parent_id' => $foodBeverages->id,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Packaged Foods',
            'slug' => 'packaged-foods',
            'description' => 'Canned goods, cereals, and packaged food items',
            'parent_id' => $foodBeverages->id,
            'sort_order' => 2,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Beverages',
            'slug' => 'beverages',
            'description' => 'Soft drinks, juices, water, and hot beverages',
            'parent_id' => $foodBeverages->id,
            'sort_order' => 3,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Snacks & Confectionery',
            'slug' => 'snacks-confectionery',
            'description' => 'Chips, chocolates, candies, and snack foods',
            'parent_id' => $foodBeverages->id,
            'sort_order' => 4,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Dairy & Frozen',
            'slug' => 'dairy-frozen',
            'description' => 'Milk, cheese, yogurt, and frozen food products',
            'parent_id' => $foodBeverages->id,
            'sort_order' => 5,
        ]);

        // Office & Stationery Category
        $officeStationery = ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Office & Stationery',
            'slug' => 'office-stationery',
            'description' => 'Office supplies, stationery, and business equipment',
            'sort_order' => 9,
            'meta_title' => 'Office & Stationery - Business Supplies and Equipment',
            'meta_description' => 'Professional office supplies and stationery items',
        ]);

        // Office & Stationery Subcategories
        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Writing Instruments',
            'slug' => 'writing-instruments',
            'description' => 'Pens, pencils, markers, and writing accessories',
            'parent_id' => $officeStationery->id,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Paper Products',
            'slug' => 'paper-products',
            'description' => 'Notebooks, printing paper, and paper supplies',
            'parent_id' => $officeStationery->id,
            'sort_order' => 2,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Office Equipment',
            'slug' => 'office-equipment',
            'description' => 'Printers, scanners, and office machines',
            'parent_id' => $officeStationery->id,
            'sort_order' => 3,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Filing & Storage',
            'slug' => 'filing-storage',
            'description' => 'File folders, storage boxes, and organization supplies',
            'parent_id' => $officeStationery->id,
            'sort_order' => 4,
        ]);

        // Toys & Games Category
        $toysGames = ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Toys & Games',
            'slug' => 'toys-games',
            'description' => 'Children\'s toys, games, and educational products',
            'sort_order' => 10,
            'meta_title' => 'Toys & Games - Fun and Educational Products for Kids',
            'meta_description' => 'Discover exciting toys and games for children of all ages',
        ]);

        // Toys & Games Subcategories
        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Educational Toys',
            'slug' => 'educational-toys',
            'description' => 'Learning toys, puzzles, and educational games',
            'parent_id' => $toysGames->id,
            'sort_order' => 1,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Action Figures & Dolls',
            'slug' => 'action-figures-dolls',
            'description' => 'Action figures, dolls, and collectible toys',
            'parent_id' => $toysGames->id,
            'sort_order' => 2,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Board Games',
            'slug' => 'board-games',
            'description' => 'Family board games, card games, and party games',
            'parent_id' => $toysGames->id,
            'sort_order' => 3,
        ]);

        ProductCategory::create([
            'tenant_id' => $tenantId,
            'name' => 'Outdoor Toys',
            'slug' => 'outdoor-toys',
            'description' => 'Bikes, scooters, and outdoor play equipment',
            'parent_id' => $toysGames->id,
            'sort_order' => 4,
        ]);
    }
}
