<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QrCode>
 */
class QrCodeFactory extends Factory
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
            'slug' => $this->faker->unique()->slug(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['static', 'dynamic']),
            'format' => $this->faker->randomElement(['url', 'text', 'email', 'phone', 'sms', 'vcard']),
            'payload' => json_encode(['url' => $this->faker->url()]),
            'target_type' => null,
            'target_id' => null,
            'options' => json_encode(['size' => 200, 'margin' => 2]),
            'scans' => 0,
            'is_active' => true,
            'last_scanned_at' => null,
        ];
    }
}
