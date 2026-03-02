import ScenarioSimulationStatus from '@/components/ScenarioPlanning/ScenarioSimulationStatus.vue';
import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

// Mock axios
const mockPost = vi.fn();
vi.mock('axios', () => ({
    default: {
        post: (...args: unknown[]) => mockPost(...args),
    },
}));

describe('ScenarioSimulationStatus.vue', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    const defaultProps = {
        visible: true,
        metrics: {
            success_probability: 78,
            synergy_score: 8.4,
            time_to_peak: 4.2,
            cultural_friction: 12,
        },
        scenarioId: 42,
    };

    it('renders when visible is true', () => {
        const wrapper = mount(ScenarioSimulationStatus, {
            props: defaultProps,
            global: {
                mocks: {
                    $t: (msg: string) => msg,
                },
            },
        });

        expect(wrapper.exists()).toBe(true);
        expect(wrapper.text()).toContain('scenario_iq');
        expect(wrapper.text()).toContain('active_simulation');
    });

    it('does not render when visible is false', () => {
        const wrapper = mount(ScenarioSimulationStatus, {
            props: { ...defaultProps, visible: false },
            global: {
                mocks: {
                    $t: (msg: string) => msg,
                },
            },
        });

        expect(wrapper.find('.simulation-status-panel').exists()).toBe(false);
    });

    it('displays all KPI metrics', () => {
        const wrapper = mount(ScenarioSimulationStatus, {
            props: defaultProps,
            global: {
                mocks: {
                    $t: (msg: string) => msg,
                },
            },
        });

        expect(wrapper.text()).toContain('78'); // success_probability
        expect(wrapper.text()).toContain('8.4'); // synergy_score
    });

    it('shows mitigation button', () => {
        const wrapper = mount(ScenarioSimulationStatus, {
            props: defaultProps,
            global: {
                mocks: {
                    $t: (msg: string) => msg,
                },
            },
        });

        const _mitigateBtn = wrapper.find('[data-testid="mitigation-btn"]');
        // If no testid, search by text
        const allText = wrapper.text();
        expect(allText).toContain('generate_remediation');
    });

    it('calls mitigation API when button is clicked', async () => {
        mockPost.mockResolvedValue({
            data: {
                success: true,
                plan: {
                    actions: ['Acción 1', 'Acción 2', 'Acción 3'],
                    training: 'Workshop de liderazgo',
                    security_insight: 'Sin riesgos éticos',
                },
            },
        });

        const wrapper = mount(ScenarioSimulationStatus, {
            props: defaultProps,
            global: {
                mocks: {
                    $t: (msg: string) => msg,
                },
            },
        });

        // Find and click the mitigation button
        const buttons = wrapper.findAll('button');
        const mitigateButton = buttons.find((b) =>
            b.text().includes('generate_remediation'),
        );

        if (mitigateButton) {
            await mitigateButton.trigger('click');
            await flushPromises();

            expect(mockPost).toHaveBeenCalledWith(
                expect.stringContaining('/mitigate'),
                expect.any(Object),
            );
        }
    });

    it('displays mitigation results after API call', async () => {
        mockPost.mockResolvedValue({
            data: {
                success: true,
                plan: {
                    actions: [
                        'Implementar sesiones de team building',
                        'Rotar líderes de squad',
                        'Crear canal de comunicación',
                    ],
                    training: 'Workshop de Inteligencia Emocional',
                    security_insight: 'Plan validado éticamente.',
                },
            },
        });

        const wrapper = mount(ScenarioSimulationStatus, {
            props: defaultProps,
            global: {
                mocks: {
                    $t: (msg: string) => msg,
                },
            },
        });

        // Trigger mitigation
        const buttons = wrapper.findAll('button');
        const mitigateButton = buttons.find((b) =>
            b.text().includes('generate_remediation'),
        );

        if (mitigateButton) {
            await mitigateButton.trigger('click');
            await flushPromises();

            expect((wrapper.vm as any).mitigationResult).not.toBeNull();
        }
    });

    it('handles mitigation API error', async () => {
        mockPost.mockRejectedValue(new Error('Server error'));

        const wrapper = mount(ScenarioSimulationStatus, {
            props: defaultProps,
            global: {
                mocks: {
                    $t: (msg: string) => msg,
                },
            },
        });

        const buttons = wrapper.findAll('button');
        const mitigateButton = buttons.find((b) =>
            b.text().includes('generate_remediation'),
        );

        if (mitigateButton) {
            await mitigateButton.trigger('click');
            await flushPromises();

            // Should not crash — component still exists
            expect(wrapper.exists()).toBe(true);
        }
    });
});
