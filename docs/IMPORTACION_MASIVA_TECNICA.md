# 🏗️ Arquitectura de Importación Masiva y Sincronización de Nómina

Este documento detalla el funcionamiento técnico del módulo de importación masiva, diseñado para mantener la integridad del **Gemelo Digital** y alimentar los modelos predictivos de Stratos.

## 1. El Flujo de Tres Etapas (Analyze-Stage-Commit)

Para garantizar la precisión de los datos, la importación se divide en tres fases críticas:

### Fase 1: Análisis Dinámico (`analyze`)
El sistema recibe los datos crudos y realiza una comparación en tiempo real contra la base de datos actual para detectar:
- **Nodos Estructurales**: Departamentos y Roles nuevos vs. existentes (usando el motor de unificación de alias).
- **Flujo de Talento**: Identificación de nuevas contrataciones, traslados internos y bajas automáticas (personas en el sistema que no están en el nuevo archivo).

### Fase 2: Preparación y Validación (`stage`)
Se crea un **ChangeSet** en estado `draft`. Esto permite:
- Persistir la propuesta de cambios sin afectar la producción.
- Generar una vista previa para el usuario responsable.
- Preparar el terreno para la auditoría y firma.

### Fase 3: Ejecución y Firma (`approveAndCommit`)
Es el paso final donde se materializan los cambios bajo una transacción de base de datos (`atomic operation`):
- **Sincronización de Nodos**: Crea departamentos/roles nuevos y actualiza alias de los existentes.
- **Sincronización de Personas**: `updateOrCreate` de empleados, vinculándolos a sus nuevos nodos.
- **Tracking de Movimientos**: Genera registros en `person_movements` (Hire, Transfer, Promotion, Exit).
- **Cierre de Ciclo**: El ChangeSet se marca como `applied`, se adjunta la firma digital y se genera un `OrganizationSnapshot` como nuevo baseline.

---

## 2. Lógica de Detección de Movimientos

El controlador utiliza una lógica jerárquica para clasificar los movimientos:

| Condición | Tipo de Movimiento | Acción en DB |
| :--- | :--- | :--- |
| Email no existe en Stratos | **Hire** | Creación de registro y person_movement 'hire'. |
| Email existe pero no está en el archivo | **Exit** | Soft-delete del registro y person_movement 'exit'. |
| Cambio de `role_id` | **Promotion** | Actualización y registro de trayectoria. |
| Cambio de `department_id` únicamente | **Transfer** | Actualización y registro de trayectoria lateral. |

---

## 3. Seguridad y Auditoría

- **Firma Digital**: Cada importación requiere una cadena de firma (ej. `USER_NAME_TIMESTAMP`) que se almacena en el metadata del ChangeSet.
- **Transaccionalidad**: Si cualquier parte del proceso falla (ej. error al crear un departamento), toda la importación se revierte automáticamente para evitar datos huérfanos.
- **Vinculación de Baseline**: El `OrganizationSnapshot` creado al final vincula la nueva foto del IQ con esta importación específica, permitiendo un historial de "causa y efecto".

---

## 4. Endpoints de la API

| Endpoint | Método | Descripción |
| :--- | :--- | :--- |
| `/api/talent/bulk-import/analyze` | `POST` | Devuelve el análisis de diferencias. |
| `/api/talent/bulk-import/stage` | `POST` | Crea el ChangeSet persistente. |
| `/api/talent/bulk-import/{id}/approve` | `POST` | Aplica y firma los cambios. |

---

> [!IMPORTANT]
> **Nota de Implementación**: El proceso de `syncDepartments` utiliza el motor de alias para evitar duplicados. Si un departamento se llama "RRHH" en la base de datos pero llega como "Recursos Humanos" en el Excel, el sistema lo reconocerá como existente si el alias está configurado.
