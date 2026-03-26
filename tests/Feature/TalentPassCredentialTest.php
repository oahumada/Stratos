<?php

namespace Tests\Feature;

use App\Models\TalentPass;
use App\Models\TalentPassCredential;

describe('TalentPassCredential Management', function () {
    it('creates a credential for talent pass', function () {
        $talentPass = TalentPass::factory()->create();
        $credential = TalentPassCredential::factory()->for($talentPass)->create();

        expect($credential)->toBeInstanceOf(TalentPassCredential::class)
            ->and($credential->talent_pass_id)->toBe($talentPass->id)
            ->and($credential->credential_name)->not->toBeEmpty();
    });

    it('creates multiple credentials for one talent pass', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassCredential::factory()->count(4), 'credentials')
            ->create();

        expect($talentPass->credentials)->toHaveCount(4);
    });

    it('stores credential metadata correctly', function () {
        $credential = TalentPassCredential::factory()->create([
            'issuer' => 'AWS',
            'credential_url' => 'https://example.com/cert',
            'credential_id' => 'cert-12345',
        ]);

        expect($credential->issuer)->toBe('AWS')
            ->and($credential->credential_url)->toBe('https://example.com/cert')
            ->and($credential->credential_id)->toBe('cert-12345');
    });

    it('soft deletes credentials', function () {
        $credential = TalentPassCredential::factory()->create();
        $id = $credential->id;

        $credential->delete();

        expect(TalentPassCredential::find($id))->toBeNull()
            ->and(TalentPassCredential::withTrashed()->find($id))->toBeInstanceOf(TalentPassCredential::class);
    });
});

describe('Credential Expiry Tracking', function () {
    it('identifies expired credentials', function () {
        $expired = TalentPassCredential::factory()->expired()->create();
        $valid = TalentPassCredential::factory()->noExpiry()->create();

        expect($expired->isExpired())->toBeTrue()
            ->and($valid->isExpired())->toBeFalse();
    });

    it('identifies expiring soon credentials', function () {
        $expiringIn30Days = TalentPassCredential::factory()->create([
            'expiry_date' => now()->addDays(15),
        ]);

        $expiringIn90Days = TalentPassCredential::factory()->create([
            'expiry_date' => now()->addDays(80),
        ]);

        expect($expiringIn30Days->isExpiringSoon())->toBeTrue()
            ->and($expiringIn90Days->isExpiringSoon())->toBeFalse();
    });

    it('handles credentials without expiry date', function () {
        $permanent = TalentPassCredential::factory()->noExpiry()->create();

        expect($permanent->expiry_date)->toBeNull()
            ->and($permanent->isExpired())->toBeFalse();
    });
});

describe('Credential Featured Status', function () {
    it('marks credentials as featured', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassCredential::factory()->state(['is_featured' => true]), 'credentials')
            ->has(TalentPassCredential::factory()->count(3)->state(['is_featured' => false]), 'credentials')
            ->create();

        $featured = $talentPass->credentials()->where('is_featured', true)->get();

        expect($featured)->toHaveCount(1);
    });

    it('sorts by featured status', function () {
        $talentPass = TalentPass::factory()
            ->has(TalentPassCredential::factory()->count(2)->state(['is_featured' => true]), 'credentials')
            ->has(TalentPassCredential::factory()->count(3)->state(['is_featured' => false]), 'credentials')
            ->create();

        $sorted = $talentPass->credentials()->orderByDesc('is_featured')->get();

        expect($sorted->pluck('is_featured')->toArray())->toBe([true, true, false, false, false]);
    });
});

describe('Credential Date Handling', function () {
    it('stores issued and expiry dates correctly', function () {
        $issuedDate = now()->subYears(2);
        $expiryDate = now()->addYears(3);

        $credential = TalentPassCredential::factory()->create([
            'issued_date' => $issuedDate,
            'expiry_date' => $expiryDate,
        ]);

        expect($credential->issued_date->format('Y-m-d'))->toBe($issuedDate->format('Y-m-d'))
            ->and($credential->expiry_date->format('Y-m-d'))->toBe($expiryDate->format('Y-m-d'));
    });

    it('calculates credential age', function () {
        $issuedDate = now()->subMonths(6);
        $credential = TalentPassCredential::factory()->create(['issued_date' => $issuedDate]);

        $ageInMonths = $credential->issued_date->diffInMonths(now());

        expect($ageInMonths)->toBeGreaterThanOrEqual(5)
            ->and($ageInMonths)->toBeLessThanOrEqual(7);
    });
});
