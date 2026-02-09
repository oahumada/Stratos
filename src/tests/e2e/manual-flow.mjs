import { chromium } from 'playwright';

(async () => {
  const base = process.env.BASE_URL || 'http://127.0.0.1:8001';
  const browser = await chromium.launch({ headless: false });
  const context = await browser.newContext();
  const page = await context.newPage();
  const root = base.replace(/\/$/, '');
  console.log('Opening login at', `${root}/login`);
  await page.goto(`${root}/login`, { waitUntil: 'networkidle' });

  // Fill login form if present
  const emailSel = 'input[type="email"], input[name="email"]';
  const passSel = 'input[type="password"], input[name="password"]';
  const hasLoginForm = await page
    .waitForSelector(emailSel, { timeout: 5000 })
    .then(() => true)
    .catch(() => false);

  if (hasLoginForm) {
    console.log('Filling login form');
    await page.fill(emailSel, process.env.E2E_ADMIN_EMAIL || 'admin@example.com');
    await page.fill(passSel, process.env.E2E_ADMIN_PASSWORD || 'password');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle' }).catch(() => null),
      page.click('button[type="submit"]'),
    ]);
  } else {
    console.log('Login form not found, attempting programmatic login');
    await page.request.get(`${root}/sanctum/csrf-cookie`).catch(() => null);
    const cookies = await context.cookies();
    const xsrfCookie = cookies.find((c) => c.name === 'XSRF-TOKEN');
    const xsrfToken = xsrfCookie ? decodeURIComponent(xsrfCookie.value) : null;
    if (xsrfToken) {
      await page.request.post(`${root}/login`, {
        form: { email: process.env.E2E_ADMIN_EMAIL || 'admin@example.com', password: process.env.E2E_ADMIN_PASSWORD || 'password' },
        headers: { 'X-XSRF-TOKEN': xsrfToken, 'X-Requested-With': 'XMLHttpRequest' },
      }).catch(() => null);
    }
    // reload to ensure cookies applied
    await page.goto(`${root}/dashboard`, { waitUntil: 'networkidle' });
  }

  console.log('Navigating to scenario page');
  await page.goto(`${root}/scenario-planning/1`, { waitUntil: 'networkidle' });
  console.log('Current URL:', page.url());
  const count = await page.locator('[data-test="generate-wizard-button"]').count();
  console.log('generate-wizard-button count =', count);

  // keep the browser open for manual inspection for a bit
  console.log('Pausing 30s for manual inspection...');
  await new Promise((res) => setTimeout(res, 30000));

  await browser.close();
  process.exit(0);
})();
