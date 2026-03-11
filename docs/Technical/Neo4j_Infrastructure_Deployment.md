# 🌐 Infraestructura Neo4j Live (Bloque B1)

Este documento detalla la implementación y puesta en marcha de la infraestructura de grafos con **Neo4j** para Stratos, cumpliendo con el hito **B1** del Roadmap (Wave 2).

## 1. Visión General

La activación de Neo4j en un entorno "Live" (local/producción) permite que los algoritmos de recomendación, Career Paths, y motores de búsqueda de expertos (Stratos Intel) resuelvan consultas sobre redes complejas de habilidades de forma altamente optimizada. El sistema ahora opera en una arquitectura de base de datos políglota:

- **PostgreSQL**: Sigue operando como la fuente de verdad estructurada primaria y transaccional.
- **Neo4j**: Actúa como un _Knowledge Graph_ sincronizado, mapeando talentos, roles, competencias y evidencias en una topología de red.

## 2. Despliegue del Contenedor (Docker)

La instancia principal de Neo4j ha sido dockerizada y expuesta para consumo directo tanto de los servicios de Python como de Laravel.

```bash
docker run -d \
  --name stratos-neo4j \
  -p 7474:7474 -p 7687:7687 \
  --env NEO4J_AUTH=neo4j/stratos2026 \
  --env NEO4J_apoc_export_file_enabled=true \
  --env NEO4J_apoc_import_file_enabled=true \
  --env NEO4J_apoc_import_file_use__neo4j__config=true \
  neo4j:5
```

### Puertos:

- **7474**: Interfaz HTTP (Browser de Neo4j).
- **7687**: Interfaz Bolt (Conexión binaria eficiente para drivers y aplicaciones).

## 3. Pipeline de Sincronización ETL (Automático)

En lugar de mantener dos bases de datos inconexas o depender de sincronizaciones manuales, hemos automatizado la inyección de datos de PostgreSQL a Neo4j mediante nuestro script ETL especializado y tareas programadas en el Kernel de Laravel.

### Pruebas Manuales

Para disparar el ETL de manera síncrona desde consola:

```bash
php artisan neo4j:sync --via=script
```

_Este comando activa el script de Python en `python_services/neo4j_etl.py`._

### Tareas en Segundo Plano (Cron Jobs)

El puente transaccional se ha establecido y blindado configurando el programador visual en `routes/console.php`. Se estableció la ejecución cíclica de las siguientes tareas clave de infraestructura global:

```php
// Sincronización de Knowledge Graph (Neo4j)
\Illuminate\Support\Facades\Schedule::command('neo4j:sync --via=script')->everySixHours();

// Generación de métricas e IQ mensual (Data Lake)
\Illuminate\Support\Facades\Schedule::command('stratos:capture-snapshots')->monthly();

// Informes de Impactos de AI de Escenarios diarios
\Illuminate\Support\Facades\Schedule::command('stratos:generate-impact-reports')->daily();
```

El servicio ahora corre en el entorno automáticamente cada 6 horas (`everySixHours`), absorbiendo las nuevas personas, competencias, y updates de skills.

## 4. Estado de los Componentes Afectados

La correcta operatividad de Neo4j destraba permanentemente:

- **`CareerPathService.php`**: Las consultas de sucesión ahora enrutan eficientemente cruzando el _Knowledge Graph_ gracias a las relaciones de habilidades que comparten los diferentes roles.
- **`MatchingService.php`**: Las consultas de proximidad para el "Matchmaker" se potenciarán.
- **Python Stratos Intel Service**: Ahora cuenta con el URI persistente local (`bolt://localhost:7687`) para efectuar travesías agénticas en LLM basadas en la corporación real.

> **Nota**: Este despliegue cumple en un 100% el requerimiento **B1** en el Roadmap de Integración, permitiéndonos saltar hacia módulos predictivos (C y D) o las fases móviles, sin preocuparnos por cuellos de botella asíncronos o de almacenamiento topológico.
