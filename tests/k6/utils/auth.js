/**
 * Stratos k6 — Auth helpers
 *
 * Handles CSRF + Fortify session login for all k6 scenarios.
 * Relies on environment variables:
 *   K6_BASE_URL   — e.g. http://localhost:8000   (default)
 *   K6_USER_EMAIL — test user email
 *   K6_USER_PASS  — test user password
 */

import http from 'k6/http';
import { check, fail } from 'k6';

export const BASE_URL = __ENV.K6_BASE_URL || 'http://localhost:8000';
export const EMAIL = __ENV.K6_USER_EMAIL || 'admin@stratos.test';
export const PASSWORD = __ENV.K6_USER_PASS || 'password';
export const API_TOKEN = __ENV.K6_API_TOKEN || '';

/**
 * Obtain a Sanctum session cookie via Fortify login.
 * Call this inside k6's `setup()` function.
 *
 * @returns {{ cookie: string }} serialized session cookie header value
 */
export function login() {
    // Fast path for local/staging automation using Sanctum Personal Access Token
    if (API_TOKEN) {
        return { token: API_TOKEN, mode: 'token' };
    }

    // 1. Seed CSRF cookie
    const csrfRes = http.get(`${BASE_URL}/sanctum/csrf-cookie`, {
        headers: { Accept: 'application/json' },
    });
    if (!check(csrfRes, { 'csrf cookie obtained': (r) => r.status === 204 || r.status === 200 })) {
        fail('Could not obtain CSRF cookie — is the server running?');
    }

    // 2. Extract XSRF-TOKEN from Set-Cookie header
    const setCookieHeader = csrfRes.headers['Set-Cookie'] || '';
    const xsrfMatch = setCookieHeader.match(/XSRF-TOKEN=([^;]+)/);
    const xsrfToken = xsrfMatch ? decodeURIComponent(xsrfMatch[1]) : '';

    // 3. POST /login
    const loginRes = http.post(
        `${BASE_URL}/login`,
        JSON.stringify({ email: EMAIL, password: PASSWORD }),
        {
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-XSRF-TOKEN': xsrfToken,
            },
        },
    );

    if (!check(loginRes, { 'login successful': (r) => r.status === 200 || r.status === 204 })) {
        fail(`Login failed — status: ${loginRes.status}. Check K6_USER_EMAIL / K6_USER_PASS.`);
    }

    // 4. Serialize all cookies for reuse across VUs
    const jar = http.cookieJar();
    const cookies = jar.cookiesForURL(`${BASE_URL}/`);
    const cookieStr = Object.entries(cookies)
        .map(([k, v]) => `${k}=${v[0]}`)
        .join('; ');

    return { cookie: cookieStr, xsrf: xsrfToken };
}

/**
 * Build default request params using auth data from setup().
 *
 * @param {{ cookie: string, xsrf: string }} auth
 * @returns {import('k6/http').Params}
 */
export function authParams(auth) {
    if (auth?.mode === 'token' && auth?.token) {
        return {
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
                Authorization: `Bearer ${auth.token}`,
            },
        };
    }

    return {
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            Cookie: auth.cookie,
            'X-XSRF-TOKEN': auth.xsrf,
        },
    };
}
