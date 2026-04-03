# 📘 Workforce Planning en Stratos

**Versión:** 2026-04-01  
**Objetivo:** Definir el fundamento conceptual y técnico del módulo de planificación dotacional, su aporte al sistema, cómo se usa y cómo bajarlo a implementación de forma ordenada.

---

## 1) ¿Qué es Workforce Planning en este contexto?

En Stratos, **Workforce Planning (Planificación Dotacional)** es el módulo que responde:

- cuánta fuerza laboral se necesita (FTE/headcount),
- con qué capacidades (skills/capabilities),
- en qué momento (horizonte temporal),
- en qué lugar/unidad (estructura organizacional),
- y con qué combinación de palancas (rotación, traslados, incorporación, reskilling, contingente, automatización, talento sintético/híbrido).

Es **distinto** de Gestión de Talento:

- Workforce Planning decide la **arquitectura de dotación futura**.
- Talent Management ejecuta el desarrollo individual y la experiencia del colaborador.

Se complementan, pero no son lo mismo.

---

## 2) Fundamento conceptual (modelo escogido)

### 2.1 Base de evidencia y convergencia de mercado

La **literatura accesible (CIPD/AIHR/OCDE)** y la convergencia observada en suites enterprise del mercado apuntan a este modelo moderno:

1. **Dirección estratégica:** prioridades de negocio, horizontes (12-36 meses), escenarios macro.
2. **Demanda futura de fuerza laboral:** FTE por rol/unidad/ubicación, estacionalidad, productividad esperada.
3. **Oferta interna/externa:** inventario de skills, movilidad interna, rotación histórica, pipeline externo.
4. **Brecha dotacional:** cantidad + capacidad (skills) + ubicación + costo + riesgo.
5. **Portafolio de palancas:** contratación, reskilling, movilidad/traslados, rotación planificada, automatización, outsourcing, talento sintético/híbrido.
6. **Optimización y trade-offs:** costo-tiempo-riesgo-servicio (no una sola métrica).
7. **Ejecución con gobernanza:** plan aprobado, dueños por unidad, cadencia mensual/trimestral.
8. **Monitoreo continuo:** forecast vs real, desvíos, recalibración de escenarios.

Este marco se adopta en Stratos como referencia canónica para el diseño funcional del módulo.

### 2.2 Desarrollo del fundamento CIPD/AIHR/OCDE

#### CIPD: planificación estratégica alineada al negocio

Desde CIPD, Workforce Planning se entiende como un proceso continuo de alineación entre estrategia de negocio y estrategia de personas. El foco no es solo “cubrir vacantes”, sino anticipar capacidad organizacional futura.

**Aportes clave al modelo:**

- Alineación explícita con objetivos de negocio de mediano plazo.
- Priorización de roles críticos para resultados (no tratar todos los roles igual).
- Gobernanza y accountability en la ejecución del plan.
- Revisión periódica para adaptar decisiones frente a cambios del contexto.

**Implicación en Stratos:**

- El escenario debe partir de objetivos estratégicos formales.
- Debe existir dueñidad por unidad/rol en el plan aprobado.
- El módulo debe soportar ciclo de revisión mensual/trimestral.

#### AIHR: modelo operativo y medible de SWP

AIHR aporta un enfoque altamente operativo: “right size, right shape, right cost, right agility” y una secuencia concreta de análisis de workforce actual, escenarios futuros y brechas accionables.

**Aportes clave al modelo:**

- Separar con claridad demanda futura vs oferta futura esperada.
- Hacer gap analysis multidimensional (cantidad, capacidad, tiempo, costo).
- Convertir diagnóstico en portafolio de acciones con priorización.
- Tratar SWP como ciclo iterativo, no proyecto one-shot.

**Implicación en Stratos:**

- Estructura de datos separada para `DemandLine`, `SupplySnapshot` y `GapLine`.
- Motor de recomendación con racional explícito por acción.
- KPIs de cierre de brecha y velocidad de cobertura por tipo de palanca.
- Capacidad de distinguir entre capacidad humana, automatizada y apalancada por talento híbrido.

#### OCDE: contexto macro y resiliencia del mercado laboral

La OCDE refuerza que la planificación laboral moderna debe incorporar megatendencias: transición digital, envejecimiento poblacional, transición verde, cambios de empleo y necesidad de reskilling continuo.

**Aportes clave al modelo:**

- Incorporar señales externas en la proyección de oferta/demanda.
- Tratar riesgo de skills obsoletas y escasez estructural de talento.
- Combinar productividad, empleabilidad y calidad del empleo en decisiones.
- Diseñar planes resilientes ante shocks (mercado, tecnología, regulación).

**Implicación en Stratos:**

- Variables de contexto externo deben formar parte del escenario.
- Las recomendaciones no pueden ser solo “hire”; deben balancear movilidad, reskilling y automatización.
- Deben existir simulaciones de sensibilidad para escenarios adversos.

#### Convergencia práctica para Stratos

La convergencia de los tres marcos define una regla simple para diseño de producto:

1. **Estrategia primero** (CIPD)
2. **Modelo analítico accionable** (AIHR)
3. **Resiliencia y señales externas** (OCDE)

En términos operativos, esto obliga a que cada escenario produzca tres salidas mínimas:

- brechas cuantificadas y priorizadas,
- recomendaciones explicables por brecha,
- plan ejecutable con seguimiento y recalibración.

Y una cuarta decisión transversal:

- identificar qué parte de la capacidad debe resolverse con **talento humano directo**, **automatización** o **talento sintético/híbrido**.

### 2.3 Matriz ejecutiva de traducción a producto (Stratos)

| Marco   | Principio rector                             | Dato requerido                                                                                            | Funcionalidad en Stratos                                                | KPI de control                                                   |
| ------- | -------------------------------------------- | --------------------------------------------------------------------------------------------------------- | ----------------------------------------------------------------------- | ---------------------------------------------------------------- |
| CIPD    | Alinear workforce con estrategia de negocio  | Objetivos estratégicos, prioridades, horizonte 12-36 meses                                                | Definición de escenario con supuestos de negocio y unidades críticas    | % iniciativas estratégicas con plan dotacional asociado          |
| CIPD    | Foco en roles críticos y gobernanza          | Catálogo de roles críticos, dueños por unidad, estado de aprobación                                       | Flujo `draft -> in_review -> approved -> active` + ownership por unidad | % acciones con owner asignado y SLA de decisión                  |
| AIHR    | Separar demanda, oferta y brecha             | `DemandLine`, `SupplySnapshot`, `GapLine` por rol/unidad/periodo                                          | Motor de cálculo de brechas y priorización                              | % brechas identificadas con severidad y prioridad                |
| AIHR    | Convertir diagnóstico en acciones            | Catálogo de palancas (`ROTATE`, `TRANSFER`, `HIRE`, `RESKILL`, `CONTINGENT`, `AUTOMATE`, `HYBRID_TALENT`) | Motor de recomendaciones explicables por brecha                         | % brechas con recomendación explicable                           |
| Stratos | Apalancar capacidad con talento híbrido      | Actividades delegables, nivel de autonomía, riesgo, necesidad de supervisión humana                       | Recomendador de apalancamiento humano + IA + automatización             | % capacidad resuelta vía apalancamiento híbrido con SLA cumplido |
| AIHR    | Planificación iterativa y medible            | Forecast por ciclo, resultados reales, historial de decisiones                                            | Revisión periódica y recalibración de escenarios                        | Forecast accuracy y lead time de cobertura                       |
| OCDE    | Incorporar señales macro del mercado laboral | Señales externas: escasez de skills, transición digital/verde, demografía                                 | Variables exógenas en escenarios y simulaciones de sensibilidad         | Desvío por shock (escenario base vs adverso)                     |
| OCDE    | Resiliencia ante riesgo estructural          | Riesgo por skill crítica, dependencia externa, obsolescencia                                              | Semáforo de riesgo por rol/unidad y plan de mitigación                  | % riesgos críticos con plan activo                               |

**Uso práctico de la matriz:**

1. Product define funcionalidades mínimas por fila.
2. Data/Engineering define fuentes y calidad del dato requerido.
3. RRHH estratégico valida reglas de decisión y prioridad de palancas.
4. Dirección monitorea KPIs y ajusta la cadencia de recalibración.

El modelo recomendado para Stratos es un **SWP (Strategic Workforce Planning) híbrido de 8 etapas**:

1. **Dirección estratégica:** objetivos del negocio (12-36 meses).
2. **Demanda futura:** FTE y capacidades requeridas por rol/unidad/ubicación.
3. **Oferta actual y proyectada:** inventario interno + mercado externo + flujos (rotación/attrition/movilidad).
4. **Brechas:** diferencia entre demanda y oferta por cantidad, skill, tiempo, costo y riesgo.
5. **Portafolio de palancas:**
    - `ROTATE` (rotación interna),
    - `TRANSFER` (traslado),
    - `HIRE` (incorporación externa),
    - `RESKILL` (reconversión/capacitación),
    - `CONTINGENT` (temporal/tercerizado),
    - `AUTOMATE` (automatización),
    - `HYBRID_TALENT` (capacidad apalancada con agentes, copilotos o fuerza laboral sintética supervisada).
6. **Optimización:** priorizar por impacto, costo, lead time y riesgo operativo.
7. **Gobernanza:** aprobación, dueños, fechas, hitos, trazabilidad.
8. **Monitoreo continuo:** forecast vs real y recalibración.

Este enfoque evita que el módulo se limite a “reportes de talento” y lo convierte en un sistema de decisión dotacional.

### 2.4 Fundamento del modelo de cálculo dotacional

Antes de recomendar contratación, traslado, rotación o reskilling, el módulo debe resolver una pregunta básica:

**¿La capacidad laboral disponible alcanza para cubrir la demanda esperada con el nivel de productividad y cobertura comprometido?**

Para responderla, el modelo debe calcular dotación desde **HH (horas-hombre / horas hábiles)** y no solo desde headcount bruto.

#### 2.4.1 Variables base del cálculo

El cálculo dotacional debe considerar al menos estas variables:

- **Volumen de trabajo esperado:** transacciones, casos, ventas, tickets, proyectos, órdenes o producción.
- **Productividad esperada:** output por persona, por FTE o por HH.
- **HH netas disponibles por persona/FTE:** horas calendario menos ausentismo, vacaciones, licencias, formación y otras pérdidas operativas.
- **Cobertura objetivo:** porcentaje mínimo de cobertura requerido para operar dentro de SLA o estándar de servicio.
- **Factor de fricción/rampa:** pérdida temporal de productividad por onboarding, movilidad o cambio de rol.
- **Mix de capacidad:** capacidad interna inmediata, capacidad interna desarrollable y capacidad externa.
- **Capacidad híbrida/sintética:** trabajo absorbible por agentes, copilotos, automatizaciones o combinaciones humano + IA bajo supervisión.

