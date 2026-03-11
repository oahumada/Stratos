# 🌊 Wave 3: Roadmap de Implementación Estratégica ("Core + Órbitas")

> Plan Maestro de Ingeniería para transformar la base MVP y los bloques de las Waves previas en una Plataforma de Orquestación de Talento Organizacional de Clase Mundial.

## 🎯 Objetivo General

Transformar el MVP actual (monolito) en una arquitectura **Modular por Feature Toggles** multitenant (Base de Datos centralizada, Módulos Activables). Esto preparará la plataforma para la venta escalonada a nivel _Enterprise_ asegurando aislamiento, degradación elegante y predictibilidad mediante IA.

---

## 🏗️ FASE 0: Refactorización Estructural y Seguridad (Semanas 1-2)

_Preparando los cimientos para que la arquitectura "Core + Órbitas" funcione sin deuda técnica_

### 1. Sistema de "Feature Toggles" (Tenant Modules)

- **Base de Datos:** Modificar la tabla principal de `tenants` para incluir una columna `active_modules` (jsonb).
- **Backend (Laravel):** Crear Middleware y un Provider (`TenantModuleProvider`) para interceptar las peticiones y verificar restricciones.
- **Frontend (Vue 3):** Store en Pinia `tenantStore.ts` y renderizado de módulos condicional (UI Candado/Upselling).

### 2. Arquitectura de Eventos Interna

- Definir bus de eventos estricto en Laravel (`App\Events\RoleRequirementsUpdated`).
- Configurar colas (`Redis`/`Database`) para procesamiento asíncrono.

### 3. Gateway Híbrido de Acceso (✅ Parcialmente Completado)

- La capa de seguridad integrando SSO (OAuth2 Google/Microsoft) ha sido completada en `SsoController`, unificando con Magic Links.
- Las bases de datos y la API (`TalentPassController`) para **Sovereign Identity** (Credenciales verificables y Talent Pass emulado localmente) han sido implementadas exitosamente, sentando el terreno para la integración W3C futura.

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

## 🔮 FASE 4: Módulos Predictivos y "Talento Sintético" (Semanas 15-20)

_Posicionamiento como el principal "Arquitecto del Negocio". El Premium Tier._

### 1. Stratos Insights (Psicometría y Fit)

- Implementación de Tests estandarizados u orientados a IA conversacional para analizar comportamiento y soft-skills, agregando ponderadores al motor de "Fitness Match".

### 2. Stratos Radar / Evolve (La Joya de la Corona)

- **Simulador de Escenarios UI:** Completado bajo el motor de `ScenarioIQ` (Proyecciones de attrition, obsolescencia, y reestructuración).
- **Triangulación Híbrida (Humano + Sintético):** Cubierto mediante las simulaciones agénticas de la plataforma (`AgenticScenarioService`).
- **Data Lake e Historial Inmutable:** Configurado con Event Sourcing nativo apoyado en `OrganizationSnapshot` (fotos mensuales automatizadas).
- **Cálculo del Stratos IQ:** Macro-KPI en tiempo real de Velocidad de Aprendizaje Organizacional disponible y en formato API (Trends).

---

## 🛠️ Normas de Ingeniería "Clase Mundial" (Aplicación Inmediata)

Para que este plan no fracase por mala calidad técnica:

1. **Tests Obligatorios (TDD pragmático):** Pest/PHPUnit en backend para la matemática de los skills (Gaps). Cypress/Vitest en Vue para los flujos de evaluación.
2. **Pipelines Rigurosos (CI/CD):** Validación de commits semánticos, linting, tests y compilación en cada Push (GitHub Actions).
3. **UI/UX Obsesiva:** Implementación de micro-interacciones (Vuetify + Motion) y tono de voz (ChatBot/Copywriting) de "Copiloto Estratégico", eliminando por completo el lenguaje punitivo tradicional de Recursos Humanos.
