#!/usr/bin/env python3
"""Neo4j ETL PoC: extrae filas actualizadas de Postgres y hace upsert en Neo4j.

Notas:
- Configuración via env vars: PG_DSN, NEO4J_URI, NEO4J_USER, NEO4J_PASSWORD, LAST_SYNC (ISO timestamp opcional)
- Este es un PoC: adaptar mapeos a tablas reales del proyecto (nombres de columnas y pivots).
"""
import os
import json
from datetime import datetime

from dotenv import load_dotenv
import psycopg2
from neo4j import GraphDatabase


load_dotenv(dotenv_path=os.path.join(os.path.dirname(__file__), '.env'))

PG_DSN = os.getenv('PG_DSN')  # Ej: postgres://user:pass@host:5432/db
NEO4J_URI = os.getenv('NEO4J_URI', 'neo4j://localhost:7687')
NEO4J_USER = os.getenv('NEO4J_USER', 'neo4j')
NEO4J_PASSWORD = os.getenv('NEO4J_PASSWORD', '')
LAST_SYNC = os.getenv('LAST_SYNC')  # opcional ISO timestamp
JOB_NAME = os.getenv('JOB_NAME', 'neo4j_etl')


def get_pg_conn():
    return psycopg2.connect(PG_DSN)


def get_neo4j_driver():
    return GraphDatabase.driver(NEO4J_URI, auth=(NEO4J_USER, NEO4J_PASSWORD))


def fetch_updated_persons(conn, since):
    q = """
    SELECT id, first_name || ' ' || last_name as name, email, organization_id, updated_at
    FROM people
    WHERE updated_at > %s
    ORDER BY updated_at ASC
    LIMIT 1000
    """
    with conn.cursor() as cur:
        cur.execute(q, (since,))
        cols = [c.name for c in cur.description]
        for row in cur.fetchall():
            yield dict(zip(cols, row))


def fetch_updated_competencies(conn, since):
    q = """
    SELECT id, name, description, updated_at
    FROM competencies
    WHERE updated_at > %s
    ORDER BY updated_at ASC
    LIMIT 1000
    """
    with conn.cursor() as cur:
        cur.execute(q, (since,))
        cols = [c.name for c in cur.description]
        for row in cur.fetchall():
            yield dict(zip(cols, row))


def fetch_person_skills(conn, since):
    q = """
    SELECT ps.people_id as person_id, ps.skill_id, ps.current_level as level, ps.updated_at, s.name as skill_name
    FROM people_role_skills ps
    JOIN skills s ON s.id = ps.skill_id
    WHERE ps.updated_at > %s
    ORDER BY ps.updated_at ASC
    LIMIT 5000
    """
    with conn.cursor() as cur:
        cur.execute(q, (since,))
        cols = [c.name for c in cur.description]
        for row in cur.fetchall():
            yield dict(zip(cols, row))


def ensure_checkpoints_table(conn):
    ddl = """
    CREATE TABLE IF NOT EXISTS sync_checkpoints (
        id bigserial primary key,
        job_name text not null,
        entity text not null,
        organization_id bigint,
        last_synced_at timestamptz,
        created_at timestamptz DEFAULT now(),
        updated_at timestamptz DEFAULT now()
    );
    DO $$ BEGIN
        IF NOT EXISTS (
            SELECT 1 FROM pg_indexes WHERE indexname = 'sync_checkpoints_job_entity_org_unique'
        ) THEN
            CREATE UNIQUE INDEX sync_checkpoints_job_entity_org_unique ON sync_checkpoints (job_name, entity, organization_id);
        END IF;
    END $$;
    """
    with conn.cursor() as cur:
        cur.execute(ddl)
        conn.commit()


def get_last_sync(conn, entity, org_id=None, job_name=JOB_NAME):
    q = """
    SELECT last_synced_at FROM sync_checkpoints
    WHERE job_name = %s AND entity = %s AND organization_id IS NULL
    LIMIT 1
    """
    with conn.cursor() as cur:
        cur.execute(q, (job_name, entity))
        row = cur.fetchone()
        if row and row[0]:
            return row[0].isoformat()
        return None


