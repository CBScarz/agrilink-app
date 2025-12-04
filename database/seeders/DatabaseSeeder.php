<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\FarmerProfile;
use App\Models\Rating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@agrilink.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        // Create Test Buyer
        $buyer = User::create([
            'name' => 'Jane Buyer',
            'email' => 'buyer@agrilink.com',
            'email_verified_at' => now(),
            'password' => Hash::make('buyer123456'),
            'role' => 'buyer',
            'status' => 'active',
        ]);

        // Farmer data
        $farmerData = [
            [
                'name' => 'John Farmer',
                'email' => 'farmer@agrilink.com',
                'business_name' => 'John\'s Organic Farm',
                'location' => 'Nueva Ecija',
                'products' => [
                    ['name' => 'Fresh Tomatoes', 'category' => 'Vegetables', 'unit' => 'kg', 'price' => 45.00, 'stock' => 150, 'harvestDays' => -2, 'expirationDays' => 15],
                    ['name' => 'Organic Rice', 'category' => 'Grains', 'unit' => 'kg', 'price' => 65.00, 'stock' => 500, 'harvestDays' => -5, 'expirationDays' => 180],
                    ['name' => 'Fresh Lettuce', 'category' => 'Vegetables', 'unit' => 'bundle', 'price' => 25.00, 'stock' => 80, 'harvestDays' => -1, 'expirationDays' => 7],
                ]
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@agrilink.com',
                'business_name' => 'Santos Family Farm',
                'location' => 'Cabanatuan City',
                'products' => [
                    ['name' => 'Sweet Mangoes', 'category' => 'Fruits', 'unit' => 'kg', 'price' => 120.00, 'stock' => 200, 'harvestDays' => -3, 'expirationDays' => 20],
                    ['name' => 'Ripe Bananas', 'category' => 'Fruits', 'unit' => 'dozen', 'price' => 55.00, 'stock' => 120, 'harvestDays' => -4, 'expirationDays' => 10],
                    ['name' => 'Fresh Pumpkin', 'category' => 'Vegetables', 'unit' => 'kg', 'price' => 35.00, 'stock' => 90, 'harvestDays' => -7, 'expirationDays' => 30],
                ]
            ],
            [
                'name' => 'Carlos Reyes',
                'email' => 'carlos.reyes@agrilink.com',
                'business_name' => 'Reyes Vegetable Farm',
                'location' => 'San Fernando',
                'products' => [
                    ['name' => 'Cabbage', 'category' => 'Vegetables', 'unit' => 'kg', 'price' => 30.00, 'stock' => 200, 'harvestDays' => -2, 'expirationDays' => 25],
                    ['name' => 'Bell Peppers', 'category' => 'Vegetables', 'unit' => 'kg', 'price' => 60.00, 'stock' => 100, 'harvestDays' => -1, 'expirationDays' => 14],
                    ['name' => 'Carrots', 'category' => 'Vegetables', 'unit' => 'kg', 'price' => 35.00, 'stock' => 150, 'harvestDays' => -5, 'expirationDays' => 35],
                    ['name' => 'Onions', 'category' => 'Vegetables', 'unit' => 'kg', 'price' => 40.00, 'stock' => 250, 'harvestDays' => -10, 'expirationDays' => 60],
                ]
            ],
            [
                'name' => 'Rosa Mercado',
                'email' => 'rosa.mercado@agrilink.com',
                'business_name' => 'Mercado Harvest Farm',
                'location' => 'Tarlac City',
                'products' => [
                    ['name' => 'Fresh Strawberries', 'category' => 'Fruits', 'unit' => 'kg', 'price' => 180.00, 'stock' => 60, 'harvestDays' => 0, 'expirationDays' => 8],
                    ['name' => 'Fresh Blueberries', 'category' => 'Fruits', 'unit' => 'kg', 'price' => 200.00, 'stock' => 40, 'harvestDays' => -1, 'expirationDays' => 12],
                    ['name' => 'Pineapple', 'category' => 'Fruits', 'unit' => 'piece', 'price' => 85.00, 'stock' => 75, 'harvestDays' => -4, 'expirationDays' => 25],
                ]
            ],
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'juan.delacruz@agrilink.com',
                'business_name' => 'Dela Cruz Rice Fields',
                'location' => 'Bulacan',
                'products' => [
                    ['name' => 'White Rice', 'category' => 'Grains', 'unit' => 'kg', 'price' => 55.00, 'stock' => 800, 'harvestDays' => -30, 'expirationDays' => 365],
                    ['name' => 'Brown Rice', 'category' => 'Grains', 'unit' => 'kg', 'price' => 70.00, 'stock' => 400, 'harvestDays' => -25, 'expirationDays' => 365],
                    ['name' => 'Rice Flour', 'category' => 'Grains', 'unit' => 'kg', 'price' => 75.00, 'stock' => 200, 'harvestDays' => -20, 'expirationDays' => 180],
                ]
            ],
            [
                'name' => 'Ana Garcia',
                'email' => 'ana.garcia@agrilink.com',
                'business_name' => 'Garcia Herb Garden',
                'location' => 'Laguna',
                'products' => [
                    ['name' => 'Fresh Basil', 'category' => 'Herbs', 'unit' => 'bundle', 'price' => 30.00, 'stock' => 120, 'harvestDays' => -1, 'expirationDays' => 5],
                    ['name' => 'Parsley', 'category' => 'Herbs', 'unit' => 'bundle', 'price' => 25.00, 'stock' => 100, 'harvestDays' => -1, 'expirationDays' => 5],
                    ['name' => 'Oregano', 'category' => 'Herbs', 'unit' => 'bundle', 'price' => 28.00, 'stock' => 110, 'harvestDays' => -2, 'expirationDays' => 7],
                ]
            ],
            [
                'name' => 'Pedro Lopez',
                'email' => 'pedro.lopez@agrilink.com',
                'business_name' => 'Lopez Dairy Farm',
                'location' => 'Cavite',
                'products' => [
                    ['name' => 'Fresh Milk', 'category' => 'Dairy', 'unit' => 'liter', 'price' => 65.00, 'stock' => 300, 'harvestDays' => -1, 'expirationDays' => 3],
                    ['name' => 'Fresh Cheese', 'category' => 'Dairy', 'unit' => 'kg', 'price' => 280.00, 'stock' => 50, 'harvestDays' => -5, 'expirationDays' => 45],
                    ['name' => 'Eggs', 'category' => 'Dairy', 'unit' => 'dozen', 'price' => 85.00, 'stock' => 400, 'harvestDays' => -2, 'expirationDays' => 21],
                ]
            ],
            [
                'name' => 'Sofia Mendoza',
                'email' => 'sofia.mendoza@agrilink.com',
                'business_name' => 'Mendoza Calamansi Farm',
                'location' => 'Quezon',
                'products' => [
                    ['name' => 'Fresh Calamansi', 'category' => 'Fruits', 'unit' => 'kg', 'price' => 75.00, 'stock' => 250, 'harvestDays' => -3, 'expirationDays' => 18],
                    ['name' => 'Lemon', 'category' => 'Fruits', 'unit' => 'kg', 'price' => 65.00, 'stock' => 180, 'harvestDays' => -2, 'expirationDays' => 20],
                    ['name' => 'Lime', 'category' => 'Fruits', 'unit' => 'kg', 'price' => 70.00, 'stock' => 200, 'harvestDays' => -4, 'expirationDays' => 22],
                ]
            ],
        ];

        // Create farmers with profiles and products
        foreach ($farmerData as $data) {
            $farmer = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
                'role' => 'farmer',
                'status' => 'active',
            ]);

            // Create farmer profile
            FarmerProfile::create([
                'user_id' => $farmer->id,
                'business_name' => $data['business_name'],
                'business_permit_url' => 'permits/permit-' . $farmer->id . '.pdf',
                'location' => $data['location'],
            ]);

            // Create products for this farmer
            foreach ($data['products'] as $product) {
                $harvestDate = now()->addDays($product['harvestDays']);
                $expirationDate = $harvestDate->copy()->addDays($product['expirationDays']);
                
                $newProduct = Product::create([
                    'farmer_id' => $farmer->id,
                    'name' => $product['name'],
                    'category' => $product['category'],
                    'unit' => $product['unit'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'description' => "High-quality {$product['name']} directly from our farm.",
                    'origin' => $data['location'],
                    'image_url' => 'images/products/placeholder-' . strtolower(str_replace(' ', '-', $product['name'])) . '.jpg',
                    'harvestDate' => $harvestDate->format('Y-m-d'),
                    'expirationDate' => $expirationDate->format('Y-m-d'),
                ]);

                // Add ratings to the product
                $ratings = [
                    ['rating' => 5, 'comment' => 'Excellent quality! Very fresh and delicious.'],
                    ['rating' => 5, 'comment' => 'Highly recommend! Great product and fast delivery.'],
                    ['rating' => 4, 'comment' => 'Good quality, arrived on time.'],
                    ['rating' => 5, 'comment' => 'Perfect! Exactly as described.'],
                    ['rating' => 4, 'comment' => 'Very satisfied with my purchase.'],
                ];

                foreach ($ratings as $ratingData) {
                    Rating::create([
                        'product_id' => $newProduct->id,
                        'buyer_id' => $buyer->id,
                        'rating' => $ratingData['rating'],
                        'comment' => $ratingData['comment'],
                    ]);
                }
            }
        }
    }
}
