# 🗺️ Roadmap de Implementación Estratégica: Visión "Core + Órbitas"

> Plan Maestro de Ingeniería para transformar la base actual de Stratos en una Plataforma de Orquestación de Talento Organizacional de Clase Mundial.

## 🎯 Objetivo General

Transformar el MVP actual (monolito) en una arquitectura **Modular por Feature Toggles** multitenant (Base de Datos centralizada, Módulos Activables). Esto preparará la plataforma para la venta escalonada a nivel _Enterprise_ asegurando aislamiento, degradación elegante y predictibilidad mediante IA.

---

## 🏗️ FASE 0: Refactorización Estructural (Semanas 1-2)

_Preparando los cimientos para que la arquitectura "Core + Órbitas" funcione sin deuda técnica._

### 1. Sistema de "Feature Toggles" (Tenant Modules)

- **Base de Datos:** Modificar la tabla principal de `tenants` (o `companies`) para incluir una columna `active_modules` (jsonb).
- **Backend (Laravel):** Crear Middleware y un Provider (`TenantModuleProvider`) para interceptar las peticiones y verificar si el tenant tiene activo el módulo respectivo.
- **Frontend (Vue 3):** Implementar en Pinia un `store/tenantStore.ts` que guarde los módulos activos. Renderizar Sidebar, Rutas (Vue Router) y Dashboards condicionalmente. Si el módulo no está activo, mostrar "Pantalla Candado" (Upselling).

### 2. Arquitectura de Eventos Interna (Low-Coupled Event Driven)

- Definir un bus de eventos estricto en Laravel.
  _Ejemplo:_ Cuando se actualiza un rol, emitir `App\Events\RoleRequirementsUpdated`, y no actualizar el perfil del usuario directamente. Los módulos que escuchen (como _Grow_ o _Radar_) reaccionarán en _Background Jobs_.
- Configurar colas de Laravel (`Redis` o `Database`) para procesamiento asíncrono.

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

## 🎯 FASE 2: Desarrollo de Módulos Fundacionales (Semanas 6-9)

_Los módulos que traen el flujo de datos real hacia el sistema._

### 1. Stratos 360 (El "Santo Grial" del Desempeño)

- **UI:** Interfaces de evaluación cruzada limpias, neutrales y rápidas.
- **Algoritmo de Triangulación de IA:** Desarrollar el servicio en Python/Laravel que neutralice las evaluaciones. Tomará como input: Puntuaciones del Manager, Puntuaciones de Pares, Evidencias/Logros anexados y KPIs del sistema para generar el "Verdadero Nivel de Skill".
- **Degradación Elegante:** Si la evaluación está incompleta, el sistema usa "fallbacks" lógicos para no detener los procesos del _Map_.

### 2. Stratos Px (People Experience y Temperatura)

- **Micro-Motor de Encuestas:** Sistema de lanzamiento de encuestas recurrentes y minimalistas vía Email/Plataforma.
- **Alerta Temprana:** Dashboards con mapas de calor de Clima, Riesgo de Rotación y Satisfacción, alimentando silenciosamente el perfil oculto del empleado.

---

## ⚡ FASE 3: Módulos de Acción (Semanas 10-14)

_La etapa donde la plataforma deja de ser pasiva y comienza a actuar automáticamente._

### 1. Stratos Grow / Navigator (Automatización de Aprendizaje)

- **Motor de Brechas (Gap Analysis Engine):** Script backend que cruza el `Rol Actual/Deseado` vs `Stratos Map/360 Actual` para generar el diferencial porcentual.
- **Generación de Rutas con IA:** Integración con LLMs (OpenAI/Anthropic) para que, basado en la brecha, sugiera un pensum automático (cursos, mentoría de Pares con skills altos, proyectos).
- **Fallback (Modo sin Stratos 360):** Permitir "Módulos de Autoevaluación" rápida para poder generar rutas incluso si la empresa no pagó el módulo 360.

### 2. Stratos Match (Liquid Talent Marketplace)

- **Lógica de Vacantes:** Interfaz para creación de Oportunidades / Proyectos internos.
- **Algoritmo de Fitness Match:** Servicio de Machine Learning/Heurística (Cosine Similarity entre Skills Requeridos y Skills del Perfil) para ranquear y proponer el talento interno ideal libre de sesgos.

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
