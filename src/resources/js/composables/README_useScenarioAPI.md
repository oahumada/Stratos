# useScenarioAPI.ts

Composable que **consolida toda la comunicación con la API** del módulo Scenario Planning. Implementa fallback strategies y retry logic para máxima resiliencia.

## Propósito

Centralizar lógica de API en un módulo testeable que:

- Maneja múltiples endpoints con fallbacks automáticos
- Gestiona errores de forma consistente
- Integra CSRF Sanctum para Laravel
- Proporciona notificaciones (showSuccess/showError)

## Funciones Principales

### `ensureCsrf()`

Asegura que el cookie XSRF-TOKEN esté presente (requerido por Sanctum). Llama a `/sanctum/csrf-cookie` si es necesario.

```typescript
await ensureCsrf(); // Garantiza CSRF disponible
```

### `loadCapabilityTree(scenarioId?: number)`

**Carga el árbol completo de capabilities** desde la API.

**Endpoints probados:**

1. `/api/strategic-planning/scenarios/{scenarioId}/capabilities`
2. `/api/capabilities?scenario_id={scenarioId}`
3. Fallback: Array vacío

```typescript
const tree = await scenarioAPI.loadCapabilityTree(123);
console.log(tree); // [{id, name, competencies, ...}, ...]
```

### `saveCapability(id, payload, scenarioId)`

**Actualiza una capability** con fallback de PATCH → POST.

**Endpoints:**

1. PATCH `/api/capabilities/{id}`
2. POST `/api/strategic-planning/scenarios/{scenarioId}/capabilities` (fallback)

```typescript
await scenarioAPI.saveCapability(
    42,
    {
        name: 'New Name',
        description: 'New Desc',
        type: 'business',
    },
    10,
);
```

### `deleteCapability(id, scenarioId)`

**Elimina una capability** con multiple endpoint fallback.

**Endpoints:**

1. DELETE `/api/strategic-planning/scenarios/{scenarioId}/capabilities/{id}`
2. DELETE `/api/capabilities/{id}` (fallback)

```typescript
await scenarioAPI.deleteCapability(42, 10);
```

### `createCompetency(capabilityId, competencyPayload, scenarioId)`

**Crea una nueva competencia** bajo una capability.

**Endpoints:**

1. POST `/api/capabilities/{capabilityId}/competencies`
2. POST `/api/strategic-planning/scenarios/{scenarioId}/capabilities/{capabilityId}/competencies` (fallback)

```typescript
await scenarioAPI.createCompetency(
    42,
    {
        name: 'New Competency',
        description: 'Desc',
    },
    10,
);
```

### `deleteCompetency(compId, capabilityId, scenarioId)`

**Elimina una competencia** con fallbacks.

```typescript
await scenarioAPI.deleteCompetency(100, 42, 10);
```

### `fetchSkillsForCompetency(compId)`

**Obtiene skills de una competencia** con 3-endpoint retry strategy.

**Endpoints:**

1. `/api/competencies/{compId}/skills`
2. `/api/skills?competency_id={compId}`
3. `/api/competency-skills?competency_id={compId}`

```typescript
const skills = await scenarioAPI.fetchSkillsForCompetency(100);
```

### `saveSkill(skillId, payload)`

**Actualiza una skill** (name, category, complexity, etc.).

```typescript
await scenarioAPI.saveSkill(5, {
    name: 'Updated Name',
    category: 'technical',
});
```

### `deleteSkill(skillId, compId, pivotId)`

**Elimina una skill** con múltiples estrategias según disponibilidad de datos.

```typescript
await scenarioAPI.deleteSkill(5, 100, 50);
```

### `savePositions(scenarioId, positions)`

**Guarda posiciones de nodos** en el canvas (pan/zoom state).

**Payload:**

```typescript
{
    nodes: [
        { id: 1, x: 100, y: 200 },
        { id: 2, x: 300, y: 400 },
    ];
}
```

## Fallback Strategy

Cada función implementa **retry automático con degradación elegante**:

```
Primary Endpoint (e.g., /api/capabilities/42)
    ↓ (error)
Secondary Endpoint (e.g., /api/strategic-planning/scenarios/10/capabilities/42)
    ↓ (error)
Fallback Response (empty array, null, or user message)
```

### Beneficios

- ✅ Tolerancia a cambios de API backend
- ✅ Soporte para múltiples versiones de endpoint
- ✅ UX mejorada (nunca falla silenciosamente)

## Error Handling

Todos los métodos:

1. **Log**: Detalle completo en console (debug mode)
2. **Show Error**: Notificación UI con `showError()` si disponible
3. **Throw**: Re-lance para que el caller maneje si es necesario

```typescript
try {
    await scenarioAPI.deleteCapability(42, 10);
} catch (err) {
    // Handle error específico
    console.error('Delete failed:', err);
}
```

## Integración Sanctum/CSRF

**Automático**: Cada función mutante (`save*`, `delete*`, `create*`) llama a `ensureCsrf()` antes de proceder.

```typescript
// Esto automáticamente:
// 1. Asegura CSRF token
// 2. Envia request con Sanctum auth
// 3. Valida response
await scenarioAPI.saveCapability(42, {...}, 10);
```

## Ejemplo de Uso Completo

```typescript
import { useScenarioAPI } from '@/composables/useScenarioAPI';

const scenarioAPI = useScenarioAPI();

// 1. Cargar tree
const capabilities = await scenarioAPI.loadCapabilityTree(123);

// 2. Actualizar capability
await scenarioAPI.saveCapability(
    capabilities[0].id,
    {
        name: 'Updated Name',
        description: 'New Desc',
    },
    123,
);

// 3. Crear competencia
await scenarioAPI.createCompetency(
    capabilities[0].id,
    {
        name: 'New Skill Area',
    },
    123,
);

// 4. Guardar posiciones
await scenarioAPI.savePositions(123, {
    nodes: capabilities.map((c) => ({ id: c.id, x: c.x, y: c.y })),
});
```

## Dependencias

- `@/composables/useApi` (axios wrapper)
- `@/composables/useNotification` (showSuccess, showError)
- Vue 3+

## Testing

Ver `Index.spec.ts` para casos de test. Ejemplos:

```typescript
test('loadCapabilityTree retorna array de capabilities', async () => {
    const api = useScenarioAPI();
    const tree = await api.loadCapabilityTree(1);
    expect(Array.isArray(tree)).toBe(true);
});

test('saveCapability muestra success notification', async () => {
    const api = useScenarioAPI();
    await api.saveCapability(1, { name: 'Test' }, 1);
    // Verify showSuccess fue llamado
});
```

## Performance & Caching

- ❌ **Sin caching**: Cada llamada va a la API
- ✅ **Recomendación**: Implementar Redis/localStorage caching en composable wrapper si es necesario
- ✅ **Optimización**: Usar tandem con `useScenarioState` para minimizar requests

## Futuras Mejoras

- Rate limiting (evitar spam de requests)
- Request debouncing para saves rápidos
- Offline queue (guardar en localStorage si offline)
- GraphQL migration (simplificar endpoints)
