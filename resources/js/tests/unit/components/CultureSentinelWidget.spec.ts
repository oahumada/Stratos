import CultureSentinelWidget from '@/components/Dashboard/CultureSentinelWidget.vue';
import { flushPromises, mount } from '@vue/test-utils';
import { beforeEach, describe, expect, it, vi } from 'vitest';

// Mock axios
const mockGet = vi.fn();
vi.mock('axios', () => ({
    default: {
        get: (...args: unknown[]) => mockGet(...args),
    },
}));

describe('CultureSentinelWidget.vue', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('renders with initial empty state', () => {
        const wrapper = mount(CultureSentinelWidget);

        expect(wrapper.exists()).toBe(true);
        expect(wrapper.text()).toContain('CULTURE SENTINEL');
        expect(wrapper.text()).toContain('Pulso Organizacional');
    });

    it('shows empty state message when no scan has been run', () => {
        const wrapper = mount(CultureSentinelWidget);

        expect(wrapper.text()).toContain(
            'Presiona el radar para ejecutar un escaneo',
        );
    });

    it('displays health score after successful scan', async () => {
        mockGet.mockResolvedValue({
            data: {
                data: {
                    health_score: 78,
                    signals: {
                        pulse_count: 25,
                        avg_sentiment: 72,
                        sentiment_trend: 'improving',
                        profiles_analyzed: 15,
                    },
                    anomalies: [],
                    ai_analysis: {
                        diagnosis: 'Organización saludable.',
                        ceo_actions: ['Mantener el rumbo'],
                        critical_node: 'Ninguno',
                    },
                },
            },
        });

        const wrapper = mount(CultureSentinelWidget);

        // Trigger scan via vm method directly (Vuetify buttons don't render as standard HTML in jsdom)
        await wrapper.vm.runScan();
        await flushPromises();

        expect(mockGet).toHaveBeenCalledWith('/api/pulse/health-scan');
        expect(wrapper.vm.healthScore).toBe(78);
    });

    it('displays anomalies when present', async () => {
        mockGet.mockResolvedValue({
            data: {
                data: {
                    health_score: 35,
                    signals: {
                        pulse_count: 3,
                        avg_sentiment: 40,
                        sentiment_trend: 'declining',
                        profiles_analyzed: 5,
                    },
                    anomalies: [
                        {
                            type: 'low_sentiment',
                            severity: 'high',
                            message: 'Sentimiento bajo',
                            icon: 'mdi-emoticon-sad-outline',
                        },
                        {
                            type: 'low_participation',
                            severity: 'low',
                            message: 'Participación baja',
                            icon: 'mdi-account-alert-outline',
                        },
                    ],
                    ai_analysis: {
                        diagnosis: 'Alerta.',
                        ceo_actions: ['Actuar'],
                        critical_node: 'Ingeniería',
                    },
                },
            },
        });

        const wrapper = mount(CultureSentinelWidget);
        await wrapper.vm.runScan();
        await flushPromises();

        expect(wrapper.vm.anomalies).toHaveLength(2);
        expect(wrapper.vm.hasAnomalies).toBe(true);
    });

    it('computes correct health color based on score', async () => {
        const wrapper = mount(CultureSentinelWidget);

        // Green for >= 70
        wrapper.vm.healthScore = 85;
        expect(wrapper.vm.healthColor).toBe('green');

        // Amber for >= 40
        wrapper.vm.healthScore = 55;
        expect(wrapper.vm.healthColor).toBe('amber');

        // Red for < 40
        wrapper.vm.healthScore = 20;
        expect(wrapper.vm.healthColor).toBe('red');
    });

    it('computes correct trend icon', async () => {
        const wrapper = mount(CultureSentinelWidget);

        wrapper.vm.signals.sentiment_trend = 'improving';
        expect(wrapper.vm.trendIcon).toBe('mdi-trending-up');

        wrapper.vm.signals.sentiment_trend = 'declining';
        expect(wrapper.vm.trendIcon).toBe('mdi-trending-down');

        wrapper.vm.signals.sentiment_trend = 'stable';
        expect(wrapper.vm.trendIcon).toBe('mdi-minus');
    });

    it('handles API error gracefully', async () => {
        mockGet.mockRejectedValue(new Error('Network error'));

        const wrapper = mount(CultureSentinelWidget);
        await wrapper.vm.runScan();
        await flushPromises();

        // Should not crash, scanning should be false after error
        expect(wrapper.vm.scanning).toBe(false);
        expect(wrapper.vm.healthScore).toBe(0);
    });

    it('shows AI diagnosis after scan', async () => {
        mockGet.mockResolvedValue({
            data: {
                data: {
                    health_score: 65,
                    signals: {
                        pulse_count: 15,
                        avg_sentiment: 60,
                        sentiment_trend: 'stable',
                        profiles_analyzed: 10,
                    },
                    anomalies: [],
                    ai_analysis: {
                        diagnosis:
                            'La organización está estable pero necesita atención.',
                        ceo_actions: ['Revisar métricas', 'Hablar con líderes'],
                        critical_node: 'Departamento de Ventas',
                    },
                },
            },
        });

        const wrapper = mount(CultureSentinelWidget);
        await wrapper.vm.runScan();
        await flushPromises();

        expect(wrapper.vm.aiAnalysis).not.toBeNull();
        expect(wrapper.vm.aiAnalysis?.diagnosis).toContain('estable');
        expect(wrapper.vm.aiAnalysis?.ceo_actions).toHaveLength(2);
        expect(wrapper.vm.aiAnalysis?.critical_node).toBe(
            'Departamento de Ventas',
        );
    });
});
