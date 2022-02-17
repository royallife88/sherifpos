<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'customer_type_id' => $this->faker->numberBetween(1, 2),
            'mobile_number' => $this->faker->numberBetween(1000000, 1000000000),
            'address' => $this->faker->text(20),
            'email' => $this->faker->unique()->safeEmail(),
            'created_by' => 1,
        ];
    }
}
