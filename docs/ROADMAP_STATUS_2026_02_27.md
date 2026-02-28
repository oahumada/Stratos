# 📊 Status Report: Roadmap & Wave 2

**Fecha:** 27 de Febrero, 2026
**Hora:** 13:38 CLT
**Contexto:** Informe detallado de progreso de la plataforma Stratos.

---

## 🧭 Roadmap Estratégico 2026

| Fase       | Título             | Estado  | Detalle                                      |
| :--------- | :----------------- | :-----: | :------------------------------------------- |
| **Fase 1** | Consolidación Core | ✅ 100% | FormSchema, unificación de tipos, multi-LLM. |
| **Fase 2** | Ciclo Metodológico | 🔄 85%  | Integración de Cubo AI en escenarios.        |
| **Fase 3** | Scenario IQ        | 🔄 40%  | Engine de simulación de riesgos.             |
| **Fase 4** | Talento 360        | ✅ 100% | IA Entrevistadora, BARS, Blind Spots.        |
| **Fase 5** | IA & Learning      | 🚀 60%  | MentorMatching, Learning Blueprints.         |

---

## 🌊 Wave 2: Plan de Implementación

### 🔵 Bloque A: Completitud Funcional

| #   | Feature                  |         Estado         | Detalle                                                                     |
| :-- | :----------------------- | :--------------------: | :-------------------------------------------------------------------------- |
| A1  | Módulo de Comando 360    |   ✅ **Finalizado**    | Backend configurado, Frontend `Comando.vue` con wizard y activación nativa. |
| A2  | Roles con Cubo Completo  |   ✅ **Finalizado**    | `RoleCubeWizard.vue` integrado en Matrix Step 2 + creación directa.         |
| A3  | Competencias Agénticas   |   ✅ **Finalizado**    | `AiOrchestratorService` genera competencias con BARS y las vincula.         |
| A4  | Criterios de Rendimiento |   ✅ **Finalizado**    | Skills incubadas generadas y enlazadas a la CompetencyVersion BARS.         |
| A5  | RBAC (Permisos)          |  ✅ **Implementado**   | Middleware + composable + sidebar reactivo + UI admin.                      |
| A6  | "Mi Stratos" Portal      | ✅ **v1 Implementada** | Dashboard premium con glassmorphism, KPIs, gaps, learning paths.            |

### 🟢 Bloque B: Visualización y Robustez

| #   | Feature       |    Estado     | Detalle                                                               |
| :-- | :------------ | :-----------: | :-------------------------------------------------------------------- |
| B1  | Neo4j Live    |   ✅ Listo    | Infraestructura Neo4j (Docker) y ETL operativos.                      |
| B3  | Investor Demo | ✅ Finalizado | Dashboard ejecutivo premium (ROI & Analytics).                        |
| B4  | API Hardening | ✅ Finalizado | Estandarización de respuestas, refactor de servicios y controladores. |

---

### 🟣 Bloque C: Inteligencia de Escala (Scenario IQ)

| #   | Feature          |    Estado    | Detalle                                       |
| :-- | :--------------- | :----------: | :-------------------------------------------- |
| C2  | Simulador Crisis | ⏳ Pendiente | Engine de simulación de riesgos (War-gaming). |
| C3  | Career Paths     | ⏳ Pendiente | Algoritmos de trayectoria en el grafo.        |

### 🟠 Bloque D: Movilidad y Ecosistema de Talento (Empoderamiento)

