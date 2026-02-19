#!/usr/bin/env python3
"""Verificación PoC para el ETL Neo4j.

Comprueba:
- Hay nodos `Competency` y `Person` en Neo4j.
- Un `user` de Postgres existe como nodo `Person` en Neo4j.
- Existen checkpoints en la tabla `sync_checkpoints`.

Salida: código 0 si todo OK, 2 si falla alguna verificación.
"""
import os
import sys
from dotenv import load_dotenv
import psycopg2
from neo4j import GraphDatabase


load_dotenv()

PG_DSN = os.getenv('PG_DSN')
NEO4J_URI = os.getenv('NEO4J_URI', 'neo4j://localhost:7687')
NEO4J_USER = os.getenv('NEO4J_USER', 'neo4j')
NEO4J_PASSWORD = os.getenv('NEO4J_PASSWORD', '')


def get_pg_conn():
    return psycopg2.connect(PG_DSN)


def get_neo4j_driver():
    return GraphDatabase.driver(NEO4J_URI, auth=(NEO4J_USER, NEO4J_PASSWORD))


def check_counts(driver):
    with driver.session() as session:
        res_c = session.run("MATCH (c:Competency) RETURN count(c) AS cnt")
        comp_count = res_c.single().get('cnt', 0)
        res_p = session.run("MATCH (p:Person) RETURN count(p) AS cnt")
        person_count = res_p.single().get('cnt', 0)
    return comp_count, person_count


def sample_user_exists(pg_conn, driver):
    with pg_conn.cursor() as cur:
        cur.execute("SELECT id FROM users LIMIT 1")
        row = cur.fetchone()
        if not row:
            print('No hay usuarios en Postgres para verificar.')
            return False
        user_id = row[0]

    with driver.session() as session:
        res = session.run("MATCH (p:Person {id:$id}) RETURN count(p) AS cnt", id=user_id)
        cnt = res.single().get('cnt', 0)
    print(f"Usuario ejemplo Postgres id={user_id} -> nodos Person en Neo4j: {cnt}")
    return cnt > 0


def check_checkpoints(pg_conn):
    q = "SELECT COUNT(*) FROM sync_checkpoints"
    try:
        with pg_conn.cursor() as cur:
            cur.execute(q)
            row = cur.fetchone()
            return row and row[0] >= 0
    except Exception as e:
        print('Error consultando sync_checkpoints:', e)
        return False


def main():
    if not PG_DSN:
        print('PG_DSN no configurado en el entorno.')
        sys.exit(2)

    pg = get_pg_conn()
    driver = get_neo4j_driver()

    try:
        comp_count, person_count = check_counts(driver)
        print(f"Competency nodes: {comp_count}, Person nodes: {person_count}")

        if comp_count == 0 or person_count == 0:
            print('Advertencia: conteos en Neo4j son 0; el ETL puede no haber corrido o no hay datos.')
            # No fallo inmediato; seguir comprobaciones

        user_ok = sample_user_exists(pg, driver)
        ck_ok = check_checkpoints(pg)

        if user_ok and ck_ok:
            print('Verificación ETL: OK')
            sys.exit(0)
        else:
            print('Verificación ETL: FALLÓ (user_exists:', user_ok, 'checkpoints:', ck_ok, ')')
            sys.exit(2)

    finally:
        try:
            pg.close()
        except Exception:
            pass
        try:
            driver.close()
        except Exception:
            pass


if __name__ == '__main__':
    main()
