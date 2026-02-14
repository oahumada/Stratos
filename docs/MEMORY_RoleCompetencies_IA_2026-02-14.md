# Memory: Re-arquitectura de Role Competencies e Integración Vectorial (2026-02-14)

## Contexto
Se identificó la necesidad de evolucionar la tabla pivote `role_competencies` para soportar funcionalidades avanzadas de IA, planificación de escenarios y análisis de brechas (Gap Analysis). Anteriormente era una tabla de asociación simple; ahora es un componente núcleo de decisión en el grafo de talento.

## Hito: IA-Ready Role Competencies
Se ha implementado una nueva estructura para la asociación entre Roles y Competencias que permite a los agentes de IA y algoritmos de optimización sugerir estrategias de cierre de brechas.

### Acciones Realizadas
1.  **Migración Evolutiva**: Se creó la migración `2026_02_14_183557_create_role_competencies_table_v2.php` que redefine la tabla con campos de decisión (criticidad, tipo de cambio, estrategia).
2.  **Instalación de pgvector**: Se instaló la extensión `postgresql-17-pgvector` en el servidor y se activó en la base de datos `stratosDB`.
3.  **Documentación en Esquema**: Se añadieron comentarios SQL directos en la base de datos para facilitar el consumo por parte de LLMs que inspeccionen el esquema.

### Componentes Clave
-   **required_level**: Benchmark para Gap Analysis.
-   **criticity**: Factor de ponderación para algoritmos de prioridad.
-   **change_type**: Metadato para Scenario IQ (impacto de transformación vs mantenimiento).
-   **strategy**: Campo de acción para agentes IA (Buy, Build, Borrow, Bot).
-   **pgvector**: Habilitado para futuras búsquedas semánticas de competencias y roles.

## Impacto en el Proyecto
-   **Scenario Planning**: Capacidad de proyectar cómo cambian los niveles requeridos en diferentes estados futuros.
-   **IA Integration**: Los agentes ahora tienen un lugar estructurado para persistir recomendaciones de talento.
-   **Performance**: Índices optimizados para consultas cruzadas por rol y competencia.

---
**Estado**: Completado y Migrado.
**Próximos Pasos**: Implementar la lógica de cálculo de brechas utilizando estos nuevos metadatos.