#### 2.4.2 Lógica general del cálculo

##### A. Demanda de capacidad

Primero se traduce la demanda operativa a HH requeridas:

$$
HH_{requeridas} = \frac{Volumen_{esperado} \times Tiempo_{estándar\ por\ unidad}}{Factor_{productividad}}
$$

o, si ya existe productividad expresada como output por HH:

$$
HH_{requeridas} = \frac{Volumen_{esperado}}{Productividad_{por\ HH}}
$$

Luego se ajusta por cobertura objetivo y contingencias operativas:

$$
HH_{ajustadas} = HH_{requeridas} \times Factor_{cobertura} \times Factor_{reserva}
$$

Donde:

- `Factor_cobertura` eleva la capacidad cuando el nivel de servicio exige holgura.
- `Factor_reserva` incorpora buffers por variabilidad, picos, turnos o riesgo operativo.

##### B. Oferta de capacidad

La oferta no se debe medir solo en personas, sino en HH netas realmente utilizables:

$$
HH_{disponibles} = FTE_{disponibles} \times HH_{netas\ por\ FTE} \times Factor_{productividad\ real}
$$

Si parte de la dotación está en rampa o reconversión, la capacidad se reduce temporalmente:

$$
HH_{efectivas} = HH_{disponibles} \times Factor_{cobertura\ efectiva} \times Factor_{rampa}
$$

Esto permite distinguir entre:

- capacidad nominal,
- capacidad productiva real,
- y capacidad realmente disponible para cubrir demanda.

##### C. Déficit o exceso dotacional

La brecha primaria se obtiene comparando HH ajustadas versus HH efectivas:

$$
Brecha_{HH} = HH_{ajustadas} - HH_{efectivas}
$$

Interpretación:

- Si `Brecha_HH > 0` → existe **déficit dotacional**.
- Si `Brecha_HH < 0` → existe **exceso dotacional** o capacidad ociosa.
- Si `Brecha_HH ≈ 0` → la dotación está balanceada para el escenario analizado.

Para traducir la brecha a FTE:

$$
Brecha_{FTE} = \frac{Brecha_{HH}}{HH_{netas\ estándar\ por\ FTE}}
$$

#### 2.4.3 Qué significa cobertura en este modelo

La cobertura no es simplemente “cantidad de personas asignadas”, sino el grado en que la capacidad efectiva cubre la capacidad requerida.

$$
Cobertura\% = \frac{HH_{efectivas}}{HH_{ajustadas}} \times 100
$$

Interpretación operativa:

- `100%` = cobertura exacta.
- `<100%` = riesgo de incumplimiento, sobrecarga o backlog.
- `>100%` = holgura operativa o posible sobrecapacidad.

Este concepto es coherente con métricas ya presentes en Stratos como `coverage_pct`, `gap_headcount` y `avg_coverage_pct`.

#### 2.4.4 Dónde entra la productividad

La productividad modifica directamente la demanda de HH y la oferta efectiva.

No basta con saber cuántas personas hay; importa cuánto producen realmente bajo condiciones esperadas. Por eso el modelo debe distinguir entre:

- **productividad estándar** (benchmark esperado),
- **productividad real** (observada),
- **tiempo a productividad** (rampa),
- **pérdida por fricción** (movilidad, cambio de rol, onboarding).

En Stratos esto es especialmente relevante porque algunas palancas, como `TRANSFER` o `RESKILL`, pueden cerrar headcount pero no cerrar capacidad inmediata si el `time_to_productivity_months` es alto.

#### 2.4.5 Cómo se determina déficit versus exceso

El modelo no debe declarar déficit o exceso usando solo headcount bruto. Debe hacerlo en dos capas:

1. **Brecha cuantitativa:** falta o sobra capacidad en HH/FTE.
2. **Brecha cualitativa:** la capacidad existe, pero no con las skills, ubicación o disponibilidad necesarias.

Esto genera cuatro situaciones:

- **Déficit real:** faltan HH y faltan skills.
- **Déficit de capacidad especializada:** hay personas, pero no con la capacidad requerida.
- **Exceso reutilizable:** sobra capacidad y puede reasignarse.
- **Exceso no utilizable:** sobra headcount, pero no es convertible fácilmente.

Y una quinta situación relevante para Stratos:

- **Déficit apalancable:** falta capacidad humana directa, pero parte del gap puede cubrirse con talento sintético/híbrido sin deteriorar control, calidad o cumplimiento.

#### 2.4.6 Implicación de diseño para Stratos

Por lo tanto, el cálculo dotacional del módulo debe producir como mínimo:

- `required_hh`
- `available_hh`
- `effective_hh`
- `coverage_pct`
- `gap_hh`
- `gap_fte`
- `gap_type` = `deficit` | `surplus` | `balanced`
- `productivity_factor`
- `time_to_productivity_months`
- `hybrid_leverage_pct`
- `hybrid_readiness`

Y sobre esa base, recién después, activar el motor de recomendaciones.

#### 2.4.7 Regla de decisión posterior

Una vez obtenida la brecha:

- si el déficit es de corto plazo y la capacidad interna no llega a tiempo → `HIRE` o `CONTINGENT`;
- si existe exceso reutilizable en otra unidad → `TRANSFER` o `ROTATE`;
- si la brecha es principalmente de skill y el tiempo lo permite → `RESKILL`;
- si el déficit proviene de baja productividad estructural o tareas automatizables → `AUTOMATE`.
- si el trabajo es parcialmente delegable, repetible, asistible o escalable con supervisión humana → `HYBRID_TALENT`.

Así, el modelo dotacional no parte de la solución, sino del cálculo de capacidad efectiva contra demanda exigida.

#### 2.4.8 Matriz de elegibilidad para `HYBRID_TALENT`

El uso de `HYBRID_TALENT` debe evaluarse con una matriz explícita de elegibilidad. La pregunta no es solo si una tarea “puede” ser asistida por IA, sino si **debe** ser apalancada con talento híbrido sin comprometer calidad, control, cumplimiento o accountability.

| Criterio                         | Señal favorable para `HYBRID_TALENT`      | Señal de cautela                  | Señal desfavorable                                      |
| -------------------------------- | ----------------------------------------- | --------------------------------- | ------------------------------------------------------- |
| Estructura de la tarea           | Flujo repetible, documentado y predecible | Flujo parcialmente variable       | Flujo altamente ambiguo o no estandarizado              |
| Necesidad de juicio experto      | Bajo o acotado                            | Juicio humano frecuente           | Juicio crítico continuo                                 |
| Riesgo regulatorio/compliance    | Bajo, auditable y con controles           | Medio, requiere validación humana | Alto, regulado o con alto riesgo legal                  |
| Sensibilidad de datos            | Datos no sensibles o redaccionables       | Datos sensibles con controles     | Datos críticos o altamente restringidos                 |
| Tolerancia al error              | Error reversible o de bajo impacto        | Error moderado con revisión       | Error de alto impacto operacional, legal o reputacional |
| Necesidad de empatía/negociación | Baja                                      | Media                             | Alta (conversaciones complejas, conflicto, liderazgo)   |
| Supervisión disponible           | Hay owner y checkpoint humano claro       | Supervisión parcial               | No hay supervisión efectiva                             |
| Escalabilidad requerida          | Alta necesidad de volumen o velocidad     | Mejora marginal                   | Sin necesidad real de escala                            |
| Tiempo a productividad           | Muy corto con copiloto/agente             | Requiere tuning o entrenamiento   | Largo o incierto                                        |
| Madurez del proceso              | Proceso medido con SLA y estándar         | Proceso parcialmente definido     | Proceso inmaduro o no medido                            |

##### Regla de clasificación sugerida

Cada criterio puede evaluarse con una escala simple:

- `2` = favorable para `HYBRID_TALENT`
- `1` = condicional / requiere control adicional
- `0` = desfavorable

Luego se calcula un puntaje total:

$$
HybridReadinessScore = \sum_{i=1}^{n} criterio_i
$$

Interpretación sugerida:

- **0-7** → no elegible; resolver con capacidad humana directa.
- **8-13** → elegible solo en modalidad híbrida asistida, con supervisión obligatoria.
- **14-20** → elegible para apalancamiento híbrido fuerte, con monitoreo de SLA/calidad.

##### Guardrails mínimos de activación

`HYBRID_TALENT` solo debe recomendarse cuando se cumplan simultáneamente estas condiciones:

1. existe owner humano responsable,
2. el riesgo regulatorio es aceptable,
3. la salida del trabajo es auditable,
4. el apalancamiento mejora al menos una de estas variables: cobertura, velocidad o costo,
5. la calidad esperada no cae por debajo del umbral definido por negocio.

##### Salidas recomendadas para el motor

Además de `hybrid_leverage_pct` y `hybrid_readiness`, el motor debería emitir:

- `hybrid_readiness_score`
- `hybrid_mode` = `not_eligible` | `assisted` | `leveraged`
- `supervision_required` = `true|false`
- `compliance_review_required` = `true|false`
- `expected_capacity_gain_pct`
- `expected_quality_risk`

Con esto, `HYBRID_TALENT` deja de ser una idea abstracta y pasa a ser una decisión auditable dentro del cálculo dotacional.

#### 2.4.9 Tabla de reglas del motor de recomendación (v1)

La siguiente tabla baja el modelo a reglas ejecutables para backend/producto.

