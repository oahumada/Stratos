# 📖 Diccionario de Datos: Métricas de Negocio (ERP Integration)

Este diccionario define la semántica y los requisitos técnicos de las métricas necesarias para alimentar el **Impact Engine** de Stratos. Provee una guía clara para equipos de TI o integradores que deban extraer estos datos desde sistemas externos (SAP, Oracle, Salesforce, etc.).

## 1. Métricas Núcleo (Core Financials)

| Métrica                 | Identificador Técnico | Descripción Semántica                                                | Origen Típico           |
| :---------------------- | :-------------------- | :------------------------------------------------------------------- | :---------------------- |
| **Ingresos Totales**    | `revenue`             | Ingreso bruto total de la organización o departamento en el periodo. | ERP (Cuentas de Ventas) |
| **Gastos Operativos**   | `opex`                | Gastos de operación totales excluyendo el costo directo de ventas.   | ERP (P&L Statements)    |
| **Costo de Nómina**     | `payroll_cost`        | Suma total de salarios, bonos, cargas sociales y beneficios.         | HRIS / Nómina           |
| **Headcount (FTE)**     | `headcount`           | Número de equivalentes a tiempo completo activos en el periodo.      | HRIS                    |
| **Beneficio Operativo** | `operating_income`    | EBITDA o utilidad operativa antes de impuestos e intereses.          | ERP                     |

## 2. Métricas de Operaciones de Personas (HR Ops & Health)

Estas métricas son críticas para el cálculo de **Riesgo de Continuidad** y **Fricción Organizacional**.

| Métrica                    | Identificador Técnico | Descripción Semántica                                                                   | Origen Típico                |
| :------------------------- | :-------------------- | :-------------------------------------------------------------------------------------- | :--------------------------- |
| **Tasa de Rotación**       | `turnover_rate`       | Porcentaje de bajas (voluntarias e involuntarias) sobre el headcount total.             | HRIS                         |
| **Ausentismo**             | `absenteeism_rate`    | Ratio de horas/días de ausencia no planificada sobre el total de horas/días laborables. | HRIS / Control de Asistencia |
| **Licencias Médicas**      | `medical_leave_days`  | Total de días perdidos por enfermedad o accidentes laborales.                           | HRIS / Seguros Sociables     |
| **Tasa de Retención HiPo** | `hipo_retention_rate` | Porcentaje de High Potentials que permanecen en la empresa (crucial para Radar).        | Stratos + HRIS               |

## 3. Métricas de Soporte (Contextual)

| Métrica                   | Identificador Técnico | Descripción Semántica                                           | Origen Típico |
| :------------------------ | :-------------------- | :-------------------------------------------------------------- | :------------ |
| **Satisfacción Cliente**  | `csat` / `nps`        | Índice de satisfacción del cliente o Net Promoter Score.        | CRM           |
| **Tasa de Churn**         | `customer_churn`      | Porcentaje de clientes perdidos en el periodo.                  | CRM           |
| **Costo de Contratación** | `recruitment_cost`    | Gastos totales de marketing, agencias y referidos de selección. | ATS           |

## 3. Guía de Mapeo Técnico para el CSV

Para que la ingesta sea exitosa, el sistema de origen debe mapear sus campos al formato de Stratos según este diccionario:

### Atributos Obligatorios

- **`metric_name`**: Debe usar preferiblemente los **Identificadores Técnicos** definidos arriba (ej: `payroll_cost`) para que el algoritmo HCVA los reconozca automáticamente.
- **`metric_value`**: Valor decimal simple. Si el sistema origen exporta con símbolos de moneda (ej: "$1.000"), estos deben ser limpiados antes de la ingesta.
- **`period_date`**: Se recomienda la extracción al **cierre del mes** (ej: `2026-03-31`) para mantener consistencia con los reportes financieros.
- **`source`**: Identificador del sistema de origen (ej: `SAP_S4HANA`, `WORKDAY`, `MANUAL_UPLOAD`).

### Uso de Metadata (Extensibilidad)

El campo `metadata` permite enviar claves que el ERP ya posea pero que Stratos no use de forma nativa.

