# Fase 2: El Puente de Agentes (CrewAI + Laravel)

Esta carpeta contiene la documentación y especificaciones para la segunda fase del desarrollo de Stratos: **La Integración de Inteligencia Artificial mediante Agentes**.

## Objetivos Clave

1.  Establecer un microservicio en Python capaz de ejecutar Agentes de CrewAI.
2.  Definir un contrato de datos claro entre el Core (Laravel) y la Inteligencia (Python).
3.  Desplegar el primer Agente: "Analista de Estrategias de Talento" (Talent Strategy Analyst).

## Documentación Técnica

- [Contrato de Datos (JSON Schema)](DataContract_GapAnalysis.md) - _Estructura exacta del payload para análisis de brechas._
- [Diseño del Primer Agente](./Agent_TalentStrategy.md) - _Prompt, Roles y Herramientas del Agente._ (Pendiente)

## Stack Específico

- **Backend:** Python 3.11+ (FastAPI)
- **Framework de Agentes:** CrewAI
- **LLM:** OpenAI GPT-4o / Claude 3.5 Sonnet (vía API)
- **Comunicación:** HTTP REST (Inicialmente) -> Colas (Futuro)
