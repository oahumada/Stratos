<?php

use App\Models\Organization;
use App\Models\RedactionAuditTrail;
use App\Models\User;
use App\Services\RedactionMetricsService;
use App\Services\RedactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->organization = Organization::factory()->create();
    $this->user = User::factory()->create(['organization_id' => $this->organization->id]);
});

describe('RedactionService', function () {
    it('redacts emails by default', function () {
        $text = 'Contact me at john.doe@example.com for more info';
        $redacted = RedactionService::redactText($text);

        expect($redacted)->toContain('[REDACTED_EMAIL]');
        expect($redacted)->not->toContain('john.doe@example.com');
    });

    it('redacts phone numbers', function () {
        $text = 'Call me at (555) 123-4567 or 555-123-4567';
        $redacted = RedactionService::redactText($text);

        expect($redacted)->toContain('[REDACTED_PHONE]');
    });

    it('redacts SSN patterns', function () {
        $text = 'SSN: 123-45-6789 or 12345678';
        $redacted = RedactionService::redactText($text);

        expect($redacted)->toContain('[REDACTED_SSN]');
    });

    it('redacts credit card numbers', function () {
        $text = 'Payment with card 1234-5678-9012-3456';
        $redacted = RedactionService::redactText($text);

        expect($redacted)->toContain('[REDACTED_CC]');
    });

    it('redacts API tokens', function () {
        $text = 'Token: sk_live_XXXXXXXXXXXXXXXXXXXXXXXX';
        $redacted = RedactionService::redactText($text);

        expect($redacted)->toContain('[REDACTED_TOKEN]');
    });

    it('redacts query parameter tokens', function () {
        $text = 'API call: /api/users?api_key=secret123456789012&user=john';
        $redacted = RedactionService::redactText($text);

        expect($redacted)->toContain('[REDACTED]');
        expect($redacted)->not->toContain('secret123456789012');
    });

    it('redacts bearer tokens', function () {
        $text = 'Authorization: Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9';
        $redacted = RedactionService::redactText($text);

        expect($redacted)->toContain('Bearer [REDACTED_TOKEN]');
    });

    it('allows selective redaction by type', function () {
        $text = 'Email: john@example.com Phone: 555-123-4567';

        // Only redact emails
        $redacted = RedactionService::redactText($text, ['email']);

        expect($redacted)->toContain('[REDACTED_EMAIL]');
        expect($redacted)->not->toContain('[REDACTED_PHONE]');
        expect($redacted)->toContain('555-123-4567');
    });

    it('redacts arrays recursively', function () {
        $data = [
            'user' => 'john@example.com',
            'contact' => [
                'email' => 'jane@example.com',
                'message' => 'Call 555-123-4567',
            ],
            'token' => 'sk_live_XXXXXXXXXXXXXXXX',
        ];

        $redacted = RedactionService::redactArray($data);

        expect($redacted['user'])->toContain('[REDACTED_EMAIL]');
        expect($redacted['contact']['email'])->toContain('[REDACTED_EMAIL]');
        expect($redacted['contact']['message'])->toContain('[REDACTED_PHONE]');
        expect($redacted['token'])->toContain('[REDACTED_TOKEN]');
    });

    it('detects PII without redacting', function () {
        $text = 'Contact: john@example.com or 555-123-4567';

        expect(RedactionService::containsPii($text))->toBeTrue();
        expect(RedactionService::containsPii('No PII here'))->toBeFalse();
    });

    it('returns specific PII matches', function () {
        $text = 'Email: john@test.com and jane@test.com Phone: 555-123-4567';

        $detected = RedactionService::detectPii($text);

        expect($detected)->toHaveKey('email');
        expect($detected['email'])->toHaveCount(2);
        expect($detected)->toHaveKey('phone');
    });

    it('allows setting enabled types globally', function () {
        RedactionService::setEnabledTypes(['email']);

        $text = 'Email: john@example.com Phone: 555-123-4567';
        $redacted = RedactionService::redactText($text);

        expect($redacted)->toContain('[REDACTED_EMAIL]');
        expect($redacted)->toContain('555-123-4567'); // Not redacted

        RedactionService::resetEnabledTypes();
    });

    it('gets current enabled types', function () {
        $types = RedactionService::getEnabledTypes();

        expect($types)->toContain('email');
        expect($types)->toContain('phone');
    });

    it('redacts using the generic redact helper for strings', function () {
        $text = 'Email: john@example.com';

        $redacted = RedactionService::redact($text);

        expect($redacted)->toBeString();
        expect($redacted)->toContain('[REDACTED_EMAIL]');
        expect($redacted)->not->toContain('john@example.com');
    });

    it('redacts using the generic redact helper for arrays', function () {
        $data = [
            'user' => 'john@example.com',
            'nested' => [
                'phone' => '555-123-4567',
            ],
        ];

        $redacted = RedactionService::redact($data);

        expect($redacted['user'])->toContain('[REDACTED_EMAIL]');
        expect($redacted['nested']['phone'])->toContain('[REDACTED_PHONE]');
    });
});

