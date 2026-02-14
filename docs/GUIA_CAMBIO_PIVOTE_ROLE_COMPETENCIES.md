# Guía técnica: Evolución del Pivote Role-Competency

Esta guía documenta el cambio estructural en la asociación entre Roles y Competencias en Stratos, pasando de un registro estático a un objeto de decisión para el motor de IA.

## 1. El Cambio de Paradigma

Anteriormente, la relación era: `Rol <-> Competencia (Nivel)`.
Ahora, la relación es: `Rol <-> Competencia (Nivel, Impacto, Evolución, Estrategia)`.

## 2. Nueva Estructura de Datos (`role_competencies`)

| Campo            | Tipo          | Propósito                        | Uso en IA / Algoritmos                                  |
| :--------------- | :------------ | :------------------------------- | :------------------------------------------------------ |
| `required_level` | Integer (1-5) | Nivel mínimo esperado.           | Base para restar el nivel actual y obtener el Gap.      |
| `criticity`      | Integer (1-5) | Importancia del rol.             | Multiplicador de prioridad en el cierre de brechas.     |
| `change_type`    | Enum          | `stable`, `increased`, `new`.    | Clasifica el esfuerzo de transformación en Scenario IQ. |
| `strategy`       | Enum          | `buy`, `build`, `borrow`, `bot`. | Sugerencia del agente IA para resolver el gap.          |
| `notes`          | Text          | Observaciones.                   | Contexto conversacional para decisiones manuales.       |

## 3. Integración con pgvector

Se ha habilitado la extensión `vector` en PostgreSQL 17. Esto permite:

- Almacenar embeddings de las descripciones de las competencias.
- Realizar búsquedas semánticas para encontrar roles similares basados en su perfil de competencias.
- Matching inteligente entre candidatos y requerimientos del rol.

**Nota técnica**: El nombre de la extensión en SQL es `vector` (aunque el paquete del SO sea `pgvector`).

## 4. Documentación Interna (Self-Documenting DB)

Cada columna incluye un `COMMENT` SQL. Esto es crítico porque permite que un LLM (Agente) entienda el significado semántico de cada campo simplemente describiendo la tabla (`\d+ role_competencies`), sin necesidad de documentación externa.

## 5. Ejemplo de consulta para Gap Analysis

```sql
SELECT
    r.name as role,
    c.name as competency,
    rc.required_level,
    rc.criticity,
    rc.strategy
FROM role_competencies rc
JOIN roles r ON r.id = rc.role_id
JOIN competencies c ON c.id = rc.competency_id
WHERE rc.criticity >= 4; -- Ver solo las críticas
```

## 6. Mantenimiento

Para futuras migraciones que requieran lógica vectorial, asegúrese de que el entorno tenga instalado el paquete `postgresql-17-pgvector` a nivel de SO.
