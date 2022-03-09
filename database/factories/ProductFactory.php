<?php

namespace Database\Factories;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'product_class_id' => $this->faker->numberBetween(1, 10),
            'category_id' => $this->faker->numberBetween(1, 10),
            'sub_category_id' => null,
            'brand_id' => $this->faker->numberBetween(1, 10),
            'sku' => $this->faker->numberBetween(1, 1000),
            'multiple_units' => [],
            'multiple_colors' => [],
            'multiple_sizes' => [],
            'multiple_grades' => [],
            'is_service' => $this->faker->numberBetween(0, 1),
            'product_details' => $this->faker->text(10),
            'barcode_type' => 'C128',
            'alert_quantity' => 3,
            'other_cost' => 0,
            'purchase_price' => $this->faker->randomFloat(0, 1, 100),
            'sell_price' => $this->faker->randomFloat(0, 100, 150),
            'tax_id' => null,
            'tax_method' => 'exclusive',
            'discount_type' => 'fixed',
            'discount_customer_types' => ['1', '2'],
            'discount_customers' => [],
            'discount' => $this->faker->randomFloat(0, 1, 10),
            'discount_start_date' => Carbon::now()->format('Y-m-d'),
            'discount_end_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'show_to_customer' => 1,
            'show_to_customer_types' => [],
            'different_prices_for_stores' => 0,
            'this_product_have_variant' => 0,
            'price_based_on_raw_material' => 0,
            'automatic_consumption' => 0,
            'type' => 'single',
            'edited_by' => null,
        ];
    }
}
