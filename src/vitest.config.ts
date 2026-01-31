import { defineConfig } from 'vitest/config'
import path from 'path'
import vue from '@vitejs/plugin-vue'

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
    hookTimeout: 20000,
    // disable worker threads to avoid fork/worker timeouts in CI/local environments
    threads: false,
  },
})
