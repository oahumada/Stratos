import { expect, test } from '@playwright/test';
import fs from 'fs';
import path from 'path';
import { mockLLMRoutes } from './helpers/intercepts';
import { login } from './helpers/login';

test('Generate -> Accept (import=true) -> View incubated entities in ScenarioDetail', async ({
    page,
    baseURL,
}, testInfo) => {
    const root = (baseURL || 'http://localhost:8000').replace(/\/$/, '');

    // load fixture used by intercepts
    const fixturePath = path.resolve(
        __dirname,
        '..',
        'fixtures',
        'llm',
        'mock_generation_response.json',
    );
    let fixture: any = {};
    try {
        fixture = JSON.parse(fs.readFileSync(fixturePath, 'utf-8'));
    } catch (e) {
        fixture = { capacities: [], competencies: [], skills: [] };
    }

    // Use centralized helper to mock generation-related LLM routes
    const generationId = 5555;
    await mockLLMRoutes(page, { fixture: fixturePath, generationId });

    // Mock accept endpoint to simulate scenario creation and import
    const scenarioId = 9999;
    await page.route(
        `**/api/strategic-planning/scenarios/generate/${generationId}/accept`,
        async (route) => {
            const body = {
                success: true,
                data: {
                    scenario_id: scenarioId,
                    message: 'Scenario created',
                },
            };
            await route.fulfill({
                status: 200,
                contentType: 'application/json',
                body: JSON.stringify(body),
            });
        },
    );

    // Mock capability-tree for the newly created scenario to return incubated entities
    const incubatedTree = [
        {
            id: 1,
            name: fixture.capacities?.[0]?.name || 'Capacity A',
            description: fixture.capacities?.[0]?.description || 'Desc A',
            competencies: [
                {
                    id: 10,
                    name: fixture.competencies?.[0]?.name || 'Competency X',
                    skills: [
                        {
                            id: 100,
                            name: fixture.skills?.[0]?.name || 'Skill 1',
                            is_incubating: true,
                        },
                    ],
                },
            ],
        },
    ];

    await page.route(
        `**/api/strategic-planning/scenarios/${scenarioId}/capability-tree`,
        async (route) => {
            const body = { success: true, data: incubatedTree };
            await route.fulfill({
                status: 200,
                contentType: 'application/json',
                body: JSON.stringify(body),
            });
        },
    );

    // Authenticate
    await login(page, root);

    // Open a seeded scenario page to trigger the GenerateWizard launch
    await page.goto(`${root}/scenario-planning/1`);
    await page.waitForLoadState('networkidle');

    // Open the GenerateWizard
    const generateButton = page
        .locator('[data-test="generate-wizard-button"]')
        .first();
    await expect(generateButton).toBeVisible({ timeout: 10000 });
    await generateButton.click();

    // Wait for wizard and advance to Generate
    const wizard = page.locator('.generate-wizard').first();
    await expect(wizard).toBeVisible({ timeout: 10000 });
    for (let i = 0; i < 4; i += 1) {
        const nextBtn = wizard.getByRole('button', { name: 'Siguiente' });
        await expect(nextBtn).toBeVisible({ timeout: 5000 });
        await nextBtn.click();
    }

    const generarBtn = wizard.getByRole('button', { name: /^Generar$/ });
    await expect(generarBtn).toBeVisible({ timeout: 5000 });
    await generarBtn.click();

    // Confirm preview / authorize
    await page.waitForSelector('text=Confirmar consulta a la IA', {
        timeout: 5000,
    });
    const autorizar = page.getByRole('button', {
        name: 'Autorizar llamada LLM',
    });
    await autorizar.click();

    // Wait until generation status shows complete (mocked)
    await page.waitForSelector('text=Estado: complete', { timeout: 10000 });

    // Perform accept via the page context (client-side fetch) including import=true
    const acceptRes = await page.evaluate(
        async ({ root, generationId }) => {
            const resp = await fetch(
                `/api/strategic-planning/scenarios/generate/${generationId}/accept`,
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ import: true }),
                },
            );
            return resp.json();
        },
        { root, generationId },
    );

    expect(acceptRes).toBeTruthy();
    expect(acceptRes.success).toBe(true);
    expect(acceptRes.data?.scenario_id).toBe(scenarioId);

    // Navigate to the created scenario detail and assert incubated block shows our fixture values
    await page.goto(`${root}/scenario-planning/${scenarioId}`);
    await page.waitForLoadState('networkidle');

    await expect(
        page.getByText('Entidades incubadas en este escenario'),
    ).toBeVisible({ timeout: 5000 });
    await expect(page.getByText(incubatedTree[0].name)).toBeVisible();
    await expect(
        page.getByText(incubatedTree[0].competencies[0].name),
    ).toBeVisible();
    await expect(
        page.getByText(incubatedTree[0].competencies[0].skills[0].name),
    ).toBeVisible();
});
