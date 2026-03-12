# Mapa de Relaciones Cerbero (Cerbero Relationship Map)

El **Mapa de Relaciones Cerbero** es el núcleo de gobernanza y orquestación social de Stratos. Define explícitamente la red social corporativa (Quién es quién y cómo se relacionan) para eliminar la ambigüedad en procesos de evaluación, retroalimentación y flujo de información.

---

## 1. Filosofía de Diseño

A diferencia de los organigramas tradicionales rígidos, Cerbero utiliza un modelo de **Grafo de Relaciones** (Graph-based) que permite:

- **Estructuras Matriciales:** Una persona puede tener múltiples jefes (ej: Jefe de Función y Jefe de Proyecto).
- **Reciprocidad Dinámica:** Las relaciones no son solo descendentes; se mapean en 360°.
- **Verdad Única:** Todos los módulos de Stratos (Evaluación 360, IA Mentor, Desarrollo de Carrera) consultan esta única fuente para identificar evaluadores y stakeholders.

---

## 2. Estructura de Datos (`people_relationships`)

La tabla `people_relationships` gestiona estas conexiones mediante el siguiente esquema:

| Campo               | Tipo      | Descripción                                                                |
| :------------------ | :-------- | :------------------------------------------------------------------------- |
| `person_id`         | ForeignID | ID de la persona "Sujeto" (Pivote central).                                |
| `related_person_id` | ForeignID | ID de la persona con la que se vincula.                                    |
| `relationship_type` | Enum      | Tipo de vínculo: `manager`, `peer`, `subordinate`, `mentor`, `other`.      |
| `metadata`          | JSON      | (Opcional) Detalles como peso de la opinión o fecha de inicio del vínculo. |

### Tipos de Relación Detallados:

1.  **Manager (Jefe):** Persona con autoridad directa o funcional. Su peso en las evaluaciones (360) suele ser el más alto (ej: 40%).
2.  **Peer (Par):** Colegas con el mismo nivel jerárquico o que colaboran estrechamente. Aportan la visión del "día a día".
3.  **Subordinate (Subordinado):** Reportes directos. Aportan la visión sobre habilidades de liderazgo y gestión de personas.
4.  **Mentor:** Relación de coaching o guía técnica, independiente de la jerarquía.

---

## 3. Funcionamiento en el Ciclo 360 (Orquestación Inteligente)

Cuando el **Mando de Control** dispara una evaluación para un colaborador "X", el motor Cerbero realiza los siguientes pasos automáticos:

1.  **Identificación de Evaluadores:** Consulta todas las relaciones donde `person_id = X`.
2.  **Filtrado por Rol:**
    - Si encuentra un `related_person_id` con tipo `manager`, dispara una solicitud de feedback de tipo Supervisor.
    - Si encuentra tipo `peer`, dispara solicitud de tipo Par.
3.  **Inyección de Banco de Preguntas:** Dependiendo del `relationship_type` de Cerbero, el sistema selecciona las preguntas del `SkillQuestionBank` que tengan el `target_relationship` coincidente.
    - _Ejemplo:_ A un Par se le pregunta sobre "Colaboración", al Jefe sobre "Resultados".

---

## 4. Integración Técnica (Laravel Model)

El modelo `People` ha sido extendido con métodos de conveniencia para acceder rápidamente al Mapa Cerbero:

```php
// Ejemplo de uso en código
$carlos = People::find(1);

// Obtener jefes directos
$jefes = $carlos->managers;

// Obtener compañeros de equipo
$equipo = $carlos->peers;

// Disparar lógica solo a subordinados
foreach($carlos->subordinates as $sub) { ... }
```

---

## 5. Casos de Uso Avanzados

- **Detección de "Silos":** Cerbero puede analizar departamentos con pocas relaciones de "Peer" cruzadas entre sí.
- **Sucesión Automática:** Si un Manager deja la empresa, Cerbero identifica inmediatamente la "Unidad de Comando" que queda acéfala para disparar alertas de sucesión.
- **IA Mentor:** El agente de IA utiliza Cerbero para saber a quién puede sugerir como mentor basado en las relaciones exitosas mapeadas en el sistema.

---

## 6. Integridad de los Datos (Stratos Sentinel)

Para garantizar que el Mapa de Relaciones y sus ejecuciones (como evaluaciones 360) sean inviolables:

- **Sello Digital:** Las solicitudes de evaluación (`AssessmentRequest`) y sesiones de análisis generadas vía Cerbero son selladas criptográficamente.
- **Inmutabilidad de Hecho:** Cualquier cambio manual en las relaciones o resultados invalida el sello Sentinel, asegurando auditoría total sobre la red social corporativa.

---

**Estado del Módulo:** Activo y Vinculado al Motor 360.  
**Responsable Técnico:** Stratos Intelligence Engine.