| Regla                             | Condición principal                                        | Fórmula/Señal                            | Output esperado                         | Palanca recomendada                   | Prioridad |
| --------------------------------- | ---------------------------------------------------------- | ---------------------------------------- | --------------------------------------- | ------------------------------------- | --------- |
| R1 Déficit crítico inmediato      | `gap_hh > 0` y `lead_time_objetivo < lead_time_interno`    | `gap_fte = gap_hh / hh_netas_fte`        | `gap_type=deficit`, `severity=critical` | `HIRE` o `CONTINGENT`                 | Alta      |
| R2 Déficit especializado          | `gap_hh > 0` y `skill_match_interno < umbral_skill`        | `coverage_pct_skill < objetivo`          | déficit de capacidad especializada      | `HIRE` + `RESKILL` combinado          | Alta      |
| R3 Déficit reasignable            | `gap_hh > 0` en unidad A y `surplus_hh > 0` en unidad B    | `movilidad_viable = true`                | déficit cubrible internamente           | `TRANSFER` o `ROTATE`                 | Alta      |
| R4 Déficit apalancable híbrido    | `gap_hh > 0` y `hybrid_readiness_score >= 8`               | `expected_capacity_gain_pct >= umbral`   | `hybrid_mode=assisted/leveraged`        | `HYBRID_TALENT`                       | Alta      |
| R5 Déficit por rampa              | `time_to_productivity_months > horizonte_tolerable`        | `factor_rampa < umbral_rampa`            | capacidad no disponible a tiempo        | `CONTINGENT` transitorio + `RESKILL`  | Media     |
| R6 Exceso reutilizable            | `gap_hh < 0` y `convertibilidad_skill >= umbral`           | `surplus_fte = abs(gap_hh)/hh_netas_fte` | `gap_type=surplus_reusable`             | `TRANSFER` / `ROTATE`                 | Alta      |
| R7 Exceso no utilizable           | `gap_hh < 0` y `convertibilidad_skill < umbral`            | `surplus_no_convertible = true`          | `gap_type=surplus_non_reusable`         | reconversión dirigida (`RESKILL`)     | Media     |
| R8 Productividad baja estructural | `productivity_real < productivity_objetivo` por n periodos | `delta_prod = objetivo - real`           | déficit por productividad               | `AUTOMATE` + rediseño de proceso      | Media     |
| R9 Riesgo alto de continuidad     | `coverage_pct < umbral_min` y `risk_score > umbral_risk`   | semáforo rojo                            | riesgo operativo crítico                | plan mixto (`HIRE` + `HYBRID_TALENT`) | Alta      |
| R10 Cumplimiento restrictivo      | `compliance_review_required = true`                        | política/regla de negocio                | bloqueo de recomendación automática     | solo opciones con supervisión humana  | Alta      |

##### Campos mínimos de salida por recomendación

Cada recomendación emitida por el motor debería devolver:

- `scenario_id`, `unit_id`, `role_id`
- `gap_hh`, `gap_fte`, `coverage_pct`, `severity`
- `recommended_lever` (una de las palancas)
- `rationale` (explicación breve y auditable)
- `priority` (`high`/`medium`/`low`)
- `expected_impact` (capacidad/costo/tiempo)
- `owner_suggestion`
- `compliance_flags`

##### Orden sugerido de evaluación del motor

1. Validaciones de seguridad y compliance.
2. Cálculo de brecha cuantitativa (`gap_hh`, `gap_fte`).
3. Evaluación de brecha cualitativa (skills/disponibilidad/ubicación).
4. Evaluación de apalancamiento híbrido (`hybrid_readiness_score`).
5. Selección de palanca principal + palanca secundaria.
6. Asignación de prioridad y generación de racional.
7. Persistencia para trazabilidad y auditoría.

---

## 3) Fundamento técnico (arquitectura funcional)

### 3.1 Núcleo de datos

El módulo debe manejar, al menos, cinco objetos funcionales:

1. **Scenario** (escenario de planificación)
2. **DemandLine** (demanda por rol/unidad/mes)
3. **SupplySnapshot** (oferta interna/externa proyectada)
4. **GapLine** (brecha calculada)
5. **WorkforceActionPlan** (plan de acciones con recomendaciones y estado)

### 3.2 Capa de servicios

Servicios recomendados:

- `WorkforceDemandService`
- `WorkforceSupplyService`
- `WorkforceGapService`
- `WorkforceRecommendationService`
- `WorkforceExecutionTrackingService`

### 3.3 Principios técnicos clave

- **Multi-tenant estricto:** todo filtrado por `organization_id`.
- **Versionado de escenarios:** baseline + alternativas what-if.
- **Trazabilidad de decisiones:** cada recomendación debe tener racional técnico.
- **Contrato API estable:** separar cálculo (`analyze`) de ejecución (`actions`).
- **Estado de flujo claro:** `draft -> in_review -> approved -> active -> completed -> archived`.

---

## 4) ¿Para qué le sirve al sistema Stratos?

### 4.1 Valor de negocio

- Reduce decisiones reactivas de dotación.
- Disminuye riesgo de vacantes críticas.
- Mejora costo total laboral por decisiones comparables.
- Alinea headcount con estrategia real de crecimiento/transformación.

### 4.2 Valor producto

- Diferencia Stratos como plataforma de **planificación estratégica**, no solo HR operativo.
- Conecta módulos ya existentes (skills, analytics, scenario planning, learning).
- Permite roadmap enterprise (simulaciones, priorización y governance).

---

## 5) ¿Cómo se usa? (flujo operativo recomendado)

### Paso 1: Crear escenario

- Definir horizonte, supuestos, unidades y roles críticos.

### Paso 2: Cargar/validar demanda

- Capturar FTE objetivo y capacidades por periodo.

### Paso 3: Consolidar oferta

- Inventario interno (skills, readiness, movilidad).
- Restricciones de mercado externo y tiempo de contratación.

### Paso 4: Ejecutar análisis

- Calcular brechas por rol/unidad/tiempo.

### Paso 5: Generar recomendaciones

- Propuesta automática por brecha (`ROTATE/TRANSFER/HIRE/RESKILL/CONTINGENT/AUTOMATE`).

### Paso 6: Aprobar plan de acción

- Asignar owner, metas, fechas y presupuesto por acción.

### Paso 7: Monitorear ejecución

- Seguimiento mensual con KPIs y semáforo de riesgo.

---

## 6) KPIs mínimos del módulo

- `% brecha cerrada` por escenario y período.
- `lead time` de cobertura por tipo de acción.
- `% cobertura interna vs externa`.
- `% capacidad resuelta por talento híbrido/sintético`.
- `costo por brecha cerrada`.
- `% acciones en riesgo` (atraso/costo/capacidad).
- `forecast accuracy` (dotación planificada vs real).

---

## 7) Límites del módulo (para evitar solapamiento)

No debe asumir funciones primarias de Talent Management:

- evaluación de desempeño individual,
- plan de carrera personal,
- sucesión individual detallada,
- gestión de engagement.

Sí debe consumir esas señales como inputs agregados para planificación.

Sí puede incorporar **talento sintético/híbrido** como palanca de capacidad cuando:

- la tarea es delegable o asistible,
- existe supervisión humana definida,
- los riesgos de calidad/cumplimiento son aceptables,
- y el apalancamiento mejora costo, velocidad o cobertura sin degradar servicio.

---

## 8) Recomendación para bajar a implementación (orden sugerido)

### Fase A — Fundación (rápida)

1. Consolidar modelo de datos de `Scenario`, `DemandLine`, `GapLine`, `ActionPlan`.
2. Normalizar catálogos de acción (`ROTATE`, `TRANSFER`, `HIRE`, `RESKILL`, `CONTINGENT`, `AUTOMATE`).
3. Definir criterio base de elegibilidad para `HYBRID_TALENT`.
4. Endpoints base para `create/update/analyze` con tenant scoping estricto.

### Fase B — Inteligencia operativa

1. Motor de recomendación con reglas transparentes.
2. Racional explicable por recomendación (por qué se sugiere esa palanca).
3. Priorización por impacto-costo-tiempo-riesgo.
4. Regla de apalancamiento híbrido para tareas asistibles/delegables.

### Fase C — Ejecución y gobierno

1. Flujo de aprobación y activación del plan.
2. Seguimiento de acciones y hitos.
3. Dashboard de KPIs y alertas.
4. Seguimiento de capacidad cubierta por talento híbrido versus humano directo.

### Fase D — Escala enterprise

1. Comparación entre escenarios.
2. Simulaciones de sensibilidad (attrition, hiring freeze, crecimiento).
3. Integración más profunda con Learning/Marketplace/Sourcing.
4. Integración con motores de talento sintético y agentes operativos de Stratos.

---

## 9) Decisión recomendada para Stratos (resumen ejecutivo)

Mantener Workforce Planning como módulo independiente y posicionarlo como:

**"Motor de planificación dotacional estratégica"**

con tres outputs obligatorios:

1. **Brechas cuantificadas** (qué falta y dónde),
2. **Recomendaciones accionables** (qué hacer),
3. **Plan ejecutable con seguimiento** (quién, cuándo, con qué resultado).

Y un cuarto vector de diseño:

4. **Apalancamiento inteligente de capacidad** (qué parte resolver con talento humano, automatización o talento híbrido/sintético).

---

## 10) Checklist de adopción

- [ ] Modelo dotacional formalizado y separado de Talent Management.
- [ ] Catálogo único de palancas de acción habilitado.
- [ ] Criterio de elegibilidad para `HYBRID_TALENT` definido y auditado.
- [ ] Tenant-security aplicado en todos los endpoints.
- [ ] Flujo `draft -> approved -> active` operativo.
- [ ] KPIs mínimos visibles en dashboard.
- [ ] Proceso mensual de revisión de escenarios institucionalizado.

---

## 11) Uso recomendado por perfil

- **Dirección/Finanzas:** definir supuestos, aprobar trade-offs.
- **RRHH estratégico:** diseñar portafolio de acciones.
- **Líderes de unidad:** validar demanda y comprometer ejecución.
- **People Analytics:** monitorear precisión y recalibrar.
- **Operación/IA aplicada:** validar qué capacidad puede apalancarse con talento sintético/híbrido sin degradar control ni cumplimiento.

---

## 12) Próximo paso sugerido

Una vez validado este marco, el siguiente entregable debe ser un **Blueprint Técnico de Implementación** con:

- objetos de dominio definitivos,
- contratos API,
- reglas de recomendación v1,
- y matriz de pruebas (401/403/422/200 + aislamiento tenant).

---

## 13) Plan de implementación (funcionalidad nueva)

### 13.1 Alcance del release

Se define Workforce Planning como **nueva funcionalidad estratégica** con despliegue incremental en 4 fases.

**Resultado esperado del release v1:**

- Escenarios dotacionales operativos por organización.
- Cálculo de brechas trazable por rol/unidad/período.
- Recomendaciones accionables por brecha con racional.
- Plan de acciones con seguimiento y gobernanza.
- Regla explícita de apalancamiento con talento sintético/híbrido.

### 13.2 Fases, entregables y criterio de salida

#### Fase 1 — Foundation (2 semanas)

**Entregables:**

- Modelo de dominio v1 (`Scenario`, `DemandLine`, `SupplySnapshot`, `GapLine`, `WorkforceActionPlan`).
- Endpoints base (`create/update/list/analyze`) con scoping multi-tenant.
- Estados de flujo habilitados (`draft -> in_review -> approved -> active`).
- Catálogo inicial de elegibilidad para `HYBRID_TALENT`.

