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
  await browser.close();
  process.exit(0);
})();
