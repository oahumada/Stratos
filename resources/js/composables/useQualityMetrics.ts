/**
 * Composable para gestionar métricas RAGAS LLM Quality Dashboard
 * Fetch desde /api/qa/llm-evaluations/metrics/summary
 */

import type { QualityMetrics, QualityResponse } from '@/types/quality';
import axios from 'axios';
import { computed, ref } from 'vue';

const baseMetrics: QualityMetrics = {
    total_evaluations: 0,
    avg_composite_score: 0,
    max_composite_score: 0,
    min_composite_score: 0,
    quality_distribution: {
        excellent: 0,
        good: 0,
        acceptable: 0,
        poor: 0,
        critical: 0,
    },
    provider_distribution: {},
    last_evaluation_at: null,
};

export function useQualityMetrics() {
    const metrics = ref<QualityMetrics>(baseMetrics);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    const lastUpdated = ref<Date | null>(null);

    // KPI derivados
    const hallucination = computed(() => {
        if (!metrics.value.quality_distribution) return 0;
        const total = Object.values(metrics.value.quality_distribution).reduce(
            (a, b) => a + b,
            0,
        );
        return total > 0
            ? (
                  (metrics.value.quality_distribution.poor +
                      metrics.value.quality_distribution.critical) /
                  total
              ).toFixed(1)
            : '0';
    });

    const qualityPassed = computed(() => {
        if (!metrics.value.quality_distribution) return 0;
        return (
            metrics.value.quality_distribution.excellent +
            metrics.value.quality_distribution.good
        );
    });

    const qualityFailed = computed(() => {
        if (!metrics.value.quality_distribution) return 0;
        return (
            metrics.value.quality_distribution.poor +
            metrics.value.quality_distribution.critical
        );
    });

    const topProvider = computed(() => {
        if (!metrics.value.provider_distribution) return 'N/A';
        const entries = Object.entries(metrics.value.provider_distribution);
        if (entries.length === 0) return 'N/A';
        return entries.reduce((max, current) =>
            current[1] > max[1] ? current : max,
        )[0];
    });

    // Fetch métricas
    const fetchMetrics = async (provider?: string) => {
        isLoading.value = true;
        error.value = null;

        try {
            const url = provider
                ? `/api/qa/llm-evaluations/metrics/summary?provider=${provider}`
                : '/api/qa/llm-evaluations/metrics/summary';

            const response = await axios.get<QualityResponse>(url);

            if (response.data.success && response.data.data) {
                metrics.value = response.data.data;
                lastUpdated.value = new Date();
            } else {
                error.value = response.data.message || 'Error fetching metrics';
            }
        } catch (err: any) {
            error.value =
                err.response?.data?.message ||
                err.message ||
                'Failed to fetch quality metrics';
            console.error('[QualityMetrics] Error:', err);
        } finally {
            isLoading.value = false;
        }
    };

    // Polling automático (opcional)
    let pollInterval: ReturnType<typeof setInterval> | null = null;

    const startPolling = (intervalMs: number = 30000, provider?: string) => {
        if (pollInterval) clearInterval(pollInterval);
        fetchMetrics(provider); // Fetch inicial
        pollInterval = setInterval(() => fetchMetrics(provider), intervalMs);
    };

    const stopPolling = () => {
        if (pollInterval) {
            clearInterval(pollInterval);
            pollInterval = null;
        }
    };

    return {
        metrics,
        isLoading,
        error,
        lastUpdated,
        // Derivados
        hallucination,
        qualityPassed,
        qualityFailed,
        topProvider,
        // Methods
        fetchMetrics,
        startPolling,
        stopPolling,
    };
}