**Criterio de salida:**

- Contrato API estable documentado.
- Matriz de seguridad mínima cubierta (401/403/422/200).

#### Fase 2 — Intelligence (2 semanas)

**Entregables:**

- Motor de recomendación v1 (palancas `ROTATE/TRANSFER/HIRE/RESKILL/CONTINGENT/AUTOMATE/HYBRID_TALENT`).
- Racional explicable por recomendación.
- Priorización por impacto-costo-tiempo-riesgo.

**Criterio de salida:**

- 100% de brechas con recomendación y justificación.
- KPIs base calculables por escenario.

#### Fase 3 — Execution & Governance (2 semanas)

**Entregables:**

- Plan de acción por unidad con owner, fechas, presupuesto y estado.
- Flujo de aprobación operable (RRHH + negocio).
- Dashboard mínimo de ejecución y alertas.
- Indicador de capacidad cubierta por apalancamiento híbrido.

**Criterio de salida:**

- Seguimiento mensual habilitado con semáforo de riesgo.
- Trazabilidad end-to-end de decisiones.

#### Fase 4 — Scale (2-3 semanas)

**Entregables:**

- Comparador de escenarios.
- Simulaciones de sensibilidad (attrition, hiring freeze, crecimiento acelerado).
- Integración incremental con Learning/Marketplace/Sourcing.
- Integración incremental con capacidades de talento sintético/híbrido.

**Criterio de salida:**

- Recalibración de escenarios basada en forecast vs real.
- Reporte ejecutivo de trade-offs por escenario.

### 13.3 Riesgos y mitigación

- **Riesgo de calidad de datos:** definir contratos de dato y validaciones por fuente.
- **Riesgo de solapamiento con Talent Management:** mantener límites funcionales explícitos (sección 7).
- **Riesgo de seguridad multi-tenant:** aplicar patrón de tenant resolution uniforme y testear aislamiento cross-tenant.
- **Riesgo de baja adopción:** diseño con salida operativa clara (brecha -> recomendación -> plan ejecutable).

### 13.4 Métricas de éxito de implementación

- `% escenarios activos con revisión mensual completa`.
- `% brechas cerradas dentro de SLA`.
- `lead time promedio por tipo de palanca`.
- `% acciones con owner y fecha comprometida`.
- `forecast accuracy de dotación por unidad`.
- `% capacidad absorbida por modelo híbrido con SLA y calidad cumplidos`.

---

## 14) Especificación API v1 (propuesta implementable)

### 14.1 Convenciones de seguridad y autorización

- Base propuesta: `/api/strategic-planning/workforce-planning`.
- Todas las rutas protegidas por `auth:sanctum`.
- Control de permisos por acción (`workforce_planning.view|create|update|approve|execute`).
- Scoping estricto por organización en todos los reads/writes.
- Resolución de organización: `current_organization_id ?? organization_id`.

### 14.2 Semántica de códigos de respuesta

- `200/201/202`: operación exitosa.
- `401`: no autenticado.
- `403`: autenticado sin permiso para la acción.
- `404`: recurso inexistente o fuera de organización (aislamiento cross-tenant).
- `422`: validación inválida o `organization_id` no resoluble.

### 14.3 Contrato estándar de respuesta

Formato recomendado:

```json
{
    "success": true,
    "message": "Texto opcional",
    "data": {}
}
```

Errores:

```json
{
    "success": false,
    "message": "Descripción del error"
}
```

### 14.4 Endpoints núcleo (Foundation)

#### 1) Crear escenario dotacional

- `POST /api/strategic-planning/workforce-planning/scenarios`

Request:

```json
{
    "name": "Plan Comercial H2",
    "horizon_months": 12,
    "start_date": "2026-07-01",
    "assumptions": {
        "growth_pct": 18,
        "attrition_pct": 12,
        "productivity_target_pct": 105
    },
    "units": ["sales", "operations"]
}
```

Response `201`:

```json
{
    "success": true,
    "message": "Scenario created",
    "data": {
        "id": 101,
        "status": "draft",
        "organization_id": 7
    }
}
```

#### 2) Listar escenarios

- `GET /api/strategic-planning/workforce-planning/scenarios?status=draft&unit=sales`

Response `200`:

```json
{
    "success": true,
    "data": [
        {
            "id": 101,
            "name": "Plan Comercial H2",
            "status": "draft",
            "horizon_months": 12
        }
    ]
}
```

#### 3) Ver escenario

- `GET /api/strategic-planning/workforce-planning/scenarios/{id}`

Response `200` con datos del escenario + resumen de brechas/calidad de dato.

#### 4) Actualizar escenario

- `PATCH /api/strategic-planning/workforce-planning/scenarios/{id}`

Request parcial permitido para campos de negocio en estado editable (`draft`, `in_review`).

#### 5) Cargar demanda

- `POST /api/strategic-planning/workforce-planning/scenarios/{id}/demand-lines`

Request:

```json
{
    "lines": [
        {
            "unit": "sales",
            "role_id": 32,
            "period": "2026-09",
            "volume_expected": 12500,
            "time_standard_minutes": 18,
            "productivity_factor": 0.92,
            "coverage_target_pct": 95
        }
    ]
}
```

#### 6) Cargar oferta

- `POST /api/strategic-planning/workforce-planning/scenarios/{id}/supply-snapshots`

Request con disponibilidad interna/externa, HH netas y factores de rampa.

#### 7) Ejecutar cálculo dotacional

- `POST /api/strategic-planning/workforce-planning/scenarios/{id}/analyze`

Response `202`:

```json
{
    "success": true,
    "message": "Analysis completed",
    "data": {
        "scenario_id": 101,
        "summary": {
            "required_hh": 152000,
            "effective_hh": 131500,
            "coverage_pct": 86.5,
            "gap_hh": 20500,
            "gap_fte": 12.4
        }
    }
}
```

#### 8) Listar brechas

- `GET /api/strategic-planning/workforce-planning/scenarios/{id}/gaps`

Response incluye `gap_type`, severidad, dimensión (cuantitativa/cualitativa), riesgo y prioridad.

### 14.5 Endpoints de recomendación (Intelligence)

#### 9) Generar recomendaciones

- `POST /api/strategic-planning/workforce-planning/scenarios/{id}/recommendations/generate`

Response `200`:

```json
{
    "success": true,
    "data": [
        {
            "rule_id": "R4",
            "role_id": 32,
            "recommended_lever": "HYBRID_TALENT",
            "priority": "high",
            "rationale": "Gap crítico con alta elegibilidad híbrida",
            "expected_capacity_gain_pct": 22,
            "expected_time_to_effect_days": 30,
            "risk_level": "medium",
            "compliance_flags": []
        }
    ]
}
```

#### 10) Consultar recomendaciones

- `GET /api/strategic-planning/workforce-planning/scenarios/{id}/recommendations`

Filtros sugeridos: `priority`, `lever`, `unit`, `risk_level`.

### 14.6 Endpoints de ejecución y gobernanza

#### 11) Aprobar escenario

- `POST /api/strategic-planning/workforce-planning/scenarios/{id}/approve`

Permiso sugerido: `workforce_planning.approve`.

#### 12) Activar plan

- `POST /api/strategic-planning/workforce-planning/scenarios/{id}/activate`

Actualiza estado a `active` y congela baseline de métricas.

#### 13) Crear plan de acción

- `POST /api/strategic-planning/workforce-planning/scenarios/{id}/action-plans`

Request:

```json
{
    "recommendation_id": 889,
    "owner_user_id": 55,
    "target_date": "2026-10-15",
    "budget": 24000,
    "notes": "Pilot híbrido con supervisión semanal"
}
```

#### 14) Actualizar avance de acción

- `PATCH /api/strategic-planning/workforce-planning/action-plans/{id}`

Campos: `status`, `progress_pct`, `actual_cost`, `risk_level`, `evidence`.

#### 15) Dashboard de ejecución

- `GET /api/strategic-planning/workforce-planning/scenarios/{id}/execution-dashboard`

Devuelve KPIs de cierre de brecha, lead time, cobertura y apalancamiento híbrido.

### 14.7 Validaciones mínimas recomendadas

- `horizon_months`: `1..36`.
- `coverage_target_pct`: `1..200`.
- `productivity_factor`: `>0`.
- `time_standard_minutes`: `>0`.
- `owner_user_id` perteneciente a la misma organización.
- `HYBRID_TALENT` requiere `hybrid_readiness_score` y flags de compliance.

### 14.8 Casos de prueba contractuales mínimos

- `POST /scenarios` sin auth -> `401`.
- `POST /scenarios` sin permiso -> `403`.
- `POST /scenarios` con org no resoluble -> `422`.
- `GET /scenarios/{id}` de otra org -> `404`.
- `POST /analyze` payload inválido -> `422`.
- `POST /recommendations/generate` exitoso -> `200`.

Con esta especificación, la guía queda lista para traducirse a Form Requests, Controllers, Services y pruebas de contrato en Laravel/Pest.

---

## 15) Matriz de implementación Laravel (endpoint -> código)

Esta matriz aterriza la API v1 a artefactos concretos de Laravel 12, siguiendo el patrón del proyecto (controller fino + service + validación explícita + tenant scoping).

