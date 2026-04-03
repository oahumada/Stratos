# Manual Operativo: Movilidad Estratégica y Sucesión

Este documento detalla el funcionamiento de los módulos de Movilidad y Sucesión, integrados con el Hub de LMS para un plan de desarrollo accionable.

## 1. Simulación de Movilidad (War Room)

El **Mobility War Room** permite simular movimientos de talento y entender su impacto financiero y operativo.

### Proceso de Simulación:
1. **Selección de Meta**: Definir la posición objetivo.
2. **Candidatos**: El sistema sugiere candidatos internos basados en afinidad de DNA.
3. **Análisis de Impacto**:
   - **ROI**: Calcula el ahorro al evitar reclutamiento externo vs. costo de capacitación interna.
   - **Gaps de Skill**: Identifica exactamente qué le falta al candidato.
   - **LMS Hub**: Busca automáticamente cursos reales en proveedores conectados (LinkedIn Learning, Udemy, Internos) para cerrar esos gaps.

### Efecto Dominó (Succession Chain):
Al mover a una persona, se genera una vacante. El sistema visualiza la "cadena de sucesión" en cascada, mostrando quién podría cubrir la posición que queda libre, manteniendo la continuidad operativa.

## 2. Planificación de Sucesión

El módulo de **Sucesión** se enfoca en la preparación a largo plazo.

### Funcionalidades clave:
- **Deep Dive de Candidatos**: Análisis detallado de trayectoria, afinidad y preparación.
- **Plan de Desarrollo Dinámico**: A diferencia de planes estáticos, vincula cursos reales del LMS basados en las brechas técnicas detectas.
- **Formalización**: Al formalizar un plan, se persiste en la base de datos para seguimiento histórico.

## 3. Persistencia y Ejecución

### Materialización (Mobility):
Cuando un escenario de movilidad es "Materializado":
1. Se crea un **ChangeSet** (Set de Cambios).
2. Si el cambio se aplica:
   - Se actualiza la posición del colaborador.
   - Se crea automáticamente una **Vacante** para la posición que dejó.
   - Se crea un **Plan de Desarrollo** (`DevelopmentPath`) con acciones específicas de LMS vinculadas.

### Seguimiento de Progreso y Ejecución:
En la pestaña de **Seguimiento de Ejecución**, se centraliza la gestión de los planes que han pasado de la simulación a la realidad:
- **Detalle de Plan**: Visualización profunda de los caminos de desarrollo (`DevelopmentPath`) generados para cada colaborador movido.
- **Interacción con LMS**: 
    - **Lanzamiento Directo**: Posibilidad de iniciar el curso en la plataforma externa (LinkedIn, Udemy, etc.) con un clic.
    - **Sincronización de Avance**: Botón para actualizar el estado del curso directamente desde la API del proveedor.
- **Gamificación**: La finalización de acciones de desarrollo (`DevelopmentAction`) otorga automáticamente puntos de experiencia (**XP**) al perfil del colaborador, incentivando la cultura de aprendizaje.

## 4. Arquitectura y Endpoints Clave

### Servicios de Dominio:
- **LmsService**: Orquestador que consulta múltiples proveedores (Moodle, LinkedIn Learning, Udemy, Mock).
- **MobilitySimulationService**: Motor estratégico que calcula el ROI basado en costos de reclutamiento evitados vs. capacitación.
- **MobilityAIAdvisorService**: Integración con LLM para proponer escenarios de movilidad basados en objetivos de negocio (ej. "Expansión a Latam").

### API Endpoints (Tracking):
- `GET /api/strategic-planning/mobility/execution-status`: Listado de ChangeSets originados en movilidad.
- `GET /api/strategic-planning/mobility/execution/{id}`: Detalle de un plan y sus trayectorias de desarrollo.
- `POST /api/strategic-planning/mobility/execution/launch/{actionId}`: Genera la URL de lanzamiento para el LMS.
- `POST /api/strategic-planning/mobility/execution/sync/{actionId}`: Sincroniza el estado de completitud.

## 5. Diseño Visual (Stratos Glass)
El módulo implementa el sistema de diseño **Stratos Glass**, caracterizado por:
- **Glassmorphism**: Tarjetas con desenfoque de fondo y bordes translúcidos.
- **Micro-interacciones**: Animaciones de entrada (`animate-fade-in`) y estados pulsantes para indicadores críticos.
- **Visualización de Datos**: Heatmaps de sucesión y barras de progreso dinámicas con colores condicionales según el nivel de riesgo o fit.
