# 🎯 Especificación Técnica: Selección Inteligente (Tailored Acquisition)

## 1. Visión

La Selección Inteligente en Stratos no busca "llenar vacantes"; busca **optimizar el ADN organizacional**. Transformamos el reclutamiento de un proceso de filtrado de CVs en una **Búsqueda de Resonancia Técnica y Cultural**.

---

## 2. El Motor de Matching Agéntico (The Matchmaker)

El proceso es liderado por un enjambre de agentes que actúan como un panel de expertos:

### A. Matchmaker de Resonancia (El Motor de ADN)

- **Input:** El "Blueprint" generado en Scenario IQ o la descripción del cargo.
- **Acción:** No se limita a leer palabras clave. Define el **Perfil de Resonancia ADN**: ¿Qué combinación de DISC, habilidades y valores del Manifiesto Stratos se requieren para que esta posición tenga un ROI positivo?
- **DNA Cloning:** Puede extraer el "Blueprint de Éxito" de cualquier High-Performer existente (`extractHighPerformerDNA`) para crear un perfil de búsqueda optimizado que encuentra candidatos con resonancia idéntica.

### B. Agente de Pre-Evaluación Cultural (El Guardián del Portal)

- **Acción:** Administra una micro-entrevista agéntica inicial centrada en el **Fit Cultural**. Compara las respuestas del candidato con el Manifiesto de la Organización en tiempo real.
- **Output:** Score de Alineación Ética y Cultural.

### C. Agente de Evaluación de Potencial (El Analista Inferencial)

- **Acción:** Analiza la trayectoria del candidato para detectar **Learning Agility**. No solo lo que sabe hacer, sino qué tan rápido aprenderá lo que la empresa necesita.
- **Output:** Predictive Success Probability (Probabilidad de Éxito en el Rol).

---

## 3. Portal de Candidatos Agéntico (Candidate Experience)

El candidato ya no es un "aplicante pasivo". El **Portal de Candidatos** (`Selection/CandidatePortal.vue`) le ofrece:

- **Resonancia ADN Visual:** Un anillo de progreso circular que muestra su % de resonancia con el rol.
- **Análisis Tridimensional:** Tarjetas de Núcleo Técnico, Alineación Cultural y Trayectoria de Crecimiento.
- **Chat con Matchmaker:** Interacción directa con el agente Matchmaker de Resonancia para resolver dudas tácticas sobre el blueprint del rol, el equipo y el plan de carrera.

**Ruta:** `/candidate-portal/{id}`

---

## 4. Integración con el Ecosistema (Sin Cabos Sueltos)

Para que Stratos sea un sistema único, la Selección se integra así:

1.  **Desde Scenario IQ (Planning → Sourcing):**
    - Cuando un escenario de planificación se aprueba, las "Vacantes Virtuales" se publican automáticamente con el perfil de competencias exacto que la simulación determinó como óptimo.
2.  **Hacia Talento 360 (Selection → Performance):**
    - Una vez contratado, las respuestas de la entrevista de selección y el perfil psicométrico inicial se convierten en la **Línea Base (Baseline)** del colaborador.
    - Esto permite medir el crecimiento real desde el día 1 en el primer ciclo 360 oficial.
3.  **Hacia Learning Paths (Onboarding → Acceleration):**
    - Los gaps detectados durante la selección se transforman automáticamente en el **Plan de Onboarding de 90 días**, optimizando el "Time to Full Capacity".
4.  **DNA Cloning Loop (Performance → Sourcing):**
    - Los High-Performers identificados en el 360 alimentan el `extractHighPerformerDNA` del Matchmaker, creando un ciclo de "clonación de éxito" donde la organización aprende qué tipo de talento le funciona mejor.

---

## 5. API de Selección Inteligente

### Endpoints Implementados

| Método | Ruta                                             | Servicio                       | Descripción                                  |
| :----- | :----------------------------------------------- | :----------------------------- | :------------------------------------------- |
| `POST` | `/api/applications`                              | `ApplicationController@store`  | Crear una nueva postulación                  |
| `PUT`  | `/api/applications/{id}`                         | `ApplicationController@update` | Actualizar estado de postulación             |
| -      | `TalentSelectionService@analyzeApplication`      | Servicio Interno               | Análisis agéntico de un aplicante            |
| -      | `TalentSelectionService@proposeShortlist`        | Servicio Interno               | Genera terna argumentada por IA              |
| -      | `TalentSelectionService@extractHighPerformerDNA` | Servicio Interno               | Decodifica el DNA de éxito de un colaborador |

---

## 6. El "Standard" de Selección vs. Tradicional

| Funcionalidad    | RPO / ATS Tradicional      | Selección Inteligente (Stratos)        |
| :--------------- | :------------------------- | :------------------------------------- |
| **Filtro**       | Palabras clave en CV       | Resonancia ADN Semántica y Cultural    |
| **Entrevista**   | Subjetiva / Humana (sesgo) | Agéntica (imparcial y científica)      |
| **Potencial**    | No se mide                 | Learning Agility & Success Probability |
| **Integración**  | Fragmentada                | Cierre de ciclo: Onboarding → 360      |
| **Candidato**    | Caja negra                 | Portal transparente con Resonancia ADN |
| **Benchmarking** | No existe                  | DNA Cloning de High-Performers         |

---

## 7. Roadmap de Implementación (Smart Selection)

### Fase 1: Portal Agéntico ✅ (Completada — Feb 2026)

- Interfaz premium de candidato con Resonancia ADN visual.
- Chat integrado con Matchmaker de Resonancia.
- DNA Cloning de High-Performers implementado en backend.

### Fase 2: Motor de Scoring Predictivo (Q2 2026)

- Cálculo de `Success Probability` cruzando perfil del candidato vs. Blueprint del rol.
- Generación automática de "Shortlists" argumentadas por la IA.

### Fase 3: Onboarding Automation (Q3 2026)

- Transferencia de datos de selección a rutas de aprendizaje.
- Dashboard de "Predicción de Desempeño" para el Hiring Manager.

---

**"En Stratos, no contratamos para un puesto, reclutamos para un propósito."**
_© 2026 Stratos Intelligence Architecture Group_
_Actualizado: 27 de Febrero de 2026_