| Endpoint                                            | Form Request                                  | Controller@method                                     | Service method                                        | Permiso/Policy                        | Test recomendado                                      |
| --------------------------------------------------- | --------------------------------------------- | ----------------------------------------------------- | ----------------------------------------------------- | ------------------------------------- | ----------------------------------------------------- |
| `POST /.../scenarios`                               | `StoreWorkforceScenarioRequest`               | `WorkforcePlanningController@storeScenario`           | `WorkforcePlanningService::createScenario`            | `workforce_planning.create`           | `WorkforcePlanningScenarioAuthValidationTest::create` |
| `GET /.../scenarios`                                | — (query validation opcional)                 | `WorkforcePlanningController@listScenarios`           | `WorkforcePlanningService::listScenarios`             | `workforce_planning.view`             | `WorkforcePlanningScenarioAuthValidationTest::index`  |
| `GET /.../scenarios/{id}`                           | —                                             | `WorkforcePlanningController@showScenario`            | `WorkforcePlanningService::showScenario`              | `workforce_planning.view` + org scope | `WorkforcePlanningScenarioAuthValidationTest::show`   |
| `PATCH /.../scenarios/{id}`                         | `UpdateWorkforceScenarioRequest`              | `WorkforcePlanningController@updateScenario`          | `WorkforcePlanningService::updateScenario`            | `workforce_planning.update`           | `WorkforcePlanningScenarioAuthValidationTest::update` |
| `POST /.../scenarios/{id}/demand-lines`             | `StoreWorkforceDemandLinesRequest`            | `WorkforcePlanningController@storeDemandLines`        | `WorkforceDemandService::upsertDemandLines`           | `workforce_planning.update`           | `WorkforcePlanningDemandValidationTest`               |
| `POST /.../scenarios/{id}/supply-snapshots`         | `StoreWorkforceSupplySnapshotsRequest`        | `WorkforcePlanningController@storeSupplySnapshots`    | `WorkforceSupplyService::upsertSupplySnapshots`       | `workforce_planning.update`           | `WorkforcePlanningSupplyValidationTest`               |
| `POST /.../scenarios/{id}/analyze`                  | `AnalyzeWorkforceScenarioRequest` (si aplica) | `WorkforcePlanningController@analyzeScenario`         | `WorkforceGapService::analyzeScenario`                | `workforce_planning.execute`          | `WorkforcePlanningAnalysisAuthValidationTest`         |
| `GET /.../scenarios/{id}/gaps`                      | —                                             | `WorkforcePlanningController@listGaps`                | `WorkforceGapService::listGaps`                       | `workforce_planning.view`             | `WorkforcePlanningGapAccessTest`                      |
| `POST /.../scenarios/{id}/recommendations/generate` | `GenerateWorkforceRecommendationsRequest`     | `WorkforcePlanningController@generateRecommendations` | `WorkforceRecommendationService::generate`            | `workforce_planning.execute`          | `WorkforcePlanningRecommendationTest`                 |
| `GET /.../scenarios/{id}/recommendations`           | —                                             | `WorkforcePlanningController@listRecommendations`     | `WorkforceRecommendationService::list`                | `workforce_planning.view`             | `WorkforcePlanningRecommendationAccessTest`           |
| `POST /.../scenarios/{id}/approve`                  | `ApproveWorkforceScenarioRequest` (opcional)  | `WorkforcePlanningController@approveScenario`         | `WorkforceWorkflowService::approve`                   | `workforce_planning.approve`          | `WorkforcePlanningWorkflowTest::approve`              |
| `POST /.../scenarios/{id}/activate`                 | `ActivateWorkforceScenarioRequest` (opcional) | `WorkforcePlanningController@activateScenario`        | `WorkforceWorkflowService::activate`                  | `workforce_planning.execute`          | `WorkforcePlanningWorkflowTest::activate`             |
| `POST /.../scenarios/{id}/action-plans`             | `StoreWorkforceActionPlanRequest`             | `WorkforcePlanningController@storeActionPlan`         | `WorkforceExecutionTrackingService::createActionPlan` | `workforce_planning.execute`          | `WorkforcePlanningActionPlanTest::create`             |
| `PATCH /.../action-plans/{id}`                      | `UpdateWorkforceActionPlanRequest`            | `WorkforcePlanningController@updateActionPlan`        | `WorkforceExecutionTrackingService::updateActionPlan` | `workforce_planning.execute`          | `WorkforcePlanningActionPlanTest::update`             |
| `GET /.../scenarios/{id}/execution-dashboard`       | —                                             | `WorkforcePlanningController@executionDashboard`      | `WorkforceExecutionTrackingService::dashboard`        | `workforce_planning.view`             | `WorkforcePlanningDashboardAccessTest`                |

### 15.1 Artefactos backend a crear

#### Controllers

- `app/Http/Controllers/Api/WorkforcePlanningController.php`

#### Form Requests (sugeridos)

- `app/Http/Requests/Api/StoreWorkforceScenarioRequest.php`
- `app/Http/Requests/Api/UpdateWorkforceScenarioRequest.php`
- `app/Http/Requests/Api/StoreWorkforceDemandLinesRequest.php`
- `app/Http/Requests/Api/StoreWorkforceSupplySnapshotsRequest.php`
- `app/Http/Requests/Api/GenerateWorkforceRecommendationsRequest.php`
- `app/Http/Requests/Api/StoreWorkforceActionPlanRequest.php`
- `app/Http/Requests/Api/UpdateWorkforceActionPlanRequest.php`

#### Services

- `app/Services/WorkforcePlanningService.php` (ya existente, extender)
- `app/Services/WorkforceDemandService.php`
- `app/Services/WorkforceSupplyService.php`
- `app/Services/WorkforceGapService.php`
- `app/Services/WorkforceRecommendationService.php`
- `app/Services/WorkforceWorkflowService.php`
- `app/Services/WorkforceExecutionTrackingService.php`

#### Tests (Pest)

- `tests/Feature/WorkforcePlanningScenarioAuthValidationTest.php`
- `tests/Feature/WorkforcePlanningAnalysisAuthValidationTest.php`
- `tests/Feature/WorkforcePlanningRecommendationAuthValidationTest.php`
- `tests/Feature/WorkforcePlanningWorkflowAuthValidationTest.php`
- `tests/Feature/WorkforcePlanningActionPlanAuthValidationTest.php`

### 15.2 Orden de implementación sugerido (2 sprints)

#### Sprint 1 (Foundation + Security)

1. Rutas + `WorkforcePlanningController` (scaffold inicial).
2. CRUD básico de escenarios con tenant scoping.
3. Carga de demand/supply.
4. Tests de contrato 401/403/422/404/200.

#### Sprint 2 (Intelligence + Workflow)

1. `analyze` + cálculo `gap_hh/gap_fte/coverage_pct`.
2. `recommendations/generate` con reglas R1-R10.
3. `approve/activate` + action plans.
4. Dashboard de ejecución y KPI híbrido.

### 15.3 Reglas de implementación obligatorias

- Cada método debe resolver organización activa con `current_organization_id ?? organization_id`.
- `organization_id` no debe venir del cliente para operaciones sensibles.
- Recursos fuera de organización deben responder `404`.
- Si no se puede resolver organización, responder `422`.
- Controller fino; lógica de negocio en services.

Con esta matriz, el equipo puede pasar directamente a desarrollo en Laravel con trazabilidad endpoint-a-código y cobertura de pruebas desde el inicio.

---

## 16) Exposición del módulo: doble contexto en un solo motor

Este aspecto se implementa con una regla de producto clara:

- **un solo motor de cálculo y recomendación**,
- **dos contextos de visualización/uso**,
- **mismo contrato semántico de KPIs** para comparación consistente.

### 16.1 Objetivo de diseño

Permitir que Workforce Planning responda simultáneamente dos preguntas distintas:

1. **¿Cómo estamos hoy?** (baseline operativo actual).
2. **¿Cómo quedaríamos en un futuro posible?** (escenario prospectivo).

Evitar dos motores distintos reduce deuda técnica, mantiene trazabilidad y permite comparación directa entre presente y futuro.

### 16.2 Contextos funcionales

#### A) Contexto `baseline` (estado actual)

Se expone en el módulo dedicado `/workforce-planning` para diagnóstico operacional del presente:

- demanda y oferta actuales/proyectadas en ventana corta,
- cobertura real vs objetivo por unidad/rol,
- brechas inmediatas y acciones tácticas,
- riesgos de continuidad de servicio.

Uso principal: planificación dotacional continua mensual/trimestral.

#### B) Contexto `scenario` (estado futuro)

Se expone dentro de `/scenario-planning/{id}` como capa de impacto en talento del escenario estratégico:

- traducción de supuestos estratégicos a demanda futura,
- cálculo de brechas por capacidades críticas,
- evaluación de trade-offs (costo-tiempo-riesgo),
- priorización de palancas para la imagen objetivo.

Uso principal: decisiones de transformación y asignación estratégica de capacidad.

### 16.3 Mapa de exposición UX recomendado

#### Entrada 1: Workforce Planning (presente)

- Ruta web: `/workforce-planning`
- Página actual: `WorkforcePlanning/Index`
- Rol del módulo: **control tower dotacional actual**.

Componentes mínimos de esta vista:

- resumen de `coverage_pct`, `gap_hh`, `gap_fte`,
- tabla de brechas por unidad/rol,
- recomendaciones tácticas priorizadas,
- alertas de riesgo operativo y SLA.

#### Entrada 2: Scenario Planning (futuro)

- Rutas web: `/scenario-planning` y `/scenario-planning/{id}`
- Páginas actuales: `ScenarioPlanning/ScenarioList` y `ScenarioPlanning/ScenarioDetail`
- Rol del módulo: **simulador de impacto de escenarios**.

En `ScenarioDetail`, Workforce Planning debe aparecer como bloque/tab de análisis de capacidad futura:

- impacto en demanda de HH/FTE,
- brechas por capacidades críticas,
- recomendaciones estratégicas con racional,
- comparación contra baseline.

### 16.4 Contrato de contexto (clave para backend/frontend)

Se recomienda parametrizar todas las operaciones de análisis con:

- `planning_context`: `baseline | scenario`
- `scenario_id`: requerido cuando `planning_context=scenario`

Ejemplo conceptual de request:

```json
{
    "planning_context": "scenario",
    "scenario_id": 101,
    "horizon_months": 18
}
```

Reglas:

1. Si `planning_context=baseline`, no exigir `scenario_id`.
2. Si `planning_context=scenario`, validar existencia y pertenencia tenant de `scenario_id`.
3. Mantener mismas métricas base en ambos contextos para comparabilidad (`required_hh`, `effective_hh`, `coverage_pct`, `gap_hh`, `gap_fte`).

### 16.5 Comparador baseline vs escenario (salida obligatoria)

Además de calcular cada contexto por separado, el sistema debe exponer delta explícito:

- `delta_gap_hh = gap_hh_scenario - gap_hh_baseline`
- `delta_gap_fte = gap_fte_scenario - gap_fte_baseline`
- `delta_coverage_pct = coverage_pct_scenario - coverage_pct_baseline`
- `delta_cost_estimate`
- `delta_risk_level`

Esto habilita una lectura ejecutiva directa:

- qué brechas nuevas crea el escenario,
- qué brechas reduce,
- qué costo/riesgo incremental introduce,
- y cuál es la mejor combinación de palancas.

### 16.6 Reglas de decisión por contexto

Las reglas R1-R10 se aplican en ambos contextos, pero con foco distinto:

- En `baseline`: priorizar continuidad operativa y cierre rápido de brecha.
- En `scenario`: priorizar sostenibilidad estratégica y capacidad futura.

