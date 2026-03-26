<?php

namespace Tests\Feature;

use App\Models\TalentPass;
use App\Models\TalentPassSkill;

describe('TalentPassSkill Management', function () {
    it('creates a skill for talent pass', function () {
        $talentPass = TalentPass::factory()->create();
        $skill = TalentPassSkill::factory()->for($talentPass)->create();

        expect($skill)->toBeInstanceOf(TalentPassSkill::class)
            ->and($skill->talent_pass_id)->toBe($talentPass->id)
            ->and($skill->skill_name)->not->toBeEmpty();
    });

    it('creates multiple skills for one talent pass', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassSkill::factory()->count(5), 'skills')
            ->create();

        expect($talentPass->skills)->toHaveCount(5);
    });

    it('filters skills by proficiency level', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassSkill::factory()->state(['proficiency_level' => 'expert']), 'skills')
            ->has(TalentPassSkill::factory()->count(2)->state(['proficiency_level' => 'beginner']), 'skills')
            ->create();

        $expertSkills = $talentPass->skills()->where('proficiency_level', 'expert')->get();

        expect($expertSkills)->toHaveCount(1)
            ->and($expertSkills->first()->proficiency_level)->toBe('expert');
    });

    it('tracks years of experience for skills', function () {
        $skill = TalentPassSkill::factory()->create(['years_of_experience' => 5]);

        expect($skill->years_of_experience)->toBe(5);
    });
});

describe('Skill Endorsements', function () {
    it('adds endorsement to a skill', function () {
        $skill = TalentPassSkill::factory()->create(['endorsed_by_people_ids' => []]);
        $peopleId = 123;

        $skill->addEndorsement($peopleId);

        expect($skill->fresh()->endorsed_by_people_ids)->toContain($peopleId)
            ->and($skill->fresh()->endorsement_count)->toBe(1);
    });

    it('prevents duplicate endorsements', function () {
        $skill = TalentPassSkill::factory()->create(['endorsed_by_people_ids' => []]);
        $peopleId = 123;

        $skill->addEndorsement($peopleId);
        $skill->addEndorsement($peopleId);

        expect($skill->fresh()->endorsement_count)->toBe(1);
    });

    it('removes endorsement from skill', function () {
        $peopleId = 123;
        $skill = TalentPassSkill::factory()->create([
            'endorsed_by_people_ids' => [$peopleId],
            'endorsement_count' => 1,
        ]);

        $skill->removeEndorsement($peopleId);

        expect($skill->fresh()->endorsement_count)->toBe(0);
    });

    it('tracks multiple endorsements', function () {
        $skill = TalentPassSkill::factory()->endorsed(5)->create();

        expect($skill->endorsement_count)->toBe(5)
            ->and($skill->endorsed_by_people_ids)->toHaveCount(5);
    });
});

describe('Skill Sorting and Filtering', function () {
    it('sorts skills by endorsement count', function () {
        $talentPass = TalentPass::factory()->create();

        TalentPassSkill::factory()->for($talentPass)->endorsed(3)->create(['skill_name' => 'PHP']);
        TalentPassSkill::factory()->for($talentPass)->endorsed(5)->create(['skill_name' => 'Laravel']);
        TalentPassSkill::factory()->for($talentPass)->endorsed(1)->create(['skill_name' => 'Vue.js']);

        $sorted = $talentPass->skills()->orderByDesc('endorsement_count')->get();

        expect($sorted->pluck('endorsement_count')->toArray())->toBe([5, 3, 1]);
    });

    it('finds expert level skills', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassSkill::factory()->expert()->count(3), 'skills')
            ->create();

        $expertSkills = $talentPass->skills()->where('proficiency_level', 'expert')->get();

        expect($expertSkills)->toHaveCount(3);
    });
});
