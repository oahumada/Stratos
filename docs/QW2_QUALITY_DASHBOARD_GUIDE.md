# QW-2: LLM Quality Dashboard (RAGAS Metricas)

## 📊 Descripción General

QW-2 implementa un dashboard Vue 3 interactivo para visualizar y monitorear la calidad de salidas LLM evaluadas por RAGAS. Consume el endpoint `/api/qa/llm-evaluations/metrics/summary` (ya existente desde QW-1) y presenta:

- **KPI Cards**: Puntuación promedio, tasa de alucinación, evaluaciones totales, distribuición de calidad
- **Charts**: Distribución de calidad (Pie chart), evaluaciones por proveedor (Bar chart)
- **Health Status**: Estado general de los LLMs (Excelente/Bueno/Requiere Atención)
- **Metrics Table**: Desglose detallado por nivel de calidad
- **Auto-refresh**: Polling cada 30 segundos (configurable)

**Duración Realizada**: 3.5 horas  
**Status**: ✅ Completado

---

## 🎯 Objetivos Logrados

✅ Crear composable `useQualityMetrics.ts` con lógica de fetch y transformación  
✅ Implementar componente Vue 3 con Glass UI cards y Vuetify charts  
✅ Integrar con Phosphor Icons para iconografía visual  
✅ Soporte de auto-refresh/polling (30s) con start/stop controls  
✅ Responsive design (mobile, tablet, desktop)  
✅ i18n ready para multiidioma  
✅ Tipos TypeScript completos (`quality.ts`)  
✅ Tests unitarios Vitest para composable  
✅ Ruta registrada en `routes/web.php`  

---

## 📁 Estructura de Archivos

```
resources/
├── js/
│   ├── composables/
│   │   ├── useQualityMetrics.ts (NEW - 150 lineas)
│   │   └── __tests__/
│   │       └── useQualityMetrics.spec.ts (NEW - 220 lineas)
│   ├── pages/
│   │   └── Intelligence/
│   │       └── QualityDashboard.vue (NEW - 600+ lineas)
│   └── types/
│       └── quality.ts (NEW - 50 lineas)
routes/
└── web.php (MODIFIED - +8 lineas)
```

---

## 🚀 Como Usar

### 1. Acceder al Dashboard

**URL**: `http://localhost:8000/intelligence/quality-dashboard`  
**Ruta**: `intelligence.quality-dashboard`  
**Middleware**: `auth`, `verified`

### 2. Desde Código Vue - Usar Composable

```typescript
import { useQualityMetrics } from '@/composables/useQualityMetrics';

const MyComponent = {
  setup() {
    const { 
      metrics, 
      isLoading, 
      error,
      hallucination,
      qualityPassed,
      qualityFailed,
      topProvider,
      fetchMetrics,
      startPolling,
      stopPolling
    } = useQualityMetrics();

    // Fetch una sola vez
    await fetchMetrics();
    
    // O con polling automático
    startPolling(30000); // cada 30 segundos
    
    // Filtrar por proveedor
    await fetchMetrics('deepseek');
    
    return { metrics, isLoading };
  }
};
```

### 3. API Response Esperado

El dashboard consume:

```
GET /api/qa/llm-evaluations/metrics/summary?provider=<provider>

Response:
{
  success: true,
  data: {
    total_evaluations: 124,
    avg_composite_score: 0.87,
    max_composite_score: 0.95,
    min_composite_score: 0.62,
    quality_distribution: {
      excellent: 45,
      good: 60,
      acceptable: 15,
      poor: 4,
      critical: 0
    },
    provider_distribution: {
      deepseek: 89,
      openai: 25,
      mock: 10
    },
    last_evaluation_at: "2026-03-21T14:23:45Z"
  }
}
```

---

## 🔧 Configuración

### Auto-refresh Interval

Por defecto: **30 segundos**

Para cambiar, modificar en `QualityDashboard.vue`:
```typescript
startPolling(60000); // 60 segundos
```

### Quality Thresholds (HTML hardcoded)

Modificar en `QualityDashboard.vue`:
- **Excelente**: score >= 0.85
- **Bueno**: 0.70 <= score < 0.85
- **Requiere Atención**: score < 0.70

```typescript
const getHealthColor = (score: number) => {
    if (score >= 0.85) return 'text-emerald-400';
    if (score >= 0.7) return 'text-amber-400';
    return 'text-rose-400';
};
```

---

## 📋 Tipos TypeScript

Se definieron en `resources/js/types/quality.ts`:

```typescript
export interface QualityMetrics {
  total_evaluations: number;
  avg_composite_score: number;
  max_composite_score: number;
  min_composite_score: number;
  quality_distribution: QualityDistribution;
  provider_distribution: ProviderMetrics;
  last_evaluation_at: string | null;
}

export interface QualityDistribution {
  excellent: number;
  good: number;
  acceptable: number;
  poor: number;
  critical: number;
}
```

---

## 🧪 Tests

### Composable Tests

Ubicación: `resources/js/composables/__tests__/useQualityMetrics.spec.ts`

Cobertura:
- ✅ Inicialización con métricas por defecto
- ✅ Fetch exitoso de datos
- ✅ Manejo de errores  
- ✅ Cálculo de porcentaje de alucinación
- ✅ Filtrado por proveedor
- ✅ Conteos de calidad (passed/failed)
- ✅ Identificación de proveedor dominante

Ejecutar:
```bash
npm run test:unit:single -- useQualityMetrics.spec.ts
```

---

## 🎨 Componentes Utilizados

