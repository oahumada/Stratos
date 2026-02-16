import MatchingResults from '@/components/ScenarioPlanning/Step2/MatchingResults.vue';
import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

global.fetch = vi.fn();

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

        (global.fetch as any).mockResolvedValueOnce({
            ok: true,
            json: async () => mockResults,
        });

        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(global.fetch).toHaveBeenCalledWith(
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

        (global.fetch as any).mockResolvedValueOnce({
            ok: true,
            json: async () => mockResults,
        });

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

        (global.fetch as any).mockResolvedValueOnce({
            ok: true,
            json: async () => mockResults,
        });

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
        (global.fetch as any).mockResolvedValueOnce({
            ok: true,
            json: async () => ({ data: [] }),
        });

        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(wrapper.vm.results).toHaveLength(0);
    });

    it('shows error on fetch failure', async () => {
        (global.fetch as any).mockResolvedValueOnce({
            ok: false,
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
        (global.fetch as any).mockImplementationOnce(() =>
            Promise.resolve({
                ok: true,
                json: async () => ({ data: [] }),
            }),
        );

        const wrapper = mount(MatchingResults, {
            props: {
                scenarioId: 1,
            },
        });

        expect(wrapper.vm.loading).toBe(true);

        await flushPromises();

        expect(wrapper.vm.loading).toBe(false);
    });

    it('updates when scenarioId changes', async () => {
        (global.fetch as any)
            .mockResolvedValueOnce({
                ok: true,
                json: async () => ({
                    data: [
                        {
                            id: 1,
                            candidate_name: 'John',
                            target_position: 'PM',
                            match_percentage: 85,
                        },
                    ],
                }),
            })
            .mockResolvedValueOnce({
                ok: true,
                json: async () => ({
                    data: [
                        {
                            id: 2,
                            candidate_name: 'Jane',
                            target_position: 'Eng',
                            match_percentage: 75,
                        },
                    ],
                }),
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
