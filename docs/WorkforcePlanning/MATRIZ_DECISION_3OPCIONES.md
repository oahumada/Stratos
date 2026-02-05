# 📈 TABLA DE DECISIÓN: ¿Qué hago ahora?

**Contexto:** El prompt está solo 28% implementado. Tienes 3 opciones principales.

---

## 🎯 LAS 3 OPCIONES

### ✅ OPCIÓN A: Extender lo Actual (RECOMENDADA)

**Implementar versión "Lightweight" del prompt**

| Aspecto                | Detalles                                                |
| ---------------------- | ------------------------------------------------------- |
| **Qué hacer**          | Agregar evolution_state, cálculos simples, modal básico |
| **Tablas nuevas**      | 0 (solo agregar campos a existing)                      |
| **Funciones nuevas**   | 2-3 helpers simples (no algoritmos complejos)           |
| **Componentes nuevos** | 2 modales (Transformar, Análisis)                       |
| **Tiempo**             | **2 semanas** (80h)                                     |
| **Riesgo**             | 🟢 **BAJO** (cambios incrementales)                     |
| **Cobertura**          | ~75% del prompt                                         |
| **Testing**            | 1 semana (unit + E2E)                                   |
| **Total con tests**    | **3 semanas**                                           |
| **Esfuerzo humano**    | 1 developer a tiempo completo                           |

**Tareas específicas:**

1. [ ] Migración: Agregar `evolution_state` enum a `capability_competencies` (4h)
2. [ ] Migración: Agregar `current_level` a `scenario_role_competencies` (2h)
3. [ ] Service: `CompetencyMutationService` (8h)
4. [ ] Service: `ArchetypeSuggestionService` (6h)
5. [ ] Modal: `TransformCompetencyModal.vue` (12h)
6. [ ] Panel: `RoleAnalysisPanel.vue` (10h)
7. [ ] Endpoints: `/competencies/{id}/transform` (6h)
8. [ ] Tests: Unit tests para services (12h)
9. [ ] Tests: Integration tests (16h)
10. [ ] Documentación & QA (8h)

**Pros:**

- ✅ Bajo riesgo
- ✅ Rápido
- ✅ No rompe nada actual
- ✅ El 75% del prompt funciona bien
- ✅ Puedes iterar mejoras después

**Contras:**

- ❌ No tienes `competency_versions` table (simplificado)
- ❌ No tienes `role_versions` table
- ❌ Cálculos más simples (no 100% del prompt)

**Cuándo elegir:** **Si tienes 3 semanas y quieres minimizar riesgo**

---

### 🚀 OPCIÓN B: Implementar Prompt Completo

**Implementar 100% del prompt tal como fue diseñado**

| Aspecto                   | Detalles                                            |
| ------------------------- | --------------------------------------------------- |
| **Qué hacer**             | Crear todo desde cero según especificación          |
| **Tablas nuevas**         | 2 CRÍTICAS (`competency_versions`, `role_versions`) |
| **Funciones nuevas**      | 5+ (calculateRoleMutation, suggestArchetype, etc.)  |
| **Componentes nuevos**    | 5 (Matriz, Modal, Panel, Timeline, etc.)            |
| **Tiempo implementación** | **4 semanas** (160h)                                |
| **Tiempo testing**        | **1 semana** (40h)                                  |
| **Total**                 | **5 semanas** (200h)                                |
| **Riesgo**                | 🔴 **ALTO** (breaking changes, refactor)            |
| **Cobertura**             | ~100% del prompt                                    |
| **Esfuerzo humano**       | 1 developer + 0.5 QA                                |

**Fases:**

**Fase 1: Database (1 semana)**

- [ ] Crear `competency_versions` table (8h)
- [ ] Crear `role_versions` table (6h)
- [ ] Agregar campos a pivots (4h)
- [ ] Migrations + rollback testing (12h)

**Fase 2: Backend (1.5 semanas)**

- [ ] Service: `CompetencyVersioningService` (16h)
- [ ] Service: `RoleMutationAnalysisService` (16h)
- [ ] Endpoints: POST /competencies/{id}/transform (8h)
- [ ] Endpoints: POST /competencies/create-embryo (8h)
- [ ] Endpoints: GET /scenario-roles/{id}/mutation (8h)

