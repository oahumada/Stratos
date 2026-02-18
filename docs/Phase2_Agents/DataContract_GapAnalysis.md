# Datos del Gap (Paso 2) para el Agente

Este contrato define los datos mínimos necesarios que Laravel debe enviar al servicio de Python para que CrewAI pueda realizar un análisis de brechas significativo.

## Estructura JSON

```json
{
    "gap_data": {
        "role_context": {
            "role_id": 123,
            "role_name": "Senior Full Stack Dev",
            "design_purpose": "Liderar el equipo de desarrollo de frontend"
        },
        "competency_context": {
            "competency_name": "React.js",
            "required_level": 4, // (Experto)
            "current_level": 2, // (Intermedio)
            "gap_size": 2
        },
        "talent_context": {
            "current_headcount": 1,
            "talent_status": "Active" // o Vacant
        },
        "market_context": {
            // Opcional, si tenemos datos externos
            "avg_market_salary": 65000
        }
    }
}
```

## Lógica del Agente

El agente recibirá este JSON y deberá responder con un objeto `StrategyRecommendation`:

```python
class StrategyRecommendation(BaseModel):
    strategy: str  # "Buy", "Build", "Borrow", "Bind"
    rationale: str # Explicación detallada del porqué
    estimated_cost: float # Estimado
    time_to_close_gap: str # "3 meses", "Immediate"
    actions: list[str] # Lista de pasos a seguir
```
