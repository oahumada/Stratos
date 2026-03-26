<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TalentPassSkill>
 */
class TalentPassSkillFactory extends Factory
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
            'skill_name' => fake()->randomElement([
                'PHP', 'Laravel', 'Vue.js', 'JavaScript', 'TypeScript', 'React', 'Python',
                'PostgreSQL', 'Redis', 'Docker', 'AWS', 'API Design', 'Testing', 'DevOps',
                'Project Management', 'Agile', 'Leadership', 'Communication', 'Data Analysis'
            ]),
            'proficiency_level' => fake()->randomElement(['beginner', 'intermediate', 'advanced', 'expert']),
            'years_of_experience' => fake()->numberBetween(0, 20),
            'endorsed_by_people_ids' => [],
            'endorsement_count' => 0,
        ];
    }

    public function expert(): self
    {
        return $this->state(fn (array $attributes) => [
            'proficiency_level' => 'expert',
            'years_of_experience' => fake()->numberBetween(10, 20),
        ]);
    }

    public function endorsed($count = 5): self
    {
        return $this->state(fn (array $attributes) => [
            'endorsed_by_people_ids' => array_map(fn() => fake()->numberBetween(1, 1000), range(1, $count)),
            'endorsement_count' => $count,
        ]);
    }
}
