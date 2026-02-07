import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  const base = process.env.E2E_BASE_URL || 'http://127.0.0.1:8001';
  console.log('Visiting', base + '/scenario-planning/1');
  await page.goto(base + '/login');
  page.on('console', msg => console.log('PAGE CONSOLE', msg.type(), msg.text()));
  page.on('pageerror', err => console.log('PAGE ERROR', err.message));
  // attempt login if form present (wait for inputs to render)
  const emailSel = 'input[type="email"], input[name="email"]';
  try {
    await page.waitForSelector(emailSel, { timeout: 5000 });
    // Perform a programmatic login via fetch in page context to ensure session cookie is set
    await page.evaluate(async (email, password) => {
      const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
      await fetch('/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ email, password })
      });
    }, process.env.E2E_ADMIN_EMAIL || 'admin@example.com', process.env.E2E_ADMIN_PASSWORD || 'password');
    await page.waitForTimeout(1000);
  } catch (e) {
    console.log('Login inputs not found, assuming already authenticated or different flow');
  }
  await page.goto(base + '/scenario-planning/1?open_generate=1');
  await page.waitForLoadState('networkidle');

  const selectors = [
    '[title="Generar escenario"]',
    '[aria-label="Generar escenario"]',
    '[data-test="generate-wizard-button"]',
    'button:has-text("Generar")',
    'button.rounded-md.w-6.h-6',
    'button:has(svg[class*="mdi-robot"])'
  ];

  for (const s of selectors) {
    const count = await page.locator(s).count();
    console.log(s, 'count=', count);
  }

  const gen = page.locator('[data-test="generate-wizard-button"]').first();
  if (await gen.count() > 0) {
    console.log('Clicking data-test button');
    await gen.click();
  } else {
    console.log('Data-test not found, trying svg button');
    const svg = await page.$('button:has(svg[class*="mdi-robot"])');
    if (svg) { await svg.click(); console.log('Clicked svg'); }
    else console.log('No svg button');
  }

  // wait briefly and dump relevant DOM
  await page.waitForTimeout(1500);
  const html = await page.content();
  const fs = await import('fs');
  fs.writeFileSync('tmp_generate_page.html', html);
  console.log('Wrote tmp_generate_page.html');
  await browser.close();
})();