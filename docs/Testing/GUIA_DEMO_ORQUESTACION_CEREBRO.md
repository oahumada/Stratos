# 🧠 Guía de Demostración: Orquestación del Cerebro Stratos

Esta guía está diseñada para que un **Revisor Beta** o **Socio Potencial** pueda validar la inteligencia y capacidad de orquestación de Stratos de manera autónoma, replicable y transparente.

---

## 🎭 Escenario Maestro: "IA-First Transformation"

**Contexto:** La empresa "Global Fintech" necesita transformar su departamento de IT tradicional en una unidad impulsada por IA. No saben qué roles necesitan ni cómo equilibrar humanos frente a agentes autónomos.

### 📋 Ficha Técnica del Caso de Uso

- **Prompt Sugerido:** _"Necesitamos escalar nuestro departamento de ingeniería para soportar el desarrollo de 10 nuevos productos basados en LLM. Queremos un enfoque agresivo en talento sintético (IA) para tareas de monitoreo y soporte, pero manteniendo el liderazgo estratégico humano."_
- **Agentes Involucrados:** `Simulador Orgánico`, `Diseñador de Roles`, `Stratos Sentinel`.

---

## 🚀 Paso a Paso de la Verificación

### Paso 1: Incepción (El Disparador)

1.  Navegar a **Scenario Planning > Nuevo Escenario**.
2.  Ingresar el nombre: `Transformación IA 2026`.
3.  En el campo de descripción/instrucciones, pegar el **Prompt Sugerido** (ver arriba).
4.  **Click en: [Generar con IA (Cerebro)]**.
5.  **Qué observar:**
    - Fíjate en el widget de **Stratos Sentinel** (salud del sistema). Debería mostrar actividad.
    - Si hay una consola de logs activa, observar cómo los agentes "discuten" el plan.

### Paso 2: Observación de la Orquestación (Backstage)

Mientras el sistema procesa (30-60 seg), explica al tester:

- _"En este momento, el **Simulador Orgánico** está traduciendo lenguaje natural a un grafo de competencias."_
- _"El **Diseñador de Roles** aplica la metodología de 'Cubo de Roles' para decidir niveles de maestría (Y) y arquetipos (X)."_
- _"**Sentinel** está auditando en tiempo real que el diseño no tenga sesgos de género o exclusión técnica."_

### Paso 3: Análisis de Resultados (La Prueba de Verdad)

Una vez terminado, el tester debe validar en la pantalla de **Scenario Detail**:

1.  **Mapa de Capacidades:** ¿Se crearon nodos de "Large Language Models", "Ciberseguridad IA", etc.?
2.  **Métrica de Sintetización:** Ver el porcentaje de **IA vs Humano**.
    - _Verificación:_ ¿Es mayor el % sintético en roles operativos? (Ej. Support Engineer IA: 70% sintético).
3.  **Audit Trail:** Click en el icono de **Stratos Sentinel**. Debería aparecer un registro: _"Diseño verificado: Alineado con Manifiesto de Transparencia"_.

---

## 🛠️ Verificación de "Poder de Razonamiento" (Rainy Day)

Pide al tester que intente "romper" la lógica con un escenario contradictorio:

1.  **Nuevo Escenario:** `Escenario Imposible`.
2.  **Prompt:** _"Quiero un equipo de 50 Arquitectos Senior (Nivel Y5) con 0% de presupuesto y entrega en 1 semana."_
3.  **Resultado Esperado:** El Cerebro **NO** debe aceptar el plan sin advertencias. Debe generar un **Análisis de Riesgo Crítico** indicando: _"Inviabilidad financiera detectada"_ o _"Déficit de confianza en el timeline"_.
    - _Valor:_ Demuestra que no es un generador de texto, sino un motor de consultoría real.

---

## 📊 Criterios de Aceptación para el Socio

- [ ] **Latencia:** La respuesta llega en menos de 90 segundos.
- [ ] **Coherencia:** Los nombres de los roles suenan profesionales y modernos.
- [ ] **Explicabilidad:** El usuario entiende POR QUÉ se sugirió cada rol (justificación lógica).
- [ ] **Consistencia:** Si repito el mismo prompt, el diseño central se mantiene estable.

---

> [!TIP]
> **Pro-Tip para la Demo:** Muestra la base de datos Neo4j (si es posible) para que el socio vea cómo los nuevos roles se conectaron automáticamente a las competencias del catálogo global. Esto demuestra **Escalabilidad**.

### 1. El Wizard como "Alimentador de Contexto"

El Wizard de 5 pasos (GenerateWizard.vue) ya no intenta reemplazar al agente, sino que actúa como su fuente de datos estructurados.

Antes: El usuario escribía un bloque de texto libre.
Ahora: El Wizard recolecta industria, desafíos, capacidades, objetivos estratégicos, etc.
Integración: Estos campos se envían al backend (ScenarioGenerationController), donde el ScenarioGenerationService los ensambla usando plantillas Markdown dinámicas.

### 2. Los Agentes (Cerebro) como "Motor de Ejecución"

Una vez que el prompt está completo (Wizard + Plantilla), el sistema decide quién lo procesa:

- Si la configuración usa el proveedor intel (Cerebro), ese prompt gigante se envía al servicio de Python.
- Allí, el agente "Strategic Talent Architect" (definido en main.py) recibe esas instrucciones. Al ser un agente con "backstory" y "goals", no solo sigue el prompt, sino que aplica su "razonamiento" para asegurar que el JSON resultante sea coherente con un modelo de talento organizacional.

### 3. ¿Por qué es mejor así?

- Visibilidad (Glass Box): Al usar el Wizard, los agentes tienen datos de mejor calidad, lo que reduce las "alucinaciones" y permite que en la demo podamos mostrar cómo el agente tomó cada decisión basándose en los datos específicos que el usuario ingresó en cada paso.
- Fallback: Si por alguna razón el servicio de agentes (Python) está caído, el sistema puede redirigir ese mismo prompt enriquecido por el Wizard a un LLM directo (Abacus/OpenAI), manteniendo la funcionalidad básica.

---

## 🔍 Verificación Post-Simulación (Glass Box)

Para asegurar que el "Cerebro" ha razonado correctamente, podemos verificar los estados internos:

1.  **Validación de JSON**: Confirmar que el output de los agentes no se trunca (ajustado a 4096 tokens) y que el bloque markdown se limpia automáticamente.
2.  **Métricas RAGAS**: El sistema evalúa automáticamente la calidad del blueprint generado. Un Score > 0.8 en la base de datos indica alta fidelidad técnica.
3.  **Logs de Orquestación**: Verificar en `laravel.log` que el `Strategic Talent Architect` ha completado todas las secciones del contrato de datos.

## 🛠️ Solución de Problemas Comunes (Fixes Aplicados)

- **Truncado de Output**: Se incrementó `max_tokens` en el servicio Python para manejar blueprints complejos con múltiples roles y habilidades.
- **Parsing Errors**: Se implementó una lógica de limpieza de bloques ```json para asegurar compatibilidad con la base de datos.
- **Timeouts**: Se extendió el timeout de la comunicación Laravel <-> Python a 300 segundos.

### 📚 Recursos Adicionales

- **Mapeo de Datos (LLM a DB):** [DATA_IMPORT_AND_MODEL_MAPPING.md](file:///home/omar/Stratos/docs/Architecture/DATA_IMPORT_AND_MODEL_MAPPING.md)
- **Especificación Scenario IQ:** [SCENARIO_IQ_TECHNICAL_SPEC.md](file:///home/omar/Stratos/docs/Architecture/SCENARIO_IQ_TECHNICAL_SPEC.md)
