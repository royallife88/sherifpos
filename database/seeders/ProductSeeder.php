<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductClass;
use App\Models\ProductStore;
use App\Models\Supplier;
use App\Models\Tax;
use App\Models\Variation;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Tax::factory()->count(3)->create();
        Customer::factory()->count(10)->create();
        Supplier::factory()->count(10)->create();
        ProductClass::factory()->count(10)->create();
        Category::factory()->count(10)->create();
        Brand::factory()->count(10)->create();
        Product::factory()->count(1000)->create()->each(function ($product) {
            $variation = Variation::create(
                [
                    'name' => 'Default',
                    'product_id' => $product->id,
                    'sub_sku' => $product->sku,
                    'color_id' => null,
                    'size_id' => null,
                    'grade_id' => null,
                    'unit_id' => null,
                    'default_purchase_price' => $product->purchase_price,
                    'default_sell_price' => $product->sell_price,
                    'is_dummy' => 1
                ]
            );

            ProductStore::create([
                'variation_id' => $variation->id,
                'product_id' => $product->id,
                'store_id' => 1,
                'qty_available' => 0
            ]);
        });
    }
}
