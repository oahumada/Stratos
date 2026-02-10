<?php

namespace Database\Seeders;

use App\Models\Capability;
use App\Models\Organizations;
use App\Models\Skill;
use Illuminate\Database\Seeder;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        $org = Organizations::first();

        if (! $org) {
            $org = Organizations::create(['name' => 'Demo Org', 'subdomain' => 'demo-org']);
        }

        $capabilities = Capability::where('organization_id', $org->id)->get();
        if ($capabilities->isEmpty()) {
            $this->call(CapabilitySeeder::class);
            $capabilities = Capability::where('organization_id', $org->id)->get();
        }

        $skillsData = [
            // Technical Skills (12)
            ['name' => 'PHP', 'category' => 'technical', 'description' => 'Server-side programming language for web development', 'is_critical' => true],
            ['name' => 'Laravel', 'category' => 'technical', 'description' => 'PHP web application framework for rapid development', 'is_critical' => true],
            ['name' => 'Vue.js', 'category' => 'technical', 'description' => 'Progressive JavaScript framework for user interfaces', 'is_critical' => true],
            ['name' => 'React', 'category' => 'technical', 'description' => 'JavaScript library for building interactive components', 'is_critical' => false],
            ['name' => 'TypeScript', 'category' => 'technical', 'description' => 'Typed superset of JavaScript for safer code', 'is_critical' => true],
            ['name' => 'Database Design', 'category' => 'technical', 'description' => 'Ability to structure and optimize database schemas', 'is_critical' => true],
            ['name' => 'PostgreSQL', 'category' => 'technical', 'description' => 'Advanced relational database management system', 'is_critical' => true],
            ['name' => 'REST APIs', 'category' => 'technical', 'description' => 'Design and development of RESTful web services', 'is_critical' => true],
            ['name' => 'Docker', 'category' => 'technical', 'description' => 'Containerization platform for application deployment', 'is_critical' => false],
            ['name' => 'Git', 'category' => 'technical', 'description' => 'Version control system for code management', 'is_critical' => true],
            ['name' => 'CSS/Tailwind', 'category' => 'technical', 'description' => 'Styling frameworks for responsive design', 'is_critical' => false],
            ['name' => 'Testing (PHPUnit)', 'category' => 'technical', 'description' => 'Unit testing framework for PHP applications', 'is_critical' => false],

            // Soft Skills (9)
            ['name' => 'Communication', 'category' => 'soft', 'description' => 'Ability to convey ideas clearly and listen effectively', 'is_critical' => true],
            ['name' => 'Leadership', 'category' => 'soft', 'description' => 'Capacity to guide and inspire teams', 'is_critical' => false],
            ['name' => 'Problem Solving', 'category' => 'soft', 'description' => 'Analytical approach to finding solutions', 'is_critical' => true],
            ['name' => 'Team Work', 'category' => 'soft', 'description' => 'Collaboration and cooperation in team environments', 'is_critical' => true],
            ['name' => 'Critical Thinking', 'category' => 'soft', 'description' => 'Logical analysis and evaluation of information', 'is_critical' => true],
            ['name' => 'Adaptability', 'category' => 'soft', 'description' => 'Ability to adjust to changing circumstances', 'is_critical' => false],
            ['name' => 'Project Management', 'category' => 'soft', 'description' => 'Planning and execution of projects', 'is_critical' => false],
            ['name' => 'Mentoring', 'category' => 'soft', 'description' => 'Guiding and developing team members', 'is_critical' => false],
            ['name' => 'Agile Methodology', 'category' => 'soft', 'description' => 'Experience with iterative development practices', 'is_critical' => false],

            // Business Skills (9)
            ['name' => 'Business Analysis', 'category' => 'business', 'description' => 'Understanding business requirements and processes', 'is_critical' => false],
            ['name' => 'Product Strategy', 'category' => 'business', 'description' => 'Developing product direction and roadmap', 'is_critical' => false],
            ['name' => 'Requirements Gathering', 'category' => 'business', 'description' => 'Collecting and documenting stakeholder needs', 'is_critical' => false],
            ['name' => 'Client Management', 'category' => 'business', 'description' => 'Building and maintaining client relationships', 'is_critical' => false],
            ['name' => 'Budget Planning', 'category' => 'business', 'description' => 'Financial planning and resource allocation', 'is_critical' => false],
            ['name' => 'Market Research', 'category' => 'business', 'description' => 'Analyzing market trends and competition', 'is_critical' => false],
            ['name' => 'Stakeholder Management', 'category' => 'business', 'description' => 'Engaging and managing stakeholder expectations', 'is_critical' => false],
            ['name' => 'Sales Skills', 'category' => 'business', 'description' => 'Ability to sell and promote products/services', 'is_critical' => false],
            ['name' => 'Data Analysis', 'category' => 'business', 'description' => 'Interpreting data to drive business decisions', 'is_critical' => false],
        ];

        // map categories to preferred capability name fragments to improve assignment
        $categoryMap = [
            'technical' => ['Software', 'Cloud', 'Engineering'],
            'soft' => ['Leadership', 'Communication'],
            'business' => ['Product', 'Data', 'Business'],
            'language' => ['Language'],
        ];

        foreach ($skillsData as $skill) {
            // try to find a capability matching category heuristics
            $cap = null;
            $fragments = $categoryMap[$skill['category']] ?? [];
            foreach ($fragments as $frag) {
                $cap = $capabilities->firstWhere('name', 'like', "%$frag%");
                if ($cap) {
                    break;
                }
            }

            // fallback to random capability
            if (! $cap) {
                $cap = $capabilities->random();
            }

            // Create skill without capability_id column (capability relation is via pivots/competencies)
            // Use firstOrCreate to make the seeder idempotent in CI/local runs
            Skill::firstOrCreate(
                ['organization_id' => $org->id, 'name' => $skill['name']],
                array_merge(['organization_id' => $org->id], $skill)
            );
        }
    }
}
