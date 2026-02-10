import { chromium } from 'playwright';

const EMAIL = process.env.E2E_ADMIN_EMAIL || 'admin@example.com';
const PASSWORD = process.env.E2E_ADMIN_PASSWORD || 'password';
const BASE = process.env.BASE_URL || 'http://127.0.0.1:8000';

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  try {
    const root = BASE.replace(/\/$/, '');
    // go to login
    await page.goto(`${root}/login`, { waitUntil: 'networkidle', timeout: 20000 });

    // try common selectors
    const emailSel = 'input[type="email"], input[name="email"]';
    const passSel = 'input[type="password"], input[name="password"]';

    if (await page.$(emailSel)) {
      await page.fill(emailSel, EMAIL);
      await page.fill(passSel, PASSWORD);
      // attempt submit
      const submitBtn = await page.$('button[type="submit"], button:has-text("Ingresar"), button:has-text("Login"), button:has-text("Entrar")');
      if (submitBtn) {
        await Promise.all([
          page.waitForNavigation({ waitUntil: 'networkidle', timeout: 10000 }).catch(() => null),
          submitBtn.click().catch(() => null),
        ]);
      }
    }

    // go to scenario page
    const url = `${root}/scenario-planning/1`;
    await page.goto(url, { waitUntil: 'networkidle', timeout: 20000 });

    // collect candidate interactive elements
    const items = await page.evaluate(() => {
      const sel = Array.from(document.querySelectorAll('button, [role="button"], a, [data-test]'));
      return sel.map((el) => {
        const rect = el.getBoundingClientRect();
        return {
          tag: el.tagName,
          text: el.textContent?.trim().slice(0, 120) || null,
          title: el.getAttribute('title'),
          aria: el.getAttribute('aria-label'),
          dataTest: el.getAttribute('data-test'),
          id: el.id || null,
          classes: el.className || null,
          visible: rect.width > 0 && rect.height > 0,
          bbox: { x: rect.x, y: rect.y, w: rect.width, h: rect.height }
        };
      }).filter(i => i.visible);
    });

    console.log(JSON.stringify({ url, count: items.length, items }, null, 2));

  } catch (e) {
    console.error('ERROR', e && e.message ? e.message : e);
  } finally {
    await browser.close();
    process.exit(0);
  }
})();
