<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TalentPassCredential>
 */
class TalentPassCredentialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'talent_pass_id' => \App\Models\TalentPass::factory(),
            'credential_name' => fake()->randomElement([
                'AWS Solutions Architect', 'Certified Kubernetes Administrator', 'Google Cloud Associate',
                'Microsoft Azure Administrator', 'HashiCorp Certified Terraform', 'Certified Ethical Hacker',
                'PHP Developer', 'Laravel Certified Developer', 'Scrum Master', 'Product Owner Certification'
            ]),
            'issuer' => fake()->randomElement(['AWS', 'Google Cloud', 'Microsoft', 'HashiCorp', 'Linux Academy', 'Coursera']),
            'issued_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'expiry_date' => fake()->optional(0.7)->dateTimeBetween('now', '+5 years'),
            'credential_url' => fake()->optional(0.8)->url(),
            'credential_id' => fake()->optional(0.6)->uuid(),
            'is_featured' => false,
        ];
    }

    public function noExpiry(): self
    {
        return $this->state(fn (array $attributes) => [
            'expiry_date' => null,
        ]);
    }

    public function expired(): self
    {
        return $this->state(fn (array $attributes) => [
            'expiry_date' => fake()->dateTimeBetween('-2 years', '-1 days'),
        ]);
    }
}
