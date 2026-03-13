# UX Mi Stratos – Portal Personal del Talento

## 1. Personas

### Persona A – Colaborador/Colaboradora Individual

- **Rol típico**: Ingeniería, producto, data, operaciones.
- **Objetivo principal**: Entender “dónde estoy hoy” (rol, nivel, brechas) y “qué hago ahora” para crecer.
- **Dolores**:
  - No tener claro qué se espera de él/ella.
  - Sentir que el sistema es “para RRHH” y no para su beneficio.
- **Contexto de uso**:
  - 5–10 minutos, varias veces al mes (antes de 1:1, evaluaciones, conversaciones de carrera).

### Persona B – Manager directo / Team Lead

- **Objetivo principal**: Ver rápido qué tan preparado está cada miembro del equipo y dónde enfocarse.
- **Dolores**:
  - Mucho dato disperso, poco “so what”.
- **Contexto**:
  - Uso menos frecuente, pero clave en reuniones 1:1 y calibraciones.

> **Foco de diseño principal de Mi Stratos:** Persona A (colaborador individual). Persona B se cubre más fuerte en dashboards de manager.

---

## 2. Historias (Storytelling)

### Historia 1 – “Quiero saber cómo estoy hoy”

- **Situación**: Ana entra a Mi Stratos un lunes a las 9:00 antes de su 1:1.
- **Recorrido ideal**:
  1. Abre Mi Stratos y ve su foto, rol, cubo y un círculo grande de “Match con tu rol”.
  2. Justo debajo, ve 3 KPIs: Potencial, Readiness, Brechas.
  3. Un bloque de texto le dice “Tu siguiente paso recomendado…” con un botón claro para ir a su Ruta de Aprendizaje.
- **Resultado**: En < 1 minuto entiende su situación y tiene un siguiente paso inequívoco.

### Historia 2 – “Quiero cerrar una brecha específica”

- **Situación**: Ana sabe que para llegar a un rol superior debe subir su nivel en una competencia concreta.
- **Recorrido ideal**:
  1. Desde el dashboard pulsa “Mis Brechas”.
  2. Ve una lista corta de brechas priorizadas por impacto.
  3. Pulsa una brecha → ve significado del nivel objetivo, su nivel actual y acciones sugeridas (parte de su ruta).
- **Resultado**: Sale con 1–2 acciones concretas que puede hacer esta semana.

### Historia 3 – “Quiero ver mis logros y sentir progreso”

- **Situación**: Tras varias semanas, Ana quiere comprobar si su esfuerzo se traduce en avances reales.
- **Recorrido ideal**:
  1. En el dashboard ve un widget de logros recientes (quests, badges, evaluaciones).
  2. Puede ir a “Mis Logros” para ver una línea de tiempo de su ADN, quests y credenciales.
- **Resultado**: Percibe progreso tangible y una narrativa positiva de su desarrollo.

---

## 3. Flujos de Actividad

### Flujo 1 – Foto rápida de estado

1. Entrar a Mi Stratos.
2. Ver hero (rol + cubo + chips de contexto) y círculo de “Match con tu rol”.
3. Escanear KPIs: Potencial, Readiness, Skills/Brechas.
4. Clic en CTA “Ver mi Ruta de Aprendizaje”.
5. Llegar a la vista de Ruta con acciones priorizadas.

### Flujo 2 – Explorar y atacar brechas

1. Entrar a Mi Stratos.
2. Sidebar → “Mis Brechas”.
3. Ver lista de brechas ordenadas por impacto/criticidad.
4. Clic en brecha → detalle de competencia y gap.
5. Clic en acción sugerida (“Añadir a mi ruta” / “Ver acciones propuestas”).

### Flujo 3 – Revisar logros y ADN

1. Entrar a Mi Stratos.
2. Ver widget “Mis Logros” en dashboard.
3. Clic en “Ver todos” → “Mis Logros”.
4. Ver línea de tiempo de evaluaciones, quests, badges y, a futuro, enlace al Talent Pass.

---

## 4. Mapa Visual – Jerarquía de la Home de Mi Stratos

### Above the fold (primer pantallazo)

- **Bloque A – Identidad**
  - Foto/avatar + saludo contextual.
  - Nombre completo.
  - Chips: Rol, Cubo, Arquetipo, Departamento, Talento Alto Potencial (si aplica).

- **Bloque B – Estado actual**
  - Círculo grande con “Match con tu rol”.
  - 3–4 KPIs clave:
    - Potencial.
    - Readiness.
    - Aprendizaje.
    - Skills activas + número de brechas.

