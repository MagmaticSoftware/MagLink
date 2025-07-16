<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::role('tenant')->inRandomOrder()->first() ?? User::factory()->create();
        return [
            'user_id' => $user->id,
            'company_id' => $user->company->id,
            'tenant_id' => $user->tenant_id,
            'slug' => $this->faker->slug(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'style' => null,
            'settings' => null,
            'is_active' => $this->faker->boolean(),
            'views' => $this->faker->numberBetween(0, 1000),
            'last_viewed_at' => $this->faker->optional()->dateTime(),
            'published_at' => $this->faker->optional()->dateTime(),
        ];
    }
}
