<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Link>
 */
class LinkFactory extends Factory
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
            'company_id' => $user->companies->first()->id,
            'tenant_id' => $user->tenant_id,
            'slug' => $this->faker->slug(),
            'url' => $this->faker->url(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'is_active' => $this->faker->boolean(),
            'type' => $this->faker->word(),
        ];
    }
}
