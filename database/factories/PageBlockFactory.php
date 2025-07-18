<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PageBlock>
 */
class PageBlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'page_id' => Page::inRandomOrder()->first()->id ?? 1,
            'type' => $this->faker->randomElement(['text', 'image', 'video', 'html']),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'position' => ['x' => $this->faker->numberBetween(0, 6), 'y' => $this->faker->numberBetween(0, 6)],
            'size' => ['width' => $this->faker->numberBetween(1, 6), 'height' => $this->faker->numberBetween(1, 6)],
            'style' => ['color' => $this->faker->hexColor, 'font_size' => $this->faker->numberBetween(12, 24)],
            'settings' => ['align' => $this->faker->randomElement(['left', 'center', 'right'])],
            'is_active' => $this->faker->boolean,
        ];
    }
}
