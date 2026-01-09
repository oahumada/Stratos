Perfecto. Ahora vamos a integrar el módulo de Planificación Dotacional como una función clave de Strato, actualizando las memorias del proyecto (prompt de contexto) y definiendo su lugar en la arquitectura funcional y técnica.

✅ Actualización de las memorias del proyecto Strato
🔧 Nueva versión del prompt de contexto (memorias actualizadas)
# PROMPT DE CONTINUACIÓN – PROYECTO Strato

Soy psicólogo organizacional y programador, desarrollando **Strato**: una plataforma SaaS + consultoría para gestión estratégica de talento basada en skills, IA y credenciales verificables (SSI).

## 🎯 Propuesta de valor

Software + consultoría integrados para:
- Mapear talento por skills.
- Identificar brechas de talento y skills.
- Diseñar rutas de desarrollo con e-learning.
- Tomar decisiones de selección (interno vs externo).
- Habilitar marketplace interno de talento.
- Visualizar dashboards estratégicos de talento.

## 🧩 Módulos funcionales definidos

1. Administración y seguridad
2. Modelo de talento (skills, roles, rutas)
3. Peopleas y perfiles de talento
4. Evaluación de skills y brechas
5. Rutas de desarrollo y formación e-learning
6. Selección por skills (interno y externo)
7. Marketplace interno de talento
8. Desempeño integrado (versión ligera)
9. Analítica y dashboards
10. IA y recomendaciones
11. Credenciales y pasaporte de skills (SSI – roadmap)
12. Soporte al proceso de consultoría
13. **Planificación Dotacional Estratégica** *(Nuevo)*

## 🏗️ Arquitectura técnica

- Multi-tenant (aislamiento por `organization_id`).
- Backend: Laravel + PostgreSQL.
- Frontend: Vuejs3 + TypeScript + Vuetify.
- IA: OpenAI API o sentence-transformers para inferencia de skills.
- Deploy: DigitalOcean (Droplet) + Docker.
- Futuro: integración con SSI (Hyperledger Aries, Verifiable Credentials).

## 🛣️ Roadmap actual: MVP en 2 semanas

### Semana 1:
- Setup, modelo de datos, perfiles de empleados.
- Cálculo de brechas peoplea ↔ rol.
- Rutas de desarrollo sugeridas.

### Semana 2:
- Selección (interno vs externo).
- Marketplace interno.
- Dashboard estratégico.
- Pulido y guion de demo.

## 🧠 Funcionalidades del MVP (2 semanas)

- Perfiles de talento con skills y niveles.
- Cálculo de brechas peoplea ↔ rol.
- Recomendaciones de rutas de desarrollo.
- Comparación de candidatos internos vs externos para vacantes.
- Marketplace interno básico (matching de peopleas a oportunidades).
- Dashboard con KPIs clave:
  - Cobertura de skills.
  - Roles en riesgo.
  - Brechas de talento.
- Datos de demo realistas (empresa ficticia "TechCorp", 20 empleados, 8 roles, 30 skills).

## 🚫 Fuera del MVP inicial

- Autenticación compleja.
- CRUD completo de todo (datos hardcodeados del seed).
- IA real (simulada con lógica de reglas).
- Integraciones externas (ATS, HRIS, LMS).
- SSI/blockchain (roadmap futuro).
- Módulo de desempeño completo (versión ligera integrada).

## 🧱 Decisiones arquitectónicas clave

- Multi-tenant con aislamiento por `organization_id`.
- Identificación de tenant por subdomain o JWT.
- Cada empresa tiene su propio catálogo de skills, roles y rutas.

## 🆕 Nuevo módulo: Planificación Dotacional Estratégica

### Objetivo

Dar soporte a la toma de decisiones estratégicas de dotación basadas en:
- Skills actuales y futuras.
- Escenarios de demanda de talento.
- Matching interno.
- Estrategias de talento: build–buy–borrow–bot.
- Sucesión y reconversión.
- Desvinculaciones planificadas.

### Funcionalidades clave

- Definición de escenarios de demanda futura (base, conservador, agresivo).
- Análisis de oferta interna (skills por peoplea, marketplace interno).
- Gap & surplus analysis (brechas de FTE y skills).
- Recomendaciones de estrategias:
  - Build (desarrollo interno).
  - Buy (reclutamiento externo).
  - Borrow (freelance, consultoría).
  - Bot (automatización).
- Integración con:
  - Marketplace interno (identificación de talento interno).
  - Rutas de desarrollo (cierre de brechas de skills).
  - Selección (fuentes e instrumentos de reclutamiento).
  - Sucesión (identificación de sucesores).
  - Desvinculación (gestión de excedentes).
