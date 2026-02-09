import { config } from '@vue/test-utils';
import { vi } from 'vitest';

// Treat all Vuetify components starting with 'v-' as custom elements to avoid
// Vue warnings in the jsdom test environment.
config.global = config.global || {};
config.global.config = config.global.config || {};
config.global.config.compilerOptions = {
    ...(config.global.config.compilerOptions || {}),
};

// Global simple stubs for common Vuetify components so tests don't need
// to register them per-mount. These render slots as basic HTML elements
// which makes DOM-based assertions straightforward.
config.global.components = {
    ...(config.global.components || {}),
    'v-btn': { template: '<button><slot/></button>' },
    'v-dialog': { template: '<div><slot/></div>' },
    'v-card': { template: '<div><slot/></div>' },
    'v-card-actions': { template: '<div><slot/></div>' },
    'v-card-text': { template: '<div><slot/></div>' },
    'v-card-title': { template: '<div><slot/></div>' },
    'v-text-field': { template: '<input />' },
    'v-form': { template: '<form><slot/></form>' },
    'v-row': { template: '<div><slot/></div>' },
    'v-col': { template: '<div><slot/></div>' },
    'v-slider': { template: '<input type="range" />' },
    'v-list': { template: '<ul><slot/></ul>' },
    'v-list-item': { template: '<li><slot/></li>' },
    'v-list-item-icon': { template: '<span><slot/></span>' },
    'v-list-item-title': { template: '<div><slot/></div>' },
    'v-list-item-subtitle': { template: '<div><slot/></div>' },
    'v-menu': { template: '<div><slot/></div>' },
    'v-textarea': { template: '<textarea />' },
    'v-checkbox': { template: '<input type="checkbox" />' },
    'v-select': { template: '<select><slot/></select>' },
    'v-switch': { template: '<input type="checkbox" />' },
    'v-spacer': { template: '<div style="flex:1 1 auto" />' },
    'v-alert': { template: '<div role="alert"><slot/></div>' },
    'v-icon': { template: '<i class="v-icon"><slot/></i>' },

    // Additional common Vuetify components used across tests
    'v-chip': { template: '<span class="v-chip"><slot/></span>' },
    'v-skeleton-loader': { template: '<div class="v-skeleton"><slot/></div>' },
    'v-container': { template: '<div class="v-container"><slot/></div>' },
    'v-sheet': { template: '<div class="v-sheet"><slot/></div>' },
    'v-progress-linear': {
        template: '<div class="v-progress-linear"><slot/></div>',
    },
    'v-progress-circular': {
        template: '<div class="v-progress-circular"><slot/></div>',
    },
    'v-app': { template: '<div class="v-app"><slot/></div>' },
    'v-radio': { template: '<input type="radio" />' },
    'v-radio-group': { template: '<div class="v-radio-group"><slot/></div>' },
};

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
};

(globalThis as any).__VITEST_MOCK_API = defaultMockApi;

// Do not mock the module here so test files can call `vi.mock('@/composables/useApi', ...)`
// to inject spies. Tests that don't mock `useApi` may still read `global.__VITEST_MOCK_API`.
