# Plan de Implementación: BARS y Skill Blueprint en Role Wizard

## Resumen
Este documento detalla la implementación de los descriptores conductuales (BARS) y el desglose técnico de habilidades (Skill Blueprint) dentro del flujo de diseño de roles asistido por IA (Role Wizard).

## Cambios Realizados (Marzo 2026)

### 1. Optimización del Motor de IA
- **Límite de Tokens**: Se incrementó `max_tokens` de 2048/1500 a **4096** en los proveedores `DeepSeekProvider` y `OpenAIProvider`. Esto permite procesar respuestas JSON extensas sin truncamiento.
- **Estrategia de Prompts**: Se dividió la generación en dos fases para evitar saturación y asegurar la calidad técnica de la salida.

### 2. Flujo del Wizard (Frontend & Backend)

#### Fase A: Síntesis de Identidad (Pasos 1-4)
- La IA genera el propósito, resultados esperados, coordenadas del cubo y 5 competencias estratégicas clave.
- **BARS Inline (Step 4)**: Las filas de competencias en el paso "DNA" son expandibles para mostrar los 5 niveles BARS (Ayuda a Maestro) con sus descripciones conductuales e indicadores de desempeño. Se resalta automáticamente el nivel de maestría requerido para el rol.

#### Fase B: Skill Blueprint (Paso 5) - *Nuevo*
- **Generación Técnica**: Al confirmar las competencias, el usuario avanza al "Skill Blueprint". Una nueva llamada a la IA desglosa cada competencia en 2-3 habilidades (skills) específicas.
- **Componentes por Skill**:
  - 5 niveles BARS detallados.
  - **Unidades de Aprendizaje**: Conocimientos y recursos necesarios para cada nivel.
  - **Criterios de Desempeño**: Indicadores de éxito tangibles.
- **Persistencia**: El blueprint se almacena en el campo `ai_archetype_config` del rol como metadatos técnicos para futura materialización en el catálogo.

## Decisiones Técnicas
- **Individuación de Pasos**: Se prefirió un paso adicional (Step 5) sobre una sola llamada masiva para garantizar que la IA no omita detalles por límites de contexto y para permitir al usuario validar las competencias antes de profundizarlas.
- **Estética Glassmorphism**: Se mantuvo la coherencia visual con el resto de la plataforma, usando efectos de desenfoque, bordes sutiles y estados de carga animados.

## Archivos Relacionados
- `app/Services/Talent/RoleDesignerService.php` (Lógica de IA y Prompts)
- `app/Http/Controllers/Api/RoleDesignerController.php` (Endpoints API)
- `resources/js/components/Roles/RoleCubeWizard.vue` (UI Step 4 y Step 5)
- `app/Services/LLMProviders/DeepSeekProvider.php` (Configuración de tokens)
