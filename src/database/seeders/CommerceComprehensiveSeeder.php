<?php

namespace Lyre\Commerce\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Lyre\Commerce\Models\Product;
use Lyre\Commerce\Models\ProductVariant;
use Lyre\Commerce\Models\UserProductVariant;
use Lyre\Commerce\Models\ProductVariantPrice;
use Lyre\Commerce\Models\Location;
use Lyre\Commerce\Models\Coupon;
use Lyre\Facet\Models\Facet;
use Lyre\Facet\Models\FacetValue;
use Lyre\File\Repositories\Contracts\FileRepositoryInterface;

class CommerceComprehensiveSeeder extends Seeder
{
    private $userModel;
    private $user;
    private $categories = [];
    private $brands = [];
    private $locations = [];
    private $products = [];
    private $imageFiles = [];

    public function run(): void
    {
        $this->userModel = get_user_model();
        $this->user = $this->userModel::first();

        if (!$this->user) {
            $this->command->warn('No user found. Creating a user first...');
            $this->user = $this->userModel::create([
                'name' => 'Demo Merchant',
                'email' => 'merchant@demo.com',
                'password' => bcrypt('password'),
            ]);
        }

        $this->command->info('Loading images from Pictures folder...');
        $this->loadImageFiles();

        $this->command->info('Creating Facets and Categories...');
        $this->createFacetsAndCategories();

        $this->command->info('Creating Locations...');
        $this->createLocations();

        $this->command->info('Creating Coupons...');
        $this->createCoupons();

        $this->command->info('Creating Products and Variants...');
        $this->createProducts();

        $this->command->info('Creating User Product Variants and Prices...');
        $this->createUserProductVariants();

        $this->command->info('Commerce comprehensive seeding completed!');
        $this->command->info("Created " . count($this->products) . " products");
        $this->command->info("Categories: " . implode(', ', array_keys($this->categories)));
    }

    private function createFacetsAndCategories(): void
    {
        // Create Category Facet
        $categoryFacet = Facet::firstOrCreate(
            ['slug' => 'category'],
            ['name' => 'Category', 'description' => 'Product Categories']
        );

        // Create Brand Facet
        $brandFacet = Facet::firstOrCreate(
            ['slug' => 'brand'],
            ['name' => 'Brand', 'description' => 'Product Brands']
        );

        // Create Collection Facet (Best Sellers, Latest, etc.)
        $collectionFacet = Facet::firstOrCreate(
            ['slug' => 'collection'],
            ['name' => 'Collection', 'description' => 'Product Collections']
        );

        // Categories
        $categories = [
            'Bags',
            'Sneakers',
            'Belts',
            'Sunglasses',
            'Electronics',
            'Clothing',
            'Accessories',
            'Footwear',
            'Watches',
            'Jewelry',
            'Laptops',
            'Phones',
            'Tablets',
            'Headphones',
            'Cameras',
            'T-Shirts',
            'Jeans',
            'Dresses',
            'Jackets',
            'Shorts',
            'Backpacks',
            'Handbags',
            'Wallets',
            'Shoes',
            'Boots'
        ];

        // Categories with descriptions
        $categoryDescriptions = [
            'Bags' => 'Stylish bags and accessories for every occasion',
            'Sneakers' => 'Comfortable and trendy sneakers for daily wear',
            'Belts' => 'Quality belts to complete your look',
            'Sunglasses' => 'Protect your eyes with stylish sunglasses',
            'Electronics' => 'Latest electronics and gadgets',
            'Clothing' => 'Fashion-forward clothing for all seasons',
            'Accessories' => 'Complete your style with our accessories',
            'Footwear' => 'Quality footwear for every occasion',
            'Watches' => 'Elegant timepieces for men and women',
            'Jewelry' => 'Beautiful jewelry pieces',
        ];

        foreach ($categories as $categoryName) {
            $slug = Str::slug($categoryName);
            $facetValue = FacetValue::firstOrCreate(
                ['slug' => $slug, 'facet_id' => $categoryFacet->id],
                [
                    'name' => $categoryName,
                    'description' => $categoryDescriptions[$categoryName] ?? null
                ]
            );
            $this->categories[$categoryName] = $facetValue->id;
        }

        // Brands
        $brands = [
            'Nike',
            'Adidas',
            'Puma',
            'Samsung',
            'Apple',
            'Sony',
            'Gucci',
            'Louis Vuitton',
            'Prada',
            'Versace',
            'Chanel',
            'Dell',
            'HP',
            'Lenovo',
            'Microsoft',
            'Google',
            'OnePlus',
            'Zara',
            'H&M',
            'Forever 21',
            'Levi\'s',
            'Calvin Klein'
        ];

        foreach ($brands as $brandName) {
            $slug = Str::slug($brandName);
            $facetValue = FacetValue::firstOrCreate(
                ['slug' => $slug, 'facet_id' => $brandFacet->id],
                ['name' => $brandName]
            );
            $this->brands[$brandName] = $facetValue->id;
        }

        // Collections with descriptions
        $collections = [
            'Best Sellers' => 'Our most popular products based on sales',
            'Latest' => 'Newly added products to our collection',
            'Featured' => 'Handpicked products we recommend',
            'On Sale' => 'Special offers and discounted items',
            'New Arrivals' => 'Recently added products'
        ];
        foreach ($collections as $collectionName => $description) {
            $slug = Str::slug($collectionName);
            FacetValue::firstOrCreate(
                ['slug' => $slug, 'facet_id' => $collectionFacet->id],
                ['name' => $collectionName, 'description' => $description]
            );
        }
    }

