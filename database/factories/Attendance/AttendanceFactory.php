<?php

namespace Database\Factories\Attendance;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance\Attendance>
 */
class AttendanceFactory extends Factory
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
            'type' => rand(1, 3),
            'start_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'start_time' => $this->faker->time(),
            'end_date' => $this->faker->dateTimeBetween('-1 month', '+1 month'),
            'end_time' => $this->faker->time(),
            'duration' => $this->faker->time(),
        ];
    }
}