| #   | Feature               |    Estado     | Detalle                                                |
| :-- | :-------------------- | :-----------: | :----------------------------------------------------- |
| D1  | Gateway Híbrido       | 🔄 En diseño  | Portal Mobile + Acceso sin fricción (Magic Links).     |
| D2  | LMS & Mentor Hub      | ⏳ Pendiente  | Orquestador de aprendizaje: alertas de avance y citas. |
| D3  | Marketplace Activo    | ⏳ Pendiente  | Matchmaking predictivo (IA) para movilidad interna.    |
| D4  | Gamificación Creativa | ⏳ Pendiente  | Badges, niveles de maestría y "Quests" de aplicación.  |
| D5  | Misiones de Gremio    | ⏳ Pendiente  | Desafíos colectivos para cerrar brechas de equipo.     |
| D6  | Timeline Evolutivo    | ⏳ Pendiente  | Historial de ADN y trayectoria de crecimiento.         |
| D7  | Smart Notifications   | 🔄 Base lista | "Nudging" proactivo en Slack, Teams y Push.            |
| D8  | Talent Pass (CV 2.0)  | ⏳ Pendiente  | Portabilidad del ADN: perfil exportable/compartible.   |
| D9  | Sovereign Identity    | ⏳ Pendiente  | Credenciales Verificables (W3C) + Nodo Blockchain.     |
| D10 | Blindaje Octalysis    |   ✅ Diseño   | Aplicación de los 8 Core Drives de Yu-kai Chou.        |

## ✅ Logros de la Sesión (27-Feb-2026)

### 1. RBAC Completo (A5)

**Problema resuelto:** La plataforma no tenía control de acceso granular — cualquier usuario autenticado podía acceder a todas las funcionalidades (escenarios, agentes AI, configuración).

**Solución implementada:**

| Componente                | Archivo                                                        | Función                                                |
| ------------------------- | -------------------------------------------------------------- | ------------------------------------------------------ |
| **Trait RBAC**            | `app/Traits/HasSystemRole.php`                                 | `hasRole()`, `can()`, `hasPermission()`, cache 1h      |
| **Middleware Role**       | `app/Http/Middleware/CheckRole.php`                            | Protege rutas por rol del sistema                      |
| **Middleware Permission** | `app/Http/Middleware/CheckPermission.php`                      | Protege rutas por permiso granular                     |
| **Inertia Sharing**       | `app/Http/Middleware/HandleInertiaRequests.php`                | Comparte `role` + `permissions[]` al frontend          |
| **Composable Vue**        | `resources/js/composables/usePermissions.ts`                   | `can()`, `canModule()`, `hasRole()`, `isAtLeast()`     |
| **Sidebar Filtrado**      | `resources/js/components/AppSidebar.vue`                       | Items con `requiredPermission` / `requiredRole`        |
| **UI Admin**              | `resources/js/pages/settings/RBAC.vue`                         | Gestión visual de la matriz de permisos                |
| **Types**                 | `resources/js/types/index.d.ts`                                | `Auth` con `role`, `permissions[]`; `NavItem` con RBAC |
| **Migración**             | `database/migrations/2026_02_27_014700_create_rbac_tables.php` | Tablas `permissions` + `role_permissions`              |
| **Seeder**                | `database/seeders/RolePermissionSeeder.php`                    | 18 permisos, 45 mappings, 5 roles                      |
| **Controller**            | `app/Http/Controllers/Api/RBACController.php`                  | CRUD de permisos (admin-only)                          |
| **Registro**              | `bootstrap/app.php`                                            | Alias `role:` y `permission:`                          |

**Rutas protegidas:**

- API: Agents (`permission:agents.view/manage`), RBAC (`role:admin`), Assessment cycles (`permission:assessments.manage`)
- Web: Comando 360, Comando PX, Talent Agents (`role:admin,hr_leader`), Settings RBAC (`role:admin`)

### 2. Portal "Mi Stratos" v1 (A6)

**Problema resuelto:** Los colaboradores no tenían un punto de entrada personal a la plataforma — solo podían acceder a herramientas administrativas.

**Solución implementada:**

| Componente     | Archivo                                            | Función                                     |
| -------------- | -------------------------------------------------- | ------------------------------------------- |
| **Controller** | `app/Http/Controllers/Api/MiStratosController.php` | Agrega People + KPIs + gaps + learning      |
| **Página Vue** | `resources/js/pages/MiStratos/Index.vue`           | Portal premium con glassmorphism            |
| **Ruta Web**   | `routes/web.php`                                   | `/mi-stratos` (auth, verified)              |
| **Ruta API**   | `routes/api.php`                                   | `/api/mi-stratos/dashboard` (auth:sanctum)  |
| **Sidebar**    | `resources/js/components/AppSidebar.vue`           | "Mi Stratos" como primer item de navegación |

