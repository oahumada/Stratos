# 🧪 Protocolo de Verificación: Orquestación del Cerebro (MAS)

Este documento detalla el procedimiento para verificar el "Cerebro", el núcleo de orquestación multi-agente (MAS) de Stratos.

---

## 🏗️ 1. Infraestructura de Inteligencia

El "Cerebro" opera bajo una arquitectura híbrida:

- **Core Orchestrator (Laravel):** Administra la identidad de los agentes (`App\Models\Agent`) y despacha tareas.
- **Intelligence Service (Python):** Ejecuta la lógica compleja usando **CrewAI** y modelos de DeepSeek (Chat v3 y Reasoner R1).

### Agentes Clave a Verificar:

1.  **Diseñador de Roles (The Blueprint):** Experto en el modelo de Cubo de Roles (X, Y, Z).
2.  **Selector de Talento (The Matchmaker):** Encargado de la resonancia técnica y cultural.
3.  **Orquestador 360 (The Arbiter):** Basado en DeepSeek R1 para análisis lógicos profundos.

---

## 🛠️ 2. Pruebas de Integración (Backend)

Ejecutar las pruebas automatizadas para asegurar que el puente Laravel <-> Python funciona:

```bash
# Verificar la generación de escenarios (Cerebro -> The Blueprint)
php artisan test tests/Feature/Integrations/ScenarioGenerationIntelTest.php

# Verificar la selección de estrategia (Cerebro -> The Architect)
php artisan test tests/Feature/Integrations/AiStrategyIntegrationTest.php
```

---

## 🧠 3. Casos de Prueba: Razonamiento de Agentes

Para verificar que cada agente "se pone en su papel" y utiliza sus herramientas correctamente:

### Caso A: Verificación de Identidad (The Blueprint)

- **Prompt:** _"Diseña un rol de 'Ingeniero de Plataforma' enfocado en escalabilidad."_
- **Resultado esperado:** La IA debe utilizar terminología de **Cubo de Roles** y devolver un JSON con porcentajes de composición de talento (Humano vs Synthetic).

### Caso B: Verificación de Juicio Crítico (The Arbiter - R1)

- **Prompt:** _"Calibra los resultados de esta evaluación 360 donde hay un sesgo de benevolencia de los pares hacia el líder."_
- **Resultado esperado:** El agente debe detectar el sesgo y justificar su calibración mediante el "Chain of Thought" (disponible en la respuesta del modelo).

### Caso C: Verificación de Resonancia (The Matchmaker)

- **Prompt:** _"Analiza el match entre este candidato Agente de IA y este rol de Arquitecto de Innovación."_
- **Resultado esperado:** Debe devolver un `resonance_score` y análisis de fit cultural basado en el Manifiesto de la empresa.

---

## 🚧 4. Protocolo "Rainy Day" (Resiliencia)

Verificar qué sucede cuando el núcleo falla:

- [ ] **Desconexión del Servicio Python:** ¿Lanza el error `LLMServerException` correctamente hacia el Quality Hub?
- [ ] **Degradación de IA:** Si DeepSeek no responde, ¿se activa el `STRATOS_MOCK_IA` para mantener la demo operativa?
- [ ] **Datos Inconsistentes:** ¿El agente rechaza inputs que no cumplen con el `DATA_CONTRACT.md`?

---

> [!IMPORTANT]
> **El Cerebro es agnóstico del modelo.** Si un agente no está rindiendo, se puede cambiar de `deepseek-chat` a `gpt-4o` o `claude-3.5` editando su registro en la tabla `agents` del backend.
