<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Capability;
use App\Models\Skills;
use App\Models\Organizations;
use Illuminate\Support\Str;

class CapabilitySeeder extends Seeder
{
    public function run(): void
    {
        $orgs = Organizations::all();

        if ($orgs->isEmpty()) {
            $orgs = collect([
                Organizations::create(['name' => 'Demo Org', 'subdomain' => Str::slug('Demo Org')])
            ]);
        }

        $samples = [
            'Cloud Architecture' => 'Platform and cloud infra design',
            'Data Analysis' => 'Analytical skills to interpret data',
            'Leadership' => 'People leadership and strategic decision making',
            'Software Engineering' => 'Design and develop software systems',
            'Product Management' => 'Define product vision and roadmap',
        ];

        foreach ($orgs as $org) {
            foreach ($samples as $name => $desc) {
                $cap = Capability::create([
                    'organization_id' => $org->id,
                    'name' => $name,
                    'description' => $desc,
                ]);

                // Try to attach existing skills from this organization
                $skills = Skills::where('organization_id', $org->id)->inRandomOrder()->limit(3)->get();

                if ($skills->isEmpty()) {
                    // create a couple of sample skills for the capability
                    $created = [];
                    for ($i = 1; $i <= 3; $i++) {
                        $created[] = Skills::create([
                            'organization_id' => $org->id,
                            'name' => "$name Skill $i",
                            'description' => "$desc - sample skill $i",
                            'complexity_level' => 'tactical',
                            'capability_id' => $cap->id,
                            'lifecycle_status' => 'active',
                            'category' => 'technical',
                            'is_critical' => false,
                            'scope_type' => 'domain',
                        ]);
                    }
                } else {
                    foreach ($skills as $s) {
                        $s->capability_id = $cap->id;
                        $s->save();
                    }
                }
            }
        }
    }
}
