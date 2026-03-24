<?php

namespace Database\Factories;

use App\Models\DeviceToken;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeviceToken>
 */
class DeviceTokenFactory extends Factory
{
    protected $model = DeviceToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'organization_id' => Organization::factory(),
            'token' => $this->generateToken($this->faker->randomElement(['android', 'ios'])),
            'platform' => $this->faker->randomElement(['android', 'ios']),
            'is_active' => true,
            'last_used_at' => now(),
            'metadata' => [
                'app_version' => '1.0.0',
                'os_version' => $this->faker->randomElement(['14.0', '15.0', '16.0']),
                'device_model' => $this->faker->randomElement(['iPhone 13', 'Pixel 6']),
            ],
        ];
    }

    /**
     * Generate a realistic token based on platform
     */
    protected function generateToken(string $platform): string
    {
        if ($platform === 'ios') {
            // APNs: 64 character hex string
            return bin2hex(random_bytes(32));
        }

        // FCM: Alphanumeric, ~150 characters
        return base64_encode(random_bytes(128));
    }

    /**
     * Mark device as inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set platform to iOS
     */
    public function ios(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'ios',
            'token' => $this->generateToken('ios'),
        ]);
    }

    /**
     * Set platform to Android
     */
    public function android(): static
    {
        return $this->state(fn (array $attributes) => [
            'platform' => 'android',
            'token' => $this->generateToken('android'),
        ]);
    }
}
