# SSO Integration Design for LMS (V2-04)

## Overview
SSO (Single Sign-On) integration allows users to authenticate with external LMS providers using OAuth 2.0 / SAML 2.0 flows without re-entering credentials.

## Supported Providers
- **LinkedIn Learning** (OAuth 2.0 with PKCE)
- **SuccessFactors** (SAML 2.0, future)

## Architecture

### Flow: User → Stratos → LinkedIn Learning → Back to Stratos

```
1. User clicks "Open in LinkedIn Learning"
2. LMS controller generates OAuth state + redirect_uri
3. Redirect to LinkedIn Learning OAuth endpoint
4. User authenticates with LinkedIn
5. LinkedIn redirects back with authorization code
6. Stratos exchanges code for access token
7. Stratos fetches user profile + learning data
8. User session established, learning progress synced
```

### Components

#### 1. LmsSsoAuthenticatorInterface
- Contract for SSO provider implementations
- Methods: `getAuthorizationUrl()`, `handleCallback()`, `syncUserData()`

#### 2. LinkedInLearningSSOAuthenticator
- Implements OAuth 2.0 flow (PKCE recommended)
- Endpoint: `POST /api/lms/sso/linkedin/authorize` → OAuth redirect URL
- Endpoint: `GET /api/lms/sso/linkedin/callback` → Verify code + sync progress

#### 3. LmsSsoService
- Orchestrates SSO flow
- Manages state/nonce validation
- Stores provider credentials securely (via encryption)

### Configuration

```env
LINKEDIN_LEARNING_CLIENT_ID=xxxxx
LINKEDIN_LEARNING_CLIENT_SECRET=xxxxx
LINKEDIN_LEARNING_REDIRECT_URI=https://stratos.app/api/lms/sso/linkedin/callback
```

## Criteria for Closure (V2-04)

✅ Design approved: This document + LmsSsoAuthenticatorInterface  
✅ PoC functional: LinkedInLearningSSOAuthenticator with OAuth 2.0 flow (authorize + callback endpoints)  
✅ E2E tests: Authentication flow validation, state validation, data sync  
✅ Security: PKCE + CSRF state validation + secure credential storage  

## Future Enhancements (Q3 2026)

- SuccessFactors SAML 2.0 integration
- Multi-provider dashboard
- Automatic credential rotation
- Audit logging for SSO events
