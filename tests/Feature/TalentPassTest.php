<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\People;
use App\Models\TalentPass;
use App\Models\TalentPassSkill;
use App\Models\TalentPassCredential;
use App\Models\TalentPassExperience;

describe('TalentPass CRUD Operations', function () {
    it('creates a new talent pass', function () {
        $org = Organization::factory()->create();
        $person = People::factory()->create(['organization_id' => $org->id]);

        $talentPass = TalentPass::factory()->for($org)->for($person, 'person')->create();

        expect($talentPass)->toBeInstanceOf(TalentPass::class)
            ->and($talentPass->organization_id)->toBe($org->id)
            ->and($talentPass->people_id)->toBe($person->id)
            ->and($talentPass->status)->toBe('draft');
    });

    it('retrieves talent pass with relationships', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassSkill::factory()->count(3), 'skills')
            ->has(TalentPassCredential::factory()->count(2), 'credentials')
            ->has(TalentPassExperience::factory()->count(2), 'experiences')
            ->create();

        $retrieved = TalentPass::find($talentPass->id);

        expect($retrieved->skills)->toHaveCount(3)
            ->and($retrieved->credentials)->toHaveCount(2)
            ->and($retrieved->experiences)->toHaveCount(2);
    });

    it('updates a talent pass', function () {
        $talentPass = TalentPass::factory()->create();
        $newTitle = 'Updated Title';

        $talentPass->update(['title' => $newTitle]);

        expect($talentPass->fresh()->title)->toBe($newTitle);
    });

    it('soft deletes a talent pass', function () {
        $talentPass = TalentPass::factory()->create();
        $id = $talentPass->id;

        $talentPass->delete();

        expect(TalentPass::find($id))->toBeNull()
            ->and(TalentPass::withTrashed()->find($id))->toBeInstanceOf(TalentPass::class);
    });
});

describe('TalentPass Status Management', function () {
    it('publishes a draft talent pass', function () {
        $talentPass = TalentPass::factory()->draft()->create();

        $talentPass->publish();

        expect($talentPass->fresh()->status)->toBe('published')
            ->and($talentPass->fresh()->isPublished())->toBeTrue();
    });

    it('archives a talent pass', function () {
        $talentPass = TalentPass::factory()->published()->create();

        $talentPass->archive();

        expect($talentPass->fresh()->status)->toBe('archived');
    });

    it('checks if draft talent pass can be edited', function () {
        $talentPass = TalentPass::factory()->draft()->create();

        expect($talentPass->isDraft())->toBeTrue()
            ->and($talentPass->canBeEdited())->toBeTrue();
    });

    it('prevents editing published talent pass', function () {
        $talentPass = TalentPass::factory()->published()->create();

        expect($talentPass->canBeEdited())->toBeFalse();
    });
});

describe('TalentPass Visibility Scopes', function () {
    it('filters by organization', function () {
        $org1 = Organization::factory()->create();
        $org2 = Organization::factory()->create();

        TalentPass::factory()->count(3)->for($org1)->create();
        TalentPass::factory()->count(2)->for($org2)->create();

        $results = TalentPass::byOrganization($org1->id)->get();

        expect($results)->toHaveCount(3)
            ->and($results->every(fn ($tp) => $tp->organization_id === $org1->id))->toBeTrue();
    });

    it('filters published talent passes', function () {
        TalentPass::factory()->draft()->count(2)->create();
        TalentPass::factory()->published()->count(3)->create();

        $results = TalentPass::published()->get();

        expect($results)->toHaveCount(3)
            ->and($results->every(fn ($tp) => $tp->status === 'published'))->toBeTrue();
    });

    it('filters public talent passes', function () {
        TalentPass::factory()->private()->count(2)->create();
        TalentPass::factory()->public()->count(3)->create();

        $results = TalentPass::public()->get();

        expect($results)->toHaveCount(3)
            ->and($results->every(fn ($tp) => $tp->visibility === 'public'))->toBeTrue();
    });
});

describe('TalentPass View Tracking', function () {
    it('increments view count', function () {
        $talentPass = TalentPass::factory()->create(['view_count' => 0]);

        $talentPass->increment('view_count');

        expect($talentPass->fresh()->view_count)->toBe(1);
    });

    it('tracks featured status', function () {
        $featured = TalentPass::factory()->create(['is_featured' => true]);
        $notFeatured = TalentPass::factory()->create(['is_featured' => false]);

        expect($featured->is_featured)->toBeTrue()
            ->and($notFeatured->is_featured)->toBeFalse();
    });
});
