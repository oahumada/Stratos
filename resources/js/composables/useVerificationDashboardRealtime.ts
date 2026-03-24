import { ref, computed, onUnmounted } from 'vue'

interface DashboardMetrics {
  currentPhase: string
  confidenceScore: number
  errorRate: number
  retryRate: number
  sampleSize: number
  recentTransitions: any[]
  upcomingTransitions: any[]
  complianceScore: number
  systemHealth: 'healthy' | 'warning' | 'critical'
  timestamp?: string
}

interface RealtimeConnection {
  type: 'websocket' | 'sse' | 'polling'
  connected: boolean
  error?: string
  lastHeartbeat?: Date
}

interface ComplianceData {
  score: number
  passedTests: number
  failedTests: number
  lastAudit: string
  trend: 'up' | 'down' | 'stable'
}

interface RealtimeEvent {
  id: string
  type: 'transition' | 'alert' | 'config' | 'notification'
  severity: 'info' | 'warning' | 'error'
  message: string
  timestamp: string
  data?: any
}

interface MetricsTimeseries {
  labels: string[]
  confidence: number[]
  errorRate: number[]
  retryRate: number[]
  sampleSize: number[]
}

export function useVerificationDashboardRealtime() {
  // State
  const metrics = ref<DashboardMetrics | null>(null)
  const complianceData = ref<ComplianceData | null>(null)
  const realtimeEvents = ref<RealtimeEvent[]>([])
  const metricsHistory = ref<any[]>([])
  const isLoading = ref(false)
  const error = ref<string | null>(null)
  const lastUpdated = ref<Date | null>(null)
  const queryWindow = ref(24)

  // Real-time connection state
  const connectionStatus = ref<RealtimeConnection>({
    type: 'polling',
    connected: false,
  })

  // Private refs for connection management
  let wsConnection: WebSocket | null = null
  let eventSource: EventSource | null = null
  let pollingInterval: ReturnType<typeof setInterval> | null = null
  let heartbeatInterval: ReturnType<typeof setInterval> | null = null

  // Computed
  const systemStatus = computed(() => {
    if (!metrics.value) return 'unknown'
    return metrics.value.systemHealth
  })

  const transitionReadiness = computed(() => {
    if (!metrics.value) return null
    return {
      isReady: metrics.value.confidenceScore >= 90 && metrics.value.errorRate <= 40,
      confidence: metrics.value.confidenceScore,
      errorRate: metrics.value.errorRate,
      retryRate: metrics.value.retryRate,
      blockers: getBlockers(),
    }
  })

  const recentAlerts = computed(() => {
    return realtimeEvents.value.filter(e => e.severity !== 'info').slice(0, 5)
  })

  const isConnected = computed(() => connectionStatus.value.connected)

  const connectionTypeLabel = computed(() => {
    switch (connectionStatus.value.type) {
      case 'websocket':
        return '⚡ WebSocket'
      case 'sse':
        return '📡 SSE'
      case 'polling':
        return '🔄 Polling'
      default:
        return 'Unknown'
    }
  })

  // Methods
  const fetchMetrics = async () => {
    isLoading.value = true
    error.value = null

    try {
      const response = await fetch('/api/deployment/verification/metrics', {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      })

      if (!response.ok) throw new Error(`API error: ${response.status}`)

      const data = await response.json()
      metrics.value = data
      lastUpdated.value = new Date()
    } catch (err) {
      error.value = err instanceof Error ? err.message : 'Failed to fetch metrics'
      console.error('Dashboard fetch error:', err)
    } finally {
      isLoading.value = false
    }
  }

  const fetchComplianceData = async () => {
    if (isLoading.value) return

    try {
      const response = await fetch('/api/deployment/verification/compliance-metrics', {
        headers: {
          Accept: 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
        },
      })

      if (!response.ok) throw new Error(`API error: ${response.status}`)

      const data = await response.json()
      complianceData.value = data
    } catch (err) {
      console.error('Compliance fetch error:', err)
    }
  }

  const fetchMetricsHistory = async (hours: number = 24) => {
    try {
      const response = await fetch(
        `/api/deployment/verification/metrics-history?window=${hours}`,
        {
          headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        }
      )

      if (!response.ok) throw new Error(`API error: ${response.status}`)

      const data = await response.json()
      metricsHistory.value = Array.isArray(data) ? data : data.data || []
    } catch (err) {
      console.error('History fetch error:', err)
    }
  }

  // WebSocket Connection
  const setupWebSocketConnection = async () => {
    try {
      const wsProtocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:'
      const wsHost = window.location.hostname === 'localhost' 
        ? 'localhost:8080' 
        : window.location.host

      const wsUrl = `${wsProtocol}//${wsHost}`

      wsConnection = new WebSocket(wsUrl)

      wsConnection.onopen = () => {
        connectionStatus.value = {
          type: 'websocket',
          connected: true,
          lastHeartbeat: new Date(),
        }
        console.log('✓ WebSocket connected')

        // Subscribe to channels
        wsConnection?.send(JSON.stringify({
          event: 'subscribe',
          channel: `verification-metrics.org-${getUserOrgId()}`,
        }))
      }

      wsConnection.onmessage = (event: MessageEvent) => {
        try {
          const data = JSON.parse(event.data)
          handleRealtimeMessage(data)
          connectionStatus.value.lastHeartbeat = new Date()
        } catch (err) {
          console.error('Failed to parse WebSocket message:', err)
        }
      }

      wsConnection.onerror = (event: Event) => {
        console.warn('⚠ WebSocket error:', event)
        connectionStatus.value.error = 'WebSocket error'
        setupSSEConnection()
      }

      wsConnection.onclose = () => {
        console.log('⚠ WebSocket closed, retrying in 5s...')
        connectionStatus.value.connected = false
        setTimeout(setupWebSocketConnection, 5000)
      }
    } catch (err) {
      console.warn('Failed to setup WebSocket:', err)
      setupSSEConnection()
    }
  }

  // SSE Connection
  const setupSSEConnection = () => {
    try {
      eventSource = new EventSource('/api/deployment/verification/realtime-events?stream=true')

      eventSource.onopen = () => {
        connectionStatus.value = {
          type: 'sse',
          connected: true,
          lastHeartbeat: new Date(),
        }
        console.log('✓ SSE connected')
      }

      eventSource.onmessage = (event: MessageEvent) => {
        try {
          const data = JSON.parse(event.data)
          handleRealtimeMessage(data)
          connectionStatus.value.lastHeartbeat = new Date()
        } catch (err) {
          console.error('Failed to parse SSE message:', err)
        }
      }

      eventSource.onerror = () => {
        console.warn('⚠ SSE error, disconnecting and falling back to polling')
        eventSource?.close()
        eventSource = null
        connectionStatus.value.connected = false
        startPolling(15000)
      }
    } catch (err) {
      console.warn('Failed to setup SSE:', err)
      startPolling(15000)
    }
  }

  // Polling Fallback
  const startPolling = (interval: number = 30000) => {
    if (pollingInterval) clearInterval(pollingInterval)

    const poll = async () => {
      await fetchMetrics()
      await fetchComplianceData()
    }

    poll() // Fetch immediately
    pollingInterval = setInterval(poll, interval)

    connectionStatus.value = {
      type: 'polling',
      connected: true,
      lastHeartbeat: new Date(),
    }

    console.log(`📊 Polling started (${interval}ms)`)

    return () => {
      if (pollingInterval) clearInterval(pollingInterval)
    }
  }

  // Handle real-time messages from WebSocket, SSE, or polling
  const handleRealtimeMessage = (data: any) => {
    if (!data) return

    if (data.type === 'metrics' && data.payload) {
      metrics.value = data.payload
      lastUpdated.value = new Date()
    } else if (data.type === 'alert' && data.payload) {
      realtimeEvents.value.unshift({
        id: `evt_${Date.now()}`,
        type: data.payload.type || 'alert',
        severity: data.payload.severity || 'warning',
        message: data.payload.message,
        timestamp: data.payload.timestamp || new Date().toISOString(),
        data: data.payload.data,
      })
      // Limit to last 100 events
      if (realtimeEvents.value.length > 100) {
        realtimeEvents.value = realtimeEvents.value.slice(0, 100)
      }
    } else if (data.type === 'compliance' && data.payload) {
      complianceData.value = data.payload
    }
  }

  // Setup real-time connection with fallback strategy
  const setupRealtimeConnection = async () => {
    // Check browser support
    if (typeof WebSocket === 'undefined' && typeof EventSource === 'undefined') {
      console.warn('⚠ No real-time support, using polling')
      startPolling(15000)
      return
    }

    // Try WebSocket first
    if (typeof WebSocket !== 'undefined' && 
        (window.location.protocol === 'https:' || window.location.hostname === 'localhost')) {
      setupWebSocketConnection()
    } else if (typeof EventSource !== 'undefined') {
      // Fallback to SSE
      setupSSEConnection()
    } else {
      // Fallback to polling
      startPolling(15000)
    }
  }

  // Disconnect all real-time connections
  const disconnectRealtime = () => {
    if (wsConnection) {
      wsConnection.close()
      wsConnection = null
    }
    if (eventSource) {
      eventSource.close()
      eventSource = null
    }
    if (pollingInterval) {
      clearInterval(pollingInterval)
      pollingInterval = null
    }
    if (heartbeatInterval) {
      clearInterval(heartbeatInterval)
      heartbeatInterval = null
    }
    connectionStatus.value.connected = false
  }

  // Helpers
  const getBlockers = () => {
    if (!metrics.value) return []

    const blockers: any[] = []

    if (metrics.value.confidenceScore < 90) {
      blockers.push({
        metric: 'Confidence Score',
        current: metrics.value.confidenceScore,
        required: 90,
        gap: 90 - metrics.value.confidenceScore,
      })
    }

    if (metrics.value.errorRate > 40) {
      blockers.push({
        metric: 'Error Rate',
        current: metrics.value.errorRate,
        required: 40,
        gap: metrics.value.errorRate - 40,
      })
    }

    if (metrics.value.retryRate > 20) {
      blockers.push({
        metric: 'Retry Rate',
        current: metrics.value.retryRate,
        required: 20,
        gap: metrics.value.retryRate - 20,
      })
    }

    if (metrics.value.sampleSize < 100) {
      blockers.push({
        metric: 'Sample Size',
        current: metrics.value.sampleSize,
        required: 100,
        gap: 100 - metrics.value.sampleSize,
      })
    }

    return blockers
  }

  const exportMetrics = async (format: 'json' | 'csv' | 'pdf') => {
    try {
      const response = await fetch(
        `/api/deployment/verification/export-metrics?format=${format}`,
        {
          headers: { Accept: 'application/json' },
        }
      )

      if (!response.ok) throw new Error('Export failed')

      const blob = await response.blob()
      const url = window.URL.createObjectURL(blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `verification-metrics-${Date.now()}.${format}`
      link.click()
      window.URL.revokeObjectURL(url)
    } catch (err) {
      error.value = `Failed to export as ${format}`
      console.error('Export error:', err)
    }
  }

  const getUserOrgId = (): number => {
    // Get from window context or LocalStorage
    return (window as any).__INITIAL_STATE__?.props?.auth?.user?.organization_id || 1
  }

  // Cleanup on unmount
  onUnmounted(() => {
    disconnectRealtime()
  })

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
    connectionStatus,

    // Computed
    systemStatus,
    transitionReadiness,
    recentAlerts,
    isConnected,
    connectionTypeLabel,

    // Methods
    fetchMetrics,
    fetchComplianceData,
    fetchMetricsHistory,
    setupRealtimeConnection,
    disconnectRealtime,
    startPolling,
    handleRealtimeMessage,
    getBlockers,
    exportMetrics,
  }
}
