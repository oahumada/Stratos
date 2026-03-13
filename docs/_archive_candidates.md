## 🗂️ Candidatos a archivo (redundante / obsoleto)

Este archivo lista documentación que parece **redundante, muy específica de una iteración pasada o de soporte a PRs**, y que se puede mover a una carpeta de archivo (`docs/_archive/` por ejemplo) para revisión y posible eliminación.

### Criterios usados

- **Redundante**: La misma lógica/contenido existe en otros documentos más recientes y estructurados.
- **Obsoleto / muy de PR**: Textos pensados como cuerpo de PR, runbooks de despliegue únicos o planes de trabajo ya ejecutados.
- **No enlazado desde `INDEX.md`**: Difícil de descubrir salvo por búsqueda directa.

---

### 📁 Workforce Planning / UX / Testing (planes ya ejecutados o superados)

- `docs/OPCION_A_CHARTS_COMPLETADA.md`  
  - Resumen muy detallado de la **implementación de charts con ApexCharts** para Workforce Planning.  
  - El estado actual del módulo está cubierto por documentación más global (WFP guía, resúmenes, roadmap). Este archivo es histórico de una sesión concreta.

- `docs/OPCION_B_UX_POLISH_PLAN.md`  
  - Plan minucioso de **UX Polish** (loading states, empty states, accesibilidad, etc.).  
  - Útil como referencia, pero gran parte ya se refleja en el estado actual del código y otras guías más recientes.

- `docs/OPCION_C_TESTING_PLAN.md`  
  - Plan de **testing completo** (unit, integration, E2E, performance) para Workforce Planning.  
  - Contenido valioso pero muy orientado a una iteración; hoy funciona mejor como referencia histórica que como guía viva.

---

### 📁 Importación LLM – documentación de PR y borradores

- `docs/PR_DRAFT_IMPORT_GENERATION.md`  
  - Borrador de cuerpo de PR para la feature de importación de generaciones LLM.  
  - La funcionalidad final está documentada en `MEMORIA_SISTEMA_IMPORTACION_LLM.md` y `FLUJO_IMPORTACION_LLM.md`, por lo que este archivo es principalmente histórico.

- `docs/PR_DESCRIPTION_IMPORT_GENERATION.md`  
  - Descripción más corta del mismo PR de importación LLM.  
  - Redundante con el borrador anterior y con la documentación técnica consolidada.

---

### 📁 Metodología de planificación (duplicada por ScenarioPlanning/*)

- `docs/Metodología paso a paso y flujo del proceso 2d76208b6bd18069849cf47ed873639f.md`  
  - Descripción detallada de la **metodología de planificación en 7 fases** y flujo de decisión.  
  - El mismo contenido está recogido y refinado en:
    - `docs/ScenarioPlanning/MetodologiaPasoaPaso.md`
    - `docs/ScenarioPlanning/ArquitecturaSieteFases.md`
    - `docs/ScenarioPlanning/EstrategiaSieteFases.md`
  - Este archivo puede considerarse un **draft/working note** de los documentos anteriores.

---

### ✔️ Siguiente paso recomendado

Para archivar físicamente estos archivos (manteniéndolos por si los necesitas):

1. Crear carpeta de archivo:

   ```bash
   mkdir -p docs/_archive
   ```

2. Mover los archivos candidatos:

   ```bash
   git mv docs/OPCION_A_CHARTS_COMPLETADA.md docs/_archive/
   git mv docs/OPCION_B_UX_POLISH_PLAN.md docs/_archive/
   git mv docs/OPCION_C_TESTING_PLAN.md docs/_archive/
   git mv docs/PR_DRAFT_IMPORT_GENERATION.md docs/_archive/
   git mv docs/PR_DESCRIPTION_IMPORT_GENERATION.md docs/_archive/
   git mv "docs/Metodología paso a paso y flujo del proceso 2d76208b6bd18069849cf47ed873639f.md" docs/_archive/
   ```

3. Revisar en `docs/_archive/` y eliminar definitivamente lo que consideres que ya no aporta valor.

