# 📊 GUÍA RÁPIDA - Workforce Planning en MVP

**Para integrar el módulo de Planificación Dotacional en Día 6-7**

---

## 🎯 Qué es Workforce Planning

Herramienta estratégica que permite:

1. **Definir escenarios de demanda futura**

   - Ej: "Crecimiento 20% en ventas → necesito 5 nuevos analistas de datos"

2. **Recibir recomendaciones automáticas**

   - Sistema compara demanda vs oferta interna
   - Sugiere estrategia: BUILD (desarrollo) → BUY (contratación) → BORROW (freelance) → BOT (automatización)

3. **Registrar y trackear estrategias en ejecución**
   - Monitorear % de brechas cerradas
   - Alertas de riesgo

---

## 📐 Integración en MVP (Prioridad Secundaria)

### Backend Required

**3 Migraciones nuevas:**

```sql
-- 1. Escenarios de demanda
CREATE TABLE workforce_scenarios (
    id UUID PRIMARY KEY,
    organization_id UUID NOT NULL,
    name VARCHAR(100),
    description TEXT,
    timeframe_start DATE,
    timeframe_end DATE,
    created_at TIMESTAMP
);

-- 2. Estrategias de talento por rol
CREATE TABLE talent_strategies (
    id UUID PRIMARY KEY,
    role_id UUID NOT NULL,
    scenario_id UUID NOT NULL,
    strategy_type VARCHAR(20), -- BUILD, BUY, BORROW, BOT
    target_fte INT,
    execution_status VARCHAR(20), -- PLANNED, IN_PROGRESS, COMPLETED
    created_at TIMESTAMP
);

-- 3. Tracking de ejecución
CREATE TABLE strategy_executions (
    id UUID PRIMARY KEY,
    strategy_id UUID NOT NULL,
    action_taken TEXT,
    result TEXT,
    executed_by UUID,
    executed_at TIMESTAMP
);
```

**1 Servicio nuevo:**

```php
// app/Services/WorkforcePlanningService.php
class WorkforcePlanningService {
    public function getRecommendations(Scenario $scenario): Collection {
        // Por cada rol en demanda, analiza:
        // - Personas internas con skills
        // - Gap vs demanda
        // - Recomendación BUILD/BUY/BORROW/BOT
    }
}
```

**2-3 Endpoints nuevos:**

```
POST   /api/workforce-planning/scenarios
GET    /api/workforce-planning/scenarios/{id}
POST   /api/workforce-planning/recommendations
GET    /api/workforce-planning/strategies
```

### Frontend Required

**1 Página `/workforce-planning`:**

```vue
<template>
  <v-container>
    <!-- Formulario: Crear Escenario -->
    <v-card>
      <v-form>
        <v-text-field label="Nombre Escenario" />
        <v-text-field label="% Crecimiento" type="number" />
        <v-date-input label="Periodo" />
        <v-btn @click="createScenario">Analizar</v-btn>
      </v-form>
    </v-card>

    <!-- Tabla: Recomendaciones Automáticas -->
    <v-data-table
      :headers="['Rol', 'Demanda', 'Oferta Interna', 'Recomendación', 'Acción']"
      :items="recommendations"
    >
      <template #[`item.recomendacion`]="{ item }">
        <v-chip :color="getColor(item.strategy_type)">
          {{ item.strategy_type }}
        </v-chip>
      </template>
    </v-data-table>

    <!-- Dashboard: KPIs -->
    <v-row>
      <v-col>% Brechas Cerradas</v-col>
      <v-col>Estrategias en Ejecución</v-col>
      <v-col>Alertas de Riesgo</v-col>
    </v-row>
  </v-container>
</template>
```

---

## 📅 Plan de Implementación

### Opción A: Incluir en Día 6 (Si tiempo lo permite)

1. **Crear 3 migraciones** (20 min)
2. **Crear 3 modelos** (10 min)
3. **Crear servicio de recomendaciones** (30 min)
4. **Crear 2-3 endpoints** (30 min)
5. **Crear página `/workforce-planning`** (45 min)

**Total: ~2.5 horas**

### Opción B: Mover a Día 7 (Recomendado)

- Completar Prioridad 1+2 (10 páginas CRUD) en Día 6
- Agregar Workforce Planning en Día 7 como "enhanced feature"

---

## 🎯 Caso de Uso Demo

**Escenario:** TechCorp necesita 5 analistas de datos en 12 meses (crecimiento 20%)

**Oferta Interna:**

- 2 personas con skills parciales
- 1 persona con potencial (ruta de 3 meses)

**Recomendación del Sistema:**

```
┌─────────────────────────────────────────┐
│ WORKFORCE PLANNING RECOMMENDATION       │
├─────────────────────────────────────────┤
│ Rol: Analista de Datos                  │
│ Demanda: 5 FTE                          │
│ Oferta Interna: 3 FTE posibles          │
├─────────────────────────────────────────┤
│ 2 FTE → BUILD (movilidad interna)       │
│ 1 FTE → BORROW (contratación externa)   │
│ 2 FTE → BUY (reclutamiento directo)     │
├─────────────────────────────────────────┤
│ Brecha Cerrada: 60%                     │
│ Timeline: 12 meses                      │
└─────────────────────────────────────────┘
```

---

## 📚 Referencias

- **Documentación completa:** [MODULE_TASKFORCE.md](MODULE_TASKFORCE.md)
- **Arquitectura:** Ver sección "Integración funcional en MVP"
- **Datos de demo:** Seeder para TechCorp en MODULE_TASKFORCE.md

---

## ✅ Checklist de Implementación

### Backend

- [ ] Crear 3 migraciones
- [ ] Crear 3 modelos (WorkforceScenario, TalentStrategy, StrategyExecution)
- [ ] Crear WorkforcePlanningService
- [ ] Crear WorkforcePlanningController
- [ ] Registrar endpoints en routes/api.php
- [ ] Testear con Postman

### Frontend

- [ ] Crear página `/workforce-planning`
- [ ] Formulario para crear escenarios
- [ ] Tabla de recomendaciones
- [ ] Dashboard KPIs
- [ ] Agregar ruta en vue-router
- [ ] Testear flujo completo

### Documentación

- [ ] Actualizar memories.md
- [ ] Documentar endpoints
- [ ] Agregar a Postman collection

---

**Prioridad:** Secundaria (después de Prioridades 1-2)  
**Tiempo:** ~2.5 horas backend + frontend  
**Impacto MVP:** Alto (cierra ciclo de decisiones)
