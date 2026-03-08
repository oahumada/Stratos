# 🧬 Especificación Técnica: Timeline Evolutivo (DNA)

**Status:** ✅ Implementado (Backend Core)  
**Fecha:** 8 de Marzo de 2026  
**Versión:** 1.0  
**Responsable:** Stratos Architecture Group

---

## 📋 1. Visión y Propósito

El **Timeline Evolutivo (DNA)** es la visualización histórica del crecimiento de un colaborador en Stratos. A diferencia de un currículum estático, el DNA es una secuencia dinámica de hitos que demuestran la evolución real de capacidades, compromiso y reconocimiento.

Este componente es vital para:

1.  **Transparencia de Desarrollo:** El colaborador ve exactamente cómo cada acción impacta su perfil.
2.  **Toma de Decisiones (HR/Líderes):** Permite identificar la velocidad de aprendizaje (Learning Velocity) de un talento.
3.  **Gamificación:** Conecta los "Quests" y "Badges" con la trayectoria profesional.

---

## 🏗️ 2. Arquitectura de Datos (Event Aggregator)

El sistema no utiliza una única tabla de historial, sino que actúa como un **Agregador de Eventos** que consulta diversas fuentes de verdad:

| Fuente          | Evento en Timeline | Datos Extraídos                                         |
| :-------------- | :----------------- | :------------------------------------------------------ |
| `evaluations`   | `skill_evolution`  | Cambio en nivel calculado (N), Gaps, Confianza.         |
| `people_points` | `xp_gain`          | Cantidad de puntos y motivo (ej: "Mentoría realizada"). |
| `people_badges` | `badge_award`      | Insignia obtenida, icono y color.                       |
| `person_quests` | `quest_complete`   | Misión finalizada y recompensa asociada.                |

### Orquestador: `DnaTimelineService.php`

Este servicio se encarga de:

1.  Recuperar registros de todas las fuentes mencionadas.
2.  Normalizarlos a un esquema común de eventos.
3.  Cálculo de estadísticas rápidas (nº de habilidades analizadas, total XP, etc.).
4.  Ordenamiento cronológico inverso (el evento más reciente primero).

---

## 🛠️ 3. API y Endpoints

### `GET /api/people/profile/{id}/timeline`

**Respuesta Exitosa (Resumen):**

```json
{
    "success": true,
    "data": {
        "person": {
            "full_name": "Roberto García",
            "current_role": "Senior Fullstack Dev",
            "total_xp": 1250
        },
        "timeline": [
            {
                "date": "2026-03-08",
                "type": "quest_complete",
                "title": "Misión Cumplida: Arquitecto de IA",
                "description": "Completó la ruta de transformación de capas vectoriales.",
                "status": "completed",
                "icon": "mdi-check-decagram"
            },
            {
                "date": "2026-02-15",
                "type": "skill_evolution",
                "title": "Evolución en Python",
                "description": "Nivel alcanzado: 4.2 (Requerido: 4.0)",
                "status": "success",
                "icon": "mdi-trending-up"
            }
        ],
        "stats": {
            "skills_analyzed": 12,
            "badges_count": 3,
            "quests_completed": 5
        }
    }
}
```

---

## 🎨 4. Visualización Recomendada (Frontend)

Para el componente `EvolutionaryTimeline.vue`, se recomienda:

- **Vertical Stepper CSS:** Un hilo conductor visual que conecta los iconos de cada evento.
- **Color coding:**
    - Azul/Info para XP.
    - Verde/Success para Medallas y Habilidades completadas.
    - Naranja/Warning para Gaps o misiones activas.
- **Filtros:** Permitir al usuario filtrar por tipo de hito (ej: "Ver solo Habilidades").

---

## 🚀 5. Próximos Pasos (DNA 2.0)

1.  **Predicción de Trayectoria:** Cruzar el timeline con el **CareerPathService** para proyectar la fecha estimada de llegada al siguiente rol.
2.  **Integración con Audit Trail:** Incluir decisiones de mánagers (promociones, movimientos de equipo) que actualmente solo están en logs de IA.
3.  **DNA Público/Privado:** Configuración de privacidad para que el colaborador elija qué hitos compartir con su comunidad de mánagers.

---

> _"En Stratos, tu historia no se escribe, se construye paso a paso."_
