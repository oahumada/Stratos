import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import axios from 'axios';

export interface IntelligenceAggregate {
    id: number;
    organization_id: number | null;
    metric_type: 'rag' | 'llm_call' | 'agent' | 'evaluation';
    source_type: string | null;
    date_key: string;
    total_count: number;
    success_count: number;
    success_rate: number;
    avg_duration_ms: number;
    p50_duration_ms: number;
    p95_duration_ms: number;
    p99_duration_ms: number;
    avg_confidence: number;
    avg_context_count: number;
    created_at: string;
    updated_at: string;
}

export interface IntelligenceSummary {
    total_records: number;
    total_calls: number;
    successful_calls: number;
    success_rate: number;
    avg_duration_ms: number;
    p50_duration_ms: number;
    p95_duration_ms: number;
    p99_duration_ms: number;
    avg_confidence: number;
    avg_context_count: number;
}

export interface AggregatesFilters {
    metric_type?: 'rag' | 'llm_call' | 'agent' | 'evaluation';
    source_type?: string;
    date_from?: string;
    date_to?: string;
    per_page?: number;
}

export function useIntelligenceMetrics() {
    const aggregates = ref<IntelligenceAggregate[]>([]);
    const summary = ref<IntelligenceSummary | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    const currentFilters = ref<AggregatesFilters>({});
    const pagination = ref({
        total: 0,
        per_page: 50,
        current_page: 1,
        last_page: 1,
    });

    const lastUpdated = ref<Date | null>(null);
    let pollingInterval: NodeJS.Timeout | null = null;

    /**
     * Fetch aggregates with optional filters
     */
    const fetchAggregates = async (filters?: AggregatesFilters) => {
        isLoading.value = true;
        error.value = null;

        try {
            const queryParams = new URLSearchParams();

            const mergedFilters = { ...currentFilters.value, ...filters };

            if (mergedFilters.metric_type) {
                queryParams.append('metric_type', mergedFilters.metric_type);
            }
            if (mergedFilters.source_type) {
                queryParams.append('source_type', mergedFilters.source_type);
            }
            if (mergedFilters.date_from) {
                queryParams.append('date_from', mergedFilters.date_from);
            }
            if (mergedFilters.date_to) {
                queryParams.append('date_to', mergedFilters.date_to);
            }
            if (mergedFilters.per_page) {
                queryParams.append('per_page', mergedFilters.per_page.toString());
            }

            const response = await axios.get('/api/intelligence/aggregates', {
                params: Object.fromEntries(queryParams),
            });

            aggregates.value = response.data.data;
            pagination.value = response.data.pagination;
            currentFilters.value = mergedFilters;
            lastUpdated.value = new Date();
        } catch (err) {
            error.value = err instanceof Error ? err.message : 'Error loading aggregates';
            console.error('Error fetching intelligence aggregates:', err);
        } finally {
            isLoading.value = false;
        }
    };

    /**
     * Fetch summary statistics
     */
    const fetchSummary = async (filters?: AggregatesFilters) => {
        try {
            const queryParams = new URLSearchParams();

            const mergedFilters = { ...currentFilters.value, ...filters };

            if (mergedFilters.metric_type) {
                queryParams.append('metric_type', mergedFilters.metric_type);
            }
            if (mergedFilters.date_from) {
                queryParams.append('date_from', mergedFilters.date_from);
            }
            if (mergedFilters.date_to) {
                queryParams.append('date_to', mergedFilters.date_to);
            }

            const response = await axios.get('/api/intelligence/aggregates/summary', {
                params: Object.fromEntries(queryParams),
            });

            summary.value = response.data.data;
        } catch (err) {
            console.error('Error fetching intelligence summary:', err);
        }
    };

    /**
     * Start polling for updates
     */
    const startPolling = (intervalMs = 30000) => {
        if (pollingInterval) {
            clearInterval(pollingInterval);
        }

        pollingInterval = setInterval(async () => {
            await fetchAggregates();
            await fetchSummary();
        }, intervalMs);
    };

    /**
     * Stop polling
     */
    const stopPolling = () => {
        if (pollingInterval) {
            clearInterval(pollingInterval);
            pollingInterval = null;
        }
    };

    /**
     * Formatted time-series data for charts
     */
    const timeSeriesData = computed(() => {
        const dates = aggregates.value.map((a) => a.date_key);
        const uniqueDates = [...new Set(dates)].sort();

        const successRates = uniqueDates.map((date) => {
            const item = aggregates.value.find((a) => a.date_key === date);
            return item ? (item.success_rate * 100).toFixed(2) : 0;
        });

        const avgLatencies = uniqueDates.map((date) => {
            const item = aggregates.value.find((a) => a.date_key === date);
            return item?.avg_duration_ms || 0;
        });

        const p95Latencies = uniqueDates.map((date) => {
            const item = aggregates.value.find((a) => a.date_key === date);
            return item?.p95_duration_ms || 0;
        });

        return {
            labels: uniqueDates,
            successRate: successRates,
            avgLatency: avgLatencies,
            p95Latency: p95Latencies,
        };
    });

    /**
     * Aggregates grouped by metric type for segmented view
     */
    const aggregatesByMetricType = computed(() => {
        const grouped = aggregates.value.reduce(
            (acc, item) => {
                if (!acc[item.metric_type]) {
                    acc[item.metric_type] = [];
                }
                acc[item.metric_type].push(item);
                return acc;
            },
            {} as Record<string, IntelligenceAggregate[]>,
        );

        return grouped;
    });

    onMounted(() => {
        fetchAggregates();
        fetchSummary();
    });

    onBeforeUnmount(() => {
        stopPolling();
    });

    return {
        // State
        aggregates,
        summary,
        isLoading,
        error,
        lastUpdated,
        pagination,
        currentFilters,

        // Methods
        fetchAggregates,
        fetchSummary,
        startPolling,
        stopPolling,

        // Computed
        timeSeriesData,
        aggregatesByMetricType,
    };
}
