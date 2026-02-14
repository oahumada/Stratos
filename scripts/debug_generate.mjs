import { chromium } from 'playwright';

(async () => {
  const browser = await chromium.launch();
  const page = await browser.newPage();
  const base = process.env.E2E_BASE_URL || 'http://127.0.0.1:8001';
  console.log('Visiting', base + '/scenario-planning/1');
  await page.goto(base + '/login');
  // attempt login if form present
  const emailSel = 'input[type="email"], input[name="email"]';
  if (await page.$(emailSel)) {
    await page.fill(emailSel, process.env.E2E_ADMIN_EMAIL || 'admin@example.com');
    await page.fill('input[type="password"], input[name="password"]', process.env.E2E_ADMIN_PASSWORD || 'password');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle' }).catch(()=>null),
      page.click('button[type="submit"]')
    ]);
  }
  await page.goto(base + '/scenario-planning/1');
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
  const fs = await import('node:fs');
  fs.writeFileSync('tmp_generate_page.html', html);
  console.log('Wrote tmp_generate_page.html');
  await browser.close();
})();