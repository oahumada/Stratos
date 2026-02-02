# Guía de Layout Configuration - Scenario Planning

## Introducción

Este documento explica cómo ajustar el layout (posicionamiento) de nodos en el mapa de **Scenario Planning** (Capacidades → Competencias → Skills) de forma centralizada y sin tocar la lógica del código.

**Ubicación del archivo:** `src/resources/js/pages/ScenarioPlanning/Index.vue` (línea ~663)

---

## Estructura del LAYOUT_CONFIG

El objeto `LAYOUT_CONFIG` controla todo el posicionamiento y comportamiento de los tres niveles:

```javascript
const LAYOUT_CONFIG = {
  capability: { ... },   // Configuración de nodos capacidad (raíz)
  competency: { ... },   // Configuración de nodos competencia
  skill: { ... }         // Configuración de nodos skills
};
```

---

## 1. Configuración de Capacidades (`capability`)

### 1.1 Layout General (Matriz D3)

Las capacidades se distribuyen usando simulación de fuerzas D3 en matriz, con parámetros configurables.

```javascript
capability: {
  spacing: {
    hSpacing: 100,    // ← Espaciado horizontal en la matriz
    vSpacing: 80,     // ← Espaciado vertical en la matriz
  },
  forces: {
    linkDistance: 120,      // ← Distancia deseada entre nodos conectados
    linkStrength: 0.5,      // ← Fuerza del tirón (0-1, más alto = más fuerte)
    chargeStrength: -220,   // ← Repulsión entre nodos (negativo = repulsión)
  },
  scenarioEdgeDepth: 90,    // ← Curvatura de aristas Scenario → Capability
}
```

#### Parámetros Spacing:

| Parámetro  | Rango  | Efecto                                                         |
| ---------- | ------ | -------------------------------------------------------------- |
| `hSpacing` | 80-150 | Distancia horizontal entre capacidades. Aumenta si se solapan  |
| `vSpacing` | 60-120 | Distancia vertical entre filas. Aumenta para mejor legibilidad |

#### Parámetros Forces (Simulación D3):

| Parámetro        | Rango      | Efecto                                                               |
| ---------------- | ---------- | -------------------------------------------------------------------- |
| `linkDistance`   | 80-200     | Distancia que "prefieren" tener conectados. Aumenta para más espacio |
| `linkStrength`   | 0.1-0.9    | Cuán fuerte es el tirón entre nodos. Disminuye si tiemblan mucho     |
| `chargeStrength` | -300 a -50 | Repulsión entre nodos. Más negativo = más repulsión                  |

#### Parámetro Edge:

| Parámetro           | Rango  | Efecto                                                             |
| ------------------- | ------ | ------------------------------------------------------------------ |
| `scenarioEdgeDepth` | 40-150 | Curvatura de aristas Scenario → Capability. Aumenta para más curva |

---

## 2. Configuración de Competencias (`competency`)

### 2.1 Layout Radial (>5 competencias con una seleccionada)

Cuando hay **más de 5 competencias y seleccionas una**, se activa el layout radial automáticamente.

```javascript
competency: {
  radial: {
    radius: 140,              // ← DISTANCIA del centro a otros nodos
    selectedOffsetY: 10,      // ← ESPACIO VERTICAL para la competencia seleccionada
    startAngle: -Math.PI / 4, // ← Ángulo inicio (-45°, esquina inferior-izquierda)
    endAngle: (5 * Math.PI) / 4, // ← Ángulo fin (225°, evita tapa del padre arriba)
  },
  spacing: { ... },
  edge: { ... }
}
```

#### Parámetros Radial:

| Parámetro         | Rango   | Efecto                                                                                    |
| ----------------- | ------- | ----------------------------------------------------------------------------------------- |
| `radius`          | 150-300 | Cuán lejos están los nodos no-seleccionados del centro. Aumenta si se solapan             |
| `selectedOffsetY` | 0-80    | Espacio vertical que se deja para las skills debajo. Aumenta si las skills quedan pegadas |
| `startAngle`      | -π a 0  | Ángulo donde empieza el arco (más negativo = más hacia la izquierda)                      |
| `endAngle`        | π a 2π  | Ángulo donde termina el arco (controla qué lado abarca)                                   |

#### Ejemplos de ajuste:

**Caso: Competencias muy pegadas entre sí**

```javascript
radius: 240 → 280  // Aumentar separación
```

**Caso: Skills se solapan con competencia seleccionada**

```javascript
selectedOffsetY: 40 → 80  // Dar más espacio abajo
```

**Caso: Quiero que los nodos se distribuyan solo a los lados (no abajo)**

```javascript
startAngle: -Math.PI / 4 → -Math.PI / 6  // Cambiar de -45° a -30°
endAngle: (5 * Math.PI) / 4 → (3 * Math.PI) / 2  // Cambiar a 270°
```

### 1.2 Spacing (Layout Matriz para <5 competencias)

Cuando hay **5 o menos competencias**, se usa un layout matriz:

