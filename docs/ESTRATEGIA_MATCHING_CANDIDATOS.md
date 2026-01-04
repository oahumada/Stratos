# 🎯 Estrategia de Matching y Selección de Candidatos Internos

**Fecha:** 3 de enero de 2026  
**Sistema:** TalentIA - Marketplace Interno  
**Objetivo:** Definir criterios claros para la priorización de talento interno vs. búsqueda externa

---

## 📋 Resumen Ejecutivo

El sistema implementa una **estrategia de tres niveles** que:

1. ✅ **Muestra TODOS los candidatos** organizados por niveles de match
2. 🎯 **Prioriza el talento interno** cuando hay match ≥70%
3. 🚀 **Recomienda búsqueda externa** de forma inteligente basada en el análisis de match

### Principio Rector

> **"Priorizar interno, pero no exclusivamente"**  
> El sistema balancea la preferencia por movilidad interna con la necesidad práctica de búsqueda externa cuando el match interno es insuficiente.

---

## 🎨 Sistema de Clasificación

### Niveles de Match

| Nivel | Rango | Color | Icono | Significado | Acción Recomendada |
|-------|-------|-------|-------|-------------|-------------------|
| **Excelente** | ≥80% | 🟢 Verde | ⭐ | Candidato ideal, listo para el rol | Proceso interno inmediato |
| **Alto** | 70-79% | 🟢 Verde claro | ✅ | Candidato viable, mínimos gaps | Proceso interno prioritario |
| **Moderado** | 50-69% | 🟡 Amarillo | ⏰ | Requiere capacitación moderada | Dual: Interno + Búsqueda externa preventiva |
| **Bajo** | 40-49% | 🟠 Naranja | ⚠️ | Gaps significativos | Búsqueda externa paralela |
| **Muy Bajo** | <40% | 🔴 Rojo (excluido) | ❌ | Match insuficiente | **NO aparece como candidato** |

> ⚠️ **Nota Importante:** Candidatos con <40% de match son automáticamente excluidos del marketplace. Si todas las personas evaluadas tienen <40%, la posición mostrará "Sin candidatos viables" y recomendará búsqueda externa inmediata.

---

## 🔄 Estrategias de Reclutamiento por Escenario

### ⚠️ Reglas de Exclusión Automática

#### 1. Exclusión por Mismo Rol

**Las personas que ya ocupan el mismo rol que la vacante NO son consideradas candidatas.**

**Razón:**
- No tiene sentido que alguien se postule al mismo puesto que ya ocupa
- Esto es diferente a:
  - **Movilidad lateral**: Mismo nivel, diferente área/departamento ✅
  - **Promoción**: Nivel superior ✅
  - **Cambio de especialización**: Mismo nivel, diferente rol ✅

**Ejemplo:**
```
Vacante: Senior Backend Developer (role_id: 5)
Candidato: Juan Pérez - Senior Backend Developer (role_id: 5)
Resultado: ❌ NO aparece como candidato (mismo rol)

Vacante: Senior Backend Developer (role_id: 5)
Candidato: María López - Senior Frontend Developer (role_id: 6)
Resultado: ✅ Aparece como candidato (rol diferente)
```

#### 2. Exclusión por Match Muy Bajo (<40%)

**Las personas con menos del 40% de match NO aparecen como candidatos.**

**Razón:**
- Un match <40% indica brechas de habilidades demasiado grandes
- El desarrollo interno sería muy costoso en tiempo y recursos
- No es realista considerarlos candidatos viables
- Mantiene el marketplace enfocado en oportunidades reales

**Configuración:**
- **Umbral actual:** 40% (constante `MINIMUM_MATCH_THRESHOLD`)
- **Futuro:** Configurable por organización en settings

**Ejemplo:**
```
Vacante: Data Scientist
Candidato: Carlos - Match 35%
  - Gaps: Python avanzado, ML, estadística, big data
  - Time to productivity: 360+ días
Resultado: ❌ NO aparece como candidato (match muy bajo)

Candidato: Ana - Match 52%
  - Gaps: ML avanzado, experiencia con modelos
  - Time to productivity: 120 días
Resultado: ✅ Aparece como candidato (match suficiente)
```

**Impacto:**
- Reduce ruido en el marketplace
- Alertas de "búsqueda externa" son más precisas
- Reclutadores ven solo opciones realistas

---

### Escenario 1: Talento Interno Fuerte (≥70%)

**Situación:** El mejor candidato interno tiene ≥70% de match

```
✅ ACCIÓN: Proceso interno prioritario
- Iniciar proceso de selección interna
- Evaluación completa del candidato
- Plan de onboarding/capacitación si aplica
- Búsqueda externa solo si el interno rechaza o no pasa
```