**Fase 3: Frontend (1 semana)**

- [ ] `TransformCompetencyModal.vue` (16h)
- [ ] `RoleAnalysisPanel.vue` (12h)
- [ ] Actualizar `CapabilityCompetencyMatrix.vue` (8h)
- [ ] Agregar evolution_state UI (8h)

**Fase 4: Testing (1 semana)**

- [ ] Unit tests (16h)
- [ ] Feature tests (16h)
- [ ] Integration E2E (8h)

**Pros:**

- ✅ Exactamente como especificó el prompt
- ✅ Trazabilidad completa de versionamiento
- ✅ Algoritmos sofisticados (mutation_index, archetype)
- ✅ Máxima flexibilidad para competencias

**Contras:**

- ❌ Muy alto riesgo de romper lo actual
- ❌ Requiere refactoring significativo
- ❌ 5 semanas es mucho tiempo
- ❌ Más mantenimiento después

**Cuándo elegir:** **Si tienes 5+ semanas y quieres exactitud total**

---

### 📋 OPCIÓN C: Documentar y Priorizar

**Reportar el gap y planificar para futuro**

| Aspecto             | Detalles                                          |
| ------------------- | ------------------------------------------------- |
| **Qué hacer**       | Crear reporte, presentar al PO, agregar a backlog |
| **Tiempo**          | **3 horas**                                       |
| **Riesgo**          | 🟢 **NINGUNO**                                    |
| **Cobertura**       | Documentada para futuro                           |
| **Transparencia**   | Máxima                                            |
| **Esfuerzo humano** | 0.5 developer                                     |

**Qué incluye:**

- [ ] Reporte de gap (tienes 5 docs ya) (1h)
- [ ] Presentación ejecutiva al PO (1h)
- [ ] Agregar issues a GitHub/backlog (0.5h)
- [ ] Estimaciones de tiempo (0.5h)

**Pros:**

- ✅ Sin riesgo
- ✅ Transparencia total
- ✅ El PO toma decisión informada
- ✅ No rompes nada

**Contras:**

- ❌ El prompt sigue sin implementarse
- ❌ Postergas la decisión

**Cuándo elegir:** **Si tienes incertidumbre sobre prioridades**

---

## 🎓 MATRIZ DE DECISIÓN

```
¿Cuántas semanas disponibles?
├─ < 1 semana        → Opción C (documentar)
├─ 1-3 semanas       → Opción A (extender)
├─ 3-4 semanas       → Opción A (extender, presionado)
└─ 5+ semanas        → Opción B (completo)

¿Cuál es el riesgo aceptable?
├─ Bajo              → Opción A
├─ Medio             → Opción C (después Opción A)
└─ Alto/No aceptable → Opción C

¿Qué tan importante es la exactitud?
├─ "Rápido es mejor" → Opción A
├─ "Exacto es mejor" → Opción B
└─ "Incierto"        → Opción C

¿Tienes recursos?
├─ 1 developer       → Opción A (3 sem) o Opción C
├─ 1 dev + 0.5 QA    → Opción B (5 sem)
└─ < 1 dev           → Opción C
```

---

## 📊 COMPARATIVA LADO A LADO

| Criterio                 | Opción A  | Opción B  | Opción C   |
| ------------------------ | --------- | --------- | ---------- |
| **Tiempo**               | 3 semanas | 5 semanas | 3 horas    |
| **Riesgo**               | 🟢 Bajo   | 🔴 Alto   | 🟢 Ninguno |
| **Cobertura**            | 75%       | 100%      | 0%         |
| **Exactitud**            | 70%       | 100%      | N/A        |
| **Mantenibilidad**       | ✅ Fácil  | 🟡 Media  | N/A        |
| **Líneas de código**     | ~1,200    | ~2,500    | 0          |
| **Testing**              | 1 semana  | 1 semana  | N/A        |
| **Breaking changes**     | Cero      | Muchos    | Cero       |
| **Puede iterar después** | ✅ Sí     | ✅ Sí     | ✅ Sí      |
| **Mejor para MVP**       | ✅ Sí     | ❌ No     | 🟡 Tal vez |