- **Bloque C – Siguiente paso**
  - Panel tipo “Tu siguiente paso recomendado”.
  - 1 CTA primario:
    - “Ir a mi Ruta de Aprendizaje” **o**
    - “Hablar con Mentor IA”.

### Debajo / secciones secundarias

- Conversaciones (AssessmentChat, 360, pulses).
- Evaluaciones recientes.
- Logros / quests.
- Navegación hacia vistas detalladas (Mis Brechas, Mi Ruta, Mi ADN, Mis Logros).

---

## 5. Checklist UX/UI – Mi Stratos

### Claridad

- [ ] En 5 segundos se entiende quién es el usuario, qué rol tiene y qué tan alineado está (match).
- [ ] Hay un único CTA claramente destacado en el dashboard.

### Enfoque

- [ ] Los KPIs mostrados son máximo 3–4 y todos relevantes para la persona colaborador.
- [ ] Las brechas están accesibles, pero no saturan la vista inicial (van en su sección).

### Consistencia visual (Stratos Glass)

- [ ] Los bloques principales usan patrones visuales de tarjetas glass (no layouts genéricos).
- [ ] Botones y chips siguen el mismo lenguaje visual que el resto de la app.

### Emoción y motivación

- [ ] Hay un lugar visible en la home donde se ven logros/progreso reciente.
- [ ] El tono de textos y microcopys es cercano y aspiracional, no burocrático.

---

## 6. Puntuación UX/UI – Mi Stratos (estado actual aproximado)

### 6.1 Tabla de puntuación

> Nota: puntuación cualitativa basada en el estado actual de `MiStratos/Index.vue` a 13-03-2026. Escala 1–5 (1 muy pobre, 5 excelente).

| Eje                                       | Peso | Score (1–5) | Comentario breve                                                            |
| ----------------------------------------- | :--: | :---------: | ---------------------------------------------------------------------------- |
| Claridad & narrativa                      |  15  |      3      | Hero claro, pero falta un bloque explícito de “siguiente paso” siempre visible. |
| Flujo & orientación (carga cognitiva)     |  15  |      3      | Sidebar simple, pero la ruta principal (estado → acción) no está totalmente guiada. |
| Jerarquía visual & priorización           |  15  |      3      | Hero + KPIs funcionan, aunque hay bastante contenido debajo compitiendo por atención. |
| Estado, feedback & manejo de errores      |  10  |      3      | Buen loading/empty inicial; falta revisar estados vacíos/errores en secciones internas. |
| Consistencia visual (Stratos Glass)       |  15  |      3      | Estética alineada, pero aún mezcla bastante Vuetify clásico con patrones Glass. |
| Accesibilidad & responsividad             |  10  |      3      | Base Vuetify ayuda; falta validación específica (foco, contraste, mobile fine-tuning). |
| Eficiencia & foco en la tarea             |  10  |      4      | Pocos clics para llegar a secciones clave; información principal relativamente directa. |
| Delight & emoción (engagement)            |  10  |      4      | Gamificación, ADN y conversaciones IA dan una sensación diferencial y atractiva. |
| **Total ponderado (/100)**                | 100  |   **64**    | Buen punto de partida, con margen claro de mejora en claridad, glass y accesibilidad. |

### 6.2 Fortalezas, debilidades y acciones

- **Fortalezas**:
  - Hero con identidad fuerte (rol, cubo, chips de contexto).
  - Integración visible de elementos diferenciales: GamificationWidget, ADN, conversaciones IA.
  - Navegación lateral sencilla y fácil de entender.

- **Debilidades**:
  - Falta un bloque dedicado y constante de “Tu siguiente paso” que conecte estado con acción.
  - Mezcla de patrones visuales (Vuetify clásico vs Stratos Glass) que reduce el impacto estético.
  - Accesibilidad y experiencia mobile aún no han sido auditadas ni optimizadas de forma específica.

- **Acciones UX prioritarias (próximo sprint)**:
  1. Implementar un panel “Tu siguiente paso recomendado” fijo en el dashboard (CTA único y claro).
  2. Migrar hero + KPIs principales a componentes/patrones Stratos Glass para un look más consistente.
  3. Hacer una pasada rápida de accesibilidad/responsive: contrastes, tamaños mínimos, breakpoints y foco.

---

## 7. Uso de esta guía

- Este documento sirve como pauta para:
  - Revisar cambios futuros en `MiStratos/Index.vue`.
  - Diseñar features nuevas (ej. UI del Talent Pass) sin romper la experiencia central.
- El mismo formato (Personas → Historias → Flujos → Mapa Visual → Checklist) se reutilizará para:
  - Scenario IQ.
  - Marketplace & Gamificación.
  - Talent 360 / Evaluaciones.

