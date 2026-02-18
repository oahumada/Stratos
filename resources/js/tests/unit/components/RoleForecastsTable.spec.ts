import RoleForecastsTable from '@/components/ScenarioPlanning/Step2/RoleForecastsTable.vue';
import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

const mockGet = vi.fn();

vi.mock('@/composables/useApi', () => ({
    useApi: () => ({
        get: mockGet,
    }),
}));

describe('RoleForecastsTable.vue', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders component with correct structure', () => {
        const wrapper = mount(RoleForecastsTable, {
            props: {
                scenarioId: 1,
            },
        });

        expect(wrapper.exists()).toBe(true);
        expect(wrapper.find('.role-forecasts-container').exists()).toBeTruthy();
    });

    it('loads forecast data on mount', async () => {
        const mockForecasts = {
            data: [
                {
                    id: 1,
                    scenario_id: 1,
                    role_id: 101,
                    role_name: 'Product Manager',
                    fte_current: 2,
                    fte_future: 3,
                    fte_delta: 1,
                    evolution_type: 'transformation',
                    impact_level: 'high',
                    rationale: 'Business expansion',
                },
            ],
        };

        mockGet.mockResolvedValue(mockForecasts);

        const wrapper = mount(RoleForecastsTable, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(mockGet).toHaveBeenCalledWith(
            '/api/scenarios/1/step2/role-forecasts',
        );
        expect(wrapper.vm.forecasts).toHaveLength(1);
    });

    it('displays forecast data in table', async () => {
        const mockForecasts = {
            data: [
                {
                    id: 1,
                    role_id: 1,
                    role_name: 'Product Manager',
                    fte_current: 2,
                    fte_future: 3,
                    fte_delta: 1,
                    evolution_type: 'transformation',
                    impact_level: 'high',
                    rationale: 'Growth',
                },
                {
                    id: 2,
                    role_id: 2,
                    role_name: 'Engineer',
                    fte_current: 5,
                    fte_future: 5,
                    fte_delta: 0,
                    evolution_type: 'upgrade_skills',
                    impact_level: 'medium',
                    rationale: 'Stable',
                },
            ],
        };

        mockGet.mockResolvedValue(mockForecasts);

        const wrapper = mount(RoleForecastsTable, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        const rows = wrapper.findAll('tbody tr');
        expect(rows).toHaveLength(2);
    });

    it('shows error message on fetch failure', async () => {
        mockGet.mockRejectedValue({
            response: {
                data: {
                    message: 'Error fetching',
                },
            },
        });

        const wrapper = mount(RoleForecastsTable, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(wrapper.vm.error).toBeTruthy();
    });

    it('handles empty forecast list', async () => {
        mockGet.mockResolvedValue({ data: [] });

        const wrapper = mount(RoleForecastsTable, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(wrapper.vm.forecasts).toHaveLength(0);
        // Checking for text instead of class since class name might vary
        expect(wrapper.text()).toContain('No hay pronósticos definidos');
    });

    it('updates data when scenarioId prop changes', async () => {
        mockGet
            .mockResolvedValueOnce({
                data: [
                    {
                        id: 1,
                        role_id: 1,
                        role_name: 'PM',
                        fte_current: 1,
                        fte_future: 2,
                        evolution_type: 'transformation',
                        impact_level: 'medium',
                    },
                ],
            })
            .mockResolvedValueOnce({
                data: [
                    {
                        id: 2,
                        role_id: 2,
                        role_name: 'Engineer',
                        fte_current: 3,
                        fte_future: 5,
                        evolution_type: 'transformation',
                        impact_level: 'high',
                    },
                ],
            });

        const wrapper = mount(RoleForecastsTable, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(wrapper.vm.forecasts).toHaveLength(1);
        expect(wrapper.vm.forecasts[0].role_id).toBe(1);
        expect(wrapper.vm.forecasts[0].role_name).toBe('PM');

        await wrapper.setProps({ scenarioId: 2 });
        await flushPromises();

        expect(wrapper.vm.forecasts).toHaveLength(1);
        expect(wrapper.vm.forecasts[0].role_id).toBe(2);
    });
});
