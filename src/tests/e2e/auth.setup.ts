// tests/auth.setup.ts
import { chromium } from '@playwright/test';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();

  await page.goto('http://localhost:8000/login');
  await page.fill('input[name=email]', 'admin@example.com');
  await page.fill('input[name=password]', 'password');
  await page.click('button[type=submit]');

  await page.waitForURL('**/dashboard');

  await page.context().storageState({ path: 'auth.json' });
  await browser.close();
})();