Quiero que actúes como arquitecto de software y technical writer.

Contexto:
Estoy construyendo un sistema llamado TalentIA, una plataforma de gestión estratégica de talento y planificación de workforce basada en Laravel + PostgreSQL + Vue3.
El sistema trabaja con 3 niveles de abstracción de capacidades:
- Capability (pilar organizacional estratégico)
- Competency (agrupa un conjunto coherente de skills)
- Skill (unidad mínima evaluable, con niveles 1–5)

Además, el sistema tiene 3 niveles de gestión:
- Estratégico: Escenarios, Capabilities
- Táctico: Competencies por rol en escenarios
- Operacional: Skills por rol y persona

Arquitectura de datos (tablas clave, ya implementadas):

- capabilities (id, organization_id, name, description, category, status, discovered_in_scenario_id)
- competencies (id, organization_id, capability_id, name, description)
- competency_skills (id, competency_id, skill_id, weight)

- scenarios (id, organization_id, name, description, horizon_months, status, assumptions)
- scenario_capabilities (scenario_id, capability_id, strategic_role, strategic_weight, priority, rationale)

- scenario_roles (id, scenario_id, role_id, role_change, impact_level, evolution_type, rationale)
- role_competencies (id, role_id, competency_id, required_level, is_core, rationale)
- scenario_role_competencies (id, scenario_id, role_id, competency_id, required_level, is_core, change_type, rationale)

- role_skills (id, role_id, skill_id, required_level, is_critical, source, competency_id)
- scenario_role_skills (id, scenario_id, role_id, skill_id, required_level, is_critical, change_type, source, competency_id, rationale)

- people (personas de la organización)
- person_role_skills (id, person_id, role_id, skill_id, current_level, verified, evidence_source, evidence_date, notes)

Hay dos servicios de negocio importantes:
1) RoleSkillDerivationService:
   - Deriva automáticamente scenario_role_skills a partir de scenario_role_competencies × competency_skills.
   - Marca las skills derivadas con source='competency'.
   - Respeta skills agregadas manualmente (source='manual').

2) ScenarioAnalyticsService:
   - Calcula el Scenario IQ (0-100) usando esta lógica:
       a) Skill Readiness = min(1, current_level / required_level) por rol y skill en el escenario.
       b) Competency Readiness = promedio ponderado de Skill Readiness (usando competency_skills.weight).
       c) Capability Readiness = promedio de Competency Readiness.
       d) Scenario IQ = promedio ponderado de Capability Readiness (usando scenario_capabilities.strategic_weight) * 100.
   - Calcula un Confidence Score en base a la calidad de la evidencia (self_assessment, manager_review, certification, test).
   - Permite obtener análisis de gaps a nivel de competencia por rol.

Lo que necesito de ti:
Genera una documentación técnica estructurada en Markdown para desarrolladores, que incluya:

1. Introducción a Stratos
   - Objetivo del sistema
   - Diferenciador (trabajar por escenarios, capacidades y evolución de roles/personas)

2. Arquitectura Lógica
   - Diagrama conceptual texto: Capabilities → Competencies → Skills
   - Diagrama conceptual texto: Escenario → Roles → Competencies → Skills → Personas

3. Modelo de Datos
   - Tabla por tabla:
     - Propósito
     - Campos clave
     - Relaciones con otras tablas
   - Explica especialmente:
     - difference entre role_competencies y role_skills
     - difference entre scenario_role_competencies y scenario_role_skills
     - uso de discovered_in_scenario_id (incubación de capacidades y skills)
     - uso de source ('competency' vs 'manual') en role_skills y scenario_role_skills

4. Flujos de Negocio Principales
   - Diseño de un escenario (Fase 1 y 2):
     - definir capacidades estratégicas (scenario_capabilities)
     - definir competencias por rol en escenario (scenario_role_competencies)
   - Derivación de skills:
     - cómo RoleSkillDerivationService genera scenario_role_skills desde competencias
     - cómo se manejan las excepciones (skills manuales)
   - Cálculo del Scenario IQ:
     - explicación paso a paso de la fórmula
     - cómo se usa para priorizar capacidades
   - Diagnóstico de gaps:
     - cómo se calcula readiness por competency y capability
     - cómo se interpretan los gaps a nivel de competencia y skill

5. Ejemplo guiado
   - Ejemplo narrativo con el escenario “Adopción de IA Generativa 2026”
   - 2–3 roles (Product Manager, AI Engineer, UX Designer)
   - Mostrar cómo se configuran:
     - capabilities
     - competencies
     - scenario_role_competencies
     - cómo se derivan las scenario_role_skills
     - cómo se vería un resultado de IQ y gaps

6. Buenas prácticas y decisiones de diseño
   - Por qué se eligió la arquitectura híbrida (competencies + skills)
   - Ventajas para escalabilidad y mantenibilidad
   - Cómo extender el modelo (nuevos escenarios, nuevas capacidades, nuevos roles)

Formato:
- Usa títulos y subtítulos Markdown (#, ##, ###).
- Usa tablas Markdown cuando tenga sentido.
- Usa ejemplos de código PHP y SQL solo donde ayuden a entender (no es un manual de referencia de API).
- El tono debe ser claro y didáctico, orientado a un equipo de desarrollo que va a mantener y evolucionar TalentIA.

Empieza la documentación directamente, no repitas el prompt ni agregues explicaciones sobre lo que estás haciendo.