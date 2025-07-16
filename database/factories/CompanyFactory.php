<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'slug' => fake()->slug(),
            'logo' => fake()->imageUrl(640, 480, 'business', true),
            'email' => fake()->unique()->safeEmail(),
            'website' => fake()->url(),
            'social_links' => json_encode([
                'facebook' => fake()->url(),
                'twitter' => fake()->url(),
                'linkedin' => fake()->url(),
                'instagram' => fake()->url(),
            ]),
            'tenant_id' => Tenant::first()->id,
        ];
    }
}