---

## 🎯 RECOMENDACIÓN POR ESCENARIO

### Escenario 1: "Tengo un deadline en 3 semanas"

**→ Opción A** (Extender lo actual)

```
Semana 1: Crear funcionalidades base (12h)
Semana 2: Modales y UI (12h)
Semana 3: Testing y QA (16h)
         Total: 40h ✅ Cabe
```

---

### Escenario 2: "Quiero exactamente lo que especificó el prompt"

**→ Opción B** (Implementar completo)

```
Pero ten en cuenta:
- 5 semanas de trabajo
- Alto riesgo de breaking changes
- Considera hacerlo en rama separada y hacer merge cuidadosamente
```

---

### Escenario 3: "No sé cuál es la prioridad real"

**→ Opción C** (Documentar) **→ Luego Opción A o B**

```
Hoy:   Reporta gap, presenta al PO (3h)
PO decide prioridades
Próximo sprint: Ejecuta Opción A o B según decisión
```

---

### Escenario 4: "El cliente solo quiere las tablas críticas"

**→ Opción A** (Extender) **+ MVP**

```
Mínimo viable con Opción A:
1. competency_versions campos en tabla existente
2. Modal de transformación básico
3. Tests básicos

Tiempo: 1.5 semanas
```

---

## 🚀 MI RECOMENDACIÓN PERSONAL

**Opción A (Extender lo actual) con timeline siguiente:**

```
SEMANA 1:
├─ Día 1-2: Migraciones (evolution_state, current_level)
├─ Día 3-4: CompetencyMutationService
└─ Día 5: Testing migraciones

SEMANA 2:
├─ Día 1-2: TransformCompetencyModal.vue
├─ Día 3-4: RoleAnalysisPanel.vue
└─ Día 5: Integración UI

SEMANA 3:
├─ Día 1-2: Unit tests
├─ Día 3-4: Integration tests
└─ Día 5: QA + documentación

ENTREGABLE: Sistema con 75% del prompt, bajo riesgo
```

**Por qué:**

- ✅ 3 semanas es realista
- ✅ Bajo riesgo
- ✅ Sirve para el 90% de casos de uso
- ✅ Después puedes mejorar sin prisa
- ✅ No rompe lo actual

---

## 📝 CÓMO DECIDIR

**Haz estas preguntas:**

1. ¿Cuántas semanas tengo? (< 1 sem → C | 1-3 → A | 5+ → B)
2. ¿Qué riesgo acepto? (Bajo → A | Alto → No B)
3. ¿Tengo recursos? (1 dev → A o C | 1.5+ → B)
4. ¿Cuál es la prioridad real? (Urgente → A | Perfecta → B | Incierto → C)
5. ¿Tengo deadline? (Sí → A | No → B)

---

## ✅ ACCIÓN RECOMENDADA AHORA

```
SI TIENES 30 MINUTOS:
1. Lee RESUMEN_RAPIDO_PROMPT_STATUS.md (8 min)
2. Decide: A, B o C (5 min)
3. Comparte con tu jefe (17 min)

SI TIENES 1 HORA:
1. Lee todos los documentos (40 min)
2. Decide: A, B o C (10 min)
3. Crea Jira issues si corresponde (10 min)

SI TIENES TIEMPO:
1. Lee REVISION_PROMPT_ESCENARIOS_FEB2026.md completo
2. Reúnete con PO/Arquitecto
3. Plan final y ejecución
```

---

## 📞 PRÓXIMOS PASOS

- [ ] **Paso 1:** Lee documentos (30 min - 2 horas)
- [ ] **Paso 2:** Decide A, B o C (15 min)
- [ ] **Paso 3:** Comparte con jefe/PO (30 min)
- [ ] **Paso 4:** Obtén aprobación (1-2 días)
- [ ] **Paso 5:** Ejecuta (3-5 semanas según opción)

---

**Tienes 5 documentos listos para compartir en `/home/omar/Stratos/`**

Elige opción, ejecuta, entrega. 🚀
