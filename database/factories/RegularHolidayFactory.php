<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RegularHoliday>
 */
class RegularHolidayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'day' => fake()->dayOfWeek(),
            'day' => fake()->dayOfWeek(), // 'day' カラムに曜日を生成
            'day_index' => fake()->optional()->randomDigit,
        ];
    }
}
