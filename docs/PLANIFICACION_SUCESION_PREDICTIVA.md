# 🎯 Planificación de Sucesión Predictiva (Trayectoria Inversa)

Este módulo implementa el algoritmo de **Trayectoria Inversa**, diseñado para identificar proactivamente a los mejores sucesores para cualquier rol clave dentro de la organización. A diferencia del matching tradicional, este motor utiliza el historial de movimientos y la velocidad de aprendizaje para predecir la preparación real.

## 1. El Algoritmo de Readiness (Preparación)

El score de preparación (*Readiness Score*) se calcula mediante una ponderación de cuatro dimensiones críticas:

| Dimensión | Peso | Origen de Datos | Propósito |
| :--- | :--- | :--- | :--- |
| **Skill Match** | 40% | PeopleRoleSkills ↔ RoleSkills | Valida la brecha técnica actual. |
| **Learning Velocity** | 25% | Historial de Evaluaciones | Mide qué tan rápido la persona cierra gaps. |
| **Stability Index** | 20% | PersonMovement (12 meses) | Evalúa el riesgo de rotación si se mueve de nuevo. |
| **Trajectory Fit** | 15% | Historial de Promociones | Valida si su carrera sigue una línea lógica. |

### Bono por Alto Potencial
Si un colaborador está marcado como `is_high_potential`, el sistema otorga un bono estratégico de **+10 puntos** sobre el score final, reflejando la apuesta de la compañía por su crecimiento.

---

## 2. Niveles de Disponibilidad

El sistema clasifica a los sucesores en cuatro cuadrantes:

1.  **Listo Ahora (Successor A)** (Score 85+): Candidato con fit técnico casi total y trayectoria probada.
2.  **Listo Corto Plazo (6-12m)** (Score 70-84): Candidato fuerte que requiere pulir competencias específicas.
3.  **Desarrollo Necesario (1-2y)** (Score 50-69): Talento con potencial que necesita un plan de desarrollo estructurado.
4.  **Potencial Emergente (2y+)** (Score <50): Identificación temprana de talento joven.

---

## 3. Integración con el Gemelo Digital

Las predicciones de sucesión alimentan el **Gemelo Digital** permitiendo simulaciones de "What-if":
- *¿Qué pasa si el Director Comercial se va mañana?*
- El sistema identifica al sucesor A, calcula el impacto en su departamento actual y sugiere un efecto cascada de promociones para cubrir los huecos resultantes.

---

## 4. Endpoints de la API

| Endpoint | Método | Descripción |
| :--- | :--- | :--- |
| `/api/talent/succession/role/{id}` | `GET` | Lista de top sucesores para un rol. |
| `/api/talent/succession/analyze-candidate` | `POST` | Análisis profundo de un candidato ↔ rol. |

---

> [!TIP]
> **Optimización de Estabilidad**: El sistema penaliza a candidatos que han tenido más de 2 traslados laterales en el último año, priorizando la estabilidad del nodo organizacional antes de una nueva transición.
