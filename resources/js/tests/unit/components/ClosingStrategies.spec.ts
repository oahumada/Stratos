import ClosingStrategies from '@/components/ScenarioPlanning/Step4/ClosingStrategies.vue';
import { flushPromises, mount } from '@vue/test-utils';
import axios from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

vi.mock('axios');
const mockedAxios = axios as vi.Mocked<typeof axios>;

// Mock useNotification
vi.mock('@/composables/useNotification', () => ({
    useNotification: () => ({
        showSuccess: vi.fn(),
        showError: vi.fn(),
    }),
}));

describe('ClosingStrategies.vue', () => {
    const mockStrategies = [
        {
            id: 1,
            strategy: 'bot',
            strategy_name: 'AI Agent',
            skill_name: 'Data Analysis',
            description: 'Use AI for analysis',
            estimated_cost: 5000,
            estimated_time_weeks: 4,
            risk_level: 'low',
            status: 'proposed',
            blueprint: {
                human_leverage: 20,
                synthetic_leverage: 80,
                role_name: 'Analyst',
            },
        },
        {
            id: 2,
            strategy: 'build',
            strategy_name: 'Training',
            skill_name: 'Management',
            description: 'Internal workshop',
            estimated_cost: 2000,
            estimated_time_weeks: 8,
            risk_level: 'medium',
            status: 'approved',
            blueprint: null,
        },
    ];

    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders component and loads strategies', async () => {
        mockedAxios.get.mockImplementation((url) => {
            if (url.includes('strategies')) {
                return Promise.resolve({ data: { data: mockStrategies } });
            }
            return Promise.resolve({ data: { data: [] } });
        });

        const wrapper = mount(ClosingStrategies, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        expect(mockedAxios.get).toHaveBeenCalledWith(
            '/api/strategic-planning/scenarios/1/strategies',
        );
        expect(wrapper.findAll('.strategy-card')).toHaveLength(2);
    });

    it('displays blueprint mix when available', async () => {
        mockedAxios.get.mockImplementation((url) => {
            if (url.includes('strategies')) {
                return Promise.resolve({ data: { data: mockStrategies } });
            }
            return Promise.resolve({ data: { data: [] } });
        });

        const wrapper = mount(ClosingStrategies, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        // Check for the mix display in the first card (AI Agent)
        expect(wrapper.text()).toContain('Mix de Talento');
        expect(wrapper.text()).toContain('20% H / 80% S');
    });

    it('generates new strategies when button is clicked', async () => {
        mockedAxios.get.mockResolvedValue({ data: { data: [] } });
        mockedAxios.post.mockResolvedValue({ data: { success: true } });

        const wrapper = mount(ClosingStrategies, {
            props: {
                scenarioId: 1,
            },
        });

        await flushPromises();

        const generateBtn = wrapper
            .findAll('button')
            .find((b) => b.text().includes('Generar con IA'));
        if (!generateBtn) throw new Error('Button not found');
        await generateBtn.trigger('click');

        expect(mockedAxios.post).toHaveBeenCalledWith(
            '/api/strategic-planning/scenarios/1/refresh-suggested-strategies',
        );
        // It should reload after generation. loadStrategies makes 2 calls. (2 initial + 2 after reload = 4 total)
        expect(mockedAxios.get).toHaveBeenCalledTimes(4);
    });

    it('shows different colors for different strategy types', () => {
        const wrapper = mount(ClosingStrategies, {
            props: {
                scenarioId: 1,
            },
        });

        // Accessing the methods via vm
        const vm = wrapper.vm as any;
        expect(vm.getStrategyColor('build')).toBe('indigo');
        expect(vm.getStrategyColor('bot')).toBe('purple');
        expect(vm.getStrategyColor('buy')).toBe('emerald');
    });
});
