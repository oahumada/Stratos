<?php

namespace Tests\Feature;

use App\Models\TalentPass;
use App\Models\TalentPassExperience;

describe('TalentPassExperience Management', function () {
    it('creates an experience for talent pass', function () {
        $talentPass = TalentPass::factory()->create();
        $experience = TalentPassExperience::factory()->for($talentPass)->create();

        expect($experience)->toBeInstanceOf(TalentPassExperience::class)
            ->and($experience->talent_pass_id)->toBe($talentPass->id)
            ->and($experience->job_title)->not->toBeEmpty();
    });

    it('creates multiple experiences for one talent pass', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassExperience::factory()->count(5), 'experiences')
            ->create();

        expect($talentPass->experiences)->toHaveCount(5);
    });

    it('stores experience metadata correctly', function () {
        $experience = TalentPassExperience::factory()->create([
            'job_title' => 'Senior Software Engineer',
            'company' => 'Acme Corp',
            'location' => 'San Francisco, CA',
            'employment_type' => 'full-time',
        ]);

        expect($experience->job_title)->toBe('Senior Software Engineer')
            ->and($experience->company)->toBe('Acme Corp')
            ->and($experience->location)->toBe('San Francisco, CA')
            ->and($experience->employment_type)->toBe('full-time');
    });

    it('soft deletes experiences', function () {
        $experience = TalentPassExperience::factory()->create();
        $id = $experience->id;

        $experience->delete();

        expect(TalentPassExperience::find($id))->toBeNull()
            ->and(TalentPassExperience::withTrashed()->find($id))->toBeInstanceOf(TalentPassExperience::class);
    });
});

describe('Experience Duration Calculations', function () {
    it('calculates duration in months', function () {
        $startDate = now()->subMonths(12);
        $endDate = now();

        $experience = TalentPassExperience::factory()->create([
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_current' => false,
        ]);

        $durationMonths = $experience->getDurationInMonths();

        expect($durationMonths)->toBeGreaterThanOrEqual(11)
            ->and($durationMonths)->toBeLessThanOrEqual(13);
    });

    it('formats duration as readable string', function () {
        $startDate = now()->subYears(2)->subMonths(3);

        $experience = TalentPassExperience::factory()->create([
            'start_date' => $startDate,
            'is_current' => true,
        ]);

        $formatted = $experience->getDurationFormatted();

        expect($formatted)->toContain('year') || $formatted->toContain('2');
    });

    it('handles current positions without end date', function () {
        $startDate = now()->subMonths(6);

        $experience = TalentPassExperience::factory()->current()->create([
            'start_date' => $startDate,
        ]);

        expect($experience->is_current)->toBeTrue()
            ->and($experience->end_date)->toBeNull();
    });
});

describe('Experience Type and Status', function () {
    it('filters by employment type', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassExperience::factory()->state(['employment_type' => 'full-time']), 'experiences')
            ->has(TalentPassExperience::factory()->count(2)->state(['employment_type' => 'contract']), 'experiences')
            ->create();

        $fullTime = $talentPass->experiences()->where('employment_type', 'full-time')->get();
        $contract = $talentPass->experiences()->where('employment_type', 'contract')->get();

        expect($fullTime)->toHaveCount(1)
            ->and($contract)->toHaveCount(2);
    });

    it('identifies current employment', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassExperience::factory()->current(), 'experiences')
            ->has(TalentPassExperience::factory()->count(3), 'experiences')
            ->create();

        $currentRole = $talentPass->experiences()->where('is_current', true)->first();

        expect($currentRole)->not->toBeNull()
            ->and($currentRole->is_current)->toBeTrue();
    });

    it('sorts experiences by start date', function () {
        $talentPass = TalentPass::factory()->create();

        TalentPassExperience::factory()->for($talentPass)->create(['start_date' => now()->subYears(1)]);
        TalentPassExperience::factory()->for($talentPass)->create(['start_date' => now()->subMonths(6)]);
        TalentPassExperience::factory()->for($talentPass)->create(['start_date' => now()->subMonths(3)]);

        $sorted = $talentPass->experiences()->orderByDesc('start_date')->get();
        $dates = $sorted->pluck('start_date')->toArray();

        expect($dates[0]->timestamp)->toBeGreaterThan($dates[1]->timestamp)
            ->and($dates[1]->timestamp)->toBeGreaterThan($dates[2]->timestamp);
    });
});

describe('Experience Location Tracking', function () {
    it('stores location information', function () {
        $experience = TalentPassExperience::factory()->create([
            'location' => 'New York, NY',
        ]);

        expect($experience->location)->toBe('New York, NY');
    });

    it('identifies remote work', function () {
        $remote = TalentPassExperience::factory()->remote()->create();
        $onsite = TalentPassExperience::factory()->create(['location' => 'Boston, MA']);

        expect($remote->location)->toBe('Remote')
            ->and($onsite->location)->not->toBe('Remote');
    });

    it('filters experiences by location', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassExperience::factory()->remote()->count(2), 'experiences')
            ->has(TalentPassExperience::factory()->count(3)->state(['location' => 'San Francisco, CA']), 'experiences')
            ->create();

        $remoteExperiences = $talentPass->experiences()->where('location', 'Remote')->get();

        expect($remoteExperiences)->toHaveCount(2);
    });
});

describe('Experience Description', function () {
    it('stores experience description', function () {
        $description = 'Led team of developers building scalable microservices';
        $experience = TalentPassExperience::factory()->create(['description' => $description]);

        expect($experience->description)->toBe($description);
    });

    it('handles empty descriptions', function () {
        $experience = TalentPassExperience::factory()->create(['description' => '']);

        expect($experience->description)->toBeEmpty();
    });
});