Por eso, el motor debe permitir ponderadores por contexto:

- `weight_speed`,
- `weight_cost`,
- `weight_risk`,
- `weight_capability_future`.

Configuración sugerida:

- `baseline`: mayor peso en `speed` y `risk`.
- `scenario`: mayor peso en `capability_future` y `cost_total_horizon`.

### 16.7 Ajustes API recomendados para este aspecto

Sobre la API v1 definida en la sección 14, agregar:

1. `GET /api/strategic-planning/workforce-planning/baseline/summary`
    - resumen operativo actual por organización.

2. `POST /api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline`
    - devuelve delta de brecha/cobertura/costo/riesgo entre escenario y baseline.

3. Soporte de `planning_context` en `POST /scenarios/{id}/analyze` (o endpoint equivalente de análisis contextual).

### 16.8 Seguridad y aislamiento tenant en doble exposición

En ambos contextos, mantener exactamente las mismas garantías:

- `auth:sanctum` obligatorio,
- permiso específico por acción,
- recursos fuera de organización -> `404`,
- organización no resoluble -> `422`.

No debe existir ninguna ruta de comparación que permita inferir datos de otro tenant por referencia indirecta de `scenario_id`.

### 16.9 Criterio de aceptación de producto

Este aspecto queda correctamente implementado cuando:

1. un usuario puede operar Workforce Planning desde `/workforce-planning` para estado actual,
2. el mismo usuario puede analizar impacto futuro desde `ScenarioDetail`,
3. ambos usan el mismo motor y mismas métricas nucleares,
4. existe comparador baseline vs escenario con deltas claros,
5. no hay divergencia de resultados atribuible a lógica duplicada.

### 16.10 Decisión final de arquitectura

Para Stratos, la decisión recomendada es:

- **exposición dual (Baseline + Scenario),**
- **motor único de Workforce Planning,**
- **contrato común de métricas y seguridad.**

Con esto, el módulo cubre tanto la gestión dotacional presente como la planificación estratégica futura, sin fragmentar la lógica analítica.

---

## 17) Estado implementado (actualización 2026-04-02)

Esta sección refleja el estado real ya implementado en código y validado por pruebas.

### 17.1 Backend operativo entregado

- Endpoints de contexto dual y comparación baseline activos en `api/strategic-planning/workforce-planning`:
    - `GET /workforce-planning/baseline/summary`
    - `POST /workforce-planning/scenarios/{id}/compare-baseline`
    - `POST /workforce-planning/scenarios/{id}/analyze` (con `planning_context=baseline|scenario`)
    - `POST /workforce-planning/scenarios/{id}/compare-baseline-impact`
- Demand Lines implementado end-to-end:
    - `GET /workforce-planning/scenarios/{id}/demand-lines`
    - `POST /workforce-planning/scenarios/{id}/demand-lines`
    - `PATCH /workforce-planning/scenarios/{id}/demand-lines/{lineId}`
    - `DELETE /workforce-planning/scenarios/{id}/demand-lines/{lineId}`
- Umbrales de semaforización implementados y persistidos por organización:
    - `GET /workforce-planning/thresholds`
    - `PATCH /workforce-planning/thresholds`
    - Persistencia en `organizations.workforce_thresholds` (JSON) con merge sobre defaults de `config/workforce_planning.php`.
- Action Plan + Execution Dashboard (P0) implementado en API:
    - `GET /workforce-planning/scenarios/{id}/action-plan`
    - `POST /workforce-planning/scenarios/{id}/action-plan`
    - `PATCH /workforce-planning/scenarios/{id}/action-plan/{actionId}`
    - `GET /workforce-planning/scenarios/{id}/execution-dashboard`
- Workflow de estado de escenario Workforce (P1 inicial):
    - `PATCH /workforce-planning/scenarios/{id}/status`
    - Transiciones permitidas: `draft -> in_review -> approved -> active -> completed -> archived`
    - Retrocesos controlados: `in_review -> draft`, `approved -> in_review`
- Monitoreo operativo Workforce (P1):
    - `GET /workforce-planning/monitoring/summary`
    - Entrega métricas de uso por tenant (`scenarios`, `demand_lines`, `action_plans`, writes y cambios de umbral en ventana) y salud operativa disponible (`latency_ms`, `error_rate_pct`) cuando existe telemetría agregada.

### 17.2 Reglas de negocio implementadas

- El motor (`WorkforcePlanningService`) prioriza cálculo desde `workforce_demand_lines` cuando existen líneas; si no existen, mantiene fallback a `kpis/assumptions`.
- Se mantiene comparabilidad baseline vs scenario en métricas núcleo (`required_hh`, `effective_hh`, `coverage_pct`, `gap_hh`, `gap_fte`) y deltas ejecutivos.
- Se conserva la decisión de arquitectura de este documento: **motor único + exposición dual**.
- Guardas por estado aplicadas para escrituras de ejecución Workforce:
    - Demand Lines (`POST/PATCH/DELETE`) y Action Plan (`POST/PATCH`) retornan `409` cuando el escenario está bloqueado por estado (ej. `completed` o `archived`).
- Auditoría mínima de umbrales Workforce implementada:
    - En `PATCH /workforce-planning/thresholds` se registra evento en `audit_logs` con `user_id`, `organization_id`, `changed_at`, y diff `before/after` de `workforce_thresholds`.

### 17.3 Seguridad y aislamiento tenant aplicados

- Scoping por `organization_id` en lectura/escritura de escenarios, líneas de demanda y umbrales.
- Recursos cross-tenant responden `404`.
- Organización no resoluble responde `422`.
- Protección de edición de umbrales por rol (`admin`, `hr_leader`) en backend (`PATCH`) y en frontend (modo solo lectura para otros roles).

### 17.4 Frontend entregado (Workforce Planning)

- Página `WorkforcePlanning/Index.vue` integrada con endpoints reales de baseline/análisis/impacto.
- Panel “Mis escenarios” con recarga de análisis.
- Gestión completa de Demand Lines en UI: alta en lote, listado, edición inline, eliminación y recálculo.
- Semaforización visual de cobertura/gap y deltas ejecutivos con tooltips.
- Filtros, ordenamiento y paginación para líneas persistidas.
- Persistencia local de estado de tabla (filtros/orden/página/tamaño) en `localStorage`.
- Feedback operativo en guardado de umbrales (éxito/error), bloqueo de doble submit y estado “Recalculando…”.
- Bloque MVP de Action Plan en `Index.vue`: alta de acciones, listado por escenario activo, edición de `status/priority/progress_pct/owner_user_id/due_date` y recálculo de dashboard de ejecución.
- Feedback visual inline para create/update de acciones (éxito/error) sin cambiar el sistema de diseño existente.
- Demand Lines con validación de campos en UI (errores por campo) y manejo consistente de fallos API con feedback inline en carga/guardado/edición/eliminación.

### 17.5 Validación ejecutada

- Suites foco Workforce en verde:
    - `tests/Feature/WorkforceEndToEndFlowTest.php`
    - `tests/Feature/WorkforceDemandLineApiTest.php`
    - `tests/Feature/WorkforcePlanningBaselineApiTest.php`
    - `tests/Feature/WorkforceActionPlanApiTest.php`
    - `tests/Feature/WorkforceScenarioStatusApiTest.php`
    - `tests/Feature/WorkforceMonitoringApiTest.php`
- Cobertura adicional de auditoría de umbrales validada en `WorkforcePlanningBaselineApiTest` (assert de `audit_logs` con actor y diff).
- Cobertura de integración end-to-end validada por API para el flujo: crear escenario -> cargar líneas -> analizar -> ajustar umbrales -> crear/actualizar acción -> transicionar estado -> monitorear.
- Nota técnica: existe `tests/Browser/WorkforcePlanningFlowTest.php` como placeholder skip mientras el runner browser del entorno no conserva sesión autenticada utilizable para `/workforce-planning`.
- Regresiones de permisos/estado agregadas:
    - transición de estado Workforce restringida por rol (`admin`, `hr_leader`),
    - bloqueo `409` de actualización en escenarios `completed` para Demand Lines y Action Plan.
- Pruebas de performance focal para alta volumetría de Demand Lines:
    - inserción por lote máximo (`50` líneas) y lectura/listado sobre escenario con alta carga,
    - verificación de umbrales de tiempo aceptables en entorno de prueba para detectar regresiones de rendimiento.
- Regresión completa del proyecto en verde al cierre:
    - `1070 passed`, `6 skipped`.

### 17.6 Nota de alcance

- Este avance implementa los pilares Foundation + Intelligence del módulo y parte del frente de Governance (umbrales y controles de edición).
- No se modificó la lógica estabilizada de `scenarios/{id}/step1` (diagrama de nodos), manteniendo el aislamiento solicitado.

## 18) Lista de pendientes (priorizada)

### P0 — Cierre funcional inmediato

- [x] Implementar MVP de **plan de acción** de Workforce (API + UI) con creación y actualización de avance/owner/fechas.
- [x] Publicar MVP de **dashboard de ejecución** por escenario activo.
- [x] Añadir validaciones de UX para Demand Lines (mensajería de error por campo y manejo consistente de fallos API en tabla persistida).

### P1 — Gobernanza y operación

- [x] Definir reglas de transición operacional completas (`in_review`, `approved`, `active`, `completed`, `archived`) para escenario Workforce y reforzar guardas por estado en escrituras de ejecución.
- [x] Incorporar auditoría mínima de cambios en configuración de umbrales (quién, cuándo, valor anterior/nuevo).
- [x] Estandarizar tablero de monitoreo operativo (latencia disponible, error rate disponible y uso de Workforce por tenant).

### P2 — Pruebas y calidad

- [x] Extender cobertura de integración para flujo completo: crear escenario -> cargar líneas -> analizar -> ajustar umbrales -> recalcular.
- [x] Añadir casos de regresión para reglas de estado y permisos por rol en endpoints de ejecución.
- [x] Incluir pruebas de performance focal para escenarios con alto volumen de Demand Lines.

### P3 — Escala y capacidad avanzada

- [x] Implementar sensibilidad/what-if operativo de Workforce (variaciones de productividad, cobertura y ramp factor con impacto en gap/costo).
- [x] Incorporar mayor parametrización de costos y riesgo para enriquecer `compare-baseline-impact`.
- [x] Preparar integración enterprise con tableros de planificación transversales (alineado a la fase Scale del plan del documento).

Avance implementado P3 (slice 1):