**Secciones implementadas (5 de 8):**

- ✅ Dashboard Personal (4 KPIs: Potencial, Readiness, Learning, Skills)
- ✅ Mi Rol (competencias agrupadas con progreso por skill)
- ✅ Mi Brecha (gap analysis visual con match % y gaps individuales)
- ✅ Mi Ruta (learning paths con % de avance y acciones completadas)
- ✅ Conversaciones (sesiones de evaluación/mentor/pulse activas)
- ✅ Mi ADN (perfil psicométrico integrado)
- ✅ Mis Logros (gamificación UI y badges)
- ⏳ Mis Evaluaciones (resultados 360 históricos)

**Diseño:**

- Dark mode premium: gradiente `#0f0c29 → #1a1a3e → #24243e`
- Glassmorphism: `backdrop-filter: blur(12px)`, bordes `rgba(255,255,255,0.08)`
- Micro-animaciones: hover scale, translateY, fade transitions
- Responsive: sidebar → tabs en mobile

### 3. Cubo de Roles y Competencias AI (A2, A3, A4)

**Problema resuelto:** Faltaba conectar el flujo de generación del diseño de roles (Role Cube) y llevar esos datos a metadatos complejos de competencias (niveles, anclajes BARS) de forma automatizada.

**Solución implementada:**

| Componente                | Archivo                                                     | Función                                                       |
| ------------------------- | ----------------------------------------------------------- | ------------------------------------------------------------- |
| **Integración Matrix**    | `RoleCompetencyMatrix.vue`                                  | Evento `@created` repuebla la matriz tras el Role Cube        |
| **Controlador**           | `Step2RoleCompetencyController.php`                         | Cambio de target a `Competency` (no `Skill`) para mapping     |
| **Generación BARS**       | `AiOrchestratorService` & `TalentDesignOrchestratorService` | Prompts refinados para emitir comportamientos y Skills        |
| **Transformación (Save)** | `TransformCompetencyController.php`                         | Lee requerimiento y guarda automáticamente `Skills` incubadas |
| **UI Ingeniería**         | `EngineeringBlueprintSheet.vue`                             | Permite edición fina antes de grabar permanentemente          |

**Flujo End-to-End validado:**
RoleCubeWizard -> Actualización de Matriz en tiempo real -> Clic en estado -> Transformación -> EngineeringBlueprintSheet (Generar AI) -> Confirmación -> Competencia versionada con Skills base vinculadas.

### 4. Integración Cerbero & BARS (A3/A4 completado)

**Problema resuelto:** El motor de IA (Cerbero) no estaba utilizando el contexto cuantitativo de BARS ni KPIs de desempeño en el análisis 360, lo que restaba precisión predictiva sobre los niveles de dominio.

**Solución implementada:**

| Componente               | Archivo                        | Función                                                                                                      |
| ------------------------ | ------------------------------ | ------------------------------------------------------------------------------------------------------------ |
| **Pydantic Schemas**     | `python_services/app/main.py`  | Ampliación de `FeedbackItem` y `ThreeSixtyAnalysisRequest` para recibir metadata BARS y JSON de Performance. |
| **Python Agent Prompts** | `python_services/app/main.py`  | Agentes Analyst y Predictor de Cerbero ahora correlacionan scores con niveles estratificados.                |
| **Curator Prompt**       | `CompetencyCuratorService.php` | Nomenclatura oficial de Stratos añadida (Ayuda, Aplica, Habilita, Asegura, Maestro).                         |

### 5. Corrección de Bug en RoleCompetencyMatrix

**Problema:** `fetchInitialData` no existía como método en `roleCompetencyStore`.
**Fix:** Renombrado a `loadScenarioData` en `handleRoleCreated()`.

