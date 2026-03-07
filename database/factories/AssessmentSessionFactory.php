<?php

namespace Database\Factories;

use App\Models\AssessmentSession;
use App\Models\Organizations;
use App\Models\People;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssessmentSessionFactory extends Factory
{
    protected $model = AssessmentSession::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organizations::factory(),
            'people_id' => People::factory(),
            'type' => 'psychometric',
            'status' => 'started',
            'started_at' => now(),
        ];
    }
}
