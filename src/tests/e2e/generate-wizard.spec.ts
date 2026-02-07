import { expect, test } from '@playwright/test';
import { mockLLMRoutes } from './helpers/intercepts';
import { login } from './helpers/login';

test('GenerateWizard - happy path: preview and authorize generation', async ({
    page,
    baseURL,
}, testInfo) => {
    const root = (baseURL || 'http://localhost:8000').replace(/\/$/, '');

    // Use centralized helper to mock LLM routes (preview, enqueue, status)
    await mockLLMRoutes(page, { generationId: 12345 });

    // Ensure authenticated using shared helper
    await login(page, root);

    // Open a scenario detail page (assumes scenario id 1 exists in seeded data)
    await page.goto(`${root}/scenario-planning/1`);
    await page.waitForLoadState('networkidle');

    // quick check: if we were redirected to login, fail early
    const currentUrl = page.url();
    if (/login|sanctum|unauthorized/i.test(currentUrl)) {
        throw new Error(`Navigated to login/unauthorized page: ${currentUrl}`);
    }

    const generateButton = page
        .locator('[data-test="generate-wizard-button"]')
        .first();
    await expect(generateButton).toBeVisible({ timeout: 10000 });
    await generateButton.click();

    // Wait for wizard to appear
    const wizard = page.locator('.generate-wizard').first();
    await expect(wizard).toBeVisible({ timeout: 10000 });

    // Move to final step to enable the Generate button
    for (let i = 0; i < 4; i += 1) {
        const nextBtn = wizard.getByRole('button', { name: 'Siguiente' });
        await expect(nextBtn).toBeVisible({ timeout: 5000 });
        await nextBtn.click();
    }

    // Click the Generar button in the wizard to trigger preview
    const generarBtn = wizard.getByRole('button', { name: /^Generar$/ });
    await expect(generarBtn).toBeVisible({ timeout: 5000 });
    await generarBtn.click();

    // Wait for preview modal content to appear
    await page.waitForSelector('text=Confirmar consulta a la IA', {
        timeout: 5000,
    });

    // Confirm (authorize) the LLM call
    const autorizar = page.getByRole('button', {
        name: 'Autorizar llamada LLM',
    });
    await autorizar.click();

    // After authorization, generation is queued; request status refresh
    await page.waitForSelector('text=/Estado:/i', { timeout: 10000 });
    const actualizarBtn = page.getByRole('button', { name: 'Actualizar' });
    await expect(actualizarBtn).toBeVisible({ timeout: 5000 });
    await actualizarBtn.click();

    // Wait for the completed status
    await page.waitForSelector('text=Estado: complete', { timeout: 10000 });

    // Assert that the result content is shown (mocked llm_response)
    const resultPre = page.locator('.generation-status pre');
    await expect(resultPre).toBeVisible({ timeout: 5000 });
    await expect(resultPre).toContainText('Capacity A');
});
