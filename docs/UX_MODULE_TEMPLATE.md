# Plantilla UX – Módulo Stratos

> Usa esta guía como base para documentar y revisar UX/UI de cualquier módulo (Scenario IQ, Marketplace, Talent 360, etc.).

## 0. Cómo usar esta plantilla (secuencia sugerida)

1. **Explorar el módulo actual**  
   - Navega el módulo como si fueras un usuario real y recorre 1–2 flujos típicos sin modificar nada.

2. **Definir Personas**  
   - Completa la sección 1 con la persona foco y, si aplica, una secundaria.

3. **Escribir Historias (storytelling)**  
   - Añade 2–3 historias clave en la sección 2 (situación → recorrido ideal → resultado).

4. **Mapear Flujos de Actividad**  
   - Deriva 2–3 flujos concretos a partir de las historias (sección 3).

5. **Definir el Mapa Visual**  
   - Especifica qué debe verse above the fold y qué va en secciones secundarias (sección 4).

6. **Aplicar el Checklist UX/UI**  
   - Usa la sección 5 para marcar qué se cumple y anotar gaps.

7. **Calificar con el Sistema de Puntuación UX/UI**  
   - Rellena la tabla de la sección 6 (8 ejes × 1–5) y calcula el score global.

8. **Plan de acción / Implementación**  
   - Usa la sección 7 para listar componentes y endpoints afectados y definir las acciones UX prioritarias para el próximo sprint.

## 1. Personas

### Persona A – [Rol principal del módulo]

- **Rol típico**: …
- **Objetivo principal**: …
- **Dolores**:
  - …
- **Contexto de uso**:
  - …

### Persona B – [Rol secundario, si aplica]

- **Objetivo principal**: …
- **Dolores**:
  - …
- **Contexto**:
  - …

> Especifica claramente cuál es la **persona foco** del módulo (para quién se optimiza primero).

---

## 2. Historias (Storytelling)

### Historia 1 – “[Nombre corto de la historia principal]”

- **Situación**: …
- **Recorrido ideal**:
  1. …
  2. …
  3. …
- **Resultado**: …

### Historia 2 – “[Segunda historia clave]”

- **Situación**: …
- **Recorrido ideal**:
  1. …
  2. …
  3. …
- **Resultado**: …

> Recomiendo 2–3 historias máximo por módulo (estado actual, acción principal, validación/progreso).

---

## 3. Flujos de Actividad

### Flujo 1 – [Nombre del flujo principal]

1. Punto de entrada (¿desde dónde llega el usuario?).
2. Vista 1 (home del módulo / dashboard).
3. Decisión o acción clave.
4. Vista 2 / detalle.
5. Salida (estado logrado, feedback).

### Flujo 2 – [Flujo secundario]

1. …
2. …
3. …

---

## 4. Mapa Visual – Jerarquía de la Vista Principal

### Above the fold (primer pantallazo)

- **Bloque A – Identidad / Contexto**
  - ¿Qué ve el usuario sobre sí mismo o sobre el objeto principal (escenario, posición, persona, etc.)?
- **Bloque B – Estado actual**
  - KPIs clave (máximo 3–4) con significado claro.
- **Bloque C – Siguiente paso**
  - 1 CTA principal muy visible.

### Debajo / Secciones secundarias

- Listados detallados (tablas, matrices, tarjetas).
- Configuración / filtros avanzados.
- Historial / logs / análisis profundos.

> Describe explícitamente qué debe verse siempre en el primer scroll y qué se mueve a niveles secundarios.

---

## 5. Checklist UX/UI del Módulo

### Claridad

- [ ] En 5 segundos se entiende **dónde está** el usuario y **qué puede hacer**.
- [ ] Existe un CTA primario inequívoco en la vista principal.

### Enfoque

- [ ] Los KPIs visibles son pocos y relevantes para la persona foco.
- [ ] La información secundaria no compite visualmente con la principal.

### Consistencia visual (Stratos Glass)

- [ ] Los paneles principales usan patrones glass definidos (tarjetas, sombras, glows).
- [ ] Botones, iconografía y tipografía son consistentes con `STRATOS_GLASS_DESIGN_SYSTEM.md`.

### Emoción / Motivación

- [ ] Hay elementos que refuerzan progreso, logro o impacto (no solo tareas).
- [ ] El tono de microcopys es cercano y alineado a la marca Stratos.

### Fase de Refinamiento: "Sacar Brillo" 💎

- [ ] **Micro-interacciones**: ¿Todos los botones y cards tienen estados de hover/active fluidos (duration-300)?
- [ ] **Iconografía Phosphor**: ¿Se han eliminado todos los iconos MDI/genéricos en favor de Phosphor?
- [ ] **Cero Call-to-Actions muertos**: ¿Cada vista guía proactivamente al usuario al siguiente paso logico?
- [ ] **Estados de Carga**: ¿Se utilizan Skeletons que respetan la forma del contenido final?
- [ ] **Jerarquía Z-Axis**: ¿Se usa correctamente el `backdrop-blur` y sombras para denotar profundidad?

---

## 6. Sistema de Puntuación UX/UI

### 6.1 Dimensiones y pesos

Califica cada eje de **1 a 5** (1 = muy pobre, 5 = excelente) y aplica los pesos para obtener un score global sobre 100.

| Eje                                       | Peso | Score (1–5) | Comentario breve                          |
| ----------------------------------------- | :--: | :---------: | ------------------------------------------ |
| Claridad & narrativa                      |  15  |             |                                            |
| Flujo & orientación (carga cognitiva)     |  15  |             |                                            |
| Jerarquía visual & priorización           |  15  |             |                                            |
| Estado, feedback & manejo de errores      |  10  |             |                                            |
| Consistencia visual (Stratos Glass)       |  15  |             |                                            |
| Accesibilidad & responsividad             |  10  |             |                                            |
| Eficiencia & foco en la tarea             |  10  |             |                                            |
| Delight & emoción (engagement)            |  10  |             |                                            |
| **Total ponderado (/100)**                | 100  |             |                                            |

> Fórmula:  
> Score módulo = \(\sum (score\_eje × peso\_eje) / 5\)

### 6.2 Notas y acciones

- **Fortalezas**:
  - …
- **Debilidades**:
  - …
- **Acciones UX prioritarias (próximo sprint)**:
  - …

---

## 7. Notas de Implementación

- **Componentes clave del módulo**:  
  - `.../Index.vue`  
  - `.../components/...`  
- **Endpoints principales**:  
  - `GET /api/...`  
  - `POST /api/...`

> Usa esta sección para enlazar vistas y APIs reales que implementan lo descrito arriba.

