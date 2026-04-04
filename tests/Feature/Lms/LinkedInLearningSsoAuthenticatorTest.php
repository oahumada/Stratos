<?php

use App\Services\Talent\Lms\Sso\LinkedInLearningSsoAuthenticator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

beforeEach(function () {
    config(['services.linkedin_learning.client_id' => 'test-client-id']);
    config(['services.linkedin_learning.client_secret' => 'test-client-secret']);
    config(['services.linkedin_learning.redirect_uri' => 'http://localhost/api/lms/sso/linkedin/callback']);
});

it('generates authorization url with pkce challenge', function () {
    $authenticator = new LinkedInLearningSsoAuthenticator();
    $result = $authenticator->getAuthorizationUrl();

    expect($result)
        ->toHaveKeys(['authorization_url', 'state', 'code_challenge'])
        ->and($result['authorization_url'])->toContain('client_id=test-client-id')
        ->and($result['authorization_url'])->toContain('code_challenge=')
        ->and($result['authorization_url'])->toContain('code_challenge_method=S256')
        ->and($result['state'])->toHaveLength(32);
});

it('stores state in cache during authorization', function () {
    $authenticator = new LinkedInLearningSsoAuthenticator();
    $result = $authenticator->getAuthorizationUrl();
    $state = $result['state'];

    $cached = Cache::get('lms_sso_linkedin_state_'.$state);

    expect($cached)
        ->toHaveKey('code_verifier')
        ->and($cached['code_verifier'])->toHaveLength(64);
});

it('validates state against cached value', function () {
    $authenticator = new LinkedInLearningSsoAuthenticator();
    $result = $authenticator->getAuthorizationUrl();
    $state = $result['state'];

    expect($authenticator->validateState($state))->toBeTrue();
    expect($authenticator->validateState('invalid_state'))->toBeFalse();
});

it('exchanges authorization code for access token', function () {
    Http::fake([
        'https://www.linkedin.com/oauth/v2/accessToken' => Http::response([
            'access_token' => 'test-access-token',
            'expires_in' => 3600,
            'token_type' => 'Bearer',
        ], 200),
        'https://api.linkedin.com/v2/me' => Http::response([
            'id' => 'linkedin-user-123',
            'email' => 'user@example.com',
            'localizedFirstName' => 'John',
        ], 200),
    ]);

    $authenticator = new LinkedInLearningSsoAuthenticator();
    $authResult = $authenticator->getAuthorizationUrl();
    $state = $authResult['state'];
    $codeVerifier = Cache::get('lms_sso_linkedin_state_'.$state)['code_verifier'];

    $result = $authenticator->handleCallback('auth-code-123', $state, $codeVerifier);

    expect($result)
        ->toHaveKeys(['provider_user_id', 'email', 'name', 'access_token', 'expires_in'])
        ->and($result['access_token'])->toBe('test-access-token')
        ->and($result['email'])->toBe('user@example.com')
        ->and($result['name'])->toBe('John');
});

it('rejects invalid state during callback', function () {
    Http::fake();

    $authenticator = new LinkedInLearningSsoAuthenticator();

    expect(function () use ($authenticator) {
        $authenticator->handleCallback('auth-code-123', 'invalid_state', 'code-verifier');
    })->toThrow(\RuntimeException::class, 'Invalid or expired state parameter');
});

it('returns provider name', function () {
    $authenticator = new LinkedInLearningSsoAuthenticator();

    expect($authenticator->getProviderName())->toBe('linkedin');
});

it('syncs user data successfully', function () {
    Http::fake([
        'https://api.linkedin.com/v2/me' => Http::response([
            'id' => 'linkedin-user-123',
            'email' => 'user@example.com',
            'localizedFirstName' => 'John',
        ], 200),
    ]);

    $authenticator = new LinkedInLearningSsoAuthenticator();
    $result = $authenticator->syncUserData('user-123', 'test-access-token');

    expect($result)->toBeTrue();
});

it('handles token exchange failure gracefully', function () {
    Http::fake([
        'https://www.linkedin.com/oauth/v2/accessToken' => Http::response([
            'error' => 'invalid_request',
        ], 400),
    ]);

    $authenticator = new LinkedInLearningSsoAuthenticator();
    $authResult = $authenticator->getAuthorizationUrl();
    $state = $authResult['state'];
    $codeVerifier = Cache::get('lms_sso_linkedin_state_'.$state)['code_verifier'];

    expect(function () use ($authenticator, $state, $codeVerifier) {
        $authenticator->handleCallback('invalid-code', $state, $codeVerifier);
    })->toThrow(\RuntimeException::class, 'Failed to exchange authorization code');
});

it('throws error when credentials are missing', function () {
    config(['services.linkedin_learning.client_id' => '']);
    config(['services.linkedin_learning.redirect_uri' => '']);

    $authenticator = new LinkedInLearningSsoAuthenticator();

    expect(function () use ($authenticator) {
        $authenticator->getAuthorizationUrl();
    })->toThrow(\RuntimeException::class, 'LinkedIn Learning credentials not configured');
});