describe('RedactionAuditTrail Models', function () {
    it('logs redaction to audit trail', function () {
        $this->actingAs($this->user);

        // Trigger redaction
        $text = 'Test email: john@example.com and phone 555-123-4567';
        RedactionService::redactText($text);

        // Check audit trail created
        expect(RedactionAuditTrail::count())->toBeGreaterThan(0);

        $trail = RedactionAuditTrail::latest()->first();
        expect($trail->organization_id)->toEqual($this->organization->id);
        expect($trail->count)->toBeGreaterThan(0);
        expect($trail->redaction_types)->toContain('email');
    });

    it('records user and organization in audit', function () {
        $this->actingAs($this->user);

        RedactionService::redactText('Email: test@example.com');

        $trail = RedactionAuditTrail::latest()->first();
        expect($trail->user_id)->toEqual($this->user->id);
        expect($trail->organization_id)->toEqual($this->organization->id);
    });

    it('stores original text hash', function () {
        $this->actingAs($this->user);

        $text = 'Sensitive: john@example.com';
        RedactionService::redactText($text);

        $trail = RedactionAuditTrail::latest()->first();
        expect($trail->original_hash)->toEqual(hash('sha256', $text));
    });

    it('stores redaction context', function () {
        $this->actingAs($this->user);

        RedactionService::redactText('Email: test@example.com');

        $trail = RedactionAuditTrail::latest()->first();
        expect($trail->context)->not->toBeNull(); // HTTP context in tests
    });
});

describe('RedactionMetricsService', function () {
    beforeEach(function () {
        $this->service = new RedactionMetricsService();
    });

    it('gets organization metrics', function () {
        $this->actingAs($this->user);

        // Create some redactions
        RedactionService::redactText('Email: test1@example.com');
        RedactionService::redactText('Phone: 555-123-4567 Email: test2@example.com');

        $metrics = $this->service->getOrganizationMetrics($this->organization->id);

        expect($metrics)->toHaveKey('summary');
        expect($metrics['summary']['total_events'])->toBeGreaterThan(0);
        expect($metrics['summary']['total_redactions'])->toBeGreaterThan(0);
    });

    it('tracks redactions by type', function () {
        $this->actingAs($this->user);

        RedactionService::redactText('Email: test@example.com');
        RedactionService::redactText('Phone: 555-123-4567');

        $breakdown = $this->service->getRedactionsByType($this->organization->id);

        expect($breakdown)->toHaveKey('email');
        expect($breakdown)->toHaveKey('phone');
    });

    it('checks text for PII', function () {
        $result = $this->service->checkTextForPii('Contact: john@test.com');

        expect($result['contains_pii'])->toBeTrue();
        expect($result['types_found'])->toContain('email');

        $result2 = $this->service->checkTextForPii('No sensitive data');
        expect($result2['contains_pii'])->toBeFalse();
    });

    it('calculates redaction coverage score', function () {
        $this->actingAs($this->user);

        for ($i = 0; $i < 5; $i++) {
            RedactionService::redactText("Email: test{$i}@example.com");
        }

        $score = $this->service->getRedactionCoverageScore($this->organization->id);

        expect($score)->toBeGreaterThanOrEqual(0);
        expect($score)->toBeLessThanOrEqual(1);
    });

    it('gets daily trend', function () {
        $this->actingAs($this->user);

        RedactionService::redactText('Test@example.com');

        $trend = $this->service->getDailyTrend($this->organization->id, days: 7);

        expect($trend)->toBeArray();
        expect($trend)->not->toBeEmpty();
    });

    it('invalidates cache correctly', function () {
        $this->actingAs($this->user);

        // Get metrics (caches)
        $this->service->getOrganizationMetrics($this->organization->id);

        // Invalidate
        $this->service->invalidateMetricsCache($this->organization->id);

        // Should not error when getting again
        $metrics = $this->service->getOrganizationMetrics($this->organization->id);
        expect($metrics)->toBeArray();
    });
});
