<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use App\Models\UserNotificationChannel;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserNotificationChannelFactory extends Factory
{
    protected $model = UserNotificationChannel::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'organization_id' => Organization::factory(),
            'channel_type' => $this->faker->randomElement(['email', 'slack', 'telegram']),
            'is_active' => true,
            'channel_config' => match ($this->faker->randomElement(['email', 'slack', 'telegram'])) {
                'email' => ['email' => $this->faker->email()],
                'slack' => ['webhook_url' => 'https://hooks.slack.com/services/test'],
                'telegram' => ['chat_id' => $this->faker->numberBetween(1000000, 9999999)],
            },
        ];
    }
}
