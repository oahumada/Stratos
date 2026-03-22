# QW-5: Agent Interaction Metrics

## Descripción General

QW-5 implementa un sistema completo de observabilidad para las interacciones entre agentes IA y modelos LLM. Proporciona métricas detalladas de latencia, tasa de éxito, distribución de tokens y tendencias temporales.

## Arquitectura

### Componentes Backend

#### 1. **AgentInteraction Model**
Modelo Eloquent que almacena cada interacción entre un agente y un LLM.

```php
// app/Models/AgentInteraction.php
- agent_name: string (nombre del agente)
- user_id: int (usuario que disparó la interacción)
- organization_id: int (multi-tenancy)
- prompt_hash: string (SHA-256 del prompt)
- latency_ms: int (tiempo de respuesta en ms)
- token_count: int (estimación de tokens)
- status: enum('success', 'error')
- error_message: string (opcional)
- input_length: int
- output_length: int
- provider: string (OpenAI, Anthropic, etc.)
- model: string (gpt-4, claude-3, etc.)
- context: string (contexto de ejecución)
```

#### 2. **AgentInteractionMetricsService**
Servicio que agrega y calcula métricas desde la base de datos.

**Métodos públicos:**

```php
getOrganizationMetrics(?DateTimeInterface $since = null): array
// Retorna:
// - summary (total, tasa_éxito, latencia_promedio, tokens)
// - by_agent (desglose por agente)
// - by_provider (distribución por proveedor)
// - by_status (cuenta de éxito/error)
// - daily_trend (series temporal de 30 días)
// - error_distribution (top 10 errores)
// - latency_percentiles (P50, P95, P99)

getTopFailingAgents(?DateTimeInterface $since = null, int $limit = 10): array
// Retorna agentes con más errores

getAverageLatencyByAgent(?DateTimeInterface $since = null): array
// Retorna latencia promedio, mediana y máxima por agente

getDailyTrend(?DateTimeInterface $since = null): array
// Retorna tendencia diaria de 30 días (con zero-fill)
```

**Características:**
- Cache de 1 hora TTL con key pattern: `agent_metrics:{org_id}:{date}`
- Multi-tenant scoping automático
- Manejo elegante de datasets vacíos

#### 3. **AiOrchestratorService Instrumentation**
El servicio central de orquestación de agentes ha sido actualizado para registrar automáticamente cada interacción.

```php
// Captura timing antes del LLM call
$startMicrotime = microtime(true);
$response = $this->generate(...);  // LLM call
// Calcula latencia en ms
$latencyMs = intval((microtime(true) - $startMicrotime) * 1000);

// En éxito:
$this->recordInteraction($agentName, $promptHash, $latencyMs, $tokenCount, 'success', ...);

// En error:
$this->recordInteraction($agentName, $promptHash, $latencyMs, 0, 'error', $exception->getMessage(), ...);
```

**Integración:**
- Todas las llamadas a `AiOrchestratorService::agentThink()` son auto-registradas
- Soporta múltiples proveedores LLM (OpenAI, Anthropic, etc.)
- Valida aislamiento multi-tenant automáticamente

### Componentes Frontend

#### 1. **useAgentMetrics Composable**
```typescript
// resources/js/composables/useAgentMetrics.ts
const {
    metrics,                  // Métricas globales
    failingAgents,           // Top 10 agentes con errores
    agentLatencies,          // Latencias por agente
    isLoading,
    error,
    lastUpdated,
    
    // KPIs derivados
    successRate,             // Tasa de éxito
    totalInteractions,       // Total de interacciones
    avgLatency,              // Latencia promedio en ms
    topFailingAgent,         // Agente con más errores
    topSlowAgent,            // Agente más lento
    
    // Métodos
    fetchAllMetrics(since?: string),
    startPolling(intervalMs: number),
    stopPolling(),
} = useAgentMetrics();
```

#### 2. **AgentMetricsDashboard Vue Component**
```vue
<!-- resources/js/pages/Intelligence/AgentMetricsDashboard.vue -->
```

**Visualizaciones:**
- **KPI Cards**: Interacciones totales, tasa éxito, latencia promedio, agentes fallidos
- **Bar Chart**: Interacciones y tasa éxito por agente
- **Pie Chart**: Distribución por proveedor LLM
- **Line Chart (ECharts)**: Tendencia diaria con 3 series (Total, Exitosas, Errores)
- **Horizontal Bar Chart**: Top 5 errores más frecuentes
- **Failing Agents Table**: Top 5 agentes con errores
- **Percentiles Card**: P50, P95, P99 de latencia

