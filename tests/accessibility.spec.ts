import { expect, test } from '@playwright/test';
import { checkA11y, injectAxe } from 'axe-playwright';

const BASE_URL = process.env.BASE_URL || 'http://127.0.0.1:8000';

test.describe('Accessibility Audit (WCAG 2.1 AA)', () => {
    test('Dashboard should pass accessibility checks', async ({ page }) => {
        await page.goto(`${BASE_URL}/dashboard`);
        await injectAxe(page);

        // Run comprehensive accessibility check
        await checkA11y(page, null, {
            detailedReport: true,
            detailedReportOptions: {
                html: true,
            },
        });
    });

    test('Scenario Planning page should have proper ARIA labels', async ({
        page,
    }) => {
        await page.goto(`${BASE_URL}/scenario-planning`);
        await injectAxe(page);

        // Check for specific accessibility rules
        const violations = await page.evaluate(() => {
            return new Promise((resolve) => {
                // @ts-ignore
                window.axe.run((results) => {
                    resolve(results.violations.map((v: any) => v.id));
                });
            });
        });

        // Should have no critical violations
        expect(violations).toEqual([]);
    });

    test('Keyboard navigation on forms', async ({ page }) => {
        await page.goto(`${BASE_URL}/workforce-planning`);

        // Tab through form elements
        await page.keyboard.press('Tab');
        const focusedElement = await page.evaluate(() => {
            return (document.activeElement as any)?.tagName;
        });

        // Should focus on an interactive element
        expect(['BUTTON', 'INPUT', 'SELECT', 'TEXTAREA', 'A']).toContain(
            focusedElement,
        );
    });

    test('Color contrast ratios should meet WCAG AA', async ({ page }) => {
        await page.goto(`${BASE_URL}/dashboard`);
        await injectAxe(page);

        const contrastResults = await page.evaluate(() => {
            return new Promise((resolve) => {
                // @ts-ignore
                window.axe.run(
                    {
                        rules: ['color-contrast'],
                    },
                    (results) => {
                        resolve({
                            passes: results.passes.length,
                            violations: results.violations,
                        });
                    },
                );
            });
        });

        expect((contrastResults as any).violations).toEqual([]);
    });

    test('Images should have alt text', async ({ page }) => {
        await page.goto(`${BASE_URL}/dashboard`);

        const imagesWithoutAlt = await page.locator('img:not([alt])').count();
        expect(imagesWithoutAlt).toBe(0);
    });

    test('All buttons should have accessible labels', async ({ page }) => {
        await page.goto(`${BASE_URL}/scenario-planning`);

        const buttons = await page.locator('button').all();
        for (const button of buttons) {
            const text = await button.textContent();
            const ariaLabel = await button.getAttribute('aria-label');

            // Button should either have text content or aria-label
            expect(text?.trim() || ariaLabel).toBeTruthy();
        }
    });

    test('Form labels should be properly associated with inputs', async ({
        page,
    }) => {
        await page.goto(`${BASE_URL}/workforce-planning`);

        const inputs = await page.locator('input').all();
        for (const input of inputs) {
            const inputId = await input.getAttribute('id');
            if (inputId) {
                // Check if there's a label with matching "for" attribute
                const label = await page
                    .locator(`label[for="${inputId}"]`)
                    .first();
                const labelExists = (await label.count()) > 0;

                // Either label exists or input has aria-label
                const ariaLabel = await input.getAttribute('aria-label');
                expect(labelExists || ariaLabel).toBeTruthy();
            }
        }
    });

    test('Headings should have proper hierarchy', async ({ page }) => {
        await page.goto(`${BASE_URL}/dashboard`);

        const headings = await page.locator('h1, h2, h3, h4, h5, h6').all();
        let lastLevel = 0;

        for (const heading of headings) {
            const tagName = await heading.evaluate((el) => el.tagName);
            const level = parseInt(tagName.charAt(1));

            // Headings should not skip levels (h1 -> h3 is bad)
            expect(Math.abs(level - lastLevel)).toBeLessThanOrEqual(1);
            lastLevel = level;
        }
    });

    test('Focus visible should be present on interactive elements', async ({
        page,
    }) => {
        await page.goto(`${BASE_URL}/dashboard`);

        // Tab to next element
        await page.keyboard.press('Tab');

        // Check if focus is visible
        const focusStyle = await page.evaluate(() => {
            const el = document.activeElement as any;
            if (!el) return null;

            return {
                outline: getComputedStyle(el).outline,
                boxShadow: getComputedStyle(el).boxShadow,
                backgroundColor: getComputedStyle(el).backgroundColor,
            };
        });

        // Should have some visual indicator (outline, shadow, or background change)
        const hasVisibleFocus =
            (focusStyle?.outline && focusStyle.outline !== 'none') ||
            (focusStyle?.boxShadow && focusStyle.boxShadow !== 'none');

        expect(hasVisibleFocus).toBeTruthy();
    });
});
