<?php

namespace App\Services\Talent\Lms\Sso;

interface LmsSsoAuthenticatorInterface
{
    /**
     * Get OAuth authorization URL with PKCE challenge.
     *
     * @return array{
     *   authorization_url: string,
     *   state: string,
     *   code_challenge: string
     * }
     */
    public function getAuthorizationUrl(): array;

    /**
     * Handle OAuth callback and exchange code for access token.
     *
     * @return array{
     *   provider_user_id: string,
     *   email: string,
     *   name: string,
     *   access_token: string,
     *   expires_in: int
     * }
     */
    public function handleCallback(string $code, string $state, string $codeVerifier): array;

    /**
     * Sync learning data from provider to Stratos.
     */
    public function syncUserData(string $userId, string $accessToken): bool;

    /**
     * Validate state and nonce for CSRF protection.
     */
    public function validateState(string $state): bool;

    /**
     * Get provider name (linkedin, successfactors, etc).
     */
    public function getProviderName(): string;
}
