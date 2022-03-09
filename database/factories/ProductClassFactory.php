<?php

namespace Database\Factories;

use App\Models\ProductClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductClassFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProductClass::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(5),
            'description' => $this->faker->text(10),
        ];
    }
}
