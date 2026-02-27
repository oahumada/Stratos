# Documentación de Arquitectura: Knowledge Graph con Neo4j en Stratos

## 1. Visión General
Stratos utiliza una arquitectura de persistencia políglota donde **PostgreSQL** actúa como la base de datos transaccional (Sistema de Registro) y **Neo4j** actúa como el motor de grafos (Sistema de Conocimiento). Esta combinación permite gestionar eficientemente las transacciones diarias y, al mismo tiempo, realizar análisis profundos sobre la red de talento y competencias.

## 2. Propósito y Casos de Uso
El uso de Neo4j en Stratos está impulsado por la necesidad de resolver consultas complejas que involucran múltiples niveles de relación, las cuales serían ineficientes en un modelo relacional tradicional:

*   **Análisis de Redundancia y Riesgo**: Identificar "puntos únicos de fallo" en el talento (ej: habilidades críticas poseídas por una sola persona).
*   **Descubrimiento de Expertos**: Encontrar personas con habilidades específicas navegando a través de la jerarquía de competencias.
*   **Caminos de Desarrollo**: Sugerir rutas inteligentes para cerrar brechas de competencias basadas en la proximidad de habilidades dentro del grafo.
*   **Contexto para Agentes de IA**: Proporcionar a los Agentes de Talento una visión relacional y holística de la organización para recomendaciones más precisas.

## 3. Modelo de Datos del Grafo (Ontología)

### Nodos Principales
*   `Person`: Representa a los colaboradores (id, name, email, organization_id).
*   `Role`: Posiciones organizacionales.
*   `Competency`: Agrupadores de capacidades estratégicas.
*   `Skill`: Habilidades específicas y medibles.
*   `Evidence`: Pruebas de dominio (certificaciones, evaluaciones, proyectos).
*   `Project`: Contexto donde se han aplicado las habilidades.

### Relaciones Clave
*   `(Person)-[:HAS_SKILL {level, score}]->(Skill)`
*   `(Skill)-[:IS_PART_OF]->(Competency)`
*   `(Person)-[:HOLDS_ROLE]->(Role)`
*   `(Role)-[:REQUIRES]->(Competency)`
*   `(Evidence)-[:EVIDENCE_OF]->(Skill)`

## 4. Proceso de Sincronización (ETL)

La sincronización entre Postgres y Neo4j es **incremental** e **idempotente**, gestionada a través de un pipeline especializado.

### Arquitectura del ETL
1.  **Extracción**: El script `python_services/neo4j_etl.py` consulta Postgres buscando registros que han cambiado desde el último checkpoint (`updated_at > last_synced_at`).
2.  **Transformación**: Los datos relacionales y las tablas pivot (como `person_skill`) se normalizan en estructuras compatibles con grafos.
3.  **Carga (Upsert)**: Se utiliza el comando `MERGE` de Cypher para asegurar que los nodos y relaciones se creen o actualicen sin generar duplicados.

### Componentes de Control
*   **Checkpoints**: La tabla `sync_checkpoints` en Postgres registra el timestamp de la última sincronización exitosa por cada entidad y organización.
*   **Orquestación**: 
    *   **Laravel**: Comandos como `php artisan app:run-neo4j-sync` permiten disparar la carga manualmente o mediante tareas programadas.
    *   **Python Intel Service**: Expone endpoints como `/sync` (para disparar el proceso) y `/status` (para monitorear el progreso).

## 5. Consultas Típicas (Cypher)

### Encontrar el dominio de una habilidad y su redundancia
```cypher
MATCH (p:Person {id: $personId})-[:HAS_SKILL]->(s:Skill)
OPTIONAL MATCH (other:Person)-[:HAS_SKILL]->(s)
WHERE other.id <> $personId
RETURN s.name AS habilidad, count(DISTINCT other) AS otros_poseedores
```

### Identificar expertos para una competencia
```cypher
MATCH (c:Competency {id: $compId})<-[:IS_PART_OF]-(s:Skill)<-[h:HAS_SKILL]-(p:Person)
RETURN p.name, h.score
ORDER BY h.score DESC
```

## 6. Seguridad y Multitenancy
Stratos es una plataforma multitenant. El aislamiento de datos en Neo4j se garantiza mediante la propiedad `organization_id` presente en todos los nodos y relaciones sensibles, asegurando que las consultas y el proceso de ETL siempre estén filtrados por la organización del usuario autenticado.

---
> [!NOTE]
> Para detalles de ejecución técnica, consulte el archivo [NEO4J_ETL_RUN.md](file:///home/omar/Stratos/docs/Architecture/NEO4J_ETL_RUN.md).
