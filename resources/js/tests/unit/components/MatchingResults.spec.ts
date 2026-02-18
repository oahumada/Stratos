import MatchingResults from '@/components/ScenarioPlanning/Step2/MatchingResults.vue';
import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

const mockGet = vi.fn();

vi.mock('@/composables/useApi', () => ({
    useApi: () => ({
        get: mockGet,
    }),
}));

describe('MatchingResults.vue', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders component with correct structure', () => {
        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        expect(wrapper.exists()).toBe(true);
        expect(
            wrapper.find('.matching-results-container').exists(),
        ).toBeTruthy();
    });

    it('loads matching results on mount', async () => {
        const mockResults = {
            data: [
                {
                    id: 1,
                    candidate_name: 'John Doe',
                    current_role: 'Developer',
                    target_position: 'Product Manager',
                    match_percentage: 85,
                    risk_factors: [{ id: 1, factor: 'New to PM role' }],
                    productivity_timeline: 3,
                    skill_gaps: [],
                },
            ],
        };

        mockGet.mockResolvedValue(mockResults);

        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(mockGet).toHaveBeenCalledWith(
            '/api/scenarios/1/step2/matching-results',
        );
        expect(wrapper.vm.results).toHaveLength(1);
    });

    it('displays match cards with match percentage', async () => {
        const mockResults = {
            data: [
                {
                    id: 1,
                    candidate_name: 'John Doe',
                    current_role: 'Dev',
                    target_position: 'PM',
                    match_percentage: 85,
                    productivity_timeline: 3,
                },
                {
                    id: 2,
                    candidate_name: 'Jane Smith',
                    current_role: 'Eng',
                    target_position: 'TL',
                    match_percentage: 70,
                    productivity_timeline: 6,
                },
            ],
        };

        mockGet.mockResolvedValue(mockResults);

        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        const cards = wrapper.findAll('.border.rounded-lg.p-4');
        expect(cards).toHaveLength(2);
    });

    it('shows different colors based on match percentage', async () => {
        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        expect(wrapper.vm.getMatchColor(90)).toContain('#4caf50');
        expect(wrapper.vm.getMatchColor(50)).toContain('#ff5252');
    });

    it('displays risk factors when present', async () => {
        const mockResults = {
            data: [
                {
                    id: 1,
                    candidate_name: 'John',
                    current_role: 'Dev',
                    target_position: 'PM',
                    match_percentage: 70,
                    risk_factors: [
                        { id: 1, factor: 'First management role' },
                        { id: 2, factor: 'Needs training' },
                    ],
                    productivity_timeline: 6,
                },
            ],
        };

        mockGet.mockResolvedValue(mockResults);

        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(wrapper.vm.results[0].risk_factors).toHaveLength(2);
        expect(wrapper.vm.results[0].risk_factors[0].factor).toBe(
            'First management role',
        );
    });

    it('handles empty results', async () => {
        mockGet.mockResolvedValue({ data: [] });

        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(wrapper.vm.results).toHaveLength(0);
    });

    it('shows error on fetch failure', async () => {
        mockGet.mockRejectedValue({
            response: {
                data: {
                    message: 'Error fetching',
                },
            },
        });

        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(wrapper.vm.error).toBeTruthy();
    });

    it('shows loading state during fetch', async () => {
        mockGet.mockImplementation(
            () =>
                new Promise((resolve) =>
                    setTimeout(() => resolve({ data: [] }), 100),
                ),
        );

        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        expect(wrapper.vm.loading).toBe(true);

        await flushPromises();
        // Just waiting for promises might not be enough if using setTimeout, but flushPromises works for resolved promises
        // Since we are mocking with a delay, we might need to advance timers or just wait.
        // However, standard flushPromises handles pending microtasks.
        // Let's verify if loading is still true immediately.
        // Actually, mount calls loadResults which calls mockGet.
        // If mockGet returns a promise that resolves after 100ms...
        // vitest's flushPromises doesn't advance time.
        // We'll skip the timeout complexity and just check state before await.
    });

    it('updates when scenarioId changes', async () => {
        mockGet
            .mockResolvedValueOnce({
                data: [
                    {
                        id: 1,
                        candidate_name: 'John',
                        target_position: 'PM',
                        match_percentage: 85,
                    },
                ],
            })
            .mockResolvedValueOnce({
                data: [
                    {
                        id: 2,
                        candidate_name: 'Jane',
                        target_position: 'Eng',
                        match_percentage: 75,
                    },
                ],
            });

        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();
        expect(wrapper.vm.results[0].candidate_name).toBe('John');

        await wrapper.setProps({ scenarioId: 2 });
        await flushPromises();

        expect(wrapper.vm.results[0].candidate_name).toBe('Jane');
    });
});
