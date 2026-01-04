# 📊 Dashboard de Talento - Módulo Analytics

**Fecha:** 3 de enero de 2026  
**Status:** ✅ Beta - Datos Mockados (Listo para conectar a datos reales)

---

## 🎯 Propósito

Proporcionar una **visión integral del talento organizacional** mediante indicadores clave (KPIs) que integren:

- 📈 Métricas de talento general
- 🎯 Marketplace de oportunidades internas
- 👥 Reclutamiento (interno vs externo)
- 📚 Desarrollo y capacitación
- ⚠️ Indicadores de riesgo

---

## 📍 Ubicación

**Ruta:** `http://localhost/dashboard/analytics`  
**Componente:** `/src/resources/js/pages/Dashboard/Analytics.vue`  
**Nombre de ruta:** `dashboard.analytics`

---

## 🎨 Secciones del Dashboard

### 1. Indicadores Clave (KPIs) - 4 Cards principales

```
┌─────────────────┬──────────────────┬──────────────────┬─────────────┐
│  Empleados Tot. │  Posiciones Ab.   │  Colocación Int. │  Retención  │
│      124        │        12         │       65%        │     92%     │
└─────────────────┴──────────────────┴──────────────────┴─────────────┘
```

**Propósito:** Visión rápida de métricas críticas de la organización

**Datos mostrados:**
- Total de empleados activos
- Vacantes abiertas en el sistema
- Tasa de colocación interna (% de vacantes cubiertas con talento interno)
- Tasa de retención anual

---

### 2. Estado del Talento (Talent Readiness)

**Card izquierdo con 3 secciones:**

```
🚀 Listos para Promoción: 23 empleados
  → Tienen match ≥80% para roles superiores
  
📚 En Desarrollo: 45 empleados
  → Actualmente en programas de capacitación
  
⚠️ Con Gaps Críticos: 87 empleados
  → Requieren desarrollo para alcanzar siguiente nivel
```

**Propósito:** Entender el "estado de salud" del talento disponible

---

### 3. Distribución de Candidatos Marketplace

**Card derecho con 4 rangos de match:**

```
⭐ Excelente (≥80%):   7 candidatos   (22%)
✅ Bueno (70-79%):    12 candidatos   (39%)
⏰ Moderado (50-69%):  5 candidatos   (16%)
🔴 Bajo (40-49%):     3 candidatos   (10%)
```

**Propósito:** Medir qué porcentaje del talento es viable para vacantes actuales

**Cálculo real:**
- Datos del marketplace (todas las posiciones abiertas)
- Todos los candidatos en esos rangos de match
- Porcentaje calculado automáticamente

---

### 4. Indicadores de Riesgo (Risk Dashboard)

**4 Cards alertando sobre problemas:**

```
🚨 Talento en Riesgo: 5        (tendencia: ↓ 2 mejora)
⏰ Vacantes Urgentes: 2        (tendencia: ↑ 1 empeora)
📉 Desempeño Bajo: 3           (tendencia: → sin cambios)
⚙️ Skills Depreciadas: 7       (tendencia: ↑ 3 empeora)
```

**Propósito:** Alertar inmediatamente sobre problemas críticos

**Cada indicador muestra:**
- Número actual
- Tendencia vs período anterior (↑ ↓ →)
- Color de estado (rojo para riesgo)
- Descripción del impacto

---

### 5. Desarrollo & Capacitación

**Card izquierdo con progress bars:**

```
Planes de Desarrollo Activos: 12 / 20
████████░░░░░░░░░░░░ 60%

Cursos Completados: 18 / 30
██████████████░░░░░░░░░░░░ 60%

Cobertura de Skills: 156 / 200 skills
██████████████████░░░░░░ 78%
```

**Propósito:** Ver progreso en iniciativas de desarrollo

---

### 6. Métricas de Reclutamiento

**Card derecho con lista:**

```
Tiempo Promedio de Contratación: 32 días
Contrataciones Internas: 3
Contrataciones Externas: 5
Nuevos en Últimos 30 Días: +8
```

**Propósito:** Entender velocidad y composición de crecimiento

---

### 7. Recomendaciones & Alertas

**3 Alerts contextuales:**

```
🚨 5 Empleados en Riesgo
   Se recomienda realizar retención inmediata

⚠️ 2 Posiciones sin Candidatos Viables
   Iniciar búsqueda externa para: Senior Backend, Data Scientist

✅ 23 Candidatos para Promoción
   Listos para nuevos roles - considera planes de carrera
```

**Propósito:** Guiar acciones prioritarias basadas en datos

---

## 📊 Datos Mockados Actuales

