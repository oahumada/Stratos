# 🌊 Wave 3: Roadmap de Implementación Estratégica ("Core + Órbitas")

> Plan Maestro de Ingeniería para transformar la base MVP y los bloques de las Waves previas en una Plataforma de Orquestación de Talento Organizacional de Clase Mundial.

## 🎯 Objetivo General

Transformar el MVP actual (monolito) en una arquitectura **Modular por Feature Toggles** multitenant (Base de Datos centralizada, Módulos Activables). Esto preparará la plataforma para la venta escalonada a nivel _Enterprise_ asegurando aislamiento, degradación elegante y predictibilidad mediante IA.

---

## 🏗️ FASE 0: Refactorización Estructural y Seguridad (Semanas 1-2)

_Preparando los cimientos para que la arquitectura "Core + Órbitas" funcione sin deuda técnica_ **(Incluye integraciones rezagadas de Wave 2)**.

### 1. Sistema de "Feature Toggles" (Tenant Modules)

- **Base de Datos:** Modificar la tabla principal de `tenants` para incluir una columna `active_modules` (jsonb).
- **Backend (Laravel):** Crear Middleware y un Provider (`TenantModuleProvider`) para interceptar las peticiones y verificar restricciones.
- **Frontend (Vue 3):** Store en Pinia `tenantStore.ts` y renderizado de módulos condicional (UI Candado/Upselling).

### 2. Arquitectura de Eventos Interna

- Definir bus de eventos estricto en Laravel (`App\Events\RoleRequirementsUpdated`).
- Configurar colas (`Redis`/`Database`) para procesamiento asíncrono.

### 3. Gateway Híbrido de Acceso (Pendiente Wave 2)

- Finalizar la capa de seguridad integrando SSO (OAuth2 / SAML) para despliegues _Enterprise_, unificando con los Magic Links existentes.
- Sentar las bases conceptuales y modelos de control para **Sovereign Identity** (Credenciales verificables W3C / Blockchain).

---

## 🪐 FASE 1: Consolidación de "Stratos Core" y "Stratos Map" (Semanas 3-5)

_El corazón del sistema. Tiene que funcionar impecable para poder vender el producto base._

### 1. Stratos Core (El Fundamento)

- **Diccionarios Dinámicos:** Finalizar el CRUD robusto y jerárquico del Catálogo Universal de Roles y el Diccionario de Skills de la Empresa.
- **Gestión Jerárquica:** Organigrama interactivo y gestión robusta de permisos y perfiles (RBAC transversal).

### 2. Stratos Map (La Radiografía)

- **Motor de Renderizado:** Construcción de UI (Vue 3 + Gráficos Echarts/D3) para visualizar la "Temperatura Organizacional de Skills" por área, equipo y rol.
- **Algoritmo de Mapeo Base:** Cálculo en tiempo real de cobertura de conocimientos y capacidades transversales de negocio.

---

## ✅ FASE 2: Desarrollo de Módulos Fundacionales (COMPLETADA)

_Los módulos que traen el flujo de datos real hacia el sistema._

### 1. Stratos 360 (El "Santo Grial" del Desempeño) - [x] ✅

- **UI:** Interfaces de evaluación cruzada limpias, neutrales y rápidas.
- **Algoritmo de Triangulación de IA:** Desarrollado un motor LLM robusto que traduce _Skill Atoms_ (rúbricas) en _Competency Molecules_ (agrupaciones), neutralizando sesgos de severidad y complacencia.
- **Degradación Elegante:** Sistema con fallbacks lógicos para reportes incompletos.

### 2. Stratos Px (People Experience y Temperatura) - [x] ✅

- **Micro-Motor de Encuestas (Pulse Engine):** Sistema de lanzamiento recurrente minimalista (eNPS, Stress, Engagement).
- **Mobile First Px:** Interfaz UX fluida y 100% móvil integrada (`/people-experience`).
- **Alerta Temprana (Predictive Turnover):** Dashboard para el CHRO (`/people-experience/comando`) con predicción de riesgo de renuncia mediante IA, cruzando sentimientos cualitativos y métricas cuantitativas.

---

## ✅ FASE 3: Módulos de Acción (COMPLETADA)

_La etapa donde la plataforma deja de ser pasiva y comienza a actuar automáticamente._

### 1. Stratos Grow / Navigator (Automatización de Aprendizaje) - [x] ✅

- **Motor de Brechas (Gap Analysis Engine):** Script backend que cruza el `Rol Actual/Deseado` vs `Stratos Map/360 Actual`.
- **Generación de Rutas con IA:** Integración con `AiDevelopmentNavigatorService` para generar planes 70-20-10 con mentores internos.
- **Visualización Premium:** Interfaz `StratosNavigator.vue` con timeline interactivo.

### 2. Stratos Match (Liquid Talent Marketplace) - [x] ✅

- **Algoritmo de Match DNA:** Heurística de IA (`AiInternalMatchmakerService`) que cruza Gap Analysis, Triangulación 360 y Growth Velocity para ranquear candidatos internos.
- **Insights de Resonancia:** Reportes automatizados de Fit, Riesgos y Planes de Mitigación para el reclutador.

---

## ⚡ FASE 4: Predicción y Escalamiento (En progreso)

### 1. Stratos Magnet (Reclutamiento y Adquisición Abierta) - [x] ✅

- **Reclutamiento Espejo:** Portal público `/career/{tenant}` operativo.
- **Candidate Experience "Stratos Glass":** UI premium implementada en `PublicPortal.vue`.
- **Integración con Matchmaker:** Candidatos externos capturados listos para ser procesados por el motor de IA.

---

## 🔮 FASE 4: Módulos Predictivos y "Talento Sintético" (Semanas 15-20)

_Posicionamiento como el principal "Arquitecto del Negocio". El Premium Tier._

### 1. Stratos Insights (Psicometría y Fit)

- Implementación de Tests estandarizados u orientados a IA conversacional para analizar comportamiento y soft-skills, agregando ponderadores al motor de "Fitness Match".

### 2. Stratos Radar / Evolve (La Joya de la Corona)

- **Data Lake e Historial Inmutable:** Configurar Event Sourcing básico; cada 30 días se guarda una "Foto" entera del estado de la empresa.
- **Simulador de Escenarios UI:** Interfaz donde el CFO/CEO carga variables: "T + 2 años, abrir en mercado B". El servidor calcula brechas.
- **Triangulación Híbrida (Humano + Sintético):** Definir una estructura en Base de Datos para "Agentes Sintéticos / IAs". Al predecir vacantes, el sistema propone cubrir un 40% de la carga operativa con Agentes IA y un 60% de estrategia con un Reskilling Humano, calculando el "KPI de Apalancamiento".
- **Cálculo del Stratos IQ:** Macro-KPI derivado de la tasa histórica de velocidad con la que la organización ha sobrepasado los "Gaps" (Velocidad de Aprendizaje Organizacional).

---

## 🛠️ Normas de Ingeniería "Clase Mundial" (Aplicación Inmediata)

Para que este plan no fracase por mala calidad técnica:

1. **Tests Obligatorios (TDD pragmático):** Pest/PHPUnit en backend para la matemática de los skills (Gaps). Cypress/Vitest en Vue para los flujos de evaluación.
2. **Pipelines Rigurosos (CI/CD):** Validación de commits semánticos, linting, tests y compilación en cada Push (GitHub Actions).
3. **UI/UX Obsesiva:** Implementación de micro-interacciones (Vuetify + Motion) y tono de voz (ChatBot/Copywriting) de "Copiloto Estratégico", eliminando por completo el lenguaje punitivo tradicional de Recursos Humanos.