- Dashboards de planificación dotacional:
  - Brechas por rol/área.
  - Estrategias aplicadas.
  - Avance en cierre de brechas.
  - Riesgos de talento.

### Impacto en MVP

- Se extiende el dashboard estratégico para incluir KPIs de planificación dotacional.
- Se amplía el modelo de datos para incluir:
  - Escenarios de demanda.
  - Estrategias de talento por rol.
  - Tracking de ejecución de estrategias.
- Se enriquece el marketplace interno con lógica de matching avanzado (skills + potencial + intereses).
- Se introduce una vista de “portafolio de estrategias” para cada rol/brecha.

## 🎯 Objetivo actual

"Necesito integrar el módulo de Planificación Dotacional como parte funcional del MVP de 2 semanas, actualizando el modelo de datos, el dashboard y el flujo de decisiones de talento."

🧠 ¿Por qué este módulo en el MVP?

Aunque el MVP está centrado en perfiles, brechas y rutas, el módulo de Planificación Dotacional:

Amplía el valor estratégico de la plataforma.
Cierra el ciclo de gestión de talento: de la identificación de brechas a la toma de decisiones de dotación.
Justifica la presencia de IA y analítica en el producto.
Facilita la demo mostrando cómo Strato ayuda a tomar decisiones de talento a nivel ejecutivo.
📐 Integración funcional en el MVP
Elemento	Impacto en MVP
Dashboard estratégico	Agregar KPIs de planificación dotacional (brechas, estrategias aplicadas, riesgos).
Marketplace interno	Enriquecer matching con lógica de “potencial” y “interés”.
Selección (interno vs externo)	Mostrar recomendaciones de fuentes de reclutamiento por rol.
Rutas de desarrollo	Usarlas como base para estrategias de “build”.
Modelo de datos	Ampliar con tablas de escenarios, estrategias, tracking de ejecución.
🧱 Modelo de datos extendido (fragmento clave)
-- Escenarios de demanda futura
CREATE TABLE Strato.workforce_scenarios (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    organization_id UUID NOT NULL REFERENCES organizations(id),
    name VARCHAR(100),
    description TEXT,
    timeframe_start DATE,
    timeframe_end DATE,
    created_at TIMESTAMP DEFAULT NOW()
);

-- Estrategias de talento por rol
CREATE TABLE Strato.talent_strategies (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    role_id UUID NOT NULL REFERENCES roles(id),
    scenario_id UUID NOT NULL REFERENCES workforce_scenarios(id),
    strategy_type VARCHAR(20), -- BUILD, BUY, BORROW, BOT
    description TEXT,
    target_fte INT,
    execution_status VARCHAR(20), -- PLANNED, IN_PROGRESS, COMPLETED
    created_at TIMESTAMP DEFAULT NOW()
);

-- Tracking de ejecución de estrategias
CREATE TABLE Strato.strategy_executions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    strategy_id UUID NOT NULL REFERENCES talent_strategies(id),
    action_taken TEXT,
    result TEXT,
    kpi_impact JSONB,
    executed_by UUID REFERENCES users(id),
    executed_at TIMESTAMP DEFAULT NOW()
);

🧭 Flujo de decisiones integrado en el sistema
Usuario define escenario de demanda futura (ej. crecimiento del 20% en ventas → necesito 5 nuevos analistas de datos).
Sistema compara con oferta interna (¿tenemos peopleas con skills de analista de datos?).
Sistema recomienda estrategia:
Si hay 2 internos con buen ajuste → sugerir movilidad interna.
Si hay 1 peoplea con potencial → sugerir ruta de desarrollo + plan de transición.
Si faltan 3 FTE → sugerir contratación externa con fuentes e instrumentos.
Usuario aprueba estrategia → se registra en talent_strategies.
Sistema muestra avance en dashboard:
% de brechas cerradas.
Estrategias en ejecución.
Alertas de riesgo (ej. rol crítico sin sucesor).
🧪 Datos de demo sugeridos (TechCorp)
Escenario base: crecimiento del 15% en ventas en 12 meses.
Rol afectado: Analista de Datos (nuevo rol).
Demanda: 5 FTE adicionales.
Oferta interna: 2 peopleas con skills parciales.
Decisión sugerida:
2 FTE por movilidad interna.
1 FTE por reconversión (ruta de desarrollo de 3 meses).
2 FTE por contratación externa (Buy).
Tracking en dashboard:
40% de brecha cerrada.
1 estrategia en ejecución (ruta de desarrollo).
Alerta: “Rol crítico sin sucesor”.
