<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use App\Models\ProductCategory;
use Illuminate\Support\Str;

class DefaultProductCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        $this->seedForTenant(null);
    }

    /**
     * Seed default product categories for a specific tenant
     */
    public static function seedForTenant($tenantId)
    {
        // Check if categories already exist for this tenant
        $existingCategories = ProductCategory::where('tenant_id', $tenantId)->count();
        if ($existingCategories > 0) {
            Log::info("Product categories already exist for tenant, skipping seeding", [
                'tenant_id' => $tenantId,
                'existing_count' => $existingCategories
            ]);
            return; // Skip seeding if categories already exist
        }

        $categories = [
            // Physical Product Categories
            [
                'name' => 'Raw Materials',
                'description' => 'Unprocessed inputs for manufacturing',
                'sort_order' => 1,
            ],
            [
                'name' => 'Finished Goods',
                'description' => 'Products ready for sale',
                'sort_order' => 2,
            ],
            [
                'name' => 'Consumables',
                'description' => 'Items used internally (e.g. paper, fuel)',
                'sort_order' => 3,
            ],
            [
                'name' => 'Spare Parts',
                'description' => 'Replacement items and accessories',
                'sort_order' => 4,
            ],
            [
                'name' => 'Packaging Materials',
                'description' => 'Cartons, bottles, wraps, labels',
                'sort_order' => 5,
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Stationery, ink, electronics',
                'sort_order' => 6,
            ],
            [
                'name' => 'Electronics',
                'description' => 'Phones, computers, appliances',
                'sort_order' => 7,
            ],
            [
                'name' => 'Clothing & Apparel',
                'description' => 'Wears, uniforms, fashion items',
                'sort_order' => 8,
            ],
            [
                'name' => 'Food & Beverages',
                'description' => 'For groceries or F&B businesses',
                'sort_order' => 9,
            ],
            [
                'name' => 'Furniture',
                'description' => 'Tables, chairs, shelves',
                'sort_order' => 10,
            ],
            [
                'name' => 'Tools & Equipment',
                'description' => 'Machinery, tools',
                'sort_order' => 11,
            ],
            [
                'name' => 'Medical Supplies',
                'description' => 'For healthcare-related businesses',
                'sort_order' => 12,
            ],
            [
                'name' => 'Books & Stationery',
                'description' => 'Bookshops and educational supply',
                'sort_order' => 13,
            ],

            // Service Categories
            [
                'name' => 'Consulting Services',
                'description' => 'Business, IT, Legal advisory',
                'sort_order' => 14,
            ],
            [
                'name' => 'Installation Services',
                'description' => 'Setup or configuration jobs',
                'sort_order' => 15,
            ],
            [
                'name' => 'Maintenance Services',
                'description' => 'Repairs, cleaning, servicing',
                'sort_order' => 16,
            ],
            [
                'name' => 'Delivery & Logistics',
                'description' => 'Shipping, transport, dispatch',
                'sort_order' => 17,
            ],
            [
                'name' => 'Training & Education',
                'description' => 'Online courses, seminars, coaching',
                'sort_order' => 18,
            ],
            [
                'name' => 'Creative Services',
                'description' => 'Design, video, writing, branding',
                'sort_order' => 19,
            ],
            [
                'name' => 'Software Development',
                'description' => 'Web, mobile, or SaaS projects',
                'sort_order' => 20,
            ],
            [
                'name' => 'Advertising Services',
                'description' => 'Digital, print, influencer marketing',
                'sort_order' => 21,
            ],
            [
                'name' => 'Legal & Compliance',
                'description' => 'Legal representation and filing',
                'sort_order' => 22,
            ],
            [
                'name' => 'Professional Services',
                'description' => 'Accounting, auditing, etc.',
                'sort_order' => 23,
            ],
             [
                'name' => 'None',
                'description' => 'None',
                'sort_order' => 24,
            ],
        ];

        foreach ($categories as $categoryData) {
            // Check if category already exists for this tenant
            $existingCategory = ProductCategory::where('tenant_id', $tenantId)
                ->where('name', $categoryData['name'])
                ->first();

            if (!$existingCategory) {
                ProductCategory::create([
                    'tenant_id' => $tenantId,
                    'name' => $categoryData['name'],
                    'slug' => Str::slug($categoryData['name']),
                    'description' => $categoryData['description'],
                    'parent_id' => null,
                    'image' => null,
                    'sort_order' => $categoryData['sort_order'],
                    'is_active' => true,
                    'meta_title' => $categoryData['name'],
                    'meta_description' => $categoryData['description'],
                ]);
            }
        }
    }
}
