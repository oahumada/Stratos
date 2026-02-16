# Plan de Trabajo: Completar Sistema de Roles-Competencias en Stratos

**Fecha de Creación:** 2026-02-15  
**Objetivo General:** Completar el flujo de Scenario Planning (Steps 1-3) integrando coherencia arquitectónica, competencias base/escenario, y análisis de brechas.

---

## 📋 Roadmap General

```
┌─────────────────────────────────────────────────────────────────┐
│ FASE 1: Validación y Pulido (Semana 1)                         │
│ - Validar coherencia arquitectónica                             │
│ - Mejorar visualización en matriz                               │
│ - Añadir tooltips y ayudas contextuales                         │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ FASE 2: Gap Analysis - Step 3 (Semana 2)                       │
│ - Calcular brechas de competencias                              │
│ - Generar recomendaciones de capacitación                       │
│ - Integrar racionales estratégicos en análisis                  │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ FASE 3: Competencias Base vs. Escenario (Semana 3)             │
│ - Implementar tabla role_base_competencies                      │
│ - Herencia automática al añadir roles                           │
│ - Diferenciación visual en matriz                               │
└─────────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│ FASE 4: Flujo del Cubo Completo (Semana 4)                     │
│ - Completar flujo de incubación                                 │
│ - Dashboard de escenarios                                        │
│ - Reportes y exportación                                         │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🎯 FASE 1: Validación y Pulido (Inmediato - Esta Semana)

### **Sesión 1.1: Validación Exhaustiva (30-45 min)**

**Objetivo:** Asegurar que todo lo implementado hoy funciona correctamente.

**Tareas:**

- [ ] **Validar Semáforo de Coherencia**
    - [ ] Probar rol Estratégico con nivel 3 → Debe mostrar warning
    - [ ] Probar rol Operacional con nivel 5 sin referente → Debe mostrar info
    - [ ] Probar rol Operacional con nivel 5 con referente → Debe mostrar success
    - [ ] Probar rol Táctico con nivel 1 → Debe mostrar warning

- [ ] **Validar Checkbox de Referente**
    - [ ] Verificar que aparece solo cuando: (O y nivel > 3) o (T y nivel > 4)
    - [ ] Verificar que se persiste correctamente en la base de datos
    - [ ] Verificar que se carga correctamente al editar un mapping existente

- [ ] **Validar Racionales Estratégicos**
    - [ ] Probar disminución de nivel → Debe aparecer selector
    - [ ] Verificar que se guarda el racional seleccionado
    - [ ] Verificar que no aparece cuando el nivel aumenta o se mantiene

- [ ] **Validar Persistencia de Datos**
    - [ ] Crear mapping con is_referent = true
    - [ ] Recargar página y verificar que se mantiene
    - [ ] Verificar en base de datos directamente

**Entregables:**

- Documento de casos de prueba ejecutados
- Lista de bugs encontrados (si los hay)

---

### **Sesión 1.2: Mejorar Visualización en Matriz (1-2 horas)**

**Objetivo:** Hacer que la coherencia arquitectónica sea visible directamente en la matriz.

**Tareas:**

- [ ] **Indicadores de Coherencia en Celdas**
    - [ ] Añadir borde de color según coherencia:
        - Verde: Coherente
        - Amarillo: Info/Warning
        - Rojo: Error crítico
    - [ ] Añadir ícono pequeño en la celda (✓, ⚠, ✗)

- [ ] **Badge de Referente**
    - [ ] Mostrar ícono de estrella (⭐) en celdas con is_referent = true
    - [ ] Tooltip explicativo: "Este rol actúa como mentor técnico"

- [ ] **Mejorar Columna de Roles**
    - [ ] Añadir indicador visual de coherencia general del rol
    - [ ] Mostrar contador de competencias por tipo de cambio
    - [ ] Añadir badge de "Referente" si el rol tiene alguna competencia marcada

**Entregables:**

- Matriz visualmente mejorada
- Documentación de colores y símbolos usados

---

### **Sesión 1.3: Tooltips y Ayudas Contextuales (1 hora)**

**Objetivo:** Mejorar la experiencia de usuario con información contextual.

**Tareas:**

- [ ] **Tooltips en Arquetipos**
    - [ ] E: "Estratégico - Requiere visión global y maestría (niveles 4-5)"
    - [ ] T: "Táctico - Requiere gestión experta (niveles 2-4)"
    - [ ] O: "Operacional - Enfocado en ejecución (niveles 1-2)"

- [ ] **Tooltips en Semáforo de Coherencia**
    - [ ] Explicar por qué se muestra cada tipo de alerta
    - [ ] Sugerir acciones correctivas

- [ ] **Tooltips en Tipos de Cambio**
    - [ ] Mantenimiento: Explicar Job Stabilization
    - [ ] Transformación: Explicar Job Enrichment
    - [ ] Enriquecimiento: Explicar Job Enlargement
    - [ ] Extinción: Explicar Job Substitution

- [ ] **Ayuda Contextual en Modal**
    - [ ] Añadir botón "?" con explicación de cada campo
    - [ ] Link a documentación metodológica

**Entregables:**

- Sistema de tooltips implementado
- Documentación de ayudas contextuales

---

## 🔍 FASE 2: Gap Analysis - Step 3 (Semana 2)

### **Sesión 2.1: Diseño de Gap Analysis (2 horas)**

**Objetivo:** Definir la estructura y algoritmo de análisis de brechas.

**Tareas:**

- [ ] **Definir Modelo de Datos**
    - [ ] Tabla `competency_gaps` (scenario_id, role_id, competency_id, current_level, required_level, gap, priority)
    - [ ] Tabla `learning_paths` (gap_id, recommended_action, timeline, cost_estimate)

- [ ] **Diseñar Algoritmo de Cálculo**
    - [ ] Gap = required_level - current_level
    - [ ] Prioridad basada en:
        - is_core (competencias core tienen mayor prioridad)
        - impact_level del rol
        - Tamaño del gap
    - [ ] Considerar is_referent (roles referentes pueden ser mentores)

- [ ] **Definir Tipos de Recomendaciones**
    - [ ] Upskilling (gap positivo)
    - [ ] Reskilling (cambio de competencia)
    - [ ] Mentorship (usar roles referentes)
    - [ ] External Hiring (gap muy grande)

**Entregables:**

- Documento de diseño de Gap Analysis
- Diagramas de flujo del algoritmo
- Migraciones de base de datos

---

### **Sesión 2.2: Implementación de Gap Analysis (3-4 horas)**

**Objetivo:** Implementar el cálculo y visualización de brechas.

**Tareas:**

- [ ] **Backend**
    - [ ] Endpoint: `GET /api/scenarios/{id}/step3/gaps`
    - [ ] Calcular gaps para todos los roles del escenario
    - [ ] Generar recomendaciones automáticas
    - [ ] Considerar racionales estratégicos (efficiency vs. risk)

- [ ] **Frontend**
    - [ ] Vista de Gap Analysis (Step 3)
    - [ ] Tabla de brechas por rol
    - [ ] Gráficos de distribución de gaps
    - [ ] Panel de recomendaciones

- [ ] **Integración con Coherencia**
    - [ ] Roles con warnings de coherencia tienen mayor prioridad
    - [ ] Roles referentes aparecen como recursos de mentoría

**Entregables:**

- Gap Analysis funcional
- Reportes de brechas generados

---

### **Sesión 2.3: Recomendaciones de Capacitación (2 horas)**

**Objetivo:** Generar planes de acción concretos.

**Tareas:**

- [ ] **Algoritmo de Recomendaciones**
    - [ ] Agrupar gaps por competencia
    - [ ] Identificar roles referentes que pueden ser mentores
    - [ ] Estimar timelines basados en tamaño del gap
    - [ ] Calcular costos estimados

- [ ] **Visualización**
    - [ ] Timeline de capacitación
    - [ ] Matriz de mentoría (quién puede enseñar a quién)
    - [ ] Dashboard de inversión en capacitación

**Entregables:**

- Sistema de recomendaciones implementado
- Reportes de capacitación generados

---

## 🏗️ FASE 3: Competencias Base vs. Escenario (Semana 3)

### **Sesión 3.1: Diseño de Competencias Base (2 horas)**

**Objetivo:** Definir la arquitectura de competencias base.

**Tareas:**

- [ ] **Definir Modelo de Datos**
    - [ ] Tabla `role_base_competencies` (role_id, competency_id, required_level, competency_type)
    - [ ] Tabla `role_archetypes` (id, name, description, competencies_template)

- [ ] **Definir Criterios de Clasificación**
    - [ ] Competencias Transversales (ej: Comunicación, Trabajo en Equipo)
    - [ ] Competencias Corporativas (ej: Cultura, Compliance)
    - [ ] Competencias Técnicas Core (ej: Gestión de Inventarios para Jefe de Bodega)

- [ ] **Diseñar Flujo de Herencia**
    - [ ] Al añadir rol a escenario → heredar competencias base
    - [ ] Marcar como `is_base_competency = true`
    - [ ] Permitir override en escenario si es necesario

**Entregables:**

- Documento de diseño de competencias base
- Migraciones de base de datos
- Definición de arquetipos iniciales

---

### **Sesión 3.2: Implementación de Herencia (3 horas)**

**Objetivo:** Implementar la herencia automática de competencias base.

**Tareas:**

- [ ] **Backend**
    - [ ] Modificar `addRole` para heredar competencias base
    - [ ] Endpoint: `GET /api/roles/{id}/base-competencies`
    - [ ] Endpoint: `POST /api/roles/{id}/base-competencies` (para definir)

- [ ] **Frontend**
    - [ ] Diferenciar visualmente competencias base (fondo gris claro)
    - [ ] Mostrar origen de la competencia (catálogo vs. escenario)
    - [ ] Filtro para ver solo competencias de escenario

- [ ] **Lógica de Actualización**
    - [ ] Competencias base no se pueden eliminar del escenario
    - [ ] Se pueden ajustar niveles si es necesario
    - [ ] Cambios en base afectan a todos los escenarios futuros

**Entregables:**

- Sistema de herencia implementado
- Competencias base visualmente diferenciadas

---

### **Sesión 3.3: Arquetipos de Roles (2 horas)**

**Objetivo:** Crear plantillas de roles reutilizables.

**Tareas:**

- [ ] **Definir Arquetipos Iniciales**
    - [ ] Supervisor Operacional
    - [ ] Especialista Técnico
    - [ ] Coordinador Táctico
    - [ ] Líder Estratégico

- [ ] **Interfaz de Gestión**
    - [ ] CRUD de arquetipos
    - [ ] Asignar competencias base a arquetipos
    - [ ] Aplicar arquetipo al crear nuevo rol

**Entregables:**

- Catálogo de arquetipos
- Interfaz de gestión de arquetipos

---

## 🔄 FASE 4: Flujo del Cubo Completo (Semana 4)

### **Sesión 4.1: Completar Flujo de Incubación (2-3 horas)**

**Objetivo:** Integrar incubación con coherencia y competencias base.

**Tareas:**

- [ ] **Calcular Arquetipo Automáticamente**
    - [ ] Basado en human_leverage del TalentBlueprint
    - [ ] Aplicar al aprobar rol incubado

- [ ] **Heredar Competencias Base**
    - [ ] Al aprobar rol incubado → heredar competencias base
    - [ ] Aplicar arquetipo correspondiente

- [ ] **Validación de Coherencia**
    - [ ] Validar que competencias incubadas son coherentes con arquetipo
    - [ ] Mostrar warnings si hay inconsistencias

**Entregables:**

- Flujo de incubación completo
- Integración con coherencia arquitectónica

---

### **Sesión 4.2: Dashboard de Escenarios (3 horas)**

**Objetivo:** Vista comparativa de múltiples escenarios.

**Tareas:**

- [ ] **Métricas Clave**
    - [ ] FTE total por escenario
    - [ ] Distribución de arquetipos (E/T/O)
    - [ ] Número de competencias en transformación
    - [ ] Inversión estimada en capacitación

- [ ] **Comparativa de Escenarios**
    - [ ] Tabla comparativa lado a lado
    - [ ] Gráficos de distribución
    - [ ] Análisis de impacto

- [ ] **Exportación**
    - [ ] Exportar a Excel
    - [ ] Exportar a PDF
    - [ ] Generar presentación ejecutiva

**Entregables:**

- Dashboard de escenarios
- Sistema de exportación

---

### **Sesión 4.3: Reportes y Documentación (2 horas)**

**Objetivo:** Generar reportes ejecutivos y documentación técnica.

**Tareas:**

- [ ] **Reportes Ejecutivos**
    - [ ] Resumen de escenario
    - [ ] Análisis de brechas
    - [ ] Plan de capacitación
    - [ ] Análisis de riesgos

- [ ] **Documentación Técnica**
    - [ ] Actualizar README
    - [ ] Documentar APIs
    - [ ] Guía de usuario
    - [ ] Casos de uso

**Entregables:**

- Suite de reportes
- Documentación completa

---

## 📊 Métricas de Éxito

### **FASE 1: Validación y Pulido**

- ✅ 100% de casos de prueba pasando
- ✅ Matriz visualmente mejorada
- ✅ Tooltips implementados

### **FASE 2: Gap Analysis**

- ✅ Algoritmo de gaps funcionando
- ✅ Recomendaciones generadas automáticamente
- ✅ Reportes de capacitación disponibles

### **FASE 3: Competencias Base**

- ✅ Herencia automática funcionando
- ✅ Al menos 3 arquetipos definidos
- ✅ Diferenciación visual implementada

### **FASE 4: Flujo Completo**

- ✅ Incubación integrada con coherencia
- ✅ Dashboard de escenarios funcional
- ✅ Sistema de exportación operativo

---

## 🎯 Priorización

**Crítico (Debe hacerse):**

- FASE 1 completa
- FASE 2: Sesiones 2.1 y 2.2

**Importante (Debería hacerse):**

- FASE 2: Sesión 2.3
- FASE 3: Sesiones 3.1 y 3.2

**Deseable (Puede hacerse):**

- FASE 3: Sesión 3.3
- FASE 4 completa

---

## 📅 Timeline Sugerido

```
Semana 1: FASE 1 (Validación y Pulido)
├─ Lunes: Sesión 1.1 (Validación)
├─ Martes: Sesión 1.2 (Visualización)
└─ Miércoles: Sesión 1.3 (Tooltips)

Semana 2: FASE 2 (Gap Analysis)
├─ Lunes: Sesión 2.1 (Diseño)
├─ Martes-Miércoles: Sesión 2.2 (Implementación)
└─ Jueves: Sesión 2.3 (Recomendaciones)

Semana 3: FASE 3 (Competencias Base)
├─ Lunes: Sesión 3.1 (Diseño)
├─ Martes-Miércoles: Sesión 3.2 (Herencia)
└─ Jueves: Sesión 3.3 (Arquetipos)

Semana 4: FASE 4 (Flujo Completo)
├─ Lunes-Martes: Sesión 4.1 (Incubación)
├─ Miércoles: Sesión 4.2 (Dashboard)
└─ Jueves-Viernes: Sesión 4.3 (Reportes)
```

---

## 🚀 Próxima Acción Inmediata

**Ahora mismo:** Ejecutar Sesión 1.1 (Validación Exhaustiva)

**Checklist de inicio:**

- [ ] Abrir navegador en la matriz de roles-competencias
- [ ] Preparar documento de casos de prueba
- [ ] Tener acceso a la base de datos para verificaciones
- [ ] Comenzar con el primer caso: Rol Estratégico con nivel 3

---

**¿Comenzamos con la Sesión 1.1 de validación?**