- Endpoint API: `POST /api/strategic-planning/workforce-planning/scenarios/{id}/operational-sensitivity`.
- Entrada validada (`adjustments`): `productivity_factor`, `coverage_target_pct`, `ramp_factor`, `cost_per_gap_hh`.
- Salida: snapshot `baseline`, snapshot `simulated` y `delta` con impacto en `gap_hh`, `gap_fte`, `coverage_pct` y `gap_cost_estimate`.
- Cobertura de pruebas: contrato de autenticación + caso funcional con variaciones combinadas de productividad/cobertura/rampa y cálculo de costo del gap.

Avance implementado P3 (slice 2):

- Endpoint enriquecido: `POST /api/strategic-planning/workforce-planning/scenarios/{id}/compare-baseline-impact`.
- Entrada opcional validada (`impact_parameters`):
    - `cost_per_gap_hh`
    - `cost_risk_multiplier`
    - `risk_base_offset`
    - `risk_weight_gap_pct`
    - `risk_weight_attrition_pct`
    - `risk_weight_ramp_gap`
- Resultado: costo y riesgo del escenario/baseline ahora pueden modelarse con componentes operativos (gap, attrition, ramp) y sensibilidad económica de costo por brecha/riesgo.
- Compatibilidad: sin `impact_parameters`, el endpoint conserva comportamiento previo (sin ruptura de contrato).

Avance implementado P3 (slice 3):

- Endpoint transversal enterprise: `GET /api/strategic-planning/workforce-planning/enterprise/summary`.
- Objetivo: consolidar señales ejecutivas de planificación en una vista unificada para dirección.
- Salida agregada por tenant:
    - `portfolio`: total de escenarios, escenarios activos/aprobados y escenarios en flujo de gobernanza.
    - `workforce_execution`: volumen de líneas, HH requeridas/efectivas, cobertura y avance del action plan.
    - `governance`: Change Sets totales, pendientes y aplicados.
    - `operational_health`: success rate, error rate y latencia promedio desde telemetría disponible.
- Cobertura de pruebas: contrato de autenticación + caso funcional con señales transversales de portfolio/ejecución/gobernanza/salud operativa.

## 19) Checklist de Go Live (Workforce Planning)

### 19.1 Release readiness (técnico)

- [x] Confirmar migraciones aplicadas en ambiente objetivo (incluye `workforce_demand_lines` y `organizations.workforce_thresholds`).
- [x] Verificar que rutas Workforce estén publicadas bajo prefijo canónico `api/strategic-planning`.
- [x] Confirmar variables/config de umbrales disponibles (`config/workforce_planning.php`) y valores por defecto esperados.
- [x] Ejecutar regresión mínima pre-release:
    - `tests/Feature/WorkforceDemandLineApiTest.php`
    - `tests/Feature/WorkforcePlanningBaselineApiTest.php`
    - `tests/Feature/WorkforceActionPlanApiTest.php`
    - `tests/Feature/WorkforceScenarioStatusApiTest.php`
    - `tests/Feature/WorkforceMonitoringApiTest.php`

#### Evidencia de ejecución (DEV, 2026-04-03)

- Migraciones verificadas con `php artisan migrate:status` (`Batch [1] Ran`, incluyendo migraciones Workforce).
- Rutas verificadas con `php artisan route:list --path=api/strategic-planning/workforce-planning` (`18` rutas publicadas bajo prefijo canónico).
- Regresión focal Workforce ejecutada en verde: `51 passed, 0 failed`.
- Resultado: sección **19.1** completada para ambiente DEV.

### 19.2 Seguridad y gobierno

- [x] Validar que edición de umbrales (`PATCH /workforce-planning/thresholds`) esté restringida a `admin` y `hr_leader`.
- [x] Validar scoping multi-tenant en endpoints críticos (lectura/escritura) con pruebas cross-tenant (`404`).
- [x] Confirmar guardas de estado (`409`) en Demand Lines y Action Plan para escenarios bloqueados (`completed`, `archived`).
- [x] Revisar que eventos de auditoría de umbrales se persistan en `audit_logs` con actor y diff `before/after`.

#### Evidencia de ejecución (DEV, 2026-04-03)

- Regresión focal Seguridad/Gobierno en verde: `42 passed, 0 failed` ejecutando:
    - `tests/Feature/WorkforcePlanningBaselineApiTest.php`
    - `tests/Feature/WorkforceDemandLineApiTest.php`
    - `tests/Feature/WorkforceActionPlanApiTest.php`
- Restricción de umbrales validada:
    - `forbids updating workforce thresholds for non-admin non-hr-leader roles` (`403`).
- Aislamiento tenant validado (`404`) en Demand Lines y Action Plan:
    - escenarios de otra organización no se listan, ni permiten store/update/delete.
- Guardas por estado validadas (`409`) para escrituras bloqueadas:
    - Demand Lines en `archived/completed`.
    - Action Plan en `archived/completed`.
- Auditoría de umbrales validada en `audit_logs`:
    - actor (`user_id`), acción (`updated`) y diff `before/after` de `workforce_thresholds`.
- Resultado: sección **19.2** completada para ambiente DEV.

### 19.3 Operación y observabilidad

- [x] Verificar disponibilidad del endpoint `GET /workforce-planning/monitoring/summary` en entorno objetivo.
- [x] Definir umbrales operativos internos para `error_rate_pct` y `latency_ms` (cuando telemetría esté disponible).
- [x] Establecer cadencia de revisión (semanal/mensual) de cobertura, gap HH/FTE y avance de action plan.
- [x] Definir owner operativo por tenant/área para manejo de incidentes del módulo.

#### Evidencia de ejecución (DEV, 2026-04-03)

- Endpoint de monitoreo publicado en routing:
    - `php artisan route:list --path=api/strategic-planning/workforce-planning/monitoring/summary` -> `Showing [1] routes`.
- Validación funcional de monitoreo en verde:
    - `tests/Feature/WorkforceMonitoringApiTest.php` -> `5 passed, 0 failed`.
- Umbrales operativos iniciales definidos para DEV (a calibrar en QA/PROD):
    - `error_rate_pct`: `warning >= 2.0`, `critical >= 5.0`.
    - `latency_ms`: `warning >= 1200`, `critical >= 2500`.
- Cadencia operativa definida:
    - revisión **semanal** de salud (`error_rate_pct`, `latency_ms`),
    - revisión **mensual** de ejecución (`coverage_pct`, `gap_hh/gap_fte`, avance de action plan).
- Owner operativo definido (DEV):
    - `HR Leader` del tenant como owner funcional,
    - `Backend Engineer on-call` como owner técnico de incidentes.
- Resultado: sección **19.3** completada para ambiente DEV.

### 19.4 Adopción funcional

- [x] Confirmar flujo completo en ambiente real con un escenario de negocio: crear escenario -> cargar líneas -> analizar -> ajustar umbrales -> crear/actualizar acciones -> transición de estado.
- [x] Validar uso del selector `planning_context` (`baseline|scenario`) por equipo de planificación.
- [x] Alinear criterios de interpretación ejecutiva de deltas (`gap_hh`, `coverage_pct`, `delta_cost_estimate`, `delta_risk_level`).
- [ ] Socializar guía rápida de operación para `talent_planner` y gobierno para `admin/hr_leader`.

#### Evidencia de ejecución (DEV, 2026-04-03)

- Flujo E2E validado en verde:
    - `tests/Feature/WorkforceEndToEndFlowTest.php` -> `1 passed, 0 failed`.
    - Cobertura del flujo: crear escenario -> cargar demanda -> analizar (`planning_context=scenario`) -> ajustar umbrales -> crear/actualizar action plan -> transición de estado (`draft -> in_review`) -> monitoreo.
- Validación de selector `planning_context` y comparativos baseline/scenario:
    - `tests/Feature/WorkforcePlanningBaselineApiTest.php` -> `17 passed, 0 failed`.
    - Casos validados: `planning_context=baseline`, `planning_context=scenario`, selección de `analyzed_scenario_id` correcta y respuesta consistente por contexto.
- Criterios operativos de interpretación de deltas documentados para DEV:
    - `gap_hh` positivo = déficit de capacidad; `gap_hh <= 0` = cobertura suficiente.
    - `coverage_pct` bajo objetivo = presión operativa; igual/superior objetivo = estabilidad.
    - `delta_cost_estimate` positivo = mayor costo vs baseline; negativo = ahorro estimado.
    - `delta_risk_level` (`higher|equal|lower`) = dirección del riesgo frente a baseline.
- Resultado: primeros 3 checks de **19.4** completados en DEV.
- Pendiente para QA/PROD: socialización formal con `talent_planner` y `admin/hr_leader`.

### 19.5 Criterio de salida (Done de producción)

Se considera Workforce Planning listo para producción cuando se cumplen simultáneamente:

1. Seguridad/tenant/governance validados (sin hallazgos críticos).
2. Flujo end-to-end ejecutado en entorno objetivo con datos reales de prueba.
3. Monitoreo operativo habilitado con responsables definidos.
4. Regresión focal Workforce en verde en la versión a desplegar.

#### Estado actual por ambiente (2026-04-03)

- **DEV:** cumplido para salida técnica controlada (**Go técnico condicional**), con pendiente funcional de socialización (19.4 último check).
- **QA:** pendiente de ejecución formal con stakeholders funcionales.
- **PROD:** pendiente, sujeto a cierre de QA + validación de socialización y acta final.

### 19.6 Ejecución por ambiente (Dev / QA / Prod)

#### Estado de ejecución real (corte DEV, 2026-04-03)

- **DEV:** `19.1`, `19.2`, `19.3` completos; `19.4` con `3/4` checks completos.
- **QA:** no iniciado.
- **PROD:** no iniciado.

#### DEV

| Ítem                                                           | Responsable sugerido         | Evidencia mínima                                                                       |
| -------------------------------------------------------------- | ---------------------------- | -------------------------------------------------------------------------------------- |
| Migraciones + rutas Workforce operativas                       | Backend Engineer             | Salida de migraciones aplicada + prueba rápida de `GET /workforce-planning/thresholds` |
| Flujo API E2E (escenario -> líneas -> análisis -> action plan) | Backend Engineer             | Capturas de respuestas `200/201` y validación de `409` en estado bloqueado             |
| UI integrada en `WorkforcePlanning/Index.vue`                  | Frontend Engineer            | Capturas de flujo completo en entorno local y sin errores de consola                   |
| Regresión focal Workforce                                      | QA/Engineer owner del cambio | Resultado en verde de las 5 suites focales de Workforce                                |

#### QA

