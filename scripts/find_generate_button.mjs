import { chromium } from 'playwright';

(async () => {
  const base = process.env.BASE_URL || 'http://127.0.0.1:8000';
  const url = `${base.replace(/\/$/, '')}/scenario-planning/1`;
  const browser = await chromium.launch();
  const page = await browser.newPage();
  try {
    await page.goto(url, { waitUntil: 'networkidle', timeout: 20000 });
  } catch (e) {
    console.error('NAV_ERROR', e.message);
    await browser.close();
    process.exit(2);
  }

  const selectors = [
    '[title*="Generar"]',
    '[aria-label*="Generar"]',
    '[data-test*="generate"]',
    'button:has-text("Generar")',
    'button[icon*="robot"]',
  ];

  for (const sel of selectors) {
    const found = await page.$$eval(sel, els => els.map(e => ({ outer: e.outerHTML, text: e.textContent?.trim(), title: (e.getAttribute && e.getAttribute('title')) || null, aria: (e.getAttribute && e.getAttribute('aria-label')) || null, dataTest: (e.getAttribute && e.dataset.test) || null })));
    if (found && found.length) {
      console.log(`MATCH for selector: ${sel}`);
      console.log(JSON.stringify(found.slice(0,5), null, 2));
    } else {
      console.log(`no match for: ${sel}`);
    }
  }

  // dump header area (first .v-sheet) for inspection
  const content = await page.content();
  const lower = content.toLowerCase();
  const hasGenerar = (lower.match(/generar/g) || []).length;
  const hasRobot = (lower.match(/robot/g) || []).length;
  const hasMdi = (lower.match(/mdi-robot/g) || []).length;
  console.log(`PAGE contains: generar=${hasGenerar}, robot=${hasRobot}, mdi-robot=${hasMdi}`);
  if (hasGenerar || hasRobot || hasMdi) {
    // print a small window around the first occurrence of 'generar' if present
    const idx = lower.indexOf('generar');
    if (idx >= 0) console.log('... snippet around generar: ', content.slice(Math.max(0, idx-120), idx+120));
    const idx2 = lower.indexOf('mdi-robot');
    if (idx2 >= 0) console.log('... snippet around mdi-robot: ', content.slice(Math.max(0, idx2-120), idx2+120));
  }

  await browser.close();
  process.exit(0);
})();
