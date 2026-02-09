import { chromium } from 'playwright';
import fs from 'fs';

async function run() {
  const base = process.env.BASE_URL || 'http://127.0.0.1:8001';
  const statePath = './tests/e2e/.auth.json';
  if (!fs.existsSync(statePath)) {
    console.error('storage state not found:', statePath);
    process.exit(1);
  }
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ storageState: statePath });
  const page = await context.newPage();
  await page.goto(`${base}/scenario-planning/1`, { waitUntil: 'networkidle' });
  console.log('page.url() =', page.url());
  // dump whether generate button visible
  const exists = await page.locator('[data-test="generate-wizard-button"]').first().isVisible().catch(() => false);
  console.log('generate button visible?', exists);
  await browser.close();
}

run();
