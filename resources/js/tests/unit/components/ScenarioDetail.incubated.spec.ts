// @vitest-environment jsdom
import { render, waitFor } from '@testing-library/vue';
import { describe, expect, it, vi } from 'vitest';
// Prevent Vitest from trying to load Vuetify CSS modules referenced by components
vi.mock('vuetify/lib/components/VCode/VCode.css', () => ({}));

// Provide a spyable mock for the useApi composable used by ScenarioDetail
const incubatedTree = [
    {
        id: 1,
        name: 'Capacity A',
        description: 'Desc A',
        competencies: [
            {
                id: 10,
                name: 'Competency X',
                skills: [{ id: 100, name: 'Skill 1', is_incubating: true }],
            },
        ],
    },
];

vi.doMock('@/composables/useApi', () => ({
    useApi: () => ({ get: async () => ({ data: incubatedTree }) }),
}));

// Stub heavy child components to avoid importing Vuetify internals/CSS during test
vi.mock('@/components/StrategicPlanningScenarios/ChangeSetModal.vue', () => ({
    default: { template: '<div />' },
}));
vi.mock('@/components/WorkforcePlanning/StatusTimeline.vue', () => ({
    default: { template: '<div />' },
}));
vi.mock('@/components/WorkforcePlanning/VersionHistoryModal.vue', () => ({
    default: { template: '<div />' },
}));
vi.mock(
    '@/components/WorkforcePlanning/Step2/RoleCompetencyMatrix.vue',
    () => ({ default: { template: '<div />' } }),
);
vi.mock('@/layouts/AppLayout.vue', () => ({
    default: { template: '<div><slot /></div>' },
}));
vi.mock('@/pages/ScenarioPlanning/GenerateWizard/GenerateWizard.vue', () => ({
    default: { template: '<div />' },
}));
vi.mock('@/pages/ScenarioPlanning/Index.vue', () => ({
    default: { template: '<div />' },
}));

describe('ScenarioDetail incubated entities block', () => {
    it('renders incubated capabilities, competencies and skills when API returns them', async () => {
        // dynamic import after mocks registered
        const ScenarioDetail = (
            await import('@/pages/ScenarioPlanning/ScenarioDetail.vue')
        ).default;

        const { getByText } = render(ScenarioDetail as any, {
            props: { id: 9999 },
            global: {
                stubs: {
                    'v-btn': { template: '<button><slot /></button>' },
                    'v-card': { template: '<div><slot /></div>' },
                    'v-card-text': { template: '<div><slot /></div>' },
                    'v-card-title': { template: '<div><slot /></div>' },
                    'v-icon': { template: '<i />' },
                },
            },
        });

        await waitFor(() => {
            expect(
                getByText('Entidades incubadas en este escenario'),
            ).toBeTruthy();
        });

        expect(getByText('Capacity A')).toBeTruthy();
        expect(getByText(/Competency X/)).toBeTruthy();
        expect(getByText(/Skill 1/)).toBeTruthy();
    });
});
