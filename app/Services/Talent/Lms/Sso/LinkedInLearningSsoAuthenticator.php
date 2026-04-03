<?php

namespace App\Services\Talent\Lms\Sso;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LinkedInLearningSsoAuthenticator implements LmsSsoAuthenticatorInterface
{
    protected string $clientId;

    protected string $clientSecret;

    protected string $redirectUri;

    private const OAUTH_AUTHORIZE_URL = 'https://www.linkedin.com/oauth/v2/authorization';

    private const OAUTH_TOKEN_URL = 'https://www.linkedin.com/oauth/v2/accessToken';

    private const LEARNER_API_URL = 'https://api.linkedin.com/v2/me';

    private const CACHE_PREFIX = 'lms_sso_linkedin_';

    private const STATE_EXPIRY = 600; // 10 minutes

    public function __construct()
    {
        $this->clientId = config('services.linkedin_learning.client_id', '');
        $this->clientSecret = config('services.linkedin_learning.client_secret', '');
        $this->redirectUri = config('services.linkedin_learning.redirect_uri', url('/api/lms/sso/linkedin/callback'));
    }

    public function getAuthorizationUrl(): array
    {
        if (empty($this->clientId) || empty($this->redirectUri)) {
            throw new \RuntimeException('LinkedIn Learning credentials not configured');
        }

        $state = Str::random(32);
        $codeVerifier = Str::random(64);
        $codeChallenge = $this->generateCodeChallenge($codeVerifier);

        Cache::put(self::CACHE_PREFIX.'state_'.$state, [
            'code_verifier' => $codeVerifier,
            'created_at' => now()->toDateTimeString(),
        ], self::STATE_EXPIRY);

        $params = [
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'state' => $state,
            'scope' => 'r_basicprofile r_emailaddress',
            'code_challenge' => $codeChallenge,
            'code_challenge_method' => 'S256',
        ];

        $authorizationUrl = self::OAUTH_AUTHORIZE_URL.'?'.http_build_query($params);

        return [
            'authorization_url' => $authorizationUrl,
            'state' => $state,
            'code_challenge' => $codeChallenge,
        ];
    }

    public function handleCallback(string $code, string $state, string $codeVerifier): array
    {
        if (!$this->validateState($state)) {
            throw new \RuntimeException('Invalid or expired state parameter');
        }

        try {
            $response = Http::asForm()->post(self::OAUTH_TOKEN_URL, [
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $this->redirectUri,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'code_verifier' => $codeVerifier,
            ]);

            if (!$response->successful()) {
                Log::error('LinkedIn OAuth token exchange failed', [
                    'status' => $response->status(),
                    'error' => $response->json(),
                ]);
                throw new \RuntimeException('Failed to exchange authorization code');
            }

            $tokenData = $response->json();
            $accessToken = $tokenData['access_token'] ?? null;

            if (!$accessToken) {
                throw new \RuntimeException('No access token in response');
            }

            $userProfile = $this->fetchUserProfile($accessToken);

            Cache::forget(self::CACHE_PREFIX.'state_'.$state);

            return [
                'provider_user_id' => $userProfile['id'] ?? 'li_'.$code,
                'email' => $userProfile['email'] ?? '',
                'name' => $userProfile['name'] ?? 'LinkedIn User',
                'access_token' => $accessToken,
                'expires_in' => $tokenData['expires_in'] ?? 3600,
            ];
        } catch (\Exception $e) {
            Log::error('LinkedIn SSO callback failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function syncUserData(string $userId, string $accessToken): bool
    {
        try {
            $profile = $this->fetchUserProfile($accessToken);

            Log::info('LinkedIn learning data synced', [
                'user_id' => $userId,
                'provider_user_id' => $profile['id'] ?? null,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::warning('LinkedIn data sync failed', ['error' => $e->getMessage()]);

            return false;
        }
    }

    public function validateState(string $state): bool
    {
        $cached = Cache::get(self::CACHE_PREFIX.'state_'.$state);

        return $cached !== null;
    }

    public function getProviderName(): string
    {
        return 'linkedin';
    }

    protected function fetchUserProfile(string $accessToken): array
    {
        try {
            $response = Http::withToken($accessToken)->get(self::LEARNER_API_URL);

            if (!$response->successful()) {
                throw new \RuntimeException('Failed to fetch user profile from LinkedIn');
            }

            $data = $response->json();

            return [
                'id' => $data['id'] ?? null,
                'email' => $data['email'] ?? null,
                'name' => $data['localizedFirstName'] ?? 'LinkedIn User',
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch LinkedIn user profile', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function generateCodeChallenge(string $codeVerifier): string
    {
        return rtrim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
    }
}
