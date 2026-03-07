# 📓 Documentación Técnica de Implementación: Fase 2 (Core Fundacional)

## 🎖️ Logros de Ingeniería

Se ha completado la integración del **Córtex de IA** con los flujos de "Talento" (Desempeño) y "Experiencia" (Clima). La plataforma ahora no solo reporta datos, sino que los **interpreta** y genera **predicciones** estratégicas.

---

## 🔧 1. Stratos 360: Motor de Triangulación Molecular

Transforma evaluaciones atómicas en perfiles de competencias calibrados por IA.

### Arquitectura de Datos

- **Entrada:** Evaluaciones 360 (Manager, Peers, Self) basadas en rúbricas BARS (comportamientos).
- **Servicio:** `Stratos360TriangulationService.php`.
- **Lógica IA:**
    - Cruza el promedio cuantitativo con los comentarios cualitativos.
    - Identifica sesgos (Ej: manager demasiado severo vs peers complacientes).
    - Devuelve un **Stratos Score** filtrado de anomalías.
    - **Jerarquía:** Agrupa automáticamente `Skills` (Átomos) en `Competency` (Moléculas).

### Dashboard de Resultados

- **Ruta:** `/talento360/triangulation/{id}`
- **UI:** Glassmorphism dashboard con drill-down interactivo. Permite ver la justificación de la IA para cada ajuste de nota.

---

## 📈 2. Stratos Px: People Experience & AI Alerta Temprana

Mide el pulso emocional y predice la fuga de talento antes de que ocurra.

### Componentes de Datos

- **Modelo:** `EmployeePulse` (Tabla: `employee_pulses`).
- **Métricas Clave:** eNPS (Lealtad), Nivel de Estrés, Engagement.
- **Prediccion de IA:** `TurnoverPredictorService.php`.
    - Recibe cada pulso y lo compara con el historial del empleado.
    - El LLM clasifica el riesgo de rotación: `low`, `medium`, `high`.
    - Genera un `ai_turnover_reason` explicativo para RRHH.

### Interfaces

- **Recolección:** `/people-experience` (Mobile-First wizard).
- **Comando HR:** `/people-experience/comando` (Monitor de riesgo y eNPS promedio).

---

## 📡 3. Stratos Radar: War-Gaming & Simulación Agéntica

Simulador de escenarios estratégicos que permite visualizar el impacto de cambios estructurales antes de ejecutarlos.

### Capacidades de Simulación

- **Modos de Operación**: Expansión Absoluta, Simbiosis de Equipos (Fusión), Shock Tecnológico y Optimización Estructural.
- **Capa Sintética (AI Agents)**: Propone el despliegue de agentes IA (Companion, DNA Extract, Recruitment Bots) para mitigar gaps de talento en tiempo real.
- **Intervenciones de Política/Org**: Genera planes de acción integrales que incluyen talleres de clima, mentoring inter-equipos y planes de retención individualizados.
- **Flash What-If**: Interfaz conversacional que permite realizar preguntas hipotéticas ad-hoc con análisis de probabilidad de éxito basado en el Digital Twin.

### Métricas de Viabilidad

- **Score de Viabilidad**: Un KPI compuesto que evalúa el riesgo vs. beneficio del escenario.
- **Impacto Residual**: Headcount Delta, Burn Rate proyectado y Tiempo de Estabilización.

---

## 🛡️ 4. Advanced Retention & Social Learning Engine

Nivel avanzado de resiliencia organizacional mediante predicción profunda y transferencia de conocimiento.

### Predictor de Retención Estratégico (`RetentionDeepPredictorService.php`)

- **Variables Holísticas**: Analiza el "Manager Health", eNPS del departamento y "Leadership Friction".
- **Impacto**: Calcula el `Business Continuity Risk` basándose en la escasez de las habilidades del colaborador en el mercado.
- **Acciones IA**: Sugiere planes de retención a tres niveles: Individual, Equipo y Organizacional.

### Social Learning & Knowledge Transfer (`SocialLearningService.php`)

- **Detección de Silos**: Identifica automáticamente a expertos críticos con alto riesgo de fuga.
- **Cross-Pollination matching**: Algoritmo que sugiere mentores de diferentes departamentos para romper silos de información.
- **AI Blueprints**: Genera programas de transferencia de conocimiento de 4 semanas, diseñados por IA para asegurar que el ADN crítico de la empresa permanezca incluso si el talento rota.

---

## 🚀 Próximos Pasos: FASE 3 (Acción Estratégica)

La plataforma pasará de la **Interpretación** a la **Acción Automática**.

1. **Stratos Grow (Navigator):**
    - Generación automática de planes de carrera basados en el GAP (Brecha) entre el perfil actual y el aspiracional.
    - Recomendación de mentores internos (usando los puntajes de Stratos 360).

2. **Stratos Match (Marketplace Interno):**
    - Recomendación de candidatos internos para vacantes abiertas usando el "Fitness Score" de IA.

3. **Stratos Magnet (Reclutamiento Externo):**
    - Extensión del buscador de talento al mercado externo (Career Portal).
