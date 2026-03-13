# 🌐 Gemelo Digital (Digital Twin) de Stratos

El **Gemelo Digital** es la representación virtual, dinámica y en tiempo real del capital humano y la estructura organizacional dentro de Stratos. No es solo un organigrama estático; es un grafo vivo que captura la "fisiología" de la empresa para permitir simulaciones de alta fidelidad.

---

## 📸 El Concepto: Captura de Estado (State Capture)

Antes de realizar cualquier simulación en **Vanguard** o análisis en el **Impact Engine**, el sistema genera un "Snapshot Semántico". Este proceso extrae la configuración actual de la organización para crear una instancia paralela donde los Agentes IA pueden experimentar sin riesgo.

### Componentes del Gemelo Digital:

1.  **Nodos de Talento (Talent Nodes):**
    - **Identidad:** Skills actuales, niveles de maestría (Cerbero).
    - **Potencial:** Trayectorias probables y velocidad de aprendizaje (Navigator).
    - **Psicometría:** Rasgos de personalidad (OCEAN) y ADN Cultural.

2.  **Malla de Relaciones (Relationship Mesh):**
    - **Jerarquías:** Relaciones formales de reporte.
    - **Red Social:** Colaboraciones, influencias y flujos de comunicación no oficiales.
    - **Simbiosis:** Cómo interactúan los humanos con los Agentes Sintéticos (Digital Workers).

3.  **Peso Económico (Financial Weight):**
    - Costo de payroll, benchmark de reclutamiento y valor generado (HCVA) asociado a cada nodo.

---

## 🧬 Implementación Técnica (`DigitalTwinService`)

El Gemelo Digital se materializa a través del `DigitalTwinService`, que orquestar tres capas de datos:

| Capa                  | Fuente                       | Función en el Gemelo                                      |
| :-------------------- | :--------------------------- | :-------------------------------------------------------- |
| **Org Metadata**      | DB Relacional / Digital Twin | Conteo de headcount, departamentos y estructura básica.   |
| **Semantic Nodes**    | Cerbero / Skills Mesh        | Mapeo detallado de skills y ADN cultural por individuo.   |
| **Pathfinding Edges** | Neo4j / Hierarchies          | Definición de rutas de carrera y puentes de colaboración. |

> [!NOTE]
> La captura de estado (`captureState`) ahora incluye metadatos profundos como IDs de departamento y mapas de skills por persona, permitiendo simulaciones totalmente desacopladas de la base de datos transaccional.

---

## 🧪 Casos de Uso: ¿Para qué sirve?

### 1. Simulaciones "What-If" (Vanguard)

¿Qué pasa si fusionamos el departamento de IT con el de Innovación? El Gemelo Digital permite predecir la **Fricción Cultural** y el **Gap de Skills** resultante antes de mover a una sola persona.

### 2. Proyecciones Financieras (Impact Engine)

El Gemelo Digital permite al Impact Engine proyectar el ROI de un plan de desarrollo. Si el Gemelo muestra que el 40% de los nodos críticos tienen un gap en una skill clave, el motor calcula el costo de la pérdida de productividad.

### 3. Crisis Wargaming (Cascading Risk)

El Gemelo Digital permite simular escenarios de crisis extrema. Gracias a la **Malla de Jerarquías**, el motor puede detectar el **Riesgo en Cascada**:

- Si un nodo "Manager" es removido (attrition), el sistema identifica automáticamente a todos los nodos "Reporte" conectados.
- Se calcula un incremento en el riesgo de fuga o desmotivación para esos nodos dependientes.
- La simulación evalúa la capacidad de la **Malla de Skills** para absorber el impacto mediante sucesores internos con alta proximidad semántica en el grafo.

### 4. Mobility AI Advisor

El Asesor de Movilidad Estratégica utiliza el Gemelo Digital como su **Contexto Maestro**. Al enviar el grafo a la IA:

- El agente detecta silos organizacionales (departamentos sin aristas compartidas).
- Propone movimientos basados en la sinergia de nodos y la optimización de jerarquías, no solo en nombres de roles.
- Utiliza el catálogo de aprendizaje (LMS) para proponer aristas de desarrollo que conecten el estado actual del nodo con el estado objetivo sugerido.

---

## 🤝 Interacción Humano-Sistema (Human-in-the-Loop)

El Gemelo Digital no toma decisiones autónomas; actúa como un **Consultor de Alta Fidelidad** para el operador humano. La interacción se centra en tres puntos críticos:

### 1. El War-Room de Movilidad

- **Entrada Humana:** El líder define un objetivo (ej: "Digitalizar el área de finanzas").
- **Respuesta del Gemelo:** Proyecta el **Efecto Dominó**. Si mueves a 'X', el Gemelo muestra inmediatamente qué nodo queda vacío y sugiere candidatos del grafo para cubrirlo, calculando el riesgo de sucesión en tiempo real.

### 2. Laboratorio de Escenarios (Scenario IQ)

- **Simulación Visual:** El humano interactúa con el `BrainCanvas.vue`, visualizando cómo se estresan o fortalecen las conexiones (aristas) al realizar cambios estructurales masivos.
- **Validación de Fricción:** Antes de aplicar cualquier cambio, el humano revisa el _Cultural Friction Index_ generado por el gemelo para decidir si el costo humano de la reorg es viable.

### 3. El Umbral de Compromiso (Materialización)

Este es el punto de seguridad donde el mundo virtual se une al real.

- El operador debe **aprobar explícitamente** el resultado de la simulación mediante el botón **"Materializar Plan"**.
- Al hacerlo, el Gemelo Digital traduce su estado virtual a un `ChangeSet` real, gatillando los flujos de firma electrónica, notificaciones y actualización de la base de datos transaccional.

> [!IMPORTANT]
> El humano mantiene siempre el control del "freno de emergencia". Ninguna simulación del Gemelo Digital afecta a los colaboradores reales hasta que existe un **Acto de Voluntad** documentado del operador.

---

## 🛡️ Integración con Digital Sentinel (SSS)

Cada snapshot del Gemelo Digital utilizado para una decisión estratégica es sellado criptográficamente. Esto asegura que la base de datos sobre la cual se tomó una decisión (ej. una reestructuración masiva) pueda ser auditada para verificar que los datos no fueron manipulados post-simulación.
