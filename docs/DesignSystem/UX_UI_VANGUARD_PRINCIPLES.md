# 🛡️ Stratos Vanguard: UX/UI Design Principles

**Guía de Diseño de Alta Resolución para la Fase de Excelencia**

Esta guía establece los estándares estéticos y de experiencia de usuario para elevar a **Stratos** de una herramienta funcional a una plataforma de vanguardia en Ingeniería de Talento. El objetivo es proyectar profesionalismo, poder y sofisticación técnica mediante un diseño de "Alta Resolución".

---

## 🏛️ Pilar 1: Jerarquía Visual y Profundidad (Glassmorphism 2.0)

El diseño debe sentirse físico y estructurado, no plano. La jerarquía organizacional se comunica a través del Eje Z (profundidad).

- **Capas con Propósito**: Utilizar paneles de "vidrio esmerilado" (`backdrop-blur-xl`) que floten sobre el contenido. Cuanto más importante es la información, mayor es su "elevación" visual y desenfoque de fondo.
- **Elevación Semántica**:
    - **El Rol (Edificio)**: Posee la elevación máxima y las sombras más profundas.
    - **La Competencia (Piso)**: Capas intermedias con bordes definidos.
    - **La Skill (Ladrillo)**: Integrada en la superficie del piso, con una elevación sutil.
- **Bordes de Luz**: Los paneles deben tener un borde superior (`border-t`) ligeramente más claro para simular el reflejo de una fuente de luz cenital.
- **Indicator Light (Sacar Brillo)**: Utilizar líneas de luz de 1px (`h-px`) con resplandor dinámico (`box-shadow`) en la parte superior del contenido o cabeceras para indicar foco y nivel premium. Esta es la firma visual de la fase de excelencia.

## ⚡ Pilar 2: Micro-interacciones de "Feedback Vivo"

La interfaz debe parecer "viva" y responder proactivamente a la intención del usuario.

- **Shimmer Effects (Esqueletos Progresivos)**: Al cargar datos de IA, no usar spinners genéricos; usar efectos de "barrido de luz" que imiten la forma final del bloque de datos.
- **Hover Glow Inteligente**: Al pasar el cursor sobre un ladrillo (skill), este debe emitir un sutil resplandor (`glow`) perimetral del color de su categoría.
- **Transiciones de Estado**: Los cambios de tabulación o apertura de modales deben usar curvas de aceleración `cubic-bezier(0.4, 0, 0.2, 1)` para un movimiento "orgánico".

## ✒️ Pilar 3: Tipografía y "Espacio en Blanco" (White Space)

El espacio no es vacío, es lujo. El diseño debe permitir que el "Propósito" del rol respire.

- **El Vacío como Autoridad**: Evitar la saturación de datos. Agrupar información y usar márgenes amplios para guiar el ojo hacia los mensajes estratégicos.
    - _Standard Padding_: `p-12` (48px) para tarjetas informativas. No temas al espacio; el espacio es poder.
    - _Hero Spacing_: `p-16` (64px) para paneles de acción principal.
- **Contraste Extremo de Pesos**:
    - Títulos de Roles: `font-black` (900) con espaciado estrecho (`tracking-tight`).
    - Metadatos/Labels: `font-light` (300) o `medium`, en mayúsculas, con espaciado amplio (`tracking-widest`).
- **Fuentes Recomendadas**: Inter (precisión técnica), Outfit (modernidad geométrica) o Manrope (legibilidad ejecutiva).

## 🧬 Pilar 4: Visualización del ADN (Human-AI Mix)

La simbiosis entre el humano y la IA es la propuesta de valor de Stratos y debe ser visualmente icónica.

- **Gradientes Dinámicos**: No utilizar barras de colores planos. Representar el mix de talento mediante gradientes fluidos (ej: de `Violeta-IA` a `Esmeralda-Humano`).
- **Simbología Única**: Utilizar iconografía custom (Phosphor Icons) que diferencie claramente las acciones de orquestación (IA) de las de ejecución (Humano).
- **Pulsos de Actividad**: Los componentes que representan agentes autónomos deben tener un pulso de luz muy sutil para indicar que están "operando" en segundo plano.

## 🏗️ Pilar 5: Narrativa de Diseño (Metáfora Arquitectónica)

El diseño debe contar la historia de la construcción del talento.

- **Vista "Exploded View"**: Siempre que sea posible, mostrar cómo se descompone un Rol. La transición del Rol a sus Competencias debe sentirse como "abrir" una estructura para ver sus pisos.
- **Revelación Progresiva**: Aplicar el principio de "curiosidad dirigida". Mostrar primero el Propósito (Cimientos) y permitir que el usuario profundice hacia las Skills (Ladrillos) mediante clics que expandan la información, evitando el ruido visual inicial.
- **Blueprint Mode**: Usar fondos de grilla sutiles (`grid-pattern`) en las áreas de diseño de IA para evocar la sensación de una mesa de dibujo arquitectónico.

---

> [!IMPORTANT]
> **Regla de Oro**: Si el componente parece algo que verías en un software de administración tradicional, NO es Stratos. Cada píxel debe respirar Ingeniería de Talento y Vanguardia.

---

## 💎 Evolución: Fase de Refracción de Excelencia ("Sacar Brillo")

En esta etapa, el sistema ya es funcional y robusto. El foco se desplaza de *construir* a *perfeccionar*.

1. **Eliminación de la "Opacidad Cognitiva"**: Si una sección de la interfaz requiere más de 2 segundos para entenderse, necesita brillo. Simplificar visualmente sin perder densidad de datos.
2. **Consistencia Obsesiva**: Un solo icono fuera de estilo o un margen inconsistente rompe la ilusión de vanguardia.
3. **El "Wow" en los Detalles**: Un sutil gradiente en un borde o una animación de entrada coordinada puede ser la diferencia entre una herramienta y una experiencia premium.

## 🤖 Pilar 6: La Metáfora Cyborg (Evolución de Marzo 2026)

La interfaz no es solo una herramienta, es una extensión técnica del usuario. Hemos pasado de un diseño de "herramienta" a uno de "simbiosis".

- **Neural Grain & Digital Dust**: El uso de ruido fractal sutil sobre fondos oscuros elimina la esterilidad digital y añade una textura orgánica ("Hardware con Alma").
- **Holographic Sweep**: Los componentes interactivos reaccionan con barridos de luz que simulan la activación de una interfaz táctil de alta resolución.
- **Bit-Stream Connectivity**: La presencia de telemetría constante (IDs de sesión, latencia, versiones del core) en las periferias refuerza la sensación de estar conectado a un sistema operativo vivo.
- **Micro-copy de Sincronización**: Sustitución de términos operativos por verbos de simbiosis: *Sincronizando Neural*, *Materializando Estructura*, *Sellando ADN*.
