import { test, expect } from '@playwright/test';

test('GenerateWizard - happy path: preview and authorize generation', async ({ page, baseURL }, testInfo) => {
  const root = (baseURL || 'http://localhost:8000').replace(/\/$/, '');

  // Intercept preview endpoint
  await page.route('**/api/strategic-planning/scenarios/generate/preview', async (route) => {
    const body = { success: true, data: { prompt: 'MOCK_PROMPT_PREVIEW' } };
    await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify(body) });
  });

  // Intercept generate endpoint (enqueue)
  await page.route('**/api/strategic-planning/scenarios/generate', async (route) => {
    const body = { success: true, data: { id: 12345, status: 'queued', url: `/api/strategic-planning/scenarios/generate/12345` } };
    await route.fulfill({ status: 202, contentType: 'application/json', body: JSON.stringify(body) });
  });

  // Intercept status fetch for the generation id
  await page.route('**/api/strategic-planning/scenarios/generate/12345', async (route) => {
    const body = {
      success: true,
      data: {
        id: 12345,
        status: 'complete',
        llm_response: { scenario_metadata: { summary: 'mocked result' } },
        confidence_score: 0.8,
      },
    };
    await route.fulfill({ status: 200, contentType: 'application/json', body: JSON.stringify(body) });
  });

  // Navigate and login if needed
  await page.goto(`${root}/login`);
  const emailSel = 'input[type="email"], input[name="email"]';
  const passSel = 'input[type="password"], input[name="password"]';
  if (await page.locator(emailSel).count() > 0) {
    await page.fill(emailSel, 'admin@example.com');
    await page.fill(passSel, 'password');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'networkidle', timeout: 10000 }).catch(() => null),
      page.click('button[type="submit"]'),
    ]);
  }

  // Open a scenario detail page (assumes scenario id 1 exists in seeded data)
  await page.goto(`${root}/scenario-planning/1`);
  await page.waitForLoadState('networkidle');

  // Open the GenerateWizard via header button (title attribute)
  const genButton = page.locator('[title="Generar escenario"]');
  await expect(genButton).toBeVisible({ timeout: 5000 });
  await genButton.click();

  // Wait for wizard to appear (case-insensitive)
  await page.waitForSelector('text=/Generar escenario/i', { timeout: 10000 });

  // Click the Generar button in the wizard to trigger preview
  const generarBtn = page.getByRole('button', { name: /Generar|Generar Escenario/i }).first();
  await generarBtn.click();

  // Wait for preview modal content to appear
  await page.waitForSelector('text=Confirmar consulta a la IA', { timeout: 5000 });

  // Confirm (authorize) the LLM call
  const autorizar = page.getByRole('button', { name: 'Autorizar llamada LLM' });
  await autorizar.click();

  // After authorization, the store should have a generationId and fetch status; wait for the result text
  await page.waitForSelector('text=Estado: complete', { timeout: 10000 });

  // Assert that the result content is shown (mocked llm_response)
  await expect(page.locator('text=mocked result')).toBeVisible();
});
