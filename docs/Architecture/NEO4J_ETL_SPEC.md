# Especificación ETL: Postgres → Neo4j

Resumen breve

- Propósito: mantener un Knowledge Graph (Neo4j) sincronizado con la fuente transaccional (Postgres) para consultas de multi-grado, análisis de impacto y soporte a agentes.
- Alcance: tablas principales relacionadas con competencias, skills, roles, personas, evidencias y proyectos.

Modelo propuesto (nodos y relaciones)

- Nodos:
    - `Person` (id: bigint, name, email, organization_id)
    - `Role` (id: bigint, name, organization_id)
    - `Competency` (id: bigint, key, name, description)
    - `Skill` (id: bigint, key, name, level_metadata)
    - `Evidence` (id: bigint, type, source, date)
    - `Project` (id: bigint, name, start_date, end_date)

- Relaciones (ejemplos):
    - `(p:Person)-[:HOLDS_ROLE {since,date}]->(r:Role)`
    - `(r:Role)-[:REQUIRES {weight}]->(c:Competency)`
    - `(p:Person)-[:HAS_SKILL {level,score,proven_by:evidence_id}]->(s:Skill)`
    - `(s:Skill)-[:IS_PART_OF]->(c:Competency)`
    - `(e:Evidence)-[:EVIDENCE_OF]->(s|c)`
    - `(p:Person)-[:WORKED_ON]->(proj:Project)`

Principios ETL

- Idempotencia: usar `MERGE` por `id` y propiedades únicas (ej: `competency.id`, `person.id`).
- Incremental: sincronizar por campos `updated_at`/`modified_at`. Mantener un registro de `last_synced_at` por entidad.
- Atomicidad por lote: procesar en batches y confirmar progresos (checkpoint) para reanudar.
- Mapeo de identidades: usar `organization_id` para multitenancy y particionar carga.

Índices y constraints recomendados

- `CREATE CONSTRAINT ON (c:Competency) ASSERT c.id IS UNIQUE` (y análogos para `Person`, `Role`, `Skill`).
- Índices en propiedades consultadas: `:Person(organization_id)`, `:Skill(key)`, `:Role(name)`.

Estrategia de sincronización (alto nivel)

1. Detectar filas actualizadas en Postgres (WHERE updated_at > last_synced_at).
2. Extraer datos pivot (pivots: role_competency, person_skill, role_person) y normalizar.
3. En Neo4j, para cada entidad: `MERGE` nodo por `id` y `SET` propiedades; para relaciones usar `MERGE` en patrón `(a)-[r:TYPE]->(b)` y `SET` metadatos.
4. Guardar checkpoints al final del batch (por entidad y organización).

Consideraciones de diseño

- Tolerancia a fallos: retries exponenciales en escritura Neo4j, logging y métricas.
- Volumen: si el grafo se vuelve muy grande, particionar por organización (sharding lógico) o usar sub-graphs.
- Seguridad: credenciales gestionadas por secretos (Vault / env vars), no en repositorio.

Ejemplos de queries Cypher (casos de uso clave)

1. Análisis de impacto: ¿qué habilidades se pierden si `Person` X se va?

MATCH (p:Person {id:$personId})-[:HAS_SKILL]->(s:Skill)
RETURN s.id AS skillId, s.name AS skillName

Para incluir dominio de redundancia (quién más tiene la skill):

MATCH (p:Person {id:$personId})-[:HAS_SKILL]->(s:Skill)
OPTIONAL MATCH (other:Person)-[h:HAS_SKILL]->(s)
WHERE other.id <> $personId
RETURN s.id AS skillId, s.name AS skillName, count(DISTINCT other) AS other_holders

2. Encontrar expertos para una competencia (ordenado por score/level):

MATCH (c:Competency {id:$competencyId})<-[:IS_PART_OF]-(s:Skill)<-[h:HAS_SKILL]-(p:Person)
RETURN p.id AS personId, p.name AS personName, h.level AS level, h.score AS score
ORDER BY coalesce(h.score,0) DESC

3. Camino de aprendizaje óptimo (prerrequisitos => `PREREQ` edges)

MATCH (start:Competency {id:$startId}), (goal:Competency {id:$goalId})
CALL apoc.algo.dijkstra(start, goal, 'PREREQ>', 'weight') YIELD path, weight
RETURN path, weight

4. Vecindad / influencia informal (2 saltos):

MATCH (p:Person {id:$personId})-[*1..2]-(neigh)
RETURN neigh, length((p)-[*..2]-(neigh)) AS hops LIMIT 200

5. Recomendar formación (buscar cursos conectados a competencias objetivo):

MATCH (c:Competency {id:$competencyId})<-[:COVERS]-(course:Training)
RETURN course

Ejemplo de upsert Cypher (Competency)

MERGE (c:Competency {id: $id})
SET c.key = $key,
c.name = $name,
c.description = $description,
c.updated_at = $updated_at
RETURN c

Archivos sugeridos a crear

- `python_services/neo4j_etl.py` — script de ejemplo que hace extracción incremental y upsert en Neo4j.
- `docs/Architecture/NEO4J_ETL_SPEC.md` — este documento.

Próximos pasos

- Implementar un PoC con un subset (Competency, Skill, Person) y validar latencias de consulta.
- Automatizar la ejecución desde un `Job` en Laravel (`AnalyzeTalentGap` o similar) o un cron en `python_services`.
