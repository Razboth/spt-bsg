<?php

namespace Database\Factories\Setting;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AppParameter>
 */
class AppParameterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => substr($this->faker->firstName, 0,5),
            'name' => $this->faker->name,
            'value' => $this->faker->name,
            'description' => $this->faker->name,
        ];
    }
}
