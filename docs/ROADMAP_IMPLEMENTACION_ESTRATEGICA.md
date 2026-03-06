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

## 🎯 FASE 2: Desarrollo de Módulos Fundacionales (Semanas 6-9)

_Los módulos que traen el flujo de datos real hacia el sistema._

### 1. Stratos 360 (El "Santo Grial" del Desempeño)

- **UI:** Interfaces de evaluación cruzada limpias, neutrales y rápidas.
- **Algoritmo de Triangulación de IA:** Desarrollar el servicio en Python/Laravel que neutralice las evaluaciones. Tomará como input: Puntuaciones del Manager, Puntuaciones de Pares, Evidencias/Logros anexados y KPIs del sistema para generar el "Verdadero Nivel de Skill".
- **Degradación Elegante:** Si la evaluación está incompleta, el sistema usa "fallbacks" lógicos para no detener los procesos del _Map_.

### 2. Stratos Px (People Experience y Temperatura)

- **Micro-Motor de Encuestas:** Sistema de lanzamiento recurrente minimalista.
- **Mobile First Px (Pendiente Wave 2):** Desarrollo prioritario de una interfaz UX/UI fluida y 100% móvil, bajando la fricción para que los empleados reporten su estado y clima en menos de 5 segundos desde sus teléfonos.
- **Alerta Temprana:** Dashboards predictivos de Riesgo de Rotación y Satisfacción, alimentando el mapa oculto de talento de la empresa.

---

## ⚡ FASE 3: Módulos de Acción (Semanas 10-14)

_La etapa donde la plataforma deja de ser pasiva y comienza a actuar automáticamente._

### 1. Stratos Grow / Navigator (Automatización de Aprendizaje)

- **Motor de Brechas (Gap Analysis Engine):** Script backend que cruza el `Rol Actual/Deseado` vs `Stratos Map/360 Actual` para generar el diferencial porcentual.
- **Generación de Rutas con IA:** Integración con LLMs (OpenAI/Anthropic) para que, basado en la brecha, sugiera un pensum automático (cursos, mentoría de Pares con skills altos, proyectos).
- **Fallback (Modo sin Stratos 360):** Permitir "Módulos de Autoevaluación" rápida para poder generar rutas incluso si la empresa no pagó el módulo 360.

### 2. Stratos Match y Ecosistema de Adopción (Liquid Talent Marketplace)

- **Algoritmo de Match:** Heurística de IA para ranquear y proponer el mejor talento interno de la nómina contra nuevas vacantes.
- **Talent Pass y Timeline Evolutivo (Pendientes Wave 2):** Consolidación del "CV 2.0" y diseño del _Timeline_, permitiendo visualizar gráficamente cómo ha mutado el ADN y el acervo de habilidades del empleado mes a mes.
- **Misiones de Gremio y Gamificación (Pendientes Wave 2):** Operativización técnica del marco de _Blindaje Octalysis_ (ya diseñado conceptualmente). Implementación de dinámicas grupales ("Guilds" o "Gremios") y Quests para impulsar la adopción del sistema social.

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
