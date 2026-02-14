import vue from '@vitejs/plugin-vue';
import path from 'node:path';
import { defineConfig } from 'vitest/config';

export default defineConfig({
    plugins: [vue()],
    test: {
        globals: true,
        environment: 'jsdom',
        setupFiles: ['resources/js/tests/setupVitest.ts'],
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
        // exclude Playwright e2e tests and external packages from Vitest runs
        exclude: ['tests/e2e/**', '**/node_modules/**'],
        hookTimeout: 30000,
        testTimeout: 60000,
        // Use forks pool but disable parallelism to avoid worker timeouts
        pool: 'forks',
        fileParallelism: false,
        maxWorkers: 1,
    },
});
