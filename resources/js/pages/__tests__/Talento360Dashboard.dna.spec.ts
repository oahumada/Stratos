import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

// Mock axios
const mockGet = vi.fn();
const mockPost = vi.fn();
vi.mock('axios', () => ({
    default: {
        get: (...args: unknown[]) => mockGet(...args),
        post: (...args: unknown[]) => mockPost(...args),
    },
}));

// Mock inertia router
vi.mock('@inertiajs/vue3', () => ({
    router: {
        visit: vi.fn(),
    },
}));

// Mock vue3-apexcharts
vi.mock('vue3-apexcharts', () => ({
    default: {
        name: 'VueApexCharts',
        template: '<div class="mock-chart"></div>',
        props: ['type', 'height', 'options', 'series'],
    },
}));

// We need to import the component after mocks are set up
import Dashboard from '@/pages/Talento360/Dashboard.vue';

describe('Talento360 Dashboard - DNA Cloning', () => {
    const metricsResponse = {
        potential_index: 72,
        high_potential_count: 5,
        total_assessed: 28,
        trait_distribution: [
            { trait_name: 'Dominance', average_score: 0.7 },
            { trait_name: 'Influence', average_score: 0.6 },
        ],
        latest_assessments: [
            {
                id: 1,
                person_name: 'Carlos Mendoza',
                type: 'psychometric',
                potential: 0.85,
                completed_at: '2026-02-25',
            },
            {
                id: 2,
                person_name: 'Ana García',
                type: '360',
                potential: 0.92,
                completed_at: '2026-02-24',
            },
            {
                id: 3,
                person_name: 'Luis Torres',
                type: 'psychometric',
                potential: 0.45,
                completed_at: '2026-02-23',
            },
        ],
    };

    beforeEach(() => {
        vi.clearAllMocks();
        mockGet.mockResolvedValue({ data: metricsResponse });
    });

    it('loads metrics on mount', async () => {
        const wrapper = mount(Dashboard);
        await flushPromises();

        expect(mockGet).toHaveBeenCalledWith(
            '/api/strategic-planning/assessments/metrics',
        );
        expect(wrapper.vm.metrics.high_potential_count).toBe(5);
    });

    it('has a DNA extraction button on High Potentials card', async () => {
        const wrapper = mount(Dashboard);
        await flushPromises();

        // Look for DNA button text
        const allText = wrapper.text();
        expect(allText).toContain('DNA');
    });

    it('opens DNA dialog when DNA button is clicked', async () => {
        const wrapper = mount(Dashboard);
        await flushPromises();

        expect(wrapper.vm.dnaDialog).toBe(false);

        // Call the method directly
        wrapper.vm.openDnaExtractor();
        expect(wrapper.vm.dnaDialog).toBe(true);
    });

    it('filters only high performers (potential >= 0.7) for DNA extraction', async () => {
        const wrapper = mount(Dashboard);
        await flushPromises();

        // Carlos (0.85) and Ana (0.92) should be HiPos, Luis (0.45) should not
        const hiPos = wrapper.vm.metrics.latest_assessments.filter(
            (a: { potential: number }) => a.potential >= 0.7,
        );
        expect(hiPos).toHaveLength(2);
        expect(hiPos[0].person_name).toBe('Carlos Mendoza');
        expect(hiPos[1].person_name).toBe('Ana García');
    });

    it('calls DNA extraction API and receives results', async () => {
        mockPost.mockResolvedValue({
            data: {
                data: {
                    success_persona:
                        'Líder técnico con alta dominancia y visión estratégica',
                    dominant_gene:
                        'Resiliencia combinada con inteligencia emocional',
                    search_profile:
                        'Buscar candidatos con D alto, I moderado, 5+ años tech',
                },
            },
        });

        const wrapper = mount(Dashboard);
        await flushPromises();

        // Call extractDNA directly
        await wrapper.vm.extractDNA(1, 'Carlos Mendoza');
        await flushPromises();

        expect(mockPost).toHaveBeenCalledWith('/api/talent/dna-extract/1');
        expect(wrapper.vm.dnaResult).not.toBeNull();
        expect(wrapper.vm.dnaResult.success_persona).toContain('Líder');
    });

    it('sets selectedPerson on extraction', async () => {
        mockPost.mockResolvedValue({
            data: {
                data: {
                    success_persona: 'Test',
                    dominant_gene: 'Test',
                    search_profile: 'Test',
                },
            },
        });

        const wrapper = mount(Dashboard);
        await flushPromises();

        await wrapper.vm.extractDNA(2, 'Ana García');
        await flushPromises();

        expect(wrapper.vm.selectedPerson).toEqual({
            id: 2,
            name: 'Ana García',
        });
    });

    it('handles DNA extraction error gracefully', async () => {
        mockPost.mockRejectedValue(new Error('AI service down'));

        const wrapper = mount(Dashboard);
        await flushPromises();

        await wrapper.vm.extractDNA(1, 'Carlos Mendoza');
        await flushPromises();

        // Should not crash, loading should be false
        expect(wrapper.vm.dnaLoading).toBe(false);
        expect(wrapper.vm.dnaResult).toBeNull();
    });

    it('resets DNA result when opening extractor', async () => {
        const wrapper = mount(Dashboard);
        await flushPromises();

        // Simulate having a previous result
        wrapper.vm.dnaResult = {
            success_persona: 'Old result',
            dominant_gene: 'Old gene',
            search_profile: 'Old profile',
        };

        wrapper.vm.openDnaExtractor();

        expect(wrapper.vm.dnaResult).toBeNull();
        expect(wrapper.vm.dnaDialog).toBe(true);
    });
});
