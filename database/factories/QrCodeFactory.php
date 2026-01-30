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
        $format = $this->faker->randomElement(['url', 'text', 'email', 'phone', 'sms', 'vcard']);
        
        // Generate appropriate payload based on format
        $payload = match($format) {
            'url' => ['content' => $this->faker->url()],
            'text' => ['content' => $this->faker->sentence()],
            'email' => ['content' => $this->faker->email()],
            'phone' => ['content' => $this->faker->phoneNumber()],
            'sms' => ['content' => $this->faker->phoneNumber()],
            'vcard' => ['content' => $this->faker->name()],
            default => ['content' => $this->faker->url()],
        };
        
        return [
            'user_id' => $user->id,
            'company_id' => $user->companies->first()->id,
            'tenant_id' => $user->tenant_id,
            'slug' => strtolower($this->faker->unique()->bothify('??????')), // 6 caratteri lowercase
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'type' => $this->faker->randomElement(['static', 'dynamic']),
            'format' => $format,
            'payload' => $payload,
            'target_type' => null,
            'target_id' => null,
            'options' => ['size' => 200, 'margin' => 2],
            'scans' => 0,
            'is_active' => true,
            'last_scanned_at' => null,
            'require_consent' => $this->faker->boolean(30), // 30% richiede consenso
        ];
    }
}
