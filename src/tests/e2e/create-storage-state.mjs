import { chromium } from 'playwright';

async function run() {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext();
  const page = await context.newPage();

  const base = process.env.BASE_URL || 'http://127.0.0.1:8001';
  const email = process.env.E2E_ADMIN_EMAIL || 'admin@example.com';
  const password = process.env.E2E_ADMIN_PASSWORD || 'password';

  try {
    // Prefer dev-only login endpoint if available (faster and robust)
    try {
      // Use browser navigation so cookies are set in context
      const resp = await page.goto(`${base}/__e2e_login`, { waitUntil: 'networkidle', timeout: 10000 }).catch(() => null);
      const ok = resp && resp.status && resp.status() === 200;
      if (ok) {
        console.log('Used __e2e_login endpoint via page.goto');
      } else {
        // Fallback to form-based login
        throw new Error('e2e_login not available');
      }
    } catch (e) {
      await page.goto(`${base}/login`, { waitUntil: 'networkidle', timeout: 30000 });

      // Wait longer for SPA to render login form
      const emailSel = 'input[type="email"], input[name="email"]';
      const passSel = 'input[type="password"], input[name="password"]';
      const visible = await page.waitForSelector(emailSel, { timeout: 15000 }).catch(() => null);
      if (visible) {
        await page.fill(emailSel, email);
        await page.fill(passSel, password);
        await Promise.all([
          page.waitForNavigation({ waitUntil: 'networkidle', timeout: 30000 }).catch(() => null),
          page.click('button[type=submit]'),
        ]);
      } else {
        // Fallback: use fetch via page.evaluate to post creds and set cookies in page context
        await page.evaluate(async (email, password) => {
          await fetch('/sanctum/csrf-cookie');
          await fetch('/login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ email, password }),
          });
        }, email, password);
        await page.waitForLoadState('networkidle');
      }
    }

    // Save storage state
    const statePath = './tests/e2e/.auth.json';
    await context.storageState({ path: statePath });
    console.log('Saved storage state to', statePath);
  } catch (e) {
    console.error('Failed to create storage state:', e);
    process.exitCode = 1;
  } finally {
    await browser.close();
  }
}

run();
