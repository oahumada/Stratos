import { test, expect } from '@playwright/test';
import { login } from './helpers/login';

// This E2E test assumes you run `npm run dev` from /src and the app is available at http://localhost:5173
// It validates: open scenario page, click first capability node, child nodes are rendered (<=10), and
// the selected node is approximately at 25% vertical of the viewport.

test('scenario map - click capability expands children and centers selected node', async ({ page, baseURL }, testInfo) => {
  const consoleLogs: Array<string> = [];
  const pageErrors: Array<string> = [];
  page.on('console', (msg) => {
    try { consoleLogs.push(`${msg.type()}: ${msg.text()}`); } catch { void 0; }
  });
  page.on('pageerror', (err) => {
    try { pageErrors.push(String(err?.message || err)); } catch { void 0; }
  });

  try {
    const root = (baseURL || 'http://localhost:8000').replace(/\/$/, '');
    // Ensure authenticated session
    await login(page, root);

    await page.goto(`${root}/scenario-planning/1?view=map`);

    // wait for load and possible XHRs to complete
    await page.waitForLoadState('networkidle');
    // small stabilization wait to allow client-side rendering to start
    await page.waitForTimeout(1000);

    // quick check: if we were redirected to login, fail early with helpful message
    const currentUrl = page.url();
    if (/login|sanctum|unauthorized/i.test(currentUrl)) {
      const html = await page.content();
      await testInfo.attach('redirect-html', { body: html, contentType: 'text/html' });
      const snap = await page.screenshot();
      await testInfo.attach('redirect-screenshot', { body: snap, contentType: 'image/png' });
      throw new Error(`Navigated to login/unauthorized page: ${currentUrl}`);
    }

    // wait for nodes to render (node groups)
    await page.waitForSelector('svg .node-group', { timeout: 60000 });

    // pick first capability node (exclude scenario-node)
    const capLocator = page.locator('svg .node-group:not(.scenario-node)').first();
    await expect(capLocator).toBeVisible();

    // ensure the capability is in the viewport, then click
    await capLocator.scrollIntoViewIfNeeded();
    await capLocator.click();

    // wait for possible transition/animation time
    await page.waitForTimeout(700);

    // ensure focused node appears after click
    const focused = page.locator('svg .node-group.focused').first();
    await expect(focused).toBeVisible({ timeout: 5000 });

    // child nodes may be empty depending on data; validate when present
    const children = page.locator('svg .child-nodes g');
    const count = await children.count();
    if (count > 0) {
      await expect(children.first()).toBeVisible({ timeout: 5000 });
      // assert we have at most 10 children (matrix 2x5)
      expect(count).toBeLessThanOrEqual(10);
    }

    // verify the selected node Y coordinate is near 25% of viewport height
    const viewport = page.viewportSize() || { width: 900, height: 600 };
    const expectedY = Math.round(viewport.height * 0.25);

    // get transform style from the clicked capability (the focused one should still be present)
    const capHandle = await capLocator.elementHandle();
    let style = '';
    if (capHandle) style = (await capHandle.getAttribute('style')) || '';

    // style contains translate(xpx, ypx) — extract the y value
    const m = style.match(/translate\(([-0-9.]+)px,\s*([-0-9.]+)px\)/);
    let actualY = null;
    if (m && m[2]) actualY = Math.round(Number(m[2]));

    // allow some tolerance (±40px)
    if (actualY !== null) {
      expect(Math.abs(actualY - expectedY)).toBeLessThanOrEqual(40);
    } else {
      // fallback: ensure focused node exists
      await expect(focused).toBeVisible();
    }
  } catch (err) {
    // attach debug artifacts to the test report
    try {
      const snap = await page.screenshot({ fullPage: true }).catch(() => null);
      if (snap) await testInfo.attach('error-screenshot', { body: snap, contentType: 'image/png' });
      const html = await page.content().catch(() => null);
      if (html) await testInfo.attach('error-html', { body: html, contentType: 'text/html' });
      if (consoleLogs.length) await testInfo.attach('console-logs', { body: consoleLogs.join('\n'), contentType: 'text/plain' });
      if (pageErrors.length) await testInfo.attach('page-errors', { body: pageErrors.join('\n'), contentType: 'text/plain' });
    } catch {
      // ignore attachment errors
    }
    throw err;
  }
});
