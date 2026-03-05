# Orquestador de Competencias IA (Competency-First Approach)

Este documento detalla la reingeniería de la Fase 2 (Scenario Planning/Incubated Cube) de Stratos. Hemos transicionado desde un diseño organizacional _Role-centric_ a uno estructuralmente más maduro: el modelo **Competency-First**.

## 1. El Problema Base

En su primera iteración, el LLM del Paso 1 mapeaba escenarios directamente proponiendo una lista de _Roles_, y le asignaba competencias sueltas a cada rol. Este enfoque generaba fragmentación estructural, proponiendo múltiples "micro-cargos" si un problema era nuevo para la empresa, e ignoraba que muchos de los perfiles actuales de la empresa podrían asimilar fácilmente estas nuevas necesidades si se someten a un ligero _Upskilling_.

## 2. La Solución: Intelligence Role Bundling (Paso 2)

Implementamos el endpoint `orchestrate-capabilities` para fungir como **Pasarela de Inteligencia** antes de que el usuario manipule los cubos.

### Pasos de Orquestación Interna:

1. **Recolección:** Se leen las competencias generadas por el escenario mediante la tabla pivote de enlace.
2. **Medición Vectorial (Embeddings):** Cada **competencia requerida por el escenario** se compara semánticamente (Embeddings Coseno >90%) con el **Catálogo Base (Core) de la Empresa**.
    - **Matches (>90%):** Representan el **Impacto Orgánico**. Significa que se necesita una competencia y _ya la tenemos documentada_. Stratos detecta quién la tiene actualmente y marca qué posiciones incrustadas requieren atención para escalar su fuerza de trabajo (Upskilling en vez de nuevo cargo).
    - **No Matches (<90%):** Representan **Competencias Extraterrestres ("Aliens")**. Son totalmente nuevas para la empresa (e.g. "Ingeniería de Prompt Cuántica").

3. **Invocación LLM (RoleDesignerService):** En lugar de crear un cargo por cada alienígena detectada, el `RoleDesignerService` agrupa estas competencias (Bundling).
    - El Prompt le dice al LLM: _"Aquí tienes estas 'X' competencias alien. Además, estos son los roles propuestos. ¿Te parece lógico agrupar algunas competencias alien y enriquecer un rol propuesto, o ameritan crear una posición súper específica (Creation)?"_

## 3. UI/UX: El Panel de Control en IncubatedCubeReview.vue

Visualmente en el Frontend, el usuario notará que, mientras los Roles base se están cargando, el sistema corre un proceso analítico automatizado de fondo (Glassmorphic Loading).
Al terminar arroja los dos baldes:

1. **🟩 El Balde Verde (Organic impact):**
   Lista las competencias core que serán fuertemente impactadas y necesitan una inyección de energía estructural para escalar talento.

2. **🟨 El Balde Amarillo (Alien Role Bundling):**
   Propone un "Empaquetado". Le recomienda al Director de HR: _"Añade 'Machine Learning' y 'LLM Optimization' al cargo de 'Tech Lead' (Enrichment), no le hagas un puesto de 'IA Consultant' si no tiene las horas FTE"._

**Aprobación y Flujo Normal:**
Con esa hoja de ruta táctica, el HR procede a revisar los Cubos pre-generados por el pipeline, ya entendiendo a nivel holístico cuánta reestructura real requiere su escenario para ejecutarse con éxito.
