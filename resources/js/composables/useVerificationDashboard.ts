import { computed, ref } from 'vue';

interface DashboardMetrics {
    currentPhase: string;
    confidenceScore: number;
    errorRate: number;
    retryRate: number;
    sampleSize: number;
    recentTransitions: any[];
    upcomingTransitions: any[];
    complianceScore: number;
    systemHealth: 'healthy' | 'warning' | 'critical';
}

interface RealtimeConnection {
    type: 'websocket' | 'sse' | 'polling';
    connected: boolean;
    error?: string;
}

interface ComplianceData {
    score: number;
    passedTests: number;
    failedTests: number;
    lastAudit: string;
    trend: 'up' | 'down' | 'stable';
}

interface RealtimeEvent {
    id: string;
    type: 'transition' | 'alert' | 'config' | 'notification';
    severity: 'info' | 'warning' | 'error';
    message: string;
    timestamp: string;
    data?: any;
}

interface MetricsTimeseries {
    labels: string[];
    confidence: number[];
    errorRate: number[];
    retryRate: number[];
    sampleSize: number[];
}

export function useVerificationDashboard() {
    // State
    const metrics = ref<DashboardMetrics | null>(null);
    const complianceData = ref<ComplianceData | null>(null);
    const realtimeEvents = ref<RealtimeEvent[]>([]);
    const metricsHistory = ref<MetricsTimeseries | null>(null);
    const isLoading = ref(false);
    const error = ref<string | null>(null);
    const lastUpdated = ref<Date | null>(null);
    const queryWindow = ref(24); // hours

    // WebSocket/Real-time state
    const connectionStatus = ref<RealtimeConnection>({
        type: 'polling',
        connected: false,
    });
    let wsConnection: any = null;
    let pollingInterval: ReturnType<typeof setInterval> | null = null;
    let eventSource: EventSource | null = null;

    // Computed
    const systemStatus = computed(() => {
        if (!metrics.value) return 'unknown';
        return metrics.value.systemHealth;
    });

    const transitionReadiness = computed(() => {
        if (!metrics.value) return null;
        return {
            isReady:
                metrics.value.confidenceScore >= 90 &&
                metrics.value.errorRate <= 40,
            confidence: metrics.value.confidenceScore,
            errorRate: metrics.value.errorRate,
            retryRate: metrics.value.retryRate,
            blockers: getBlockers(),
        };
    });

    const complianceTrend = computed(() => {
        if (!complianceData.value) return 'unknown';
        return complianceData.value.trend;
    });

    const recentAlerts = computed(() => {
        return realtimeEvents.value
            .filter((e) => e.severity !== 'info')
            .slice(0, 5);
    });

    // Methods
    const fetchMetrics = async () => {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await fetch(
                '/api/deployment/verification/metrics',
                {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                },
            );

            if (!response.ok) throw new Error(`API error: ${response.status}`);

            const data = await response.json();
            metrics.value = data.data;

            lastUpdated.value = new Date();
        } catch (err) {
            error.value =
                err instanceof Error ? err.message : 'Failed to fetch metrics';
            console.error('Dashboard fetch error:', err);
        } finally {
            isLoading.value = false;
        }
    };

    const fetchComplianceData = async () => {
        try {
            const response = await fetch(
                '/api/deployment/verification/compliance-metrics',
                {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                },
            );

            if (!response.ok) throw new Error(`API error: ${response.status}`);

            const data = await response.json();
            complianceData.value = data.data;
        } catch (err) {
            console.error('Compliance fetch error:', err);
        }
    };

    const fetchMetricsHistory = async () => {
        try {
            const response = await fetch(
                `/api/deployment/verification/metrics-history?window=${queryWindow.value}`,
                {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                },
            );

            if (!response.ok) throw new Error(`API error: ${response.status}`);

            const data = await response.json();
            metricsHistory.value = data.data;
        } catch (err) {
            console.error('History fetch error:', err);
        }
    };

    const subscribeToRealtimeEvents = () => {
        // Simulate real-time updates via polling
        // In production, use WebSockets (Laravel Reverb, etc)
        const interval = setInterval(async () => {
            try {
                const response = await fetch(
                    '/api/deployment/verification/realtime-events',
                    {
                        headers: { Accept: 'application/json' },
                    },
                );

                if (response.ok) {
                    const data = await response.json();
                    realtimeEvents.value = [
                        data.event,
                        ...realtimeEvents.value.slice(0, 49),
                    ];
                }
            } catch (err) {
                console.error('Realtime subscribe error:', err);
            }
        }, 5000); // Poll every 5 seconds

        return () => clearInterval(interval);
    };

    const startPolling = (interval = 30000) => {
        const pollInterval = setInterval(() => {
            fetchMetrics();
            fetchComplianceData();
            fetchMetricsHistory();
        }, interval);

        return () => clearInterval(pollInterval);
    };

    const getBlockers = () => {
        if (!metrics.value) return [];

        const blockers = [];

        if (metrics.value.confidenceScore < 90) {
            blockers.push({
                metric: 'Confidence',
                current: metrics.value.confidenceScore,
                required: 90,
                gap: 90 - metrics.value.confidenceScore,
            });
        }

        if (metrics.value.errorRate > 40) {
            blockers.push({
                metric: 'Error Rate',
                current: metrics.value.errorRate,
                required: 40,
                gap: metrics.value.errorRate - 40,
            });
        }

        if (metrics.value.retryRate > 20) {
            blockers.push({
                metric: 'Retry Rate',
                current: metrics.value.retryRate,
                required: 20,
                gap: metrics.value.retryRate - 20,
            });
        }

        if (metrics.value.sampleSize < 100) {
            blockers.push({
                metric: 'Sample Size',
                current: metrics.value.sampleSize,
                required: 100,
                gap: 100 - metrics.value.sampleSize,
            });
        }

        return blockers;
    };

    const exportMetrics = async (format: 'json' | 'csv' | 'pdf') => {
        try {
            const response = await fetch(
                `/api/deployment/verification/export-metrics?format=${format}`,
                {
                    headers: { Accept: 'application/json' },
                },
            );

            if (!response.ok) throw new Error('Export failed');

            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `verification-metrics-${Date.now()}.${format}`;
            link.click();
            window.URL.revokeObjectURL(url);
        } catch (err) {
            error.value = `Failed to export as ${format}`;
            console.error('Export error:', err);
        }
    };

    return {
        // State
        metrics,
        complianceData,
        realtimeEvents,
        metricsHistory,
        isLoading,
        error,
        lastUpdated,
        queryWindow,

        // Computed
        systemStatus,
        transitionReadiness,
        complianceTrend,
        recentAlerts,

        // Methods
        fetchMetrics,
        fetchComplianceData,
        fetchMetricsHistory,
        subscribeToRealtimeEvents,
        startPolling,
        getBlockers,
        exportMetrics,
    };
}