**Features:**
- Auto-polling cada 30 segundos
- Responsive design (móvil, tablet, desktop)
- Dark mode (tema por defecto)
- Carga elegante con skeleton states
- Manejo de errores amigable

## API Endpoints

### 1. GET `/api/agent-interactions/metrics/summary`

**Query Parameters:**
- `since` (optional): Fecha ISO 8601 (default: últimos 30 días)

**Response:**
```json
{
  "success": true,
  "data": {
    "summary": {
      "total_interactions": 1250,
      "total_succeeded": 1187,
      "total_failed": 63,
      "success_rate": 0.9496,
      "avg_latency_ms": 1234.5,
      "avg_tokens": 456
    },
    "by_agent": [
      {
        "agent_name": "TurnoverPredictor",
        "count": 350,
        "success_rate": 0.9857
      }
    ],
    "by_provider": {
      "openai": 700,
      "anthropic": 550
    },
    "daily_trend": [
      {
        "date": "2025-03-22",
        "total": 45,
        "success": 43,
        "error": 2
      }
    ],
    "error_distribution": [
      {
        "error": "rate_limit_exceeded",
        "count": 25
      }
    ],
    "latency_percentiles": {
      "p50": 892,
      "p95": 3456,
      "p99": 5234
    }
  }
}
```

### 2. GET `/api/agent-interactions/metrics/failing-agents`

**Query Parameters:**
- `limit` (optional): Número de agentes (default: 10)
- `since` (optional): Fecha ISO 8601

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "agent_name": "CompetencyAssessment",
      "error_count": 12
    }
  ]
}
```

### 3. GET `/api/agent-interactions/metrics/latency-by-agent`

**Query Parameters:**
- `since` (optional): Fecha ISO 8601

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "agent_name": "ImpactEngine",
      "avg_latency_ms": 2345,
      "median_latency_ms": 1987,
      "max_latency_ms": 8765
    }
  ]
}
```

**Todos los endpoints requieren autenticación (`Authorization: Bearer {token}`).**

## Rutas Web

| URL | Route Name | Descripción |
|-----|-----------|-------------|
| `/intelligence/agent-metrics` | `intelligence.agent-metrics` | Dashboard completo de métricas |

## Uso del Dashboard

### Accediendo al Dashboard

```typescript
import { intelligence_agent_metrics } from '@/routes';

// En un componente Vue
<Link :href="intelligence_agent_metrics()">
  Ver Métricas de Agentes
</Link>
```

### Usando el Composable Directamente

```typescript
import { useAgentMetrics } from '@/composables/useAgentMetrics';

const {
  metrics,
  fetchAllMetrics,
  startPolling,
} = useAgentMetrics();

onMounted(() => {
  // Cargar métricas con filtro de últimos 7 días
  const sevenDaysAgo = new Date();
  sevenDaysAgo.setDate(sevenDaysAgo.getDate() - 7);
  
  fetchAllMetrics(sevenDaysAgo.toISOString().split('T')[0]);
  
  // Auto-actualizar cada 60 segundos
  startPolling(60000);
});
```

## Base de Datos

### Migración

```sql
CREATE TABLE agent_interactions (
  id BIGSERIAL PRIMARY KEY,
  agent_name VARCHAR(255) NOT NULL,
  user_id BIGINT UNSIGNED,
  organization_id BIGINT UNSIGNED NOT NULL,
  prompt_hash VARCHAR(64) UNIQUE,
  latency_ms INT UNSIGNED,
  token_count INT UNSIGNED,
  status ENUM('success', 'error'),
  error_message TEXT,
  input_length INT UNSIGNED,
  output_length INT UNSIGNED,
  provider VARCHAR(100),
  model VARCHAR(100),
  context TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  -- Indexes
  INDEX idx_org_date (organization_id, created_at),
  INDEX idx_agent_date (agent_name, created_at),
  INDEX idx_status_date (status, created_at),
  UNIQUE KEY idx_prompt_hash (prompt_hash),
  
  -- Foreign Keys
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (organization_id) REFERENCES organizations(id) ON DELETE CASCADE
);
```

## Testing

### Ejecutar Tests