    private function loadImageFiles(): void
    {
        $picturesPath = env('HOME', '~') . '/Pictures';
        if (!is_dir($picturesPath)) {
            $this->command->warn("Pictures directory not found at {$picturesPath}");
            return;
        }

        // Only include formats supported by GD library
        // AVIF is not supported by GD, so we exclude it
        $supportedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $files = glob($picturesPath . '/*.{' . implode(',', $supportedExtensions) . '}', GLOB_BRACE);

        foreach ($files as $file) {
            if (is_file($file) && filesize($file) > 0) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                // Double-check extension is in supported list
                if (in_array($extension, $supportedExtensions)) {
                    $this->imageFiles[] = $file;
                }
            }
        }

        $this->command->info("Loaded " . count($this->imageFiles) . " image files");
    }

    private function createLocations(): void
    {
        $locations = [
            ['name' => 'Nairobi CBD', 'latitude' => -1.2921, 'longitude' => 36.8219, 'address' => 'Nairobi Central Business District', 'delivery_fee' => 100.00],
            ['name' => 'Westlands', 'latitude' => -1.2644, 'longitude' => 36.8031, 'address' => 'Westlands, Nairobi', 'delivery_fee' => 120.00],
            ['name' => 'Kileleshwa', 'latitude' => -1.2944, 'longitude' => 36.7819, 'address' => 'Kileleshwa, Nairobi', 'delivery_fee' => 150.00],
            ['name' => 'Parklands', 'latitude' => -1.2564, 'longitude' => 36.8011, 'address' => 'Parklands, Nairobi', 'delivery_fee' => 130.00],
            ['name' => 'Lavington', 'latitude' => -1.2833, 'longitude' => 36.7667, 'address' => 'Lavington, Nairobi', 'delivery_fee' => 140.00],
        ];

        foreach ($locations as $locationData) {
            $location = Location::firstOrCreate(
                ['slug' => Str::slug($locationData['name'])],
                $locationData
            );
            $this->locations[] = $location;
        }
    }

    private function createCoupons(): void
    {
        $coupons = [
            ['code' => 'WELCOME10', 'discount' => 10, 'discount_type' => 'percent', 'status' => 'active', 'usage_limit' => 1000, 'minimum_amount' => 50],
            ['code' => 'SAVE20', 'discount' => 20, 'discount_type' => 'percent', 'status' => 'active', 'usage_limit' => 500, 'minimum_amount' => 100],
            ['code' => 'FLAT50', 'discount' => 50, 'discount_type' => 'fixed', 'status' => 'active', 'usage_limit' => 200, 'minimum_amount' => 200],
            ['code' => 'NEWUSER', 'discount' => 15, 'discount_type' => 'percent', 'status' => 'active', 'usage_limit' => 1000, 'minimum_amount' => 25],
        ];

        foreach ($coupons as $couponData) {
            Coupon::firstOrCreate(['code' => $couponData['code']], $couponData);
        }
    }

    private function createProducts(): void
    {
        $productNames = [
            // Bags
            'Quilted Maxi Cross Bag',
            'Leather Tote Bag',
            'Canvas Backpack',
            'Designer Handbag',
            'Shoulder Bag',
            'Messenger Bag',
            'Travel Duffel Bag',
            'Clutch Purse',
            'Beach Bag',
            'Business Briefcase',

            // Sneakers
            'Air Max 270 React',
            'Classic White Sneakers',
            'Running Shoes',
            'Basketball Sneakers',
            'Training Shoes',
            'Casual Sneakers',
            'High-Top Sneakers',
            'Low-Top Sneakers',
            'Slip-On Sneakers',
            'Platform Sneakers',

            // Belts
            'Leather Belt',
            'Designer Belt',
            'Casual Belt',
            'Formal Belt',
            'Wide Belt',
            'Chain Belt',
            'Studded Belt',
            'Braided Belt',
            'Canvas Belt',
            'Buckle Belt',

            // Sunglasses
            'Aviator Sunglasses',
            'Wayfarer Sunglasses',
            'Round Sunglasses',
            'Cat Eye Sunglasses',
            'Oversized Sunglasses',
            'Sport Sunglasses',
            'Polarized Sunglasses',
            'Designer Sunglasses',
            'Retro Sunglasses',
            'Mirror Sunglasses',

            // Electronics
            'Wireless Headphones',
            'Smart Watch',
            'Bluetooth Speaker',
            'Power Bank',
            'USB Cable',
            'Wireless Mouse',
            'Keyboard',
            'Webcam',
            'Monitor Stand',
            'Laptop Stand',

            // Clothing
            'Cotton T-Shirt',
            'Denim Jeans',
            'Casual Dress',
            'Winter Jacket',
            'Summer Shorts',
            'Hoodie',
            'Sweater',
            'Polo Shirt',
            'Chinos',
            'Blazer',

            // Accessories
            'Wrist Watch',
            'Necklace',
            'Earrings',
            'Bracelet',
            'Ring',
            'Hat',
            'Cap',
            'Scarf',
            'Gloves',
            'Socks',

            // Footwear
            'Oxford Shoes',
            'Loafers',
            'Sandals',
            'Flip Flops',
            'Boots',
            'High Heels',
            'Flats',
            'Wedges',
            'Espadrilles',
            'Moccasins',

            // More categories
            'Laptop',
            'Smartphone',
            'Tablet',
            'Camera',
            'Gaming Console',
            'Fitness Tracker',
            'Wireless Earbuds',
            'Tablet Stand',
            'Phone Case',
            'Screen Protector',
        ];

        $descriptions = [
            'Premium quality product with excellent craftsmanship',
            'Stylish and modern design perfect for everyday use',
            'Durable and long-lasting material construction',
            'Comfortable fit with superior comfort',
            'Trendy design that matches current fashion trends',
            'High-quality materials ensure product longevity',
            'Perfect for both casual and formal occasions',
            'Ergonomic design for maximum comfort',
            'Elegant design that never goes out of style',
            'Versatile product suitable for multiple uses',
        ];

        $brands = array_keys($this->brands);
        $categories = array_keys($this->categories);
        $collections = ['Best Sellers', 'Latest', 'Featured', 'On Sale', 'New Arrivals'];

        $productCount = 0;
        $targetProducts = 1000;

        while ($productCount < $targetProducts) {
            foreach ($productNames as $baseName) {
                if ($productCount >= $targetProducts) break;

                $brand = $brands[array_rand($brands)];
                $category = $categories[array_rand($categories)];
                $collection = $collections[array_rand($collections)];
                $description = $descriptions[array_rand($descriptions)];

                // Create product with variations
                $variants = rand(1, 4); // 1-4 variants per product

                for ($v = 0; $v < $variants && $productCount < $targetProducts; $v++) {
                    $productName = $baseName . ($v > 0 ? ' - Variant ' . ($v + 1) : '');
                    $productSlug = Str::slug($productName . ' ' . $brand . ' ' . $productCount);

                    $color = ['Black', 'White', 'Blue', 'Red', 'Green', 'Brown'][array_rand([0, 1, 2, 3, 4, 5])];
                    $material = ['Cotton', 'Leather', 'Polyester', 'Metal', 'Plastic'][array_rand([0, 1, 2, 3, 4])];

                    $product = Product::create([
                        'slug' => $productSlug,
                        'name' => $productName,
                        'description' => $description . ' - ' . $productName . ' from ' . $brand,
                        'saleable' => true,
                        'hscode' => str_pad((string)rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                        'hstype' => 'General',
                        'hsdescription' => $description,
                        'status' => 'active',
                        'metadata' => [
                            'brand' => $brand,
                            'material' => $material,
                            'colors' => $color,
                        ],
                    ]);

                    // Attach a random image if available
                    if (!empty($this->imageFiles)) {
                        $randomImagePath = $this->imageFiles[array_rand($this->imageFiles)];
                        try {
                            $mimeType = mime_content_type($randomImagePath);
                            // Skip AVIF and other unsupported formats
                            if (strpos($mimeType, 'image/avif') !== false) {
                                continue;
                            }
                            
                            // Skip very large images (> 10MB) to prevent memory issues
                            $fileSize = filesize($randomImagePath);
                            if ($fileSize > 10 * 1024 * 1024) {
                                $this->command->warn("Skipping large image: " . basename($randomImagePath) . " (" . round($fileSize / 1024 / 1024, 2) . "MB)");
                                continue;
                            }

                            $file = new UploadedFile(
                                $randomImagePath,
                                basename($randomImagePath),
                                $mimeType,
                                null,
                                true
                            );
                            
                            // Use unique name with product ID and timestamp to avoid conflicts
                            $uniqueFileName = $productName . '-' . $productCount . '-' . time() . '-' . \Illuminate\Support\Str::random(6);
                            $fileRecord = app(FileRepositoryInterface::class)->uploadFile($file, $uniqueFileName);
                            $product->attachFile($fileRecord->id);
                            
                            // Free memory
                            unset($file, $fileRecord);
                        } catch (\Exception $e) {
                            $this->command->warn("Failed to attach image to product {$productName}: " . $e->getMessage());
                        }
                        
                        // Free memory periodically
                        if ($productCount % 50 === 0) {
                            gc_collect_cycles();
                        }
                    }

                    // Attach facet values (category, brand, collection)
                    $facetValueIds = [
                        $this->categories[$category],
                        $this->brands[$brand],
                    ];

                    // Add collection randomly (30% chance)
                    if (rand(1, 100) <= 30) {
                        $collectionFacet = Facet::where('slug', 'collection')->first();
                        $collectionValue = FacetValue::where('facet_id', $collectionFacet->id)
                            ->where('name', $collection)
                            ->first();
                        if ($collectionValue) {
                            $facetValueIds[] = $collectionValue->id;
                        }
                    }

                    $product->attachFacetValues($facetValueIds);

                    $this->products[] = $product;
                    $productCount++;
                }
            }
        }

        $this->command->info("Created {$productCount} products");
    }

    private function createUserProductVariants(): void
    {
        $this->command->info('Creating variants and pricing...');

        $progressBar = $this->command->getOutput()->createProgressBar(count($this->products));
        $progressBar->start();

        foreach ($this->products as $product) {
            $metadata = $product->metadata ?? [];
            $variant = ProductVariant::create([
                'product_id' => $product->id,
                'name' => $product->name . ' - Standard',
                'enabled' => true,
                'attributes' => [
                    'size' => ['S', 'M', 'L', 'XL'][array_rand([0, 1, 2, 3])],
                    'color' => $metadata['colors'] ?? 'Black',
                ],
            ]);

            $sku = 'SKU-' . strtoupper(Str::random(8));
            $stockLevel = rand(10, 500);
            $basePrice = rand(500, 5000); // 500-5000 in cents/KES

            $userVariant = UserProductVariant::create([
                'user_id' => $this->user->id,
                'product_variant_id' => $variant->id,
                'sku' => $sku,
                'stock_level' => $stockLevel,
                'min_qty' => 1,
                'max_qty' => min($stockLevel, 10),
            ]);

            ProductVariantPrice::create([
                'user_product_variant_id' => $userVariant->id,
                'price' => $basePrice / 100, // Convert to decimal
                'currency' => config('commerce.default_currency', 'KES'),
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->command->newLine();
    }
}
