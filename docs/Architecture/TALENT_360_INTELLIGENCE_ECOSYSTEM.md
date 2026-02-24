# Documento Maestro: Ecosistema de Inteligencia Talento 360

Este documento consolida la arquitectura, gobernanza y estándares de diseño del sistema **Talento 360 & Cerbero** en Stratos. Define cómo la plataforma transfiere datos transaccionales en insights estratégicos accionables mediante IA explicable.

---

## 1. Gobernanza Cerbero: La Columna Vertebral

El ecosistema se rige por dos pilares de gobernanza que aseguran automatización y precisión:

### 1.1 Mapa de Relaciones (Cerbero Relationship Map)

A diferencia de sistemas estáticos, Cerbero utiliza una matriz de relaciones explícitas:

- **Modelo:** Almacenado en `people_relationships`.
- **Estructura:** Permite relaciones matriciales (múltiples jefes), mentorías y redes de pares.
- **Uso:** El orquestador 360 consulta este mapa para identificar automáticamente quién debe evaluar a quién, eliminando la carga administrativa de configurar evaluaciones manualmente.

### 1.2 Unidad de Comando (Assessment Policies)

Centralización de la estrategia de medición:

- **Configuración:** Definida en el **Command Center**.
- **Reglas:** Determina la periodicidad (ej: trimestral), disparadores (ej: promoción) y el "target" (departamento o toda la empresa).
- **Control:** Permite a la organización mantener un estándar de evaluación constante sin intervención humana recurrente.

---

## 2. Recolección Multimodal de Datos

Stratos combina fuentes subjetivas y objetivas para un diagnóstico de alta fidelidad:

1.  **Entrevista Psicométrica IA:** El sujeto conversa con un agente experto que utiliza **DeepSeek R1** para indagar en incidentes críticos, valores y potencial. No es un test; es una experiencia de diálogo.
2.  **Feedback 360° (Feedback Loop):** Manager, pares y subordinados aportan su visión mediante formularios basados en **BARS (Behaviorally Anchored Rating Scales)**.
3.  **Capa de KPIs:** Integración de datos de desempeño histórico para contrastar el "ser" (psicometría) con el "hacer" (resultados).

---

## 3. El Córtex de Inteligencia y Explicabilidad (XAI)

Este es el diferencial competitivo de Stratos. La IA no es una caja negra; es un **colaborador auditable**.

### 3.1 AI Reasoning Flow (Flujo de Razonamiento)

Cada informe de inteligencia incluye un resumen de los pasos lógicos que la IA tomó para llegar a sus conclusiones. Esto permite al líder de TH o al Gerente entender el criterio del sistema:

- _Ejemplo:_ "Análisis de discrepancia entre auto-percepción y feedback de subordinados detectado en el rasgo de Liderazgo".

### 3.2 Evidence Rationale (Evidencia de Rasgos)

Cada rasgo psicométrico (ej: Resiliencia, Adaptabilidad) cuenta con un sustento textual extraído directamente de la evidencia recolectada. La IA cita comportamientos u observaciones específicas para justificar un puntaje.

### 3.3 Blind Spots (Puntos Ciegos)

El sistema identifica y alerta sobre discrepancias críticas donde el colaborador se ve a sí mismo de manera distinta a como lo percibe su entorno.

---

## 4. Visualización de "Clase Mundial" (Unicorn Standard)

Los resultados se presentan en la vista `AssessmentResults.vue`, diseñada bajo principios de **Glassmorphism** y **Premium UI**:

- **Índice de Potencial IA:** Un score sintético del 0% al 100% que resume la probabilidad de éxito en roles de mayor complejidad.
- **Gráfico de Arquitectura Cognitiva:** Radar interactivo de rasgos psicométricos.
- **Línea de Vida del Análisis:** Visualización tipo "stepper" del flujo de pensamiento de la IA.
- **Acceso mediante Tokens Seguros:** Los evaluadores externos acceden mediante URLs temporales encriptadas, asegurando anonimato y eliminando fricción (sin necesidad de login).

---

## 5. Especificaciones Técnicas (Stack Intelectual)

- **Backend:** Laravel 11.x (PHP 8.3)
- **Córtex:** Microservicio Python (FastAPI + CrewAI + LangGraph)
- **Modelos:** Claude 3.5 Sonnet / DeepSeek R1 (vía Intel Bridge)
- **Frontend:** Vue.js 3 + ApexCharts + Vuetify 3 (Stratos Modern Theme)

---

**Última Actualización:** 24 de Febrero, 2026  
**Status:** Implementación Completa (Phase 5 Roadmap)  
**Proyecto:** Stratos Intelligence System  
_Propiedad Intelectual de Stratos - Hacia la Conciencia Organizacional._
