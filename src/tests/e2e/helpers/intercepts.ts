import { Page } from '@playwright/test';
import fs from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __dirname = path.dirname(fileURLToPath(import.meta.url));

export async function mockLLMRoutes(
    page: Page,
    options?: { fixture?: string; generationId?: number },
) {
    const generationId = options?.generationId ?? 12345;
    const fixturePath =
        options?.fixture ||
        path.resolve(
            __dirname,
            '..',
            '..',
            'fixtures',
            'llm',
            'mock_generation_response.json',
        );
    let fixture: any = {};
    try {
        fixture = JSON.parse(fs.readFileSync(fixturePath, 'utf-8'));
    } catch (e) {
        fixture = { scenario_metadata: { summary: 'mocked-from-helper' } };
    }

    await page.route(
        '**/api/strategic-planning/scenarios/generate/preview',
        async (route) => {
            const body = {
                success: true,
                data: { prompt: 'MOCK_PROMPT_PREVIEW' },
            };
            await route.fulfill({
                status: 200,
                contentType: 'application/json',
                body: JSON.stringify(body),
            });
        },
    );

    await page.route(
        '**/api/strategic-planning/scenarios/generate',
        async (route) => {
            const body = {
                success: true,
                data: {
                    id: generationId,
                    status: 'queued',
                    url: `/api/strategic-planning/scenarios/generate/${generationId}`,
                },
            };
            await route.fulfill({
                status: 202,
                contentType: 'application/json',
                body: JSON.stringify(body),
            });
        },
    );

    await page.route(
        `**/api/strategic-planning/scenarios/generate/${generationId}`,
        async (route) => {
            const body = {
                success: true,
                data: {
                    id: generationId,
                    status: 'complete',
                    llm_response: fixture,
                    confidence_score: 0.8,
                },
            };
            await route.fulfill({
                status: 200,
                contentType: 'application/json',
                body: JSON.stringify(body),
            });
        },
    );
}