| Ítem                                                  | Responsable sugerido            | Evidencia mínima                                                            |
| ----------------------------------------------------- | ------------------------------- | --------------------------------------------------------------------------- |
| Validación multi-tenant (`404` cross-tenant)          | QA Engineer                     | Casos documentados (usuario org A contra escenario org B)                   |
| Validación de roles en umbrales (`admin`/`hr_leader`) | QA Engineer + Security reviewer | Casos `200/403` para `PATCH /workforce-planning/thresholds`                 |
| Auditoría de cambios de umbrales                      | QA Engineer                     | Registro en `audit_logs` con `user_id`, `changed_at`, `before/after`        |
| Validación funcional con datos cercanos a negocio     | Product/HR Ops + QA             | Escenario de prueba firmado con resultados esperados (cobertura/gap/acción) |

#### PROD

| Ítem                                | Responsable sugerido               | Evidencia mínima                                                                       |
| ----------------------------------- | ---------------------------------- | -------------------------------------------------------------------------------------- |
| Despliegue + smoke checks Workforce | Release Manager + Backend Engineer | Checklist de smoke (`thresholds`, `baseline/summary`, `monitoring/summary`) completado |
| Monitoreo operativo habilitado      | SRE/Platform + owner funcional     | Dashboard/consulta de `error_rate_pct` y `latency_ms` disponible                       |
| Gobernanza operativa activa         | HR Leader / Admin tenant           | Owner por tenant definido + cadencia de revisión mensual acordada                      |
| Cierre de salida a producción       | Product Owner + Tech Lead          | Acta breve de Go Live con riesgos abiertos y plan de mitigación                        |

### 19.7 Plantilla breve de evidencia (para ticket de release)

Usar esta estructura en el ticket de despliegue/cambio:

- **Versión desplegada:**
- **Ambiente:** DEV / QA / PROD
- **Fecha/hora:**
- **Responsables:** Backend / Frontend / QA / Producto
- **Suites ejecutadas:** (adjuntar resultado)
- **Smoke endpoints:** (adjuntar status + payload resumido)
- **Validaciones de seguridad:** (roles, tenant, estados bloqueados)
- **Hallazgos abiertos:**
- **Decisión final:** Go / No-Go

#### Ejemplo completado (DEV, 2026-04-03)

- **Versión desplegada:** rama de integración Workforce Planning (pre-release).
- **Ambiente:** DEV
- **Fecha/hora:** 2026-04-03
- **Responsables:** Backend / QA (validación técnica)
- **Suites ejecutadas:**
    - `tests/Feature/WorkforceEndToEndFlowTest.php` -> `1 passed, 0 failed`
    - `tests/Feature/WorkforcePlanningBaselineApiTest.php` -> `17 passed, 0 failed`
    - `tests/Feature/WorkforceMonitoringApiTest.php` -> `5 passed, 0 failed`
- **Smoke endpoints:**
    - `GET|HEAD /api/strategic-planning/workforce-planning/thresholds` -> `Showing [2] routes`
    - `GET|HEAD /api/strategic-planning/workforce-planning/baseline/summary` -> `Showing [1] routes`
    - `GET|HEAD /api/strategic-planning/workforce-planning/monitoring/summary` -> `Showing [1] routes`
- **Validaciones de seguridad:**
    - roles de umbrales restringidos (`403` no autorizados),
    - aislamiento tenant (`404` cross-tenant),
    - guardas por estado (`409` en `completed/archived`).
- **Hallazgos abiertos:**
    - pendiente de socialización formal con `talent_planner` y `admin/hr_leader` para cierre total de adopción funcional.
- **Decisión final:** **Go técnico condicional (DEV)**.

### 19.9 Acta breve Go/No-Go (DEV)

**Decisión:** Go técnico condicional.

**Alcance validado:**

- Release readiness técnico, seguridad/gobierno y operación/observabilidad completos en DEV.
- Adopción funcional validada técnicamente en `3/4` checks (flujo E2E, `planning_context`, criterios de deltas).

**Riesgo abierto no bloqueante para DEV:**

- falta socialización formal con usuarios funcionales objetivo (`talent_planner`, `admin/hr_leader`).

**Condición para promover a QA/PROD:**

- cerrar socialización funcional y adjuntar evidencia de sesión (participantes, acuerdos y criterios operativos).

#### Plantilla rápida: convocatoria de socialización (copiar/pegar)

```text
Asunto: Socialización operativa Workforce Planning (cierre Go Live 19.4)

Objetivo:
Cerrar el último check funcional de Go Live: socialización de operación Workforce para `talent_planner` y `admin/hr_leader`.

Participantes requeridos:
- Talent Planner
- Admin / HR Leader
- Product Owner
- Backend/QA (soporte técnico)

Agenda (30 min):
1) Flujo operativo validado: escenario -> demanda -> análisis -> action plan -> monitoreo.
2) Interpretación de KPIs/deltas: `gap_hh`, `coverage_pct`, `delta_cost_estimate`, `delta_risk_level`.
3) Roles y gobierno: quién ajusta umbrales, quién aprueba cambios, escalamiento de incidentes.
4) Confirmación final de adopción para QA/PROD.

Entregables esperados:
- Aprobación funcional explícita (Go / ajustes requeridos).
- Lista de acuerdos operativos y responsables.
- Evidencia adjunta al ticket de release.
```

#### Plantilla rápida: minuta de cierre (evidencia para marcar 19.4)

```text
Minuta - Socialización operativa Workforce Planning

Fecha:
Ambiente: DEV
Participantes:

1) Resumen de la sesión
- Flujo revisado:
- KPIs/deltas revisados:
- Reglas de gobierno revisadas:

2) Acuerdos
- [ ] Talent Planner confirma uso operativo del flujo.
- [ ] Admin/HR Leader confirma gobierno de umbrales/roles.
- [ ] Criterios de interpretación de resultados quedan aceptados.

3) Riesgos o ajustes solicitados
-

4) Decisión
- [ ] Aprobado para avanzar a QA
- [ ] Requiere ajustes previos

5) Evidencia adjunta
- Captura/reporte de sesión:
- Link al ticket Go Live:
- Owner de seguimiento:
```

Con esta minuta completa, se puede marcar como cerrado el último check pendiente de **19.4 Adopción funcional**.

### 19.8 Plantillas copiable (GitHub Issue / Jira)

Plantilla reusable recomendada para operación en releases:

- `docs/templates/WORKFORCE_GO_LIVE_CHECKLIST.md`

#### A) Plantilla para GitHub Issue (Markdown)

```markdown
# Go Live Checklist - Workforce Planning

## Contexto

- Versión desplegada:
- Ambiente: DEV / QA / PROD
- Fecha/hora:
- Responsables: Backend / Frontend / QA / Producto

## Release readiness (técnico)

- [ ] Migraciones aplicadas (`workforce_demand_lines`, `organizations.workforce_thresholds`)
- [ ] Rutas Workforce activas bajo `api/strategic-planning`
- [ ] Config de umbrales validada (`config/workforce_planning.php`)
- [ ] Regresión focal Workforce en verde:
    - [ ] `WorkforceDemandLineApiTest`
    - [ ] `WorkforcePlanningBaselineApiTest`
    - [ ] `WorkforceActionPlanApiTest`
    - [ ] `WorkforceScenarioStatusApiTest`
    - [ ] `WorkforceMonitoringApiTest`

## Seguridad y gobierno

- [ ] `PATCH /workforce-planning/thresholds` restringido a `admin` / `hr_leader`
- [ ] Pruebas cross-tenant (`404`) validadas
- [ ] Guardas de estado (`409`) validadas para `completed` / `archived`
- [ ] Auditoría de umbrales registrada en `audit_logs` (`before/after`, actor, timestamp)

## Operación y observabilidad

- [ ] `GET /workforce-planning/monitoring/summary` operativo
- [ ] Umbrales operativos definidos para `error_rate_pct` y `latency_ms`
- [ ] Cadencia de revisión acordada (semanal/mensual)
- [ ] Owner operativo asignado por tenant/área

## Adopción funcional

- [ ] Flujo E2E validado con caso real de negocio
- [ ] Uso de `planning_context` (`baseline|scenario`) validado con equipo
- [ ] Criterios de interpretación de deltas alineados
- [ ] Guía operativa socializada a `talent_planner` y `admin/hr_leader`

## Evidencia adjunta

- Suites ejecutadas:
- Smoke endpoints (status + payload):
- Validaciones de seguridad:
- Hallazgos abiertos:

## Decisión

- [ ] Go
- [ ] No-Go
- Motivo / notas finales:
```

#### B) Plantilla para Jira (texto simple)

```text
Título: Go Live Checklist - Workforce Planning - [AMBIENTE] - [VERSIÓN]

Descripción:

Contexto
- Versión desplegada:
- Ambiente: DEV / QA / PROD
- Fecha/hora:
- Responsables: Backend / Frontend / QA / Producto

Release readiness (técnico)
- [ ] Migraciones aplicadas (workforce_demand_lines, organizations.workforce_thresholds)
- [ ] Rutas Workforce activas bajo api/strategic-planning
- [ ] Config de umbrales validada (config/workforce_planning.php)
- [ ] Regresión focal Workforce en verde:
            [ ] WorkforceDemandLineApiTest
            [ ] WorkforcePlanningBaselineApiTest
            [ ] WorkforceActionPlanApiTest
            [ ] WorkforceScenarioStatusApiTest
            [ ] WorkforceMonitoringApiTest

Seguridad y gobierno
- [ ] PATCH /workforce-planning/thresholds restringido a admin / hr_leader
- [ ] Pruebas cross-tenant (404) validadas
- [ ] Guardas de estado (409) validadas para completed / archived
- [ ] Auditoría de umbrales registrada en audit_logs (before/after, actor, timestamp)

Operación y observabilidad
- [ ] GET /workforce-planning/monitoring/summary operativo
- [ ] Umbrales operativos definidos para error_rate_pct y latency_ms
- [ ] Cadencia de revisión acordada (semanal/mensual)
- [ ] Owner operativo asignado por tenant/área

Adopción funcional
- [ ] Flujo E2E validado con caso real de negocio
- [ ] Uso de planning_context (baseline|scenario) validado con equipo
- [ ] Criterios de interpretación de deltas alineados
- [ ] Guía operativa socializada a talent_planner y admin/hr_leader

Evidencia adjunta
- Suites ejecutadas:
- Smoke endpoints (status + payload):
- Validaciones de seguridad:
- Hallazgos abiertos:

Decisión final
- [ ] Go
- [ ] No-Go
- Motivo / notas finales:
```