**Justificación:**
- ROI superior (menor costo, menor tiempo)
- Retención del talento
- Conocimiento organizacional existente
- Menor curva de aprendizaje

---

### Escenario 2: Match Moderado (50-69%)

**Situación:** El mejor candidato tiene 50-69% de match

```
⚖️ ACCIÓN: Estrategia Dual (Paralela)
1. Proceso interno:
   - Evaluar candidato con plan de desarrollo
   - Calcular inversión en capacitación
   - Definir timeline de preparación
   
2. Búsqueda externa preventiva:
   - Iniciar scouting en mercado
   - Construir pipeline externo
   - No descartar interno aún
```

**Decisión Final:**
- Comparar candidato interno desarrollado vs. opciones externas
- Considerar: tiempo, costo, fit cultural, potencial de largo plazo

---

### Escenario 3: Match Bajo (<50%)

**Situación:** El mejor candidato interno tiene <50% de match

```
🚀 ACCIÓN: Búsqueda Externa Prioritaria
- Iniciar proceso de reclutamiento externo inmediato
- Si es <30%: URGENTE, búsqueda externa exclusiva
- El interno puede seguir en consideración secundaria
```

**Justificación:**
- Gaps demasiado grandes para desarrollo rápido
- Riesgo de prolongar vacante innecesariamente
- Costo/beneficio desfavorable para desarrollo interno

---

## 📊 Dashboard del Reclutador

### Métricas Clave

El dashboard muestra 4 cards principales:

1. **Match Excelente** (≥80%)
   - Listos para el rol
   - Acción: Proceso interno inmediato

2. **Buen Match** (70-79%)
   - Viables con mínima preparación
   - Acción: Proceso interno prioritario

3. **Match Moderado** (50-69%)
   - Requieren capacitación
   - Acción: Estrategia dual

4. **Búsqueda Externa** (<50%)
   - Mercado externo necesario
   - Acción: Reclutamiento externo

### Alertas Inteligentes

El sistema genera 3 tipos de alertas:

#### 🚨 Acción Inmediata (Rojo)
```
Trigger: Posiciones con mejor match <30%
Mensaje: "X posición(es) requieren búsqueda externa inmediata"
```

#### 💡 Estrategia Dual (Amarillo)
```
Trigger: Posiciones con mejor match 50-69%
Mensaje: "X posición(es) - Iniciar búsqueda externa preventiva"
```

#### ✅ Talento Disponible (Verde)
```
Trigger: Posiciones con match ≥70%
Mensaje: "X posición(es) con talento interno listo - Priorizar interno"
```

---

## 🎛️ Controles de Usuario

### Filtros Disponibles

El reclutador puede filtrar candidatos por nivel:

- **Todos**: Ver todo el talento disponible
- **Match Alto (≥70%)**: Solo candidatos listos/viables
- **Match Medio (50-69%)**: Candidatos con capacitación
- **Match Bajo (<50%)**: Identificar gaps críticos

### Toggle: Mostrar Todos

- **OFF**: Top 5 candidatos por posición (vista resumida)
- **ON**: Todos los candidatos organizados (vista completa)

**Razón:** Permite al reclutador tener visión completa sin abrumar la UI por defecto

---

## 🧮 Métricas Calculadas

### Por Posición

```typescript
{
  best_match_pct: number,           // % del mejor candidato
  candidates_by_level: {            // Distribución de candidatos
    excellent: count,
    high: count,
    medium: count,
    low: count,
    very_low: count
  },
  recommendation: {
    search_external: boolean,       // ¿Buscar en mercado?
    urgent_external: boolean,       // ¿Urgente?
    best_match_pct: number
  }
}
```

### Dashboard General

```typescript
{
  totalPositions: number,
  positionsWithExcellentMatch: number,      // ≥80%
  positionsWithGoodMatch: number,           // 70-79%
  positionsWithModerateMatch: number,       // 50-69%
  positionsNeedingExternalSearch: number,   // <50%
  positionsRequiringImmediateExternal: number, // <30%
  avgMatchPercentage: number
}
```

---

## 🎯 Casos de Uso

### Caso 1: Startup en Crecimiento

**Contexto:** Empresa con 50 empleados, cultura de desarrollo interno

**Configuración Recomendada:**
- Umbral de búsqueda externa: 60% (más tolerante)
- Enfoque: Desarrollar talento interno agresivamente
- Búsqueda externa solo si match <60%

---

### Caso 2: Empresa Grande con Urgencias

**Contexto:** 500+ empleados, vacantes críticas que deben llenarse rápido

**Configuración Recomendada:**
- Umbral de búsqueda externa: 75% (más exigente)
- Enfoque: Solo talento interno muy preparado
- Búsqueda externa paralela siempre que match <75%