def set_last_sync(conn, entity, ts, org_id=None, job_name=JOB_NAME):
    q = """
    INSERT INTO sync_checkpoints (job_name, entity, organization_id, last_synced_at, updated_at, created_at)
    VALUES (%s, %s, %s, %s, now(), now())
    ON CONFLICT (job_name, entity, organization_id)
    DO UPDATE SET last_synced_at = EXCLUDED.last_synced_at, updated_at = now();
    """
    with conn.cursor() as cur:
        cur.execute(q, (job_name, entity, org_id, ts))
        conn.commit()


def upsert_competency(tx, comp):
    tx.run(
        """
        MERGE (c:Competency {id:$id})
        SET c.name = $name,
            c.description = $description,
            c.updated_at = $updated_at
        RETURN c
        """,
        id=comp['id'], name=comp.get('name'), description=comp.get('description'), updated_at=comp.get('updated_at')
    )


def upsert_person(tx, person):
    tx.run(
        """
        MERGE (p:Person {id:$id})
        SET p.name = $name,
            p.email = $email,
            p.organization_id = $organization_id,
            p.updated_at = $updated_at
        RETURN p
        """,
        id=person['id'], name=person.get('name'), email=person.get('email'), organization_id=person.get('organization_id'), updated_at=person.get('updated_at')
    )


def upsert_person_skill(tx, ps):
    tx.run(
        """
        MATCH (p:Person {id:$person_id})
        MERGE (s:Skill {id:$skill_id})
        ON CREATE SET s.name = $skill_name
        MERGE (p)-[r:HAS_SKILL]->(s)
        SET r.level = $level, r.updated_at = $updated_at
        RETURN p, s, r
        """,
        person_id=ps['person_id'], skill_id=ps['skill_id'], skill_name=ps.get('skill_name'), level=ps.get('level'), updated_at=ps.get('updated_at')
    )


def main():
    pg = get_pg_conn()
    driver = get_neo4j_driver()

    try:
        ensure_checkpoints_table(pg)

        # Competencies
        comp_since = get_last_sync(pg, 'competencies') or '1970-01-01T00:00:00Z'
        print(f"Sync competencies since={comp_since}")
        max_comp_ts = None
        with driver.session() as session:
            for comp in fetch_updated_competencies(pg, comp_since):
                session.execute_write(upsert_competency, comp)
                if comp.get('updated_at'):
                    max_comp_ts = comp['updated_at'] if not max_comp_ts else max(max_comp_ts, comp['updated_at'])
        if max_comp_ts:
            set_last_sync(pg, 'competencies', max_comp_ts)

        # Persons
        person_since = get_last_sync(pg, 'people') or '1970-01-01T00:00:00Z'
        print(f"Sync people since={person_since}")
        max_person_ts = None
        with driver.session() as session:
            for person in fetch_updated_persons(pg, person_since):
                session.execute_write(upsert_person, person)
                if person.get('updated_at'):
                    max_person_ts = person['updated_at'] if not max_person_ts else max(max_person_ts, person['updated_at'])
        if max_person_ts:
            set_last_sync(pg, 'people', max_person_ts)

        # Person skills (pivots)
        ps_since = get_last_sync(pg, 'people_role_skills') or '1970-01-01T00:00:00Z'
        print(f"Sync people_role_skills since={ps_since}")
        max_ps_ts = None
        with driver.session() as session:
            for ps in fetch_person_skills(pg, ps_since):
                session.execute_write(upsert_person_skill, ps)
                if ps.get('updated_at'):
                    max_ps_ts = ps['updated_at'] if not max_ps_ts else max(max_ps_ts, ps['updated_at'])
        if max_ps_ts:
            set_last_sync(pg, 'people_role_skills', max_ps_ts)

        print("ETL batch complete")

    finally:
        pg.close()
        driver.close()


if __name__ == '__main__':
    main()
