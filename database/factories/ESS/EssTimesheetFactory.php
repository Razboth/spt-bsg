<?php

namespace Database\Factories\ESS;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ESS\EssTimesheet>
 */
class EssTimesheetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'employee_id' => rand(1, 256),
            'activity' => $this->faker->text(100),
            'output' => $this->faker->text(100),
            'date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'duration' => $this->faker->time(),
            'volume' => rand(1, 10),
            'type' => $this->faker->text(100),
            'description' => $this->faker->text(100),
        ];
    }
}