### 6. Módulo Comando 360 (A1)

**Problema resuelto:** Faltaba completar la interfaz y la logística de frontend para lanzar y orquestar ciclos de evaluación a la medida con las configuraciones (instrumentos, scopes) requeridas.

**Solución implementada:**

| Componente                | Archivo                                         | Función                                                 |
| ------------------------- | ----------------------------------------------- | ------------------------------------------------------- |
| **Página Vue API**        | `Comando.vue`                                   | Vista principal de listado y wizard de nuevos ciclos.   |
| **Integración Endpoints** | `Comando.vue` & `AssessmentCycleController.php` | Conexión a `POST` (create cycle), `PUT` (activación).   |
| **Wizard Configuración**  | `Comando.vue`                                   | Configuración paso a paso con previsualización de data. |
| **Dashboard Action**      | `Comando.vue`                                   | Acción para revisar métricas de "Ciclos Activos".       |
| **Corrección Errores**    | múltiples (Vue, Vite)                           | Arreglo de scripts y tipos para compilación exitosa.    |

**Flujo End-to-End validado:**
Boton "Nuevo Ciclo" -> Wizard (nombre, alcance, instrumentos, resumen previsualizado) -> Guardar como draft -> Acción de interfaz nativa "Lanzar Oficialmente" -> Transición de draft a `active` -> Seguimiento Dashboard.

---

## 📚 Documentación Actualizada

| Documento                           | Contenido                                                               | Estado         |
| ----------------------------------- | ----------------------------------------------------------------------- | -------------- |
| `docs/Architecture/RBAC_SYSTEM.md`  | Sistema RBAC completo: trait, middleware, composable, permisos, sidebar | ✅ Reescrito   |
| `docs/WAVE_2_PLAN.md`               | Secciones A5 y A6 con arquitectura implementada y listado de archivos   | ✅ Actualizado |
| `docs/ROADMAP_ESTRATEGICO_2026.md`  | Tabla de status de Wave 2, nuevos hitos técnicos                        | ✅ Actualizado |
| `docs/ROADMAP_STATUS_2026_02_27.md` | Status report del día (este documento)                                  | ✅ Creado      |

---

## 🎯 Próximos Pasos Completados (A6 v2 Parcial)

1. **A6 v2: Secciones terminadas:**
    - Mi ADN (perfil psicométrico integrado, UI completado)
    - Mis Logros (badges y gamificación leve, UI completado)
    - Chatbot integrado (Mentor AI in-page float button)

2. **Pendiente Inmediato (Bloque B):**
    - **B3: Investor Demo:** Dashboard de alto impacto para C-Suite. ✅ **Completado.**
    - **B4: API Hardening:** Limpieza de controllers y estandarización Cerbero. ✅ **Finalizado.**