---

### Caso 3: Organización con Pool Extenso

**Contexto:** Gran cantidad de empleados, movilidad interna clave

**Configuración Recomendada:**
- Umbral: 70% (balanceado)
- Mostrar todos los candidatos siempre
- Priorizar interno pero con timeline definido

---

## 🔧 Implementación Técnica

### Frontend (Vue)

**Archivo:** `/src/resources/js/pages/Marketplace/Index.vue`

```typescript
// Estados clave
const candidateMatchFilter = ref<'all' | 'high' | 'medium' | 'low'>('all');
const showAllCandidates = ref(false);
const externalSearchThreshold = ref(70);

// Funciones de clasificación
getMatchColor(percentage): string
getMatchCategory(percentage): string
getMatchIcon(percentage): string
filterCandidatesByMatch(candidates): Candidate[]
```

### Backend (Laravel)

**Archivo:** `/src/app/Http/Controllers/Api/MarketplaceController.php`

```php
// Vista de reclutador: Excluir personas con el mismo rol Y match muy bajo
$people = People::where('organization_id', $user->organization_id)
    ->where('deleted_at', null)
    ->where('role_id', '!=', $opening->role_id) // Mismo rol no es candidato
    ->get();

// Dentro del map: filtrar por match mínimo
if ($analysis['match_percentage'] < 40) { // MINIMUM_MATCH_THRESHOLD
    return null; // Excluir candidato no viable
}

// Vista de empleado: Excluir vacantes del mismo rol Y con match muy bajo
$openings = JobOpening::where('organization_id', $people->organization_id)
    ->where('status', 'open')
    ->where('role_id', '!=', $people->role_id)
    ->with('role')
    ->get();

// Dentro del map: filtrar oportunidades con match muy bajo
if ($analysis['match_percentage'] < 40) {
    return null; // No mostrar oportunidad no viable
}

// Cálculo de match con GapAnalysisService
$analysis = $gapService->calculate($person, $opening->role);

// Clasificación automática
$matchLevel = determineMatchLevel($matchPct);

// Recomendaciones
'recommendation' => [
    'search_external' => $matchPct < 70,
    'urgent_external' => $matchPct < 30,
]
```

---

## 📈 Ventajas de Este Enfoque

### 1. ✅ Transparencia Total
- El reclutador ve TODOS los candidatos
- Sin "cajas negras" o filtrado oculto
- Decisión informada basada en datos

### 2. 🎯 Guía Estratégica Clara
- Alertas contextuales según el caso
- Recomendaciones accionables
- No solo "mostrar datos", sino "qué hacer"

### 3. ⚖️ Balance Interno-Externo
- No es "solo interno" ni "solo externo"
- Estrategia adaptativa según el match
- ROI optimizado

### 4. 📊 Data-Driven
- Basado en análisis cuantitativo de gaps
- Métricas objetivas (match %)
- Consistencia en decisiones

### 5. 🔄 Flexibilidad
- Filtros para explorar diferentes escenarios
- Toggle para diferentes niveles de detalle
- Adaptable a diferentes organizaciones

---

## 🚀 Próximas Mejoras

### Corto Plazo

- [ ] Configuración de umbral personalizable por organización
- [ ] Exportar lista de candidatos filtrada
- [ ] Vista detallada de gaps por candidato

### Mediano Plazo

- [ ] Histórico de decisiones interno vs. externo
- [ ] A/B testing de diferentes umbrales
- [ ] ROI calculado por decisión (costo interno vs. externo)

### Largo Plazo

- [ ] ML para predecir probabilidad de éxito
- [ ] Recomendación de plan de desarrollo automático
- [ ] Integración con ATS externos

---

## 📚 Referencias

- Ver: [GUIA_RAPIDA_CRUD_GENERICO.md](./GUIA_RAPIDA_CRUD_GENERICO.md)
- Ver: [GapAnalysisService.php](../src/app/Services/GapAnalysisService.php)
- Ver: [Marketplace Index.vue](../src/resources/js/pages/Marketplace/Index.vue)

---

## ✍️ Conclusión

Esta estrategia responde directamente a la pregunta:

> **"¿Mostrar todos o solo los que cumplen un rango?"**

**Respuesta:** Mostrar TODOS, pero organizados inteligentemente con recomendaciones claras sobre cuándo buscar externamente.

El criterio de **"priorizar interno pero no exclusivamente"** se implementa mediante:
1. Clasificación visible de todos los candidatos
2. Alertas que indican cuándo el match interno es insuficiente
3. Recomendaciones de búsqueda externa basadas en umbrales
4. Transparencia total para que el reclutador tome la decisión final

**El sistema guía, no decide por el reclutador.**
