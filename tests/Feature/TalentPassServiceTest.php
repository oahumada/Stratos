<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\People;
use App\Models\TalentPass;
use App\Models\TalentPassSkill;
use App\Models\TalentPassCredential;
use App\Models\TalentPassExperience;
use App\Services\TalentPassService;

describe('TalentPassService CRUD Operations', function () {
    it('creates a new talent pass with service', function () {
        $service = new TalentPassService();
        $org = Organization::factory()->create();
        $person = People::factory()->create(['organization_id' => $org->id]);

        $talentPass = $service->create($org->id, $person->id, [
            'title' => 'Test CV',
            'summary' => 'Test summary',
        ]);

        expect($talentPass)->toBeInstanceOf(TalentPass::class)
            ->and($talentPass->organization_id)->toBe($org->id)
            ->and($talentPass->people_id)->toBe($person->id)
            ->and($talentPass->status)->toBe('draft');
    });

    it('updates talent pass details', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->create();

        $updated = $service->update($talentPass->id, [
            'title' => 'Updated Title',
            'summary' => 'Updated summary',
        ]);

        expect($updated->title)->toBe('Updated Title')
            ->and($updated->summary)->toBe('Updated summary');
    });

    it('throws exception when updating non-existing talent pass', function () {
        $service = new TalentPassService();

        $this->expectException(\Exception::class);
        $service->update(999, ['title' => 'Test']);
    });

    it('retrieves talent pass by organization', function () {
        $service = new TalentPassService();
        $org = Organization::factory()->create();

        TalentPass::factory()->count(3)->for($org)->create();
        TalentPass::factory()->count(2)->for(Organization::factory())->create();

        $results = $service->getByOrganization($org->id);

        expect($results)->toHaveCount(3)
            ->and($results->every(fn ($tp) => $tp->organization_id === $org->id))->toBeTrue();
    });
});

describe('TalentPassService Skill Management', function () {
    it('adds skill to talent pass', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->create();

        $skill = $service->addSkill($talentPass->id, [
            'skill_name' => 'PHP',
            'proficiency_level' => 'expert',
            'years_of_experience' => 10,
        ]);

        expect($skill)->toBeInstanceOf(TalentPassSkill::class)
            ->and($skill->skill_name)->toBe('PHP')
            ->and($talentPass->skills()->count())->toBe(1);
    });

    it('adds multiple skills with service', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->create();

        $service->addSkill($talentPass->id, ['skill_name' => 'PHP', 'proficiency_level' => 'expert']);
        $service->addSkill($talentPass->id, ['skill_name' => 'Laravel', 'proficiency_level' => 'advanced']);
        $service->addSkill($talentPass->id, ['skill_name' => 'Vue.js', 'proficiency_level' => 'intermediate']);

        expect($talentPass->fresh()->skills()->count())->toBe(3);
    });
});

describe('TalentPassService Credential Management', function () {
    it('adds credential to talent pass', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->create();

        $credential = $service->addCredential($talentPass->id, [
            'credential_name' => 'AWS Solutions Architect',
            'issuer' => 'AWS',
            'issued_date' => now()->subYears(2),
        ]);

        expect($credential)->toBeInstanceOf(TalentPassCredential::class)
            ->and($credential->credential_name)->toBe('AWS Solutions Architect')
            ->and($talentPass->credentials()->count())->toBe(1);
    });

    it('adds credentials with expiry dates', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->create();

        $expiryDate = now()->addYears(2);
        $credential = $service->addCredential($talentPass->id, [
            'credential_name' => 'Kubernetes Certified',
            'issuer' => 'CNCF',
            'issued_date' => now()->subYears(1),
            'expiry_date' => $expiryDate,
        ]);

        expect($credential->expiry_date->format('Y-m-d'))->toBe($expiryDate->format('Y-m-d'));
    });
});

describe('TalentPassService Experience Management', function () {
    it('adds experience to talent pass', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->create();

        $experience = $service->addExperience($talentPass->id, [
            'job_title' => 'Senior Developer',
            'company' => 'Tech Corp',
            'start_date' => now()->subYears(2),
            'end_date' => now()->subMonths(6),
        ]);

        expect($experience)->toBeInstanceOf(TalentPassExperience::class)
            ->and($experience->job_title)->toBe('Senior Developer')
            ->and($talentPass->experiences()->count())->toBe(1);
    });

    it('adds current employment without end date', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->create();

        $experience = $service->addExperience($talentPass->id, [
            'job_title' => 'Lead Architect',
            'company' => 'StartUp Inc',
            'start_date' => now()->subMonths(8),
            'is_current' => true,
        ]);

        expect($experience->is_current)->toBeTrue()
            ->and($experience->end_date)->toBeNull();
    });
});

describe('TalentPassService Publishing', function () {
    it('publishes draft talent pass', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->draft()->create();

        $service->publish($talentPass->id);

        expect($talentPass->fresh()->status)->toBe('published');
    });
});

describe('TalentPassService Archiving', function () {
    it('archives published talent pass', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->published()->create();

        $service->archive($talentPass->id);

        expect($talentPass->fresh()->status)->toBe('archived');
    });

    it('can archive any non-deleted talent pass', function () {
        $service = new TalentPassService();
        $drafted = TalentPass::factory()->draft()->create();
        $published = TalentPass::factory()->published()->create();

        $service->archive($drafted->id);
        $service->archive($published->id);

        expect($drafted->fresh()->status)->toBe('archived')
            ->and($published->fresh()->status)->toBe('archived');
    });
});

describe('TalentPassService View Tracking', function () {
    it('records view on talent pass', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->create(['view_count' => 0]);

        $service->recordView($talentPass);

        expect($talentPass->fresh()->view_count)->toBe(1);
    });

    it('increments view count on multiple views', function () {
        $service = new TalentPassService();
        $talentPass = TalentPass::factory()->create(['view_count' => 0]);

        $service->recordView($talentPass);
        $service->recordView($talentPass);
        $service->recordView($talentPass);

        expect($talentPass->fresh()->view_count)->toBe(3);
    });
});

describe('TalentPassService Cloning', function () {
    it('clones talent pass with all relationships', function () {
        $service = new TalentPassService();
        $original = TalentPass::factory()
            ->has(TalentPassSkill::factory()->count(2), 'skills')
            ->has(TalentPassCredential::factory()->count(2), 'credentials')
            ->has(TalentPassExperience::factory()->count(2), 'experiences')
            ->create(['status' => 'published']);

        $cloned = $service->clone($original->id);

        expect($cloned)->toBeInstanceOf(TalentPass::class)
            ->and($cloned->id)->not->toBe($original->id)
            ->and($cloned->status)->toBe('draft')
            ->and($cloned->skills()->count())->toBe(2)
            ->and($cloned->credentials()->count())->toBe(2)
            ->and($cloned->experiences()->count())->toBe(2);
    });

    it('cloned talent pass maintains same organization', function () {
        $service = new TalentPassService();
        $original = TalentPass::factory()->create();

        $cloned = $service->clone($original->id);

        expect($cloned->organization_id)->toBe($original->organization_id)
            ->and($cloned->people_id)->toBe($original->people_id);
    });
});

describe('TalentPassService Discovery', function () {
    it('retrieves public published talent passes', function () {
        $service = new TalentPassService();

        TalentPass::factory()->count(2)->public()->published()->create();
        TalentPass::factory()->count(3)->private()->published()->create();
        TalentPass::factory()->count(2)->public()->draft()->create();

        $results = $service->getPublicPublished();

        expect($results)->toHaveCount(2);
    });
});
