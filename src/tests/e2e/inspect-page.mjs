import { chromium } from 'playwright';
import fs from 'fs';

(async () => {
  const state = './tests/e2e/.auth.json';
  const base = process.env.BASE_URL || 'http://127.0.0.1:8001';
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ storageState: state });
  const page = await context.newPage();

  // capture console messages
  const logs = [];
  page.on('console', (msg) => {
    try {
      logs.push({ type: msg.type(), text: msg.text() });
    } catch (e) {
      logs.push({ type: 'console', text: String(msg) });
    }
  });

  // capture failed requests
  const requests = [];
  page.on('requestfailed', (req) => {
    requests.push({ url: req.url(), method: req.method(), failure: req.failure()?.errorText || null });
  });

  const target = `${base.replace(/\/$/, '')}/scenario-planning/1`;
  console.log('navigating to', target);
  await page.goto(target, { waitUntil: 'networkidle' });
  console.log('url=', page.url());
  const html = await page.content();
  fs.writeFileSync('/tmp/scenario1.html', html);
  fs.writeFileSync('/tmp/scenario1.console.json', JSON.stringify({ logs, requests }, null, 2));
  const has = await page.locator('[data-test="generate-wizard-button"]').count();
  console.log('generate-button count=', has);
  console.log('first 400 chars of page:', html.slice(0,400));
  await browser.close();
})();
