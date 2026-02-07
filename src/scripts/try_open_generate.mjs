import { chromium } from 'playwright';

const EMAIL = process.env.E2E_ADMIN_EMAIL || 'admin@example.com';
const PASSWORD = process.env.E2E_ADMIN_PASSWORD || 'password';
const BASE = process.env.BASE_URL || 'http://127.0.0.1:8000';

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();
  try {
    const root = BASE.replace(/\/$/, '');
    await page.goto(`${root}/login`, { waitUntil: 'networkidle', timeout: 20000 });

    const emailSel = 'input[type="email"], input[name="email"]';
    const passSel = 'input[type="password"], input[name="password"]';
    if (await page.$(emailSel)) {
      await page.fill(emailSel, EMAIL);
      await page.fill(passSel, PASSWORD);
      const submitBtn = await page.$('button[type="submit"], button:has-text("Ingresar"), button:has-text("Login"), button:has-text("Entrar")');
      if (submitBtn) {
        await Promise.all([
          page.waitForNavigation({ waitUntil: 'networkidle', timeout: 10000 }).catch(() => null),
          submitBtn.click().catch(() => null),
        ]);
      }
    }

    await page.goto(`${root}/scenario-planning/1`, { waitUntil: 'networkidle', timeout: 20000 });

    // try clicking various selectors
    const selectors = ['[title="Generar escenario"]','[aria-label="Generar escenario"]','[data-test="generate-wizard-button"]','button:has-text("Generar")','button.rounded-md.w-6.h-6'];
    for (const sel of selectors) {
      const el = await page.$(sel);
      console.log('selector', sel, 'found', !!el);
      if (el) {
        try { await el.click(); console.log('clicked', sel); } catch(e) { console.log('click failed', e.message); }
        // wait a bit
        await page.waitForTimeout(500);
      }
    }

    // try clicking svg button
    const svgBtn = await page.$('button:has(svg[class*=\"mdi-robot\"])');
    console.log('svgBtn found', !!svgBtn);
    if (svgBtn) { await svgBtn.click(); await page.waitForTimeout(500); }

    // print page snippets near top (first 4000 chars)
    const html = await page.content();
    console.log('PAGE_HTML_START:\n', html.slice(0, 4000));

    // check for modal text
    const hasGenerarText = await page.$('text=/Generar escenario/i');
    console.log('hasGenerarText', !!hasGenerarText);

  } catch (e) {
    console.error('ERROR', e);
  } finally {
    await browser.close();
    process.exit(0);
  }
})();
