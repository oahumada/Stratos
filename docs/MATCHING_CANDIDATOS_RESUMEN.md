# 🎯 Resumen: Solución de Matching de Candidatos

## ❓ Problema Original

> "No sé qué criterio usar: ¿mostrar todos los candidatos o solo los que cumplen un rango? El reclutador debe saber cuándo buscar en el mercado externo. El criterio es **priorizar interno pero no exclusivamente**."

## ✅ Solución Implementada

### Decisión Estratégica: **MOSTRAR TODOS con Clasificación Inteligente**

---

## 📊 Vista del Reclutador

```
┌─────────────────────────────────────────────────────────────┐
│  DASHBOARD - Resumen de Búsqueda de Talento                │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌──────────┐  ┌──────────┐  ┌──────────┐  ┌──────────┐  │
│  │ ⭐ ≥80%  │  │ ✅ 70-79%│  │ ⏰ 50-69%│  │ 🔍 <50% │  │
│  │    3     │  │    2     │  │    4     │  │    2     │  │
│  │ Excelente│  │   Bueno  │  │ Moderado │  │ Externa  │  │
│  └──────────┘  └──────────┘  └──────────┘  └──────────┘  │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│  ALERTAS ESTRATÉGICAS                                       │
├─────────────────────────────────────────────────────────────┤
│  🚨 ACCIÓN INMEDIATA: 2 posiciones con match <30%          │
│     → Iniciar búsqueda externa de inmediato                │
│                                                             │
│  💡 ESTRATEGIA DUAL: 4 posiciones con match 50-69%         │
│     → Búsqueda externa preventiva + desarrollo interno     │
│                                                             │
│  ✅ TALENTO DISPONIBLE: 5 posiciones con candidatos ≥70%   │
│     → Priorizar proceso interno                            │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│  FILTROS                                                    │
├─────────────────────────────────────────────────────────────┤
│  [ Todos ] [ ≥70% ] [ 50-69% ] [ <50% ]                    │
│  □ Mostrar todos los candidatos                            │
└─────────────────────────────────────────────────────────────┘
```

---

## 🎨 Sistema de Clasificación

### ⚠️ Reglas de Exclusión Automática

**1. Mismo Rol:** Las personas NO son candidatas para vacantes del mismo rol que ocupan actualmente.

**2. Match Muy Bajo (<40%):** Las personas con menos del 40% de match NO aparecen como candidatos.

| Regla | Situación | ¿Es candidato? |
|-------|-----------|----------------|
| **Mismo rol** | Vacante: Backend Dev, Actual: Backend Dev | ❌ NO |
| **Mismo rol** | Vacante: Backend Dev, Actual: Frontend Dev | ✅ SÍ (diferente rol) |
| **Match bajo** | Match: 35% | ❌ NO (bajo umbral) |
| **Match suficiente** | Match: 42% | ✅ SÍ (sobre umbral) |
| **Promoción** | Vacante: Senior Dev, Actual: Junior Dev | ✅ SÍ (si match ≥40%) |

**Razón del umbral 40%:**
- Match <40% = brechas demasiado grandes para desarrollo viable
- Mantiene el marketplace enfocado en oportunidades reales
- Reduce ruido y mejora experiencia del reclutador

### Niveles de Match (Solo candidatos viables ≥40%)

| % Match | Nivel | Visualización | Recomendación |
|---------|-------|---------------|---------------|
| **≥80%** | Excelente | 🟢 ⭐ | ✅ Proceso interno inmediato |
| **70-79%** | Alto | 🟢 ✅ | ✅ Priorizar interno |
| **50-69%** | Moderado | 🟡 ⏰ | ⚖️ Dual: Interno + Externa preventiva |
| **40-49%** | Bajo | 🟠 ⚠️ | 🔍 Búsqueda externa paralela |
| **<40%** | ❌ Excluido | - | No aparece en marketplace |

---

## 🔄 Flujo de Decisión

```
┌─────────────────────┐
│  Vacante Abierta    │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────────────┐
│  Análisis Automático de     │
│  TODOS los candidatos       │
│  (GapAnalysisService)       │
└──────────┬──────────────────┘
           │
           ▼
┌─────────────────────────────────────────────┐
│  Mejor Candidato Match?                     │
├─────────────────────────────────────────────┤
│  ≥70%  │  50-69%  │  30-49%  │  <30%       │
└────┬───────┬──────────┬──────────┬──────────┘
     │       │          │          │
     ▼       ▼          ▼          ▼
  ┌─────┐ ┌─────┐   ┌─────┐   ┌─────────┐
  │Proc │ │Dual │   │Ext  │   │Ext      │
  │Int  │ │     │   │Par  │   │Urgente  │
  │Prior│ │I+E  │   │     │   │         │
  └─────┘ └─────┘   └─────┘   └─────────┘
```

**Leyenda:**
- **Proc Int Prior**: Proceso interno prioritario
- **Dual I+E**: Estrategia dual (interno + externa preventiva)
- **Ext Par**: Externa en paralelo
- **Ext Urgente**: Externa inmediata exclusiva

> **Nota:** Si no hay candidatos con ≥40% match, la posición mostrará automáticamente "Sin candidatos viables" y recomendará búsqueda externa inmediata.

---

## 💡 Ejemplos de Uso

### Ejemplo 1: Senior Developer

