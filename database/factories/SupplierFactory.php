<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Supplier::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'company_name' => $this->faker->name(),
            'vat_number' => $this->faker->text(5),
            'mobile_number' => $this->faker->numberBetween(1000000, 1000000000),
            'address' => $this->faker->text(20),
            'city' => $this->faker->text(8),
            'state' => $this->faker->text(8),
            'email' => $this->faker->unique()->safeEmail(),
            'created_by' => 1,
        ];
    }
}
