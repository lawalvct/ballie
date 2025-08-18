<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class PosProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenantId = 19;
        $categoryId = 51;

        $this->command->info("Starting to create 20 products for tenant_id: {$tenantId}");

        $products = [
            [
                'name' => 'Apple iPhone 14 Pro',
                'description' => 'Latest iPhone with A16 Bionic chip and Pro camera system',
                'brand' => 'Apple',
                'purchase_rate' => 850000.00,
                'sales_rate' => 1200000.00,
                'mrp' => 1300000.00,
                'current_stock' => 15,
                'reorder_level' => 5,
                'barcode' => '123456789001',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Samsung Galaxy S23 Ultra',
                'description' => 'Premium Android smartphone with S Pen and advanced camera',
                'brand' => 'Samsung',
                'purchase_rate' => 780000.00,
                'sales_rate' => 1150000.00,
                'mrp' => 1250000.00,
                'current_stock' => 12,
                'reorder_level' => 3,
                'barcode' => '123456789002',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'MacBook Air M2',
                'description' => '13-inch laptop with M2 chip, perfect for productivity',
                'brand' => 'Apple',
                'purchase_rate' => 980000.00,
                'sales_rate' => 1400000.00,
                'mrp' => 1500000.00,
                'current_stock' => 8,
                'reorder_level' => 2,
                'barcode' => '123456789003',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Dell XPS 13',
                'description' => 'Ultra-portable laptop with Intel Core i7 processor',
                'brand' => 'Dell',
                'purchase_rate' => 750000.00,
                'sales_rate' => 1100000.00,
                'mrp' => 1200000.00,
                'current_stock' => 10,
                'reorder_level' => 3,
                'barcode' => '123456789004',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'iPad Pro 12.9-inch',
                'description' => 'Professional tablet with M2 chip and Liquid Retina display',
                'brand' => 'Apple',
                'purchase_rate' => 650000.00,
                'sales_rate' => 950000.00,
                'mrp' => 1050000.00,
                'current_stock' => 20,
                'reorder_level' => 5,
                'barcode' => '123456789005',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Sony WH-1000XM5',
                'description' => 'Industry-leading noise canceling wireless headphones',
                'brand' => 'Sony',
                'purchase_rate' => 180000.00,
                'sales_rate' => 280000.00,
                'mrp' => 320000.00,
                'current_stock' => 25,
                'reorder_level' => 10,
                'barcode' => '123456789006',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'AirPods Pro (2nd Gen)',
                'description' => 'Wireless earbuds with active noise cancellation',
                'brand' => 'Apple',
                'purchase_rate' => 140000.00,
                'sales_rate' => 220000.00,
                'mrp' => 250000.00,
                'current_stock' => 30,
                'reorder_level' => 10,
                'barcode' => '123456789007',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Nintendo Switch OLED',
                'description' => 'Portable gaming console with OLED screen',
                'brand' => 'Nintendo',
                'purchase_rate' => 220000.00,
                'sales_rate' => 350000.00,
                'mrp' => 380000.00,
                'current_stock' => 18,
                'reorder_level' => 5,
                'barcode' => '123456789008',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Canon EOS R6 Mark II',
                'description' => 'Full-frame mirrorless camera for professionals',
                'brand' => 'Canon',
                'purchase_rate' => 1800000.00,
                'sales_rate' => 2500000.00,
                'mrp' => 2700000.00,
                'current_stock' => 5,
                'reorder_level' => 2,
                'barcode' => '123456789009',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'LG C2 OLED 55-inch',
                'description' => '4K OLED Smart TV with webOS and Dolby Vision',
                'brand' => 'LG',
                'purchase_rate' => 850000.00,
                'sales_rate' => 1200000.00,
                'mrp' => 1350000.00,
                'current_stock' => 7,
                'reorder_level' => 2,
                'barcode' => '123456789010',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Microsoft Surface Pro 9',
                'description' => '2-in-1 laptop tablet with Windows 11',
                'brand' => 'Microsoft',
                'purchase_rate' => 680000.00,
                'sales_rate' => 980000.00,
                'mrp' => 1100000.00,
                'current_stock' => 12,
                'reorder_level' => 3,
                'barcode' => '123456789011',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Dyson V15 Detect',
                'description' => 'Cordless vacuum cleaner with laser detection',
                'brand' => 'Dyson',
                'purchase_rate' => 380000.00,
                'sales_rate' => 550000.00,
                'mrp' => 600000.00,
                'current_stock' => 15,
                'reorder_level' => 5,
                'barcode' => '123456789012',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Google Pixel 7 Pro',
                'description' => 'Android smartphone with advanced AI photography',
                'brand' => 'Google',
                'purchase_rate' => 520000.00,
                'sales_rate' => 750000.00,
                'mrp' => 820000.00,
                'current_stock' => 22,
                'reorder_level' => 8,
                'barcode' => '123456789013',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'KitchenAid Stand Mixer',
                'description' => 'Professional 5-quart stand mixer for baking',
                'brand' => 'KitchenAid',
                'purchase_rate' => 280000.00,
                'sales_rate' => 420000.00,
                'mrp' => 480000.00,
                'current_stock' => 10,
                'reorder_level' => 3,
                'barcode' => '123456789014',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Bose QuietComfort 45',
                'description' => 'Wireless noise cancelling headphones',
                'brand' => 'Bose',
                'purchase_rate' => 160000.00,
                'sales_rate' => 250000.00,
                'mrp' => 280000.00,
                'current_stock' => 28,
                'reorder_level' => 10,
                'barcode' => '123456789015',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Tesla Model Y Phone Mount',
                'description' => 'Premium wireless charging phone mount for Tesla',
                'brand' => 'Tesla',
                'purchase_rate' => 35000.00,
                'sales_rate' => 65000.00,
                'mrp' => 75000.00,
                'current_stock' => 50,
                'reorder_level' => 20,
                'barcode' => '123456789016',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Logitech MX Master 3S',
                'description' => 'Advanced wireless mouse for productivity',
                'brand' => 'Logitech',
                'purchase_rate' => 45000.00,
                'sales_rate' => 85000.00,
                'mrp' => 95000.00,
                'current_stock' => 35,
                'reorder_level' => 15,
                'barcode' => '123456789017',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Mechanical Gaming Keyboard',
                'description' => 'RGB backlit mechanical keyboard with Cherry MX switches',
                'brand' => 'Corsair',
                'purchase_rate' => 85000.00,
                'sales_rate' => 150000.00,
                'mrp' => 180000.00,
                'current_stock' => 20,
                'reorder_level' => 8,
                'barcode' => '123456789018',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Instant Pot Duo 7-in-1',
                'description' => 'Multi-use pressure cooker and slow cooker',
                'brand' => 'Instant Pot',
                'purchase_rate' => 65000.00,
                'sales_rate' => 120000.00,
                'mrp' => 140000.00,
                'current_stock' => 25,
                'reorder_level' => 8,
                'barcode' => '123456789019',
                'tax_rate' => 7.50,
            ],
            [
                'name' => 'Ring Video Doorbell Pro 2',
                'description' => 'Smart doorbell with 1536p video and 3D motion detection',
                'brand' => 'Ring',
                'purchase_rate' => 120000.00,
                'sales_rate' => 180000.00,
                'mrp' => 200000.00,
                'current_stock' => 15,
                'reorder_level' => 5,
                'barcode' => '123456789020',
                'tax_rate' => 7.50,
            ],
        ];

        try {
            foreach ($products as $index => $productData) {
                $sku = 'PRD-' . str_pad($index + 21, 3, '0', STR_PAD_LEFT);

                $this->command->info("Creating product: {$productData['name']}");

                $product = Product::create([
                    'tenant_id' => $tenantId,
                    'type' => 'item',
                    'name' => $productData['name'],
                    'sku' => $sku,
                    'description' => $productData['description'],
                    'category_id' => null, // Set to null first to avoid foreign key issues
                    'brand' => $productData['brand'],
                    'hsn_code' => '8517',
                    'purchase_rate' => $productData['purchase_rate'],
                    'sales_rate' => $productData['sales_rate'],
                    'mrp' => $productData['mrp'],
                    'primary_unit_id' => null, // Set to null first
                    'unit_conversion_factor' => 1.000000,
                    'opening_stock' => $productData['current_stock'],
                    'current_stock' => $productData['current_stock'],
                    'reorder_level' => $productData['reorder_level'],
                    'opening_stock_value' => $productData['current_stock'] * $productData['purchase_rate'],
                    'current_stock_value' => $productData['current_stock'] * $productData['purchase_rate'],
                    'tax_rate' => $productData['tax_rate'],
                    'tax_inclusive' => false,
                    'barcode' => $productData['barcode'],
                    'image_path' => 'products/product.jpg',
                    'maintain_stock' => true,
                    'is_active' => true,
                    'is_saleable' => true,
                    'is_purchasable' => true,
                    'created_by' => null, // Set to null to avoid user foreign key issues
                    'updated_by' => null,
                ]);

                $this->command->info("Successfully created product: {$productData['name']} with ID: {$product->id}");
            }

            $this->command->info('Test products created successfully!');

        } catch (\Exception $e) {
            $this->command->error('Error creating products: ' . $e->getMessage());
            $this->command->error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}
