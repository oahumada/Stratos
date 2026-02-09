/// <reference types="node" />
import { Page } from '@playwright/test';

export async function login(
    page: Page,
    baseURL: string,
    email?: string,
    password?: string,
) {
    const root = (
        baseURL ||
        process.env.BASE_URL ||
        'http://localhost:8000'
    ).replace(/\/$/, '');
    const user = email || process.env.E2E_ADMIN_EMAIL || 'admin@example.com';
    const pass = password || process.env.E2E_ADMIN_PASSWORD || 'password';

    await page.goto(`${root}/login`).catch(() => null);
    const emailSel = 'input[type="email"], input[name="email"]';
    const passSel = 'input[type="password"], input[name="password"]';
    const hasLoginForm = await page
        .waitForSelector(emailSel, { timeout: 15000 })
        .then(() => true)
        .catch(() => false);

    if (hasLoginForm) {
        await page.fill(emailSel, user);
        await page.fill(passSel, pass);
        await Promise.all([
            page
                .waitForNavigation({ waitUntil: 'networkidle', timeout: 30000 })
                .catch(() => null),
            page.click('button[type="submit"]'),
        ]);
        return;
    }

    // Fallback: programmatic login via Playwright request context to share cookies.
    await page.request.get(`${root}/sanctum/csrf-cookie`).catch(() => null);
    const cookies = await page.context().cookies();
    const xsrfCookie = cookies.find((cookie) => cookie.name === 'XSRF-TOKEN');
    const xsrfToken = xsrfCookie ? decodeURIComponent(xsrfCookie.value) : null;
    if (!xsrfToken) return;

    await page.request.post(`${root}/login`, {
        form: {
            email: user,
            password: pass,
        },
        headers: {
            'X-XSRF-TOKEN': xsrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
    });
}
