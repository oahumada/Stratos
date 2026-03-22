/**
 * Composable para gestionar métricas de interacción de agentes
 * Fetch desde /api/agent-interactions/metrics/*
 */

import axios from 'axios';
import { computed, ref } from 'vue';

export interface AgentMetrics {
    total_interactions: number;
    total_succeeded: number;
    total_failed: number;
    success_rate: number;
    avg_latency_ms: number;
    avg_tokens: number;
    by_agent: Array<{
        agent_name: string;
        count: number;
        success_rate: number;
    }>;
    by_provider: Record<string, number>;
    by_status: { success: number; error: number };
    daily_trend: Array<{
        date: string;
        total: number;
        success: number;
        error: number;
    }>;
    error_distribution: Array<{ error: string; count: number }>;
    latency_percentiles: { p50: number; p95: number; p99: number };
}

export interface FailingAgent {
    agent_name: string;
    error_count: number;
}

export interface AgentLatency {
    agent_name: string;
    avg_latency_ms: number;
    median_latency_ms: number;
    max_latency_ms: number;
}

const baseMetrics: AgentMetrics = {
    total_interactions: 0,
    total_succeeded: 0,
    total_failed: 0,
    success_rate: 0,
    avg_latency_ms: 0,
    avg_tokens: 0,
    by_agent: [],
    by_provider: {},
    by_status: { success: 0, error: 0 },
    daily_trend: [],
    error_distribution: [],
    latency_percentiles: { p50: 0, p95: 0, p99: 0 },
};

export function useAgentMetrics() {
    const metrics = ref<AgentMetrics>(baseMetrics);
    const failingAgents = ref<FailingAgent[]>([]);
    const agentLatencies = ref<AgentLatency[]>([]);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    const lastUpdated = ref<Date | null>(null);
    let pollingInterval: ReturnType<typeof setInterval> | null = null;

    // KPI derivados
    const successRate = computed(() => metrics.value.success_rate || 0);
    const totalInteractions = computed(
        () => metrics.value.total_interactions || 0,
    );
    const avgLatency = computed(() =>
        Math.round(metrics.value.avg_latency_ms || 0),
    );
    const topFailingAgent = computed(() =>
        failingAgents.value.length > 0 ? failingAgents.value[0] : null,
    );
    const topSlowAgent = computed(() =>
        agentLatencies.value.length > 0 ? agentLatencies.value[0] : null,
    );

    // Fetch métricas generales
    const fetchMetrics = async (since?: string) => {
        isLoading.value = true;
        error.value = null;

        try {
            const params = new URLSearchParams();
            if (since) params.append('since', since);

            const response = await axios.get(
                `/api/agent-interactions/metrics/summary?${params.toString()}`,
            );
            if (response.data.success) {
                metrics.value = response.data.data;
                lastUpdated.value = new Date();
            }
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Error fetching metrics';
        } finally {
            isLoading.value = false;
        }
    };

    // Fetch agentes fallidos
    const fetchFailingAgents = async (limit = 10, since?: string) => {
        try {
            const params = new URLSearchParams();
            params.append('limit', limit.toString());
            if (since) params.append('since', since);

            const response = await axios.get(
                `/api/agent-interactions/metrics/failing-agents?${params.toString()}`,
            );
            if (response.data.success) {
                failingAgents.value = response.data.data;
            }
        } catch (err) {
            console.error('Error fetching failing agents:', err);
        }
    };

    // Fetch latencias por agente
    const fetchAgentLatencies = async (since?: string) => {
        try {
            const params = new URLSearchParams();
            if (since) params.append('since', since);

            const response = await axios.get(
                `/api/agent-interactions/metrics/latency-by-agent?${params.toString()}`,
            );
            if (response.data.success) {
                agentLatencies.value = response.data.data;
            }
        } catch (err) {
            console.error('Error fetching agent latencies:', err);
        }
    };

    // Fetch todas las métricas
    const fetchAllMetrics = async (since?: string) => {
        await Promise.all([
            fetchMetrics(since),
            fetchFailingAgents(10, since),
            fetchAgentLatencies(since),
        ]);
    };

    // Polling
    const startPolling = (intervalMs = 30000) => {
        if (pollingInterval) clearInterval(pollingInterval);
        pollingInterval = setInterval(() => {
            fetchAllMetrics();
        }, intervalMs);
    };

    const stopPolling = () => {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    };

    return {
        // State
        metrics,
        failingAgents,
        agentLatencies,
        isLoading,
        error,
        lastUpdated,

        // Computed
        successRate,
        totalInteractions,
        avgLatency,
        topFailingAgent,
        topSlowAgent,

        // Methods
        fetchMetrics,
        fetchFailingAgents,
        fetchAgentLatencies,
        fetchAllMetrics,
        startPolling,
        stopPolling,
    };
}
