# 🚀 Hoja de Ruta: Transformación Estratégica y Motor de Impacto

Este documento detalla el plan de acción para las próximas sesiones de desarrollo en Stratos, enfocado en conectar el **Gemelo Digital** con resultados financieros tangibles y automatización avanzada.

## 📋 Fase 1: Ingesta de Datos y Métricas de Negocio

**Objetivo:** Alimentar al "Cerebro Agéntico" con datos reales del negocio para salir de simulaciones teóricas.

- [ ] **Comando de Ingesta:** Crear `php artisan stratos:ingest-metrics` para procesar CSVs.

### 📊 Especificación del CSV de Entrada (`business_metrics.csv`)

Para asegurar una ingesta limpia, el archivo debe seguir esta estructura:

| Campo           | Tipo    | Requerido | Descripción                                                                   |
| :-------------- | :------ | :-------: | :---------------------------------------------------------------------------- |
| `metric_name`   | String  |    Sí     | Nombre de la métrica (ej: `revenue`, `payroll_cost`, `headcount`).            |
| `metric_value`  | Decimal |    Sí     | Valor numérico de la métrica.                                                 |
| `period_date`   | Date    |    Sí     | Fecha del registro (Formato: `YYYY-MM-DD`). Se recomienda usar el fin de mes. |
| `unit`          | String  |    No     | Unidad de medida (ej: `USD`, `FTE`, `PCT`).                                   |
| `department_id` | Integer |    No     | ID del departamento si la métrica es segmentada.                              |
| `source`        | String  |    Sí     | Sistema origen (ej: `SAP`, `Salesforce`, `Manual`).                           |
| `metadata`      | JSON    |    No     | Atributos adicionales en formato key:value.                                   |

**Ejemplo de fila:**
`revenue, 1250000.00, 2026-03-31, USD,, SAP, {"region": "LATAM"}`

- [ ] **API de Conector:** Endpoint para recibir métricas desde sistemas externos (ERP/CRM).

## 🧮 Fase 2: El Algoritmo HCVA (Human Capital Value Added)

**Objetivo:** Implementar la métrica definitiva de rendimiento del capital humano dentro del `ImpactEngineService`.

- [ ] **Lógica de Cálculo:** Implementar la fórmula científica:
    - `HCVA = [Revenue - (Total Expenses - Payroll)] / Full-Time Equivalents`.
- [ ] **Correlación de Habilidades:** Vincular la degradación del HCVA con las brechas de competencias detectadas en el Grafo (Digital Twin).

## 📊 Fase 3: Dashboard de Impacto (Executive Tracking)

**Objetivo:** Visualización premium para C-Level e Inversionistas.

- [ ] **KPI Cards:** Visualizar HCVA, ROI de Capacitación y Riesgo de Reemplazo en tiempo real.
- [ ] **Gráfico de Correlación:** Mostrar cómo el "Skill Mesh" (Malla de Habilidades) afecta la rentabilidad por departamento.
- [ ] **Integración Radar:** Activar la tarjeta de "Pregunta al Agente" para que las respuestas incluyan proyecciones de HCVA.

## 🤖 Fase 4: Autonomía Agéntica Total

**Objetivo:** Que los agentes no solo analicen, sino que propongan y ejecuten cambios.

- [ ] **Protocolo de "Materialización Directa":** Flujo de aprobación rápida para micro-movimientos de personal sugeridos por la IA.
- [ ] **Sentinel Feedback Loop:** Que las encuestas de clima (PX) se disparen automáticamente cuando el HCVA de un área caiga por debajo del benchmark.

---

> [!NOTE]
> **Estado Actual:** El Gemelo Digital ya captura el estado organizacional. El siguiente paso crítico es el **Comando de Ingesta** para dotar de realismo financiero a las simulaciones.