### Glass UI Components
- `StCardGlass`: Tarjetas con efecto glass (indicadores, KPIs, tablas)
- `StButtonGlass`: Botones (si se necesitan interacciones futuras)

### Icons (Phosphor)
- `PhStackPlus`: KPI generic icon
- `PhZap`: Quality distribution
- `PhActivity`: Provider metrics
- `PhCheckCircle`: Health status
- `PhClock`: Last evaluation time
- `PhFire`: Top provider
- `PhGauge`: Quality metrics
- `PhWarning`: Error states

### Charts
- `VueApexCharts`: Pie chart (distribución) y Bar chart (proveedores)

---

## 🔄 Data Flow

```
┌─────────────────────────────────────┐
│   QualityDashboard.vue              │
│   ├─ onMounted()                    │
│   │  └─ fetchMetrics()              │
│   │  └─ startPolling(30s)           │
│   ├─ KPI Cards (computed)           │
│   ├─ Charts (VueApexCharts)         │
│   └─ Metrics Table                  │
└──────────────┬──────────────────────┘
               │
         useQualityMetrics()
         ├─ ref: metrics
         ├─ computed: hallucination
         ├─ computed: qualityPassed
         ├─ computed: qualityFailed
         ├─ computed: topProvider
         ├─ fn: fetchMetrics(provider?)
         ├─ fn: startPolling(interval?)
         └─ fn: stopPolling()
               │
               └──> axios.get('/api/qa/llm-evaluations/metrics/summary')
                              │
                              └──> RAGASEvaluationController.metrics()
                                         │
                                         └──> RAGASEvaluator.getOrganizationMetrics()
                                                      │
                                                      └──> LLMEvaluation.query()
```

---

## 🛡️ Multi-Tenancy

El endpoint `/api/qa/llm-evaluations/metrics/summary` **automáticamente filtra por organización** del usuario autenticado:

```php
// En RAGASEvaluationController.metrics():
$metrics = $this->evaluator->getOrganizationMetrics(
    organizationId: auth()->user()->organization_id,
    provider: $provider,
);
```

**El componente Vue NO necesita pasar organization_id** - Sanctum auth ya lo maneja.

---

## 🐛 Debugging

### Logs

Si los metrics no cargan:
1. Abrir DevTools → Console tab
2. Buscar errores de red (Network tab)
3. Verificar que `/api/qa/llm-evaluations/metrics/summary` retorna 200

### Mock Data

Para desarrollo sin evaluaciones reales:
```typescript
// En useQualityMetrics.ts, modificar fetchMetrics():
if (import.meta.env.DEV) {
  metrics.value = mockData; // Definir mockData
  return;
}
```

---

## 🚀 Próximos Pasos (QW-3+)

**QW-3**: `/api/rag/ask` endpoint (RAG básico sin pgvector)  
**QW-4**: Mejoras a Redaction Service  
**QW-5**: Agent Interaction Metrics  
**Sprint 0**: Implementar pgvector + knowledge base indexing  

---

## 📝 Cambios Realizados

### Archivos Creados
- ✅ `resources/js/types/quality.ts`
- ✅ `resources/js/composables/useQualityMetrics.ts`
- ✅ `resources/js/composables/__tests__/useQualityMetrics.spec.ts`
- ✅ `resources/js/pages/Intelligence/QualityDashboard.vue`

### Archivos Modificados
- ✅ `routes/web.php` - Agregada ruta `/intelligence/quality-dashboard`

### Dependencias Usadas (ya presentes)
- ✅ `axios` - HTTP client
- ✅ `@inertiajs/vue3` - Framework
- ✅ `@phosphor-icons/vue` - Iconografía
- ✅ `vue3-apexcharts` - Gráficos
- ✅ `vuetify` - UI framework (implícito en Glass UI)

---

## ✨ Características Adicionales

1. **Responsive Design**: Mobile-first con breakpoints md/lg
2. **Loading States**: Skeleton cards mientras carga
3. **Error Handling**: Mensajes de error claros
4. **Empty State**: Mensaje cuando no hay evaluaciones
5. **Auto-formatting**: Números y fechas localizadas
6. **Polling Control**: start/stop con cleanup en unmount
7. **Color Coding**: Verde/Ambar/Rojo según salud

---

## 🎓 Lecciones Aprendidas

1. **Reutilización**: El endpoint RAGAS ya existía (RAGASEvaluationController)
2. **Patterns**: Composables + Types = Type-safe UI
3. **Performance**: Polling configurable vs. constant fetches
4. **UX**: Glass UI visualmente consistente con dashboard existente
5. **Multi-tenant**: Scopeo automático por organización en backend

---

## 🔗 Referencias

- Endpoint: `app/Http/Controllers/Api/RAGASEvaluationController.php`
- Service: `app/Services/RAGASEvaluator.php`
- Model: `app/Models/LLMEvaluation.php`
- QW-1 Docs: `docs/QW1_LOGGING_PROMPTS_GUIDE.md`
- Strategic Plan: `docs/STRATOS_INTELIGENCIA_COGNITIVA_PLAN_2026.md`

---

## 📊 Commit Message

```
feat: QW-2 — LLM Quality Dashboard con RAGAS metrics y auto-refresh

- Crear composable useQualityMetrics con fetch + polling
- Implementar Vue 3 dashboard con Glass UI + ApexCharts
- Agregar tipos TypeScript para quality metrics
- Incluir tests Vitest para composable
- Registrar ruta /intelligence/quality-dashboard en web.php
- Soportar filtrado por proveedor LLM
- Auto-refresh cada 30s (configurable)
- Responsive design (mobile/tablet/desktop)
```
