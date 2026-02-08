<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\JobOpening;
use App\Models\People;
use Illuminate\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $jobOpenings = JobOpening::all();
        $people = People::all();
        $applicationStatuses = ['pending', 'under_review', 'accepted'];
        $applicationsCreated = 0;

        foreach ($jobOpenings as $jobOpening) {
            $candidates = $people->random(2);

            foreach ($candidates as $candidate) {
                if ($applicationsCreated < 10) {
                    Application::create([
                        'job_opening_id' => $jobOpening->id,
                        'people_id' => $candidate->id,
                        'status' => $applicationStatuses[rand(0, count($applicationStatuses) - 1)],
                        'message' => 'Interested in this opportunity. I believe my skills align well with the role requirements.',
                        'applied_at' => now()->subDays(rand(1, 30)),
                    ]);
                    $applicationsCreated++;
                }
            }
        }
    }
}
