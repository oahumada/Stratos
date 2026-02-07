import { Page } from '@playwright/test';

export async function login(page: Page, baseURL: string, email?: string, password?: string) {
  const root = (baseURL || process.env.BASE_URL || 'http://localhost:8000').replace(/\/$/, '');
  await page.goto(`${root}/login`);
  const emailSel = 'input[type="email"], input[name="email"]';
  const passSel = 'input[type="password"], input[name="password"]';
  const user = email || process.env.E2E_ADMIN_EMAIL || 'admin@example.com';
  const pass = password || process.env.E2E_ADMIN_PASSWORD || 'password';
  if (await page.locator(emailSel).count() > 0) {
    await page.fill(emailSel, user);
    await page.fill(passSel, pass);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle' }).catch(() => null),
      page.click('button[type="submit"]'),
    ]);
  }
}