```bash
# Todos los tests de agent metrics
php artisan test tests/Feature/AgentInteractionMetricsTest.php
php artisan test tests/Feature/AgentInteractionMetricsApiTest.php

# O juntos
php artisan test --filter=AgentInteractionMetrics
```

### Cobertura de Tests

- ✅ Recording exitoso de interacciones
- ✅ Cálculo de métricas organizacionales
- ✅ Desglose por agente
- ✅ Top agentes fallidos
- ✅ Percentiles de latencia
- ✅ Aislamiento multi-tenant
- ✅ Estados vacíos
- ✅ Autenticación en endpoints API

## Troubleshooting

### No se ven métricas en el dashboard

1. Verifica que hay interacciones registradas:
```bash
php artisan tinker
>>> App\Models\AgentInteraction::count()
```

2. Verifica que la ruta está registrada:
```bash
php artisan route:list | grep agent-metrics
```

3. Verifica logs de la app:
```bash
tail -f storage/logs/laravel.log
```

### API retorna 500

1. Verifica que el usuario está autenticado
2. Revisa que organization_id está disponible en el token
3. Verifica los logs de Laravel

### Dashboard no se carga

1. Verifica que Node.js/npm están actualizado
2. Ejecuta:
```bash
npm run build
# o
npm run dev
```

3. Limpia caché:
```bash
php artisan config:clear
php artisan cache:clear
```

## Ejemplos de Integración

### Monitorear en Tiempo Real

```typescript
import { useAgentMetrics } from '@/composables/useAgentMetrics';
import { computed, onMounted, onBeforeUnmount } from 'vue';

export default {
  setup() {
    const { metrics, failingAgents, startPolling, stopPolling, fetchAllMetrics } = useAgentMetrics();
    
    const successRatePercentage = computed(() => 
      (metrics.value.success_rate * 100).toFixed(1)
    );
    
    onMounted(() => {
      fetchAllMetrics();
      // Actualizar cada 15 segundos
      startPolling(15000);
    });
    
    onBeforeUnmount(() => stopPolling());
    
    return {
      metrics,
      failingAgents,
      successRatePercentage,
    };
  }
};
```

### Alerta si la Tasa de Éxito Baja

```typescript
watch(
  () => metrics.value.success_rate,
  (newRate) => {
    if (newRate < 0.9) {  // Menos del 90%
      notification.warn(`⚠️ Tasa de éxito baja: ${(newRate * 100).toFixed(1)}%`);
    }
  }
);
```

## Notas de Implementación

- **Multi-tenant**: Todas las queries se filtran automáticamente por `organization_id`
- **Performance**: Cache de 1 hora en métricas resumidas
- **Escalabilidad**: Agregaciones indexadas en base de datos
- **Seguridad**: Toda autenticación vía Sanctum, sin acceso cross-tenant

## Próximas Mejoras

- [ ] Exportar métricas a CSV
- [ ] Alertas configurables por umbral
- [ ] Drilldown al detalle de interacciones específicas
- [ ] Correlación con calidad (RAGAS scores)
- [ ] Predicción de latencia con ML
- [ ] Integración con APM (Application Performance Monitoring)

## Archivos Creados/Modificados

| Archivo | Tipo | Líneas | Descripción |
|---------|------|--------|-------------|
| `app/Models/AgentInteraction.php` | NEW | 40 | Modelo Eloquent |
| `database/migrations/2026_03_22_...` | NEW | 45 | Migración de DB |
| `app/Services/AgentInteractionMetricsService.php` | NEW | 280 | Servicio de métricas |
| `app/Services/AiOrchestratorService.php` | UPDATED | +70 | Instrumentación |
| `app/Http/Controllers/Api/AgentInteractionMetricsController.php` | NEW | 67 | Controlador API |
| `resources/js/composables/useAgentMetrics.ts` | NEW | 180 | Composable Vue |
| `resources/js/pages/Intelligence/AgentMetricsDashboard.vue` | NEW | 450+ | Componente dashboard |
| `routes/web.php` | UPDATED | +4 | Ruta del dashboard |
| `routes/api.php` | UPDATED | +6 | Rutas API (3 endpoints) |
| `tests/Feature/AgentInteractionMetricsTest.php` | NEW | 149 | Tests unitarios |
| `tests/Feature/AgentInteractionMetricsApiTest.php` | NEW | 76 | Tests API |

**Total: 11 archivos, 12 tests ✅ (todos passing)**
