# 🌌 Concepto: Unificación por Nodo Gravitacional

La **Unificación por Nodo Gravitacional** es la técnica propietaria de Stratos para resolver el problema de la "Esquizofrenia Organizacional" (cuando diferentes sistemas como el ERP y el HRIS tienen versiones distintas de la misma empresa).

## 1. ¿Qué es un "Nodo" en Stratos?

En lugar de una fila en una base de datos relacional, cada Departamento o Área es un **Nodo** en un Grafo Semántico (Neo4j). Este nodo tiene una identidad única y persistente que no depende de su nombre. El nombre puede cambiar de "Ventas" a "Growth & Revenue", pero el **ID del Nodo** sigue siendo el mismo.

## 2. ¿Por qué "Gravitacional"?

El concepto de **Gravedad** se usa porque este nodo actúa como un centro de masa que "atrae" y ancla datos de orígenes heterogéneos:

- **Atracción de Personas (HRIS):** Los empleados orbitan este nodo mediante relaciones de pertenencia.
- **Atracción de Capital (ERP):** Los presupuestos y gastos "caen" en este nodo mediante mapeos de centros de costo.
- **Atracción de Habilidades:** La malla de competencias (Skill Mesh) se ancla a este nodo para definir la capacidad instalada.

Sin importar lo que diga el sistema externo, si el dato tiene una coordenada que lo vincula a este "Centro de Masa", Stratos lo unifica automáticamente.

## 3. Mecanismo de Unificación (Cómo funciona)

### A. El "Alias Layer" (Capa de Mapeo)

El Nodo Gravitacional posee una lista de "nombres conocidos" en otros sistemas.

- _Nodo Gravitacional ID 45_
- _Alias SAP:_ `CC-1002-VNT`
- _Alias Workday:_ `Sales_Dept_Global`
- _Alias Manual:_ `Ventas Chile`

Cuando el comando de ingesta recibe un CSV, no busca un nombre exacto; busca qué nodo tiene la "gravedad" necesaria para reclamar ese dato basándose en sus alias.

### B. Consciencia Temporal (Time-Travel)

La estructura de la organización cambia, pero la gravedad deja rastro. Si el departamento de "Marketing" fue absorbido por "Ventas" hace 3 meses, el Nodo Gravitacional conserva la historia de esa fusión. Si recibes un reporte contable de hace 6 meses, Stratos sabe que ese dinero debe anclarse al nodo original, permitiendo un **análisis de impacto retroactivo** coherente.

### C. Resolución por Peso de Nómina (Tie-Breaking)

Si existe una ambigüedad (ej: una métrica de ingresos que podría pertenecer a dos áreas), el sistema aplica una regla de **Masa Crítica**: el nodo que tenga la mayor concentración de nómina (salarios) o personas vinculadas a la ejecución de esa métrica, atrae el dato automáticamente. De ahí el nombre "Gravitacional": la masa humana atrae el dato financiero.

## 4. El Valor para el Negocio

- **Adiós a la Conciliación Manual:** Ya no necesitas que IT pase semanas haciendo "VLOOKUPs" entre el SAP y el archivo de RRHH.
- **Lente de Realidad Única:** El CEO ve una visión unificada donde el gasto financiero está directamente conectado con el talento que lo genera, sin importar que los sistemas hablen idiomas distintos.

---

> [!NOTE]
> En resumen: Un **Nodo Gravitacional** es un ancla de identidad en el Grafo que sobrevive a los cambios de nombre y a las discrepancias entre sistemas, asegurando que Stratos siempre sepa "de quién es" cada dólar y cada habilidad.
