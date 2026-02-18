# 🧠 Índice de Inteligencia Stratos (AI Hub)

Este documento centraliza toda la documentación relacionada con la capa de inteligencia artificial, el microservicio de Python y la integración con DeepSeek.

---

## 🏗️ Arquitectura y Conexión

- **[GUIA_CONEXION_LLM.md](GUIA_CONEXION_LLM.md)** - ⭐⭐ **LEER PRIMERO**
- **[GUIA_LLM_AGNOSTICO.md](GUIA_LLM_AGNOSTICO.md)** - Guía para proveedores (OpenAI, DeepSeek, Abacus).
    - Configuración del Microservicio Python.
    - Variables de entorno para DeepSeek.
    - Flujo de comunicación Laravel ↔ Python.
- **[StratosIntelService.php](../app/Services/Intelligence/StratosIntelService.php)** - Servicio central en Laravel.
- **[python_services/app/main.py](../python_services/app/main.py)** - Orquestador de agentes CrewAI.

---

## 🎭 Generación de Escenarios (Wizard)

- **[GUIA_GENERACION_ESCENARIOS.md](GUIA_GENERACION_ESCENARIOS.md)** - Guía completa del flujo.
    - Cuestionario del Wizard.
    - Diseño del Prompt Estratégico.
    - Esquema de salida JSON (Blueprints).
    - Gestión de Jobs y Chunks.

---

## 📊 Análisis de Brechas (Gap Analysis)

- **[Milestone: AI-Driven Talent Gap Analysis](Milestones/2026-02-18-AI-Integration.md)** - Reporte de integración inicial.
- **[AnalyzeTalentGap.php](../app/Jobs/AnalyzeTalentGap.php)** - Job de procesamiento de brechas.
- **[StratosIntelServiceTest.php](../tests/Feature/Services/StratosIntelServiceTest.php)** - Pruebas de servicio.

---

## 📈 Hitos y Roadmaps (Últimos Avances)

- **[Milestone: DeepSeek & Scenario Generation Integration](Milestones/2026-02-18-DeepSeek-Scenario-Generation-Integration.md)** - Reporte del despliegue del 18 de Febrero.
- **[Milestone: Impact Visualizer & Intelligence Refinement](Milestones/2026-02-18-Impact-Visualizer-Intelligence-Refinement.md)** - 🏆 **ÚLTIMO AVANCE**: Visualización 5B e HiPos.
- **[PLAN_ATAQUE_INTELIGENCIA_KICKSTART.md](PLAN_ATAQUE_INTELIGENCIA_KICKSTART.md)** - 🚀 **FASE KICKSTART FINALIZADA**.

---

## 🧪 Testing de Inteligencia

Para validar la suite completa de IA:

```bash
vendor/bin/pest tests/Feature/Integrations/ScenarioGenerationIntelTest.php \
                tests/Feature/Integrations/AiStrategyIntegrationTest.php \
                tests/Feature/Services/StratosIntelServiceTest.php \
                tests/Feature/Jobs/AnalyzeTalentGapTest.php
```

---

**Última actualización:** 18 Febrero 2026
