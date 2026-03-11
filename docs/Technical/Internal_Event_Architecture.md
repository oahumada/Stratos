# 🏗️ Arquitectura de Eventos Interna: Event Bus y Event Sourcing Lite

Este documento define el estándar de ingeniería para la comunicación asíncrona y la inmutabilidad de acciones estratégicas en la plataforma Stratos.

## 1. Visión General

Para evolucionar de un esquema de "llamadas directas" (acoplamiento fuerte) a una arquitectura reactiva, introducimos un **Event Bus de Dominio**. Esto permite:

- **Desacoplamiento**: Un módulo (ej. `Stratos Core`) no necesita conocer las consecuencias de sus acciones en otros módulos (ej. `Neo4j Sync`).
- **Auditabilidad (Event Store)**: Cada cambio estratégico queda registrado en un log inmutable.
- **Reactividad**: Facilita la construcción de flujos complejos de IA disparados por cambios en el estado de la organización.

## 2. Componentes de la Arquitectura

### 2.1 El Bus de Eventos (Event Bus)

Utilizamos el sistema nativo de Laravel (`dispatchable`) pero extendido con una estructura de metadatos mandatoria.

### 2.2 Eventos de Dominio (Domain Events)

Todos los eventos estratégicos deben heredar de `App\Events\DomainEvent` y contener:

- `aggregate_id`: El ID del modelo principal afectado.
- `tenant_id`: Para aislamiento multitenant.
- `actor_id`: El usuario que disparó la acción (si aplica).
- `payload`: Datos específicos de la mutación.

### 2.3 Store de Eventos (Event Sourcing Lite)

No implementaremos un Event Sourcing puro (sustituyendo la DB relacional), sino un **Side-Car Event Sourcing**.

- Cada vez que un `DomainEvent` se despacha, un listener global lo guarda en la tabla `event_store`.
- Esto permite reconstruir la historia de cualquier objeto (ej. "Cómo evolucionó el rol de Data Scientist en 6 meses").

## 3. Guía de Implementación

### Implementar en Modelos

```php
use App\Traits\HasDomainEvents;

class Roles extends Model {
    use HasDomainEvents;
}
```

### Despachar Eventos

```php
RoleChanged::dispatch($role, [
    'action' => 'competency_added',
    'competency_id' => 45,
]);
```

---

> [!IMPORTANT]
> Esta arquitectura es el cimiento para los **Agentes Reactivos**. Sin eventos, la IA solo actúa cuando el usuario lo pide. Con eventos, la IA puede actuar proactivamente cuando "observa" un cambio en el grafo organizacional.