```javascript
spacing: {
  hSpacing: 100,    // Espaciado horizontal
  vSpacing: 80,     // Espaciado vertical
  parentOffset: 150, // Distancia vertical desde el padre (capacidad)
}
```

| Parámetro      | Rango   | Efecto                                         |
| -------------- | ------- | ---------------------------------------------- |
| `hSpacing`     | 50-200  | Distancia horizontal entre nodos en matriz     |
| `vSpacing`     | 40-150  | Distancia vertical entre filas                 |
| `parentOffset` | 100-250 | Cuán lejos debajo del padre comienza la matriz |

### 2.2 Curvatura de Aristas (Capability → Competency)

Las aristas que conectan capacidades con competencias se pueden hacer curvas. Este parámetro controla su curvatura:

```javascript
competency: {
  edge: {
    baseDepth: 40,       // ← Curvatura mínima (px)
    curveFactor: 0.35,   // ← Multiplicador de distancia (cuanto más separados, más curvos)
    spreadOffset: 18,    // ← Desplazamiento cuando hay aristas paralelas
  }
}
```

#### Parámetros Edge:

| Parámetro      | Rango   | Efecto                                                                                          |
| -------------- | ------- | ----------------------------------------------------------------------------------------------- |
| `baseDepth`    | 10-80   | Curvatura mínima en px. Aumenta para arcos más pronunciados                                     |
| `curveFactor`  | 0.1-0.8 | Multiplicador: `curve = baseDepth + (distancia × curveFactor)`. Más alto = más curvas dinámicas |
| `spreadOffset` | 0-30    | Cuando hay varias aristas paralelas, cuánto desplazarlas para no solapearse                     |

#### Ejemplos:

- **Arcos suaves:** `baseDepth: 25, curveFactor: 0.2`
- **Arcos pronunciados:** `baseDepth: 60, curveFactor: 0.5`
- **Recto:** `baseDepth: 0, curveFactor: 0`

---

## 3. Configuración de Skills (`skill`)

### 3.1 Display Limit

```javascript
skill: {
  maxDisplay: 10,  // Máximo de skills a mostrar (>10 se ignoran)
}
```

### 3.2 Layout Radial (>4 skills)

Cuando una competencia tiene **más de 4 skills**, se distribuyen en semicírculo:

```javascript
radial: {
  radius: 160,           // Distancia del nodo competencia a los skills
  offsetY: 120,          // Espacio vertical debajo de la competencia
  startAngle: -Math.PI / 6, // -30° (esquina inferior-izquierda)
  endAngle: (7 * Math.PI) / 6, // 210° (cubre 2/3 inferior del círculo)
}
```

| Parámetro    | Rango   | Efecto                                        |
| ------------ | ------- | --------------------------------------------- |
| `radius`     | 100-220 | Cuán lejos están los skills de la competencia |
| `offsetY`    | 80-160  | Espacio vertical, alejado de la competencia   |
| `startAngle` | -π a 0  | Inicio del arco                               |
| `endAngle`   | π a 2π  | Fin del arco                                  |

#### Ejemplo: Skills muy pegados a la competencia

```javascript
offsetY: 120 → 150  // Aumentar separación vertical
```

### 3.3 Layout Linear (≤4 skills)

Para 4 o menos skills, se alinean en fila:

```javascript
linear: {
  hSpacing: 100,  // Espaciado horizontal
  vSpacing: 60,   // Espaciado vertical
}
```

---

## Flujo Visual Completo

```
SCENARIO (arriba)
    ↓
CAPACIDAD seleccionada (centro)
    ↓
10 COMPETENCIAS distribuidas en semicírculo (radial mode)
    ↓
SKILLS de competencia seleccionada en semicírculo abajo (radial mode)
```

---

## Ejemplos de Configuración

### Ejemplo 1: Espaciado Compacto (pantallas pequeñas)

```javascript
const LAYOUT_CONFIG = {
  competency: {
    radial: {
      radius: 200, // ← Reducir
      selectedOffsetY: 30,
      // ...ángulos igual
    },
    spacing: {
      hSpacing: 80,
      vSpacing: 60,
      parentOffset: 120,
    },
  },
  skill: {
    maxDisplay: 8,
    radial: {
      radius: 130, // ← Reducir
      offsetY: 100,
    },
    linear: {
      hSpacing: 80,
      vSpacing: 50,
    },
  },
};
```

### Ejemplo 2: Espaciado Amplio (pantallas grandes)

```javascript
const LAYOUT_CONFIG = {
  competency: {
    radial: {
      radius: 300, // ← Aumentar
      selectedOffsetY: 60,
    },
    spacing: {
      hSpacing: 140,
      vSpacing: 100,
      parentOffset: 180,
    },
  },
  skill: {
    maxDisplay: 10,
    radial: {
      radius: 200, // ← Aumentar
      offsetY: 140,
    },
    linear: {
      hSpacing: 140,
      vSpacing: 80,
    },
  },
};
```

