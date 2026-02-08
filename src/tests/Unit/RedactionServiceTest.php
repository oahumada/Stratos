<?php

use App\Services\RedactionService;
use Tests\TestCase;

uses(TestCase::class);

it('redacts emails, tokens and ssn in text', function () {
    $text = 'Contact me at user@example.com and use token ABCDEFGHIJKLMNOPQRSTUVWXYZ12345 and SSN 123-45-6789.';

    $r = RedactionService::redactText($text);

    expect($r)->not->toContain('user@example.com');
    expect($r)->toContain('[REDACTED_EMAIL]');
    expect($r)->not->toContain('ABCDEFGHIJKLMNOPQRSTUVWXYZ12345');
    expect($r)->toContain('[REDACTED_TOKEN]');
    expect($r)->not->toContain('123-45-6789');
    expect($r)->toContain('[REDACTED_SSN]');
});

it('redacts strings inside arrays recursively', function () {
    $data = [
        'email' => 'a@b.com',
        'nested' => [
            'token' => 'LONGTOKEN_01234567890123456789',
        ],
    ];

    $red = RedactionService::redactArray($data);

    expect($red['email'])->toBe('[REDACTED_EMAIL]');
    expect($red['nested']['token'])->toContain('[REDACTED_TOKEN]');
});
