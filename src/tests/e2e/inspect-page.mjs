import { chromium } from 'playwright';
import fs from 'fs';
(async () => {
  const state = './tests/e2e/.auth.json';
  const base = process.env.BASE_URL || 'http://127.0.0.1:8001'\;
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ storageState: state });
  const page = await context.newPage();
  await page.goto(, { waitUntil: 'networkidle' });
  console.log('url=', page.url());
  const html = await page.content();
  fs.writeFileSync('/tmp/scenario1.html', html);
  const has = await page.locator('[data-test=generate-wizard-button]').count();
  console.log('generate-button count=', has);
  console.log('first 400 chars of page:', html.slice(0,400));
  await browser.close();
})();