```javascript
mockDashboardData = {
  talentMetrics: {
    totalEmployees: 124,
    employeesWithGaps: 87,
    employeesReadyForPromotion: 23,
    newHiresLast30Days: 8,
  },
  marketplaceMetrics: {
    openPositions: 12,
    candidatesExcellent: 7,
    candidatesGood: 12,
    candidatesModerate: 5,
    candidatesLow: 3,
    positionsWithoutCandidates: 2,
  },
  recruitmentMetrics: {
    averageTimeToHire: 32,
    internalPlacementRate: 65,
    externalHires: 5,
    internalPromotions: 3,
    retentionRate: 92,
  },
  developmentMetrics: {
    employeesInDevelopment: 45,
    completedCourses: 18,
    skillsCovered: 156,
    criticalGaps: 34,
    developmentPlansActive: 12,
  },
  riskMetrics: {
    talentAtRisk: 5,
    vacanciesUrgent: 2,
    employeesUnderperforming: 3,
    skillsDeprecating: 7,
  },
};
```

---

## 🔌 Cómo Conectar a Datos Reales

### Paso 1: Crear Endpoint en Backend

**Archivo:** `src/app/Http/Controllers/Api/DashboardController.php`

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function metrics(): JsonResponse
    {
        $user = auth()->user();
        
        // Calcular métricas reales
        $totalEmployees = People::where('organization_id', $user->organization_id)
            ->where('deleted_at', null)
            ->count();
            
        $employeesWithGaps = People::where('organization_id', $user->organization_id)
            ->where('deleted_at', null)
            ->whereHas('gaps', fn($q) => $q->where('gap', '>', 0))
            ->count();
            
        // ... más métricas
        
        return response()->json([
            'data' => [
                'talentMetrics' => [
                    'totalEmployees' => $totalEmployees,
                    'employeesWithGaps' => $employeesWithGaps,
                    // ...
                ],
                // ... resto de métricas
            ],
        ]);
    }
}
```

### Paso 2: Crear Ruta API

**Archivo:** `src/routes/api.php`

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);
});
```

### Paso 3: Actualizar Componente Vue

**En `Analytics.vue`, reemplazar:**

```javascript
// De esto:
const dashboardData = ref(mockDashboardData);

// A esto:
const dashboardData = ref<DashboardData | null>(null);

const loadMetrics = async () => {
  loading.value = true;
  try {
    const response = await axios.get('/api/dashboard/metrics');
    dashboardData.value = response.data.data;
  } catch (err) {
    notify({
      type: 'error',
      text: 'Error loading dashboard metrics'
    });
  } finally {
    loading.value = false;
  }
};

// En onMounted:
onMounted(() => {
  loadMetrics();
});
```

---

## 🎛️ Selector de Período

El dashboard incluye un selector que permite cambiar el período:

- **Semana** - Últimos 7 días
- **Mes** - Últimos 30 días (default)
- **Trimestre** - Últimos 90 días
- **Año** - Últimos 365 días

**Nota actual:** Los datos mockados no cambian con el período. Al conectar a datos reales, el selector actualizará las métricas.

---

## 💡 Indicadores Futuro

### Posibles Expansiones

1. **Gráficos de Tendencia**
   - Evolución de retención en el tiempo
   - Crecimiento de cobertura de skills
   - Velocidad de colocación interna

2. **Análisis por Departamento**
   - KPIs segmentados por area
   - Comparativa inter-departamental

3. **Ranking de Roles**
   - Cuáles roles están más "en demanda"
   - Cuáles tienen candidatos más listos

4. **Skills Hot Map**
   - Qué skills tienen más gaps
   - Qué skills están "trending up"

5. **Talent Pipeline**
   - Empleados por rango de años en la empresa
   - Distribución de seniority

6. **Exportar Reportes**
   - PDF con snapshots del dashboard
   - Excel con datos detallados

---

## 🎨 Componentes Reutilizables

El dashboard utiliza componentes Vuetify estándar:

- **v-card** - Tarjetas de contenido
- **v-chip** - Etiquetas de estado
- **v-progress-linear** - Barras de progreso
- **v-alert** - Alertas contextuales
- **v-list** - Listas de datos
- **v-icon** - Iconos (Material Design Icons)

---

## 📁 Archivos Relacionados

- ✅ `/src/resources/js/pages/Dashboard/Analytics.vue` - Componente principal
- ✅ `/src/routes/web.php` - Ruta registrada
- 📝 `/src/app/Http/Controllers/Api/DashboardController.php` - Por crear
- 📝 Tests unitarios - Por crear

---

## ✨ Características

- ✅ Diseño responsive (mobile, tablet, desktop)
- ✅ Paleta de colores coherente
- ✅ Iconos descriptivos
- ✅ Carga simulada con spinner
- ✅ Alertas contextuales basadas en datos
- ✅ Notas sobre datos mockados
- ✅ Período seleccionable
- ✅ Datos organizados por secciones temáticas

---

## 🚀 Próximos Pasos

1. **Crear API endpoints** para calcular métricas reales
2. **Implementar cálculos** en GapAnalysisService
3. **Conectar datos** del marketplace actual
4. **Agregar gráficos** con Chart.js o similar
5. **Implementar filtros** por departamento/región
6. **Crear sistema de alertas** automáticas

---

**Implementado:** 3 de enero de 2026  
**Status:** ✅ Prototipo funcional (datos mockados)  
**Próxima Fase:** Integración con datos reales
