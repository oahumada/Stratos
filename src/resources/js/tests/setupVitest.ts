import { config } from '@vue/test-utils'
import { vi } from 'vitest'

// Treat all Vuetify components starting with 'v-' as custom elements to avoid
// Vue warnings in the jsdom test environment.
config.global = config.global || {}
config.global.config = config.global.config || {}
config.global.config.compilerOptions = {
  ...(config.global.config.compilerOptions || {}),
  isCustomElement: (tag: string) => tag.startsWith('v-'),
}

// Default centralized mock for `useApi`. Tests can replace
// `global.__VITEST_MOCK_API` before mounting components to inject their
// own spies/mocks. The module factory reads the global at runtime so tests
// can swap implementations without module cache issues.
const defaultMockApi = {
  post: vi.fn(() => Promise.resolve({})),
  patch: vi.fn(() => Promise.resolve({})),
  delete: vi.fn(() => Promise.resolve({})),
  get: vi.fn(() => Promise.resolve({})),
  api: {},
}

;(globalThis as any).__VITEST_MOCK_API = defaultMockApi

// Do not mock the module here so test files can call `vi.mock('@/composables/useApi', ...)`
// to inject spies. Tests that don't mock `useApi` may still read `global.__VITEST_MOCK_API`.