### Ejemplo 3: Solo Lados (sin skills abajo)

```javascript
// Si quieres que skills no ocupen espacio abajo, distribúyelos a los lados:
skill: {
  radial: {
    startAngle: -Math.PI / 2,  // -90° (lado izquierdo)
    endAngle: Math.PI / 2,      // 90° (lado derecho)
  },
}
```

---

## Cómo Probar Cambios

### Paso 1: Ubicar LAYOUT_CONFIG

Abre el archivo en tu editor:

```bash
src/resources/js/pages/ScenarioPlanning/Index.vue
```

Busca: `const LAYOUT_CONFIG = {` (alrededor de línea 662)

### Paso 2: Hacer un cambio

Ejemplo: aumentar el radius de competencias

```javascript
// ANTES:
radius: 240,

// DESPUÉS:
radius: 280,
```

### Paso 3: Guardar y Observar

- Guarda el archivo
- El navegador recarga automáticamente (Vite dev mode)
- Expande una capacidad con 10+ competencias
- Selecciona una competencia
- Observa el nuevo layout

### Paso 4: Ajustar iterativamente

Si quedan muy pegadas: `radius: 280 → 320`
Si quedan muy sueltas: `radius: 280 → 240`

---

## Parámetros Angulares (Radianes)

Si prefieres usar **grados** en lugar de radianes:

| Radianes           | Grados | Posición           |
| ------------------ | ------ | ------------------ |
| 0                  | 0°     | Derecha            |
| π/2                | 90°    | Abajo              |
| π                  | 180°   | Izquierda          |
| -π/2               | -90°   | Arriba             |
| -π/4               | -45°   | Arriba-Izquierda   |
| -Math.PI / 6       | -30°   | Arriba-Izq (menos) |
| (5 \* Math.PI) / 4 | 225°   | Abajo-Izq          |
| (7 \* Math.PI) / 6 | 210°   | Abajo-Izq (menos)  |

**Conversión:** `grados → radianes` es `grados * Math.PI / 180`

---

## Tips y Mejores Prácticas

### ✅ DO (Hacer)

- Cambiar valores de `radius` y `offsetY` para adaptar a tu pantalla
- Ajustar `maxDisplay` si quieres mostrar más/menos skills
- Usar valores simétricos para espaciado consistente
- Probar en navegador DevTools con la consola abierta

### ❌ DON'T (No hacer)

- No toques `startAngle` / `endAngle` si no sabes radianes (puede romper el layout)
- No cambies nombres de propiedades (romperá referencias)
- No copies sin entender qué hace cada valor
- No hagas cambios sin guardar backup mental de los valores originales

### 🐛 Debugging

Si el layout no cambia después de guardar:

1. ¿Guardaste el archivo? (Ctrl+S)
2. ¿Está en modo dev? (`npm run dev`)
3. ¿Están correctos los radianes? (sin `Math.PI` es solo número)
4. ¿Actualizaste la sección correcta? (competency vs skill)

---

## Valores de Referencia (Recomendados)

### Competencias - Valores Probados

| Escenario  | radius | selectedOffsetY |
| ---------- | ------ | --------------- |
| Compacto   | 180    | 20              |
| Normal     | 240    | 40              |
| Amplio     | 300    | 60              |
| Muy Amplio | 360    | 80              |

### Skills - Valores Probados

| Escenario | radius | offsetY | maxDisplay |
| --------- | ------ | ------- | ---------- |
| Compacto  | 120    | 100     | 6          |
| Normal    | 160    | 120     | 10         |
| Amplio    | 200    | 140     | 10         |

---

## Caso de Uso Real

### Escenario: "Las skills se solapan con las competencias"

**Diagnóstico:**

- Skills demasiado cerca de competencia seleccionada
- O competencias muy bajas (no hay espacio abajo)

**Solución:**

```javascript
// Opción 1: Dar más espacio vertical a competencia
selectedOffsetY: 40 → 70

// Opción 2: Skills más lejos de competencia
skill.radial.offsetY: 120 → 150

// Opción 3: Competencias más arriba
competency.spacing.parentOffset: 150 → 180
```

**Prueba combinaciones hasta que se vea bien.**

---

## Resumen de Pasos

1. **Abre:** `src/resources/js/pages/ScenarioPlanning/Index.vue`
2. **Busca:** `const LAYOUT_CONFIG = {`
3. **Edita:** Valores en competency.radial, competency.spacing, skill.radial, skill.linear
4. **Guarda:** Archivo
5. **Prueba:** En navegador, expande capacidad y selecciona competencia
6. **Ajusta:** Repite hasta satisfecho

---

## Contacto / Preguntas

Si encuentras valores que funcionen bien para un caso específico:

- Documenta los valores aquí
- Comparte con el equipo
- Considera hacer un preset (competencyLayout, skillLayout props)

---

**Última actualización:** 2026-01-29  
**Versión:** 1.0  
**Status:** ✅ En Uso
