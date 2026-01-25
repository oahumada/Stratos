import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  timeout: 60_000,
  expect: {
    timeout: 5_000,
  },
  forbidOnly: !!(globalThis as any).process?.env?.CI,
  retries: (globalThis as any).process?.env?.CI ? 1 : 0,
  reporter: [['list'], ['html', { open: 'never' }]],
  use: {
    baseURL: (globalThis as any).process?.env?.BASE_URL || 'http://localhost:8000',
    trace: 'on-first-retry',
    actionTimeout: 0,
    ignoreHTTPSErrors: true,
  },
  projects: [
    {
      name: 'Chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