- _Ejemplo:_ `{"cost_center": "CC-102", "currency": "EUR", "region": "North_America"}`

## 4. Estrategia de Desagregación (Granularidad)

Encontrar el equilibrio entre la complejidad técnica y la visibilidad de patrones es clave. Stratos recomienda un enfoque de **Capas de Resolución**:

| Nivel            | Granularidad         | Propósito                                                       | Recomendación                    |
| :--------------- | :------------------- | :-------------------------------------------------------------- | :------------------------------- |
| **L1: Global**   | Toda la organización | Benchmarking de mercado y KPIs de Inversionista.                | **Obligatorio** para HCVA base.  |
| **L2: Org-Unit** | Departamento / Área  | Detectar brechas de eficiencia y ROI por unidad de negocio.     | **Punto Óptimo (Sweet Spot)**.   |
| **L3: Micro**    | Equipo / Proyecto    | Análisis de alta resolución para "War-Rooms" de crisis.         | Opcional (Alta complejidad).     |
| **L4: Atómico**  | Por individuo        | Solo para métricas directas de desempeño (ej: Cuota de ventas). | No recomendado para financieros. |

### El "Sweet Spot": Desagregación por Departamento (`department_id`)

Para que el **Digital Twin** sea efectivo, el ERP debería entregar los datos (Revenue, Payroll, CapEx) agrupados por **Unidad Organizativa**.

**¿Por qué?**

1. **Visibilidad de Patrones:** Permite ver si el costo de nómina de _IT_ está generando más valor que el de _Ventas_ relativo a su tamaño.
2. **Privacidad y Simplicidad:** Agregando por departamento, protegemos la privacidad salarial individual mientras mantenemos la capacidad analítica del motor agéntico.
3. **Mapeo con el Grafo:** Stratos puede cruzar estas métricas con el "Skill Mesh" del departamento detectado en el grafo de Neo4j.

---

> [!IMPORTANT]
> **Regla de Oro:** "Agrega para Reportar, Desagrega para Actuar". El `ImpactEngine` funciona mejor con datos de **Nivel L2 (Unidad de Negocio/Departamento)**.

## 5. El Concepto de "Departamento" y Normalización

Un desafío común es que el "Departamento de Ventas" en el ERP (Finanzas) puede no coincidir exactamente con "Sales & Growth" en el HRIS (RRHH). Stratos resuelve esto mediante la **Unificación por Nodo Gravitacional**.

### ¿Qué definimos como Departamento?

En Stratos, un departamento no es solo una etiqueta, es un **Nodo en el Grafo** que agrupa personas, habilidades y ahora, presupuestos.

### Estrategia de Reconciliación (Mapping)

Para evitar el caos de versiones, se deben seguir estas reglas:

1.  **ID Primario (Canonical ID):** Siempre que sea posible, usar el ID interno de Stratos (`department_id`).
2.  **Mapeo de Centro de Costos:** Si el ERP solo conoce "Centros de Costo", estos deben viajar en el campo `metadata` o ser mapeados previamente en la configuración de la organización dentro de Stratos.
3.  **Versión de la Verdad:** Stratos considera al **Digital Twin** como la versión "viva". Si una métrica llega para un departamento que cambió de nombre, el motor agéntico usará el historial del Grafo para entender que son la misma entidad en diferentes momentos.
4.  **Atributos de Cruce:** Se recomienda que el CSV incluya un alias si los nombres varían:
    - _Valor en CSV:_ `Ventas_LATAM`
    - _Mapeo en Stratos:_ Alias de `Sales Department (ID: 45)`

### Resolución de Conflictos (Ambivalencia)

Cuando hay varias versiones del organigrama, el **Agente Estratégico** de Stratos utiliza el **"Peso de Nómina"** como ancla: el departamento donde se paga el salario de la mayoría de las personas vinculadas a ese resultado es el que se considera "Dueño" de la métrica de impacto.

---

> [!TIP]
> **Recomendación para la Extracción:** Programar un reporte mensual automático en el ERP que genere el CSV directamente con estos encabezados para una sincronización "Zero-Touch".
