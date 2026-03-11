# 🧠 Introducción a Neo4j: El Motor de Grafos de Stratos

## ¿Qué es Neo4j?

**Neo4j** es un sistema de gestión de bases de datos orientado a grafos (GDBMS) nativo. A diferencia de las bases de datos relacionales tradicionales (como PostgreSQL o MySQL) que almacenan datos de forma tabular (en filas y columnas vinculadas mediante claves foráneas), Neo4j almacena la información altamente interconectada como **nodos**, **relaciones** y **propiedades**.

Es un sistema diseñado especialmente para priorizar y navegar eficientemente por las _conexiones_ entre los datos, en lugar de priorizar únicamente los datos en sí.

---

## 🔑 Conceptos Clave del Modelo de Grafos

El "Property Graph Model" sobre el que se fundamenta Neo4j se compone de cuatro elementos básicos:

1. **Nodos (Nodes):**
   Son los protagonistas del grafo. Equivalentes a los "registros" o filas en las BD relacionales, representan las entidades reales en tu dominio (ej. `Persona`, `Rol`, `Competencia`, `Skill`).

2. **Relaciones (Relationships):**
   Son las conexiones directas entre los nodos. A diferencia de un JOIN en Postgres (que se calcula en cada consulta), **una relación en Neo4j se crea físicamente en el momento en que se guardan los datos**. Toda relación tiene un tipo, una dirección, un inicio y un fin. (Ej. Una persona `APRENDIÓ` un skill).

3. **Propiedades (Properties):**
   Tanto los nodos como las relaciones pueden almacenar atributos como pares de `clave:valor`. (Ej. Un nodo `Persona` puede tener la propiedad `email="juan@empresa.com"`; una relación `APRENDIÓ` podría tener la propiedad `fecha="2026-03-01"`).

4. **Etiquetas (Labels):**
   Sirven para "agrupar" nodos asignándoles un rol o categoría dentro del grafo. Un nodo puede tener una o varias etiquetas simultáneamente. (Ej. `Person`, `Employee`, `Manager`).

---

## 🗣️ Cypher: El Lenguaje Visual

Mientras que en SQL usamos sentencias descriptivas como `SELECT ... FROM ... WHERE ...`, Neo4j utiliza **Cypher**. Cypher es un lenguaje de consultas muy visual que se inspira en el _arte ASCII_ para representar la forma espacial de los datos.

Un ejemplo para buscar a una empleada ("María") y ver a qué proyectos pertenece en lenguaje Cypher se vería así:

```cypher
MATCH (p:Person {name: "Maria"})-[:WORKS_ON]->(proj:Project)
RETURN p, proj
```

_Traducción visual: "Encuentra un nodo p etiquetado Person (cuyo nombre es 'Maria') que apunte a través de la flecha WORKS_ON hacia un nodo proj etiquetado Project"._

---

## 🚀 ¿Por qué Stratos necesita a Neo4j?

En Stratos, el pilar central del almacenamiento seguro es **Postgres**. Todos los días, creamos roles, perfiles y actualizamos diccionarios en Postgres de manera impecable.

Sin embargo, a medida que Stratos avanza hacia su ecosistema de Inteligencia Artificial (Stratos Intel) y el Talent Pass, surgen preguntas de negocio que la arquitectura relacional resuelve de forma muy lenta. Por ejemplo:

- _"¿Quién en toda la organización tiene nivel avanzado en Análisis Cuantitativo, conoce a alguien de Marketing y está a 2 meses de certificarse para reemplazar al VP Financiero ante una renuncia sorpresiva?"_

Responder a esto en bases de datos tradicionales como PostgreSQL requeriría 4 o 5 uniones recursivas o pesados `JOINs` leyendo tablas enteras en la memoria para descartar lo que no sirve. Si la empresa tiene 10,000 empleados, esto se convierte en un infierno de rendimiento (Cuello de botella).

**Neo4j soluciona esto porque el concepto de recorrer redes es nativo en él.**

### Beneficios tangibles para la Plataforma Stratos:

1. **Index-Free Adjacency (Velocidad de Exploración):**
   En Neo4j, cada nodo contiene _punteros físicos_ directos hacia cada uno de los nodos vecinos. Cuando Stratos hace la pregunta anterior, Neo4j encuentra a la primera persona en un nanosegundo y luego simplemente "camina" por la memoria saltando directamente hacia los skills y certificaciones de esa persona, ignorando instantáneamente a los otros 9,999 empleados sin consumir cómputo.

2. **Cálculos de Sucesión (Pathfinding):**
   Con los algoritmos de caminos de Neo4j podemos mapear una brecha de talentos (ej. nuestro servicio de Career Paths). Le decimos a la base de datos "Muéstrame el camino más corto de conocimientos entre la `Persona A` y el `Rol B`".

3. **Soporte Vital para Agentes Sistémicos (LLMs):**
   Proveemos un "Knowledge Graph" (Grafo de Conocimiento). Cuando un Agente Inteligente de Stratos debe planear estrategias de negocio, no le damos tablas JSON aisladas y confusas, le transferimos el grafo de Neo4j para permitirle ver inmediatamente relaciones espaciales entre áreas de la corporación para deducciones sumamente analíticas y avanzadas.

## 🔄 El Ciclo de Vida del Dato en Stratos

Se le llama arquitectura de base de datos **Políglota**. Para sacar lo mejor de ambos mundos:

1. **La Verdad es Postgres:** Un administrador asigna competencias a un talento en la UI. Esa transacción se guarda firmemente en la tabla SQL (ACID, transacciones seguras).
2. **El Puente ETL:** Para no obligarnos a mantener dos bases de forma manual, nuestros Comandos Programados (`php artisan neo4j:sync`) actúan en segundo plano periódicamente.
3. **Poblando la Red:** El script ETL extrae un resumen del nuevo Skill introducido por el administrador desde Postgres y le ordena a Cypher/Neo4j insertar (`MERGE`) un nodo nuevo y dibujar la flecha (Relación) para expandir nuestra red.
4. **La Consulta:** Finalmente, la UI le pide a Neo4j (mediante el StratosIntelService de Python) la ruta de sucesión calculada de ese rol actual y se lo traemos enriquecido en microsegundos al usuario en el mapa organizacional.

---

**\*Conclusión**: Neo4j no reemplaza a Postgres, es una capa consultiva de alto desempeño de Stratos especializada en topologías o "fotografías radiográficas" de cómo están conformados el organigrama y el capital teórico humano.\*