```
Posición: Senior Backend Developer
Candidatos evaluados: 24

Top 3:
  1. Juan Pérez      - 85% ⭐ - 15 días TTP - 0 gaps críticos
  2. María López     - 78% ✅ - 30 días TTP - 1 gap (arquitectura)
  3. Carlos Ruiz     - 62% ⏰ - 90 días TTP - 3 gaps

Alerta: ✅ Talento interno disponible
Recomendación: Priorizar proceso interno con Juan o María
Acción externa: No necesaria de inmediato
```

### Ejemplo 2: Data Scientist

```
Posición: Data Scientist
Candidatos evaluados: 18

Top 3:
  1. Ana Torres      - 52% ⏰ - 120 días TTP - 4 gaps
  2. Pedro Gómez     - 45% 🟠 - 150 días TTP - 6 gaps
  3. Laura Díaz      - 38% 🟠 - 180 días TTP - 7 gaps

Alerta: 💡 Estrategia dual recomendada
Recomendación: Iniciar búsqueda externa preventiva
              + evaluar desarrollo de Ana (52%)
Acción: Paralelo - No descartar interno aún
```

### Ejemplo 3: Cloud Architect

```
Posición: Cloud Architect
Candidatos evaluados: 12
Candidatos viables (≥40%): 0

Mejores 3 evaluados:
  1. Roberto Sánchez - 28% 🔴 - EXCLUIDO (bajo umbral)
  2. Elena Castro    - 22% 🔴 - EXCLUIDO (bajo umbral)
  3. Miguel Vargas   - 18% 🔴 - EXCLUIDO (bajo umbral)

Resultado: Sin candidatos viables
Alerta: 🚨 Búsqueda externa inmediata
Recomendación: Iniciar reclutamiento externo exclusivo
              (ningún candidato interno alcanza umbral mínimo)
Acción: Proceso externo urgente
```

---

## 🎯 Beneficios Clave

### 1. ✅ **Transparencia Total**
- El reclutador ve TODO el talento disponible
- Sin filtrado oculto
- Decisión informada

### 2. 🎯 **Guía Estratégica**
- No solo datos, sino **recomendaciones accionables**
- Alertas contextuales
- Criterios claros de cuándo buscar externamente

### 3. ⚖️ **Balance Interno-Externo**
- Prioriza interno cuando viable (≥70%)
- Recomienda externo cuando necesario (<70%)
- Permite estrategia dual (50-69%)

### 4. 📊 **Data-Driven**
- Basado en análisis cuantitativo de skills
- Métricas objetivas
- Consistencia en decisiones

### 5. 🔄 **Flexible**
- Filtros para diferentes escenarios
- Mostrar top 5 o todos
- Adaptable por organización

---

## 🛠️ Componentes Técnicos

### Frontend
- **Archivo**: `Marketplace/Index.vue`
- **Features**:
  - Dashboard con 4 métricas clave
  - 3 tipos de alertas estratégicas
  - Filtros por nivel de match
  - Toggle para mostrar todos

### Backend
- **Archivo**: `MarketplaceController.php`
- **Features**:
  - Retorna TODOS los candidatos
  - Clasificación por nivel
  - Recomendaciones de búsqueda externa
  - Metadata de distribución

### Service
- **Archivo**: `GapAnalysisService.php`
- **Features**:
  - Cálculo de match % por skills
  - Time to productivity
  - Identificación de gaps críticos

---

## 📋 Checklist de Implementación

- [x] Frontend: Dashboard con métricas
- [x] Frontend: Sistema de alertas estratégicas
- [x] Frontend: Filtros por nivel de match
- [x] Frontend: Toggle mostrar todos/top 5
- [x] Frontend: Clasificación visual (colores, íconos)
- [x] Backend: Retornar todos los candidatos
- [x] Backend: Clasificación por match_level
- [x] Backend: Recomendaciones de búsqueda externa
- [x] Documentación: Estrategia completa
- [x] Documentación: Casos de uso

---

## 🎓 Respuesta a la Pregunta Original

### **¿Qué criterio usar?**

**✅ RESPUESTA: Mostrar TODOS los candidatos, organizados inteligentemente**

**Por qué:**
1. **Transparencia**: El reclutador puede ver todo el talento disponible
2. **Flexibilidad**: Puede filtrar según necesidad
3. **Contexto**: Alertas indican cuándo buscar externamente
4. **Priorización**: Sistema sugiere interno cuando viable (≥70%)
5. **Pragmatismo**: Recomienda externo cuando gaps son grandes (<50%)

### **¿Cuándo buscar externamente?**

**Automático** según el sistema:
- **Inmediato**: Mejor match <30% 🚨
- **Preventivo/Paralelo**: Mejor match 50-69% 💡
- **Solo si interno falla**: Mejor match ≥70% ✅

### **¿Cómo priorizar interno sin exclusividad?**

**Balance implementado:**
- Match ≥70% → Proceso interno **prioritario**
- Match 50-69% → Estrategia **dual** (interno + externa preventiva)
- Match <50% → Búsqueda externa **necesaria**

---

## 📁 Archivos Modificados

1. `/src/resources/js/pages/Marketplace/Index.vue` - UI y lógica de filtros
2. `/src/app/Http/Controllers/Api/MarketplaceController.php` - Backend con clasificación
3. `/docs/ESTRATEGIA_MATCHING_CANDIDATOS.md` - Documentación completa
4. `/docs/MATCHING_CANDIDATOS_RESUMEN.md` - Este archivo (resumen visual)

---

**Implementado por:** GitHub Copilot  
**Fecha:** 3 de enero de 2026  
**Status:** ✅ Completo y listo para uso