3. **Estrategia Bloque D: Talent Ecosystem & Mobility (Nueva Visión):**
    - **D1: Gateway Híbrido:** Mobile PX se convierte en la puerta de entrada para `People`. Acceso "one-click" vía Magic Links (sin contraseñas) o SSO corporativo.
    - **D2: LMS & Mentor Bridge (El Orquestador):** Stratos no es una biblioteca, es el **Cerebro**. Conecta la brecha de skills con el contenido exacto del LMS. Alerta sobre mentorías pendientes para asegurar que el ADN crezca. Proactividad: _"Recuerda que tienes el módulo 2 pendiente"_ o _"Tu cita con el mentor es en 2 días"_.
    - **D3: Marketplace & Mobility:** Salto de un tablero pasivo a un **Recomendador de Carrera Activo**. Matchmaking basado en IA que compite en inteligencia con la Intranet tradicional.
    - **D4: Gamificación Creativa (The Hero's Journey):** No solo medallas, sino un sistema de **"Niveles de Maestría"**. El paso de Aprendiz a Mentor se visualiza como un logro de prestigio que desbloquea nuevas funciones en el Marketplace.
    - **D5: Trayectoria Evolutiva (DNA Timeline):** Visualización del progreso histórico. El colaborador es dueño de su dato: _"Así era mi ADN en 2024, así he crecido en 2026"_.
    - **D6: Smart Notifications (Nudging):** Recordatorios proactivos: _"Llevas un 30% de avance en tu Learning Path"_, _"Tu racha de aprendizaje está en peligro"_.
    - **D7: Blindaje Octalysis:** Aplicación de los 8 Core Drives de Yu-kai Chou para asegurar la motivación intrínseca y extrínseca del colaborador.

---

## 💎 Visión Detallada: Stratos como Talent Experience Platform (TXP)

Este bloque transforma a Stratos en el punto de encuentro diario entre la estrategia de RRHH y la aspiración de crecimiento del individuo de forma **entretenida, creativa y proactiva**.

### 1. El Portal de la Persona (Gateway Híbrido & UX Premium)

No es un panel administrativo, es la **Identidad Digital del Talento**. Resolvemos el dilema de acceso para que nadie se quede fuera:

- **Acceso Híbrido**: Entrada "one-click" mediante **Magic Links** (enviados por email/Slack) para una entrada sin fricción ni contraseñas, o **SSO corporativo** para integración total.
- **Mobile First**: Interfaz optimizada para el colaborador en movimiento, con una UI de alto impacto (Glassmorphism) que invita a explorar su propio potencial.
- **DNA Timeline**: El colaborador es dueño de su dato histórico. Una visualización cronológica que permite comparar: _"¿Cómo era mi ADN en 2024 y cómo he evolucionado mi maestría en 2026?"_.
- **Sovereign Talent Pass (Blockchain & SSI)**: El perfil de ADN no es solo un PDF; son **Credenciales Verificables (W3C Standard)** firmadas digitalmente por Stratos.
    - **Identidad Soberana (DID)**: El colaborador es el único dueño de su "bóveda de talento" mediante su propio identificador descentralizado.
    - **Anclaje en Blockchain**: Registro inmutable de hitos de aprendizaje. Si el colaborador cambia de empresa, sus certificaciones viajan con él en su wallet digital, verificables instantáneamente por terceros sin intermediarios (Confianza Cero).
    - **Privacidad Selectiva**: El colaborador decide qué skills mostrar y a quién, protegiendo su historial detallado pero validando su maestría real.

### 2. LMS & Mentor Hub (El "Cerebro" Orquestador y Proactivo)

Stratos no es una biblioteca pasiva de cursos; es el motor que da sentido al aprendizaje mediante el **Nudging (Nivel de acompañamiento constante)**:

- **Alertas de Acción Real**: La plataforma envía recordatorios inteligentes con voz propia:
    - _"Recuerda que tienes el **módulo 2** pendiente en tu ruta de aprendizaje."_
    - _"La fecha para que te reúnas con tu **mentor** vence en 2 días."_
    - _"Llevas un **30% de avance** en tu Learning Path, ¡no te detengas ahora!"_.
- **Deep Linking**: El botón "Cerrar Brechas" dispara directamente al contenido específico dentro del LMS, eliminando la pérdida de tiempo en búsquedas.
- **Social Learning**: Gestiona la logística de mentoría uniendo a "Maestros" (Nivel 5) con "Aprendices", asegurando que el conocimiento fluya de forma orgánica.

### 3. Marketplace Predictivo vs. Intranet Pasiva

Stratos no compite con la intranet, la **potencia e integra** mediante un "LinkedIn Interno" de alta precisión:

- **Matchmaking Activo**: Mientras la intranet es un tablero estático de anuncios, Stratos es un **Recomendador de Carrera**. La IA le dice al colaborador: _"Basado en tu ADN y tus nuevas skills, eres un 90% match para este proyecto. ¿Te postulamos?"_.
- **Movilidad Sin Sesgos**: Las recomendaciones se basan en datos de competencias reales y potencial predictivo, democratizando el acceso a oportunidades de crecimiento.
- **Recompensas en el Marketplace**: El avance en el aprendizaje desbloquea de forma anticipada el acceso a vacantes estratégicas antes de que sean visibles para el resto de la organización.

### 4. Gamificación Creativa (The Hero's Journey & Collective Quests)

Transformamos el aprendizaje en un proceso **creativo, social y entretenido** para asegurar el engagement:

- **Mapa de Carrera Interactivo**: El progreso no es una lista, es un mapa que se "descubre" (Fog of War) a medida que el usuario gana XP y Skills.
- **Quests de Aplicación Proactiva**: Desafíos en el mundo real generados por IA: _"Aplica este principio de liderazgo y solicita feedback"_.
- **Misiones de Gremio (Squad Quests)**: Desafíos colectivos para equipos. Si el equipo cierra una brecha estratégica común, todos ganan recompensas. Esto fomenta que los expertos ayuden a los novatos (Core Drive 5).
- **Sistema de Prestigio**: Alcanzar el nivel "Maestro" desbloquea el estatus de **Mentor**, permitiendo apadrinar a otros.
- **Racha de Aprendizaje (Streaks)**: Gamificación del hábito diario ("Tu racha está en peligro") para fomentar la recurrencia.

### 5. El Cierre del Círculo Estratégico (Strategic Loop)

Stratos conecta el crecimiento del Individuo con la supervivencia de la Organización:

- **Impacto Real en el Negocio**: Cuando un colaborador mejora su ADN en una Skill crítica, el **KPI de Riesgo del Escenario Estratégico (Bloque C)** se reduce automáticamente. El CEO ve cómo el aprendizaje individual está blindando la empresa.
- **Visibilidad Ejecutiva**: El C-Suite visualiza en tiempo real cómo la inversión en el Bloque D reduce el riesgo detectado en las simulaciones de crisis.
- **Retorno de Aprendizaje (ROA)**: Visualización inmediata del valor táctico de cada "badge" ganado por los colaboradores.

### 6. Blindaje de Motivación: Framework Octalysis

Para asegurar que la gamificación no sea un parche temporal de "puntos y medallas", aplicamos los 8 Core Drives de Yu-kai Chou:

| Impulso (Core Drive)           | Aplicación en Stratos                                                                            |
| :----------------------------- | :----------------------------------------------------------------------------------------------- |
| **1. Significado Épico**       | El colaborador ve cómo su crecimiento impacta en la **supervivencia/innovación** de su empresa.  |
| **2. Desarrollo y Logro**      | **Niveles de Maestría** visuales y progreso tangible de Aprendiz a Mentor.                       |
| **3. Empoderamiento Creativo** | **IA-Quests**: Desafíos que requieren pensamiento crítico y resolución de problemas.             |
| **4. Propiedad**               | **DNA Timeline**: El sentimiento de que el perfil y sus logros son un **activo personal**.       |
| **5. Influencia Social**       | **Mentor Hub & Leaderboards**: Prestigio social y reconocimiento entre pares.                    |
| **6. Escasez**                 | **Acceso Exclusive**: Vacantes o proyectos que solo se desbloquean al alcanzar niveles altos.    |
| **7. Imprevisibilidad**        | **Fog of War**: Curiosidad por descubrir nuevas rutas y skills en el mapa interactivo.           |
| **8. Pérdida / Evitación**     | **Streaks**: Motivación para no perder la racha de aprendizaje o el estatus de _High Potential_. |

- **Motivación "White Hat" (Positiva):** Aprovechamos el Significado Épico, el Logro y el Empoderamiento Creativo para que el empleado crezca por convicción propia.
- **Motivación "Right Brain" (Intrínseca):** El Social Learning y la Curiosidad (Fog of War) aseguran que la plataforma sea adictivamente útil.
- **Moneda de Cambio Real:** Al usar la Escasez (acceso exclusivo a vacantes), el esfuerzo de aprendizaje se traduce en beneficios tangibles de carrera.

---

# Logros

_Este documento sirve como referencia para el estado del proyecto al cierre de la sesión del 27 de febrero de 2026._
