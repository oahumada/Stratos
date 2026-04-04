# 🧪 PLAN DE PRUEBAS DE ACEPTACIÓN DE USUARIO (UAT) — Stratos v0.12.x

| Campo                   | Valor                                                   |
| :---------------------- | :------------------------------------------------------ |
| **Versión**             | v0.12.x                                                 |
| **Fechas de ejecución** | 4–7 Abr 2026                                            |
| **Coordinador UAT**     | QA Lead / Product Owner                                 |
| **Estado**              | 🔴 Pendiente ejecución                                  |
| **Criterio de éxito**   | ≥ 90% casos PASS · 0 bloqueadores críticos sin resolver |

---

## 1. Objetivo

Validar que las funcionalidades incluidas en v0.12.x cumplen con los criterios de aceptación definidos por producto, desde la perspectiva del usuario final, en un entorno de staging equivalente a producción.

El UAT no reemplaza los tests automatizados (1 219+ tests en CI); complementa la cobertura funcional con **flujos reales end-to-end** ejecutados manualmente por los representantes de cada persona de usuario.

---

## 2. Alcance

**Incluido:**

- Autenticación y gestión de sesión
- Gestión de organización, invitaciones y roles
- Módulo Workforce Planning (creación, flujo de aprobación, recomendaciones, gobernanza, comparador)
- Módulo Skills (heatmap de brechas, recomendaciones de upskilling)
- Módulo Performance (ciclos, calibración, insights)
- Organigrama (árbol de personas, búsqueda)
- LMS (asignación de cursos, progreso, certificados)
- Notificaciones (canal Slack, prueba de envío)
- Admin (audit logs con filtros)

**Fuera de alcance (posterior):**

- Tests E2E automatizados con Playwright — se ejecutarán en sprint siguiente basados en los flujos validados en este UAT
- Tests de carga/estrés (cubiertos por k6 en el checklist de producción)
- Integraciones con LMS externo (Cornerstone, SAP) — requieren entorno dedicado

---

## 3. Personas participantes

| Persona           | Rol en sistema           | Usuario de prueba       | Responsable de ejecución |
| :---------------- | :----------------------- | :---------------------- | :----------------------- |
| **Pedro Admin**   | `admin`                  | `pedro@stratos-uat.hr`  | DevOps / QA Lead         |
| **Ana HR Leader** | `hr_leader`              | `ana@stratos-uat.hr`    | Product Owner / QA       |
| **María L&D Mgr** | `talent_planner`         | `maria@stratos-uat.hr`  | QA / L&D representante   |
| **Carlos CHRO**   | `observer` / `hr_leader` | `carlos@stratos-uat.hr` | Product Owner            |

> Las personas provienen del documento `NARRATIVE_TESTING_STRATEGY.md`. Ver ese documento para contexto completo de dolores, objetivos y SLAs por persona.

---

## 4. Entorno de pruebas

| Parámetro             | Valor                                            |
| :-------------------- | :----------------------------------------------- |
| **URL de staging**    | `https://staging.stratos.hr`                     |
| **Base de datos**     | staging-db (seed fresco antes de cada sesión)    |
| **Versión de código** | branch `release/v0.12.x` — último commit verde   |
| **Reset de datos**    | `php artisan migrate:fresh --seed --env=staging` |

### Credenciales de prueba

| Usuario                 | Contraseña         | Rol            |
| :---------------------- | :----------------- | :------------- |
| `pedro@stratos-uat.hr`  | `UAT-Pedro-2026!`  | admin          |
| `ana@stratos-uat.hr`    | `UAT-Ana-2026!`    | hr_leader      |
| `maria@stratos-uat.hr`  | `UAT-Maria-2026!`  | talent_planner |
| `carlos@stratos-uat.hr` | `UAT-Carlos-2026!` | observer       |

> ⚠️ Cambiar estas contraseñas después del período UAT. No usar en producción.

---

## 5. Criterio de aceptación global

- **≥ 90%** de casos en estado PASS al finalizar el período UAT
- **0** bugs con severidad **Bloqueador** sin resolver antes del Go/No-Go
- **≤ 3** bugs con severidad **Crítica** abiertos (con workaround documentado)
- Todos los bugs **Mayores** con dueño asignado y fecha de fix comprometida

---

## 6. Casos de prueba

### AUTH-01 — Login, logout y token expirado

**Módulo:** Autenticación | **Persona:** Pedro Admin

| Campo            | Detalle                                             |
| :--------------- | :-------------------------------------------------- |
| **ID**           | AUTH-01                                             |
| **Módulo**       | Autenticación                                       |
| **Persona**      | Pedro Admin (`admin`)                               |
| **Flujo**        | Login normal → logout → sesión expirada             |
| **Precondición** | Usuario `pedro@stratos-uat.hr` existe y está activo |

**Pasos:**

1. Navegar a `https://staging.stratos.hr/login`
2. Ingresar email `pedro@stratos-uat.hr` y contraseña `UAT-Pedro-2026!`
3. Hacer clic en **Iniciar sesión**
4. Verificar redirección al dashboard
5. Verificar que el nombre "Pedro Admin" aparece en la barra lateral
6. Hacer clic en el avatar → **Cerrar sesión**
7. Verificar redirección a `/login`
8. Abrir sesión nueva, iniciar sesión correctamente
9. Desde la DB de staging, expirar el token: `UPDATE personal_access_tokens SET expires_at = NOW() - INTERVAL '1 hour' WHERE tokenable_id = <pedro_id>;`
10. Recargar la página
11. Verificar redirección automática a `/login` con mensaje de sesión expirada

| Resultado Esperado                                                                                       | Resultado Real | Estado               |
| :------------------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Login exitoso → dashboard carga · Logout redirige a /login · Token expirado → redirige con mensaje claro |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### ORG-01 — Crear organización, invitar usuario y asignar rol

**Módulo:** Organización | **Persona:** Pedro Admin

| Campo            | Detalle                                                                   |
| :--------------- | :------------------------------------------------------------------------ |
| **ID**           | ORG-01                                                                    |
| **Módulo**       | Gestión de Organización                                                   |
| **Persona**      | Pedro Admin (`admin`)                                                     |
| **Flujo**        | Crear org → invitar usuario → asignar rol                                 |
| **Precondición** | Pedro logueado con rol admin · Email de invitación configurado en staging |

**Pasos:**

1. Navegar a `/controlcenter` → sección de organización
2. Crear una nueva organización con nombre "Empresa UAT S.A." y slug "empresa-uat"
3. Verificar que la organización aparece en el listado
4. Ir a **Gestión de usuarios** → **Invitar usuario**
5. Ingresar email `nuevo-usuario@stratos-uat.hr` y rol `talent_planner`
6. Confirmar el envío de invitación
7. Verificar que el usuario aparece como "Pendiente" en la lista
8. Desde un segundo navegador (o modo incógnito), abrir el link de invitación del email
9. Completar el registro del usuario invitado
10. Volver como Pedro y verificar que el usuario ahora aparece como "Activo" con rol `talent_planner`
11. Cambiar el rol del usuario a `hr_leader` desde el panel de admin
12. Verificar que el cambio se refleja inmediatamente

| Resultado Esperado                                                                                                 | Resultado Real | Estado               |
| :----------------------------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Organización creada · Invitación enviada · Usuario registrado con rol correcto · Cambio de rol aplicado sin logout |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### WFP-01 — Ciclo completo de plan Workforce Planning

**Módulo:** Workforce Planning | **Persona:** Ana HR Leader

| Campo            | Detalle                                                                     |
| :--------------- | :-------------------------------------------------------------------------- |
| **ID**           | WFP-01                                                                      |
| **Módulo**       | Workforce Planning                                                          |
| **Persona**      | Ana HR Leader (`hr_leader`)                                                 |
| **Flujo**        | Crear plan → enviar a revisión → aprobar → activar                          |
| **Precondición** | Ana logueada · Organización "Empresa UAT" existente con al menos 5 personas |

**Pasos:**

1. Navegar a `/workforce-planning` → **Nuevo Plan**
2. Completar formulario: Nombre "Plan Q2 2026", período "Abr–Jun 2026", descripción
3. Guardar como borrador
4. Verificar que el plan aparece en estado `draft`
5. Agregar al menos 2 unidades de scope (departamentos/roles)
6. Agregar 1 proyecto de transformación con título y fecha objetivo
7. Hacer clic en **Enviar a revisión**
8. Verificar que el estado cambia a `in_review`
9. Verificar que Ana ya no puede editar el plan (campos deshabilitados)
10. Iniciar sesión como Pedro (admin) en pestaña separada
11. Ir al plan, hacer clic en **Aprobar**
12. Verificar que el estado cambia a `approved`
13. Volver como Ana → **Activar plan**
14. Verificar que el estado cambia a `active`
15. Intentar eliminar el plan — verificar que la opción no está disponible o está bloqueada

| Resultado Esperado                                                                                                 | Resultado Real | Estado               |
| :----------------------------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Flujo de estados: draft → in_review → approved → active · Guardias de estado respetados · Solo admin puede aprobar |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### WFP-02 — Recomendaciones de palancas: filtrar y exportar

**Módulo:** Workforce Planning | **Persona:** Ana HR Leader

| Campo            | Detalle                                                                 |
| :--------------- | :---------------------------------------------------------------------- |
| **ID**           | WFP-02                                                                  |
| **Módulo**       | WFP — Recomendaciones                                                   |
| **Persona**      | Ana HR Leader (`hr_leader`)                                             |
| **Flujo**        | Ver recomendaciones → filtrar por tipo → exportar                       |
| **Precondición** | Plan WFP en estado `active` · Recomendaciones generadas (seed de datos) |

**Pasos:**

1. Navegar a `/workforce-planning/recomendaciones`
2. Verificar que la página carga con lista de recomendaciones (al menos 3 registros de seed)
3. Aplicar filtro por tipo **"Upskilling"** — verificar que la lista se filtra correctamente
4. Aplicar filtro por tipo **"Contratación"** — verificar filtrado
5. Limpiar filtros — verificar que vuelven todas las recomendaciones
6. Aplicar filtro combinado: tipo + departamento
7. Hacer clic en **Exportar** (CSV o PDF según implementación)
8. Verificar que el archivo descargado contiene las recomendaciones filtradas
9. Abrir una recomendación individualmente — verificar detalle completo

| Resultado Esperado                                                                                                                   | Resultado Real | Estado               |
| :----------------------------------------------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Filtros funcionan sin recargar página · Exportación descarga archivo válido con datos correctos · Detalle de recomendación accesible |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### WFP-03 — Dashboard de gobernanza: semáforo y métricas

**Módulo:** Workforce Planning | **Persona:** Carlos CHRO

| Campo            | Detalle                                                                   |
| :--------------- | :------------------------------------------------------------------------ |
| **ID**           | WFP-03                                                                    |
| **Módulo**       | WFP — Gobernanza                                                          |
| **Persona**      | Carlos CHRO (`observer`)                                                  |
| **Flujo**        | Ver dashboard → verificar semáforo → leer métricas clave                  |
| **Precondición** | Datos de seed con múltiples planes en distintos estados · Carlos logueado |

**Pasos:**

1. Navegar a `/workforce-planning` o la sección de gobernanza del dashboard
2. Verificar que carga en < 3 segundos (medir con DevTools → Network)
3. Identificar el semáforo de estado general (Verde / Amarillo / Rojo)
4. Verificar que el color del semáforo corresponde al estado de los planes activos
5. Leer las métricas clave: % planes activos, % objetivos cumplidos, riesgos abiertos
6. Verificar que los números son coherentes con los datos de seed
7. Hacer drill-down en una métrica (clic en el número o gráfico)
8. Verificar que el detalle carga correctamente
9. Intentar acceder a funciones de edición — verificar que están ocultas para rol `observer`

| Resultado Esperado                                                                                  | Resultado Real | Estado               |
| :-------------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Dashboard carga < 3s · Semáforo visible y correcto · Métricas coherentes · Observer no puede editar |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### WFP-04 — Comparador de escenarios

**Módulo:** Workforce Planning | **Persona:** Ana HR Leader

| Campo            | Detalle                                                       |
| :--------------- | :------------------------------------------------------------ |
| **ID**           | WFP-04                                                        |
| **Módulo**       | WFP — Comparador de Escenarios                                |
| **Persona**      | Ana HR Leader (`hr_leader`)                                   |
| **Flujo**        | Seleccionar 2 escenarios → comparar → identificar diferencias |
| **Precondición** | Mínimo 2 escenarios/planes creados con datos distintos        |

**Pasos:**

1. Navegar a `/strategic-planning` (Scenario Planning)
2. Verificar que al menos 2 escenarios están listados
3. Seleccionar el primer escenario para comparar (checkbox o botón)
4. Seleccionar el segundo escenario
5. Hacer clic en **Comparar escenarios**
6. Verificar que la vista comparativa muestra ambos escenarios lado a lado
7. Verificar que las diferencias están destacadas visualmente (color, iconos)
8. Verificar que métricas como headcount, costo proyectado y riesgo aparecen en ambas columnas
9. Navegar de regreso a la lista sin perder el estado de selección
10. Exportar comparación (si existe la funcionalidad)

| Resultado Esperado                                                                                          | Resultado Real | Estado               |
| :---------------------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Comparación muestra ambos escenarios correctamente · Diferencias destacadas · Navegación no rompe el estado |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### SKILL-01 — Heatmap de brechas de skills

**Módulo:** Skills | **Persona:** Ana HR Leader

| Campo            | Detalle                                                          |
| :--------------- | :--------------------------------------------------------------- |
| **ID**           | SKILL-01                                                         |
| **Módulo**       | Skills — Gap Analysis                                            |
| **Persona**      | Ana HR Leader (`hr_leader`)                                      |
| **Flujo**        | Ver heatmap → identificar top gaps → explorar detalle            |
| **Precondición** | Personas con evaluaciones de skills en BD de seed · Ana logueada |

**Pasos:**

1. Navegar a `/gap-analysis`
2. Verificar que el heatmap carga con colores distinguibles (verde/amarillo/rojo)
3. Verificar que cada celda muestra skill × departamento (o persona)
4. Identificar las 3 celdas con mayor brecha (color más oscuro / rojo)
5. Hacer clic en una celda para ver el detalle de la brecha
6. Verificar que el detalle muestra: skill, nivel actual, nivel requerido, gap score
7. Filtrar por departamento específico — verificar que el heatmap se actualiza
8. Filtrar por tipo de skill (técnica / blanda) — verificar filtrado
9. Verificar que la leyenda del heatmap es legible y consistente

| Resultado Esperado                                                                                    | Resultado Real | Estado               |
| :---------------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Heatmap renderiza correctamente · Filtros actualizan vista · Detalle de brecha completo al hacer clic |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### SKILL-02 — Recomendaciones de upskilling por persona

**Módulo:** Skills | **Persona:** María L&D Manager

| Campo            | Detalle                                                                |
| :--------------- | :--------------------------------------------------------------------- |
| **ID**           | SKILL-02                                                               |
| **Módulo**       | Skills — Upskilling                                                    |
| **Persona**      | María L&D Manager (`talent_planner`)                                   |
| **Flujo**        | Ver recomendaciones de upskilling → filtrar por persona → asignar      |
| **Precondición** | Brechas de skills calculadas · Recomendaciones de cursos en BD de seed |

**Pasos:**

1. Navegar a la sección de recomendaciones de upskilling (desde `/skills` o `/learning-paths`)
2. Verificar que aparece lista de personas con recomendaciones pendientes
3. Buscar persona "Juan Pérez" (o persona de seed) por nombre
4. Verificar que las recomendaciones muestran: skill objetivo, curso sugerido, prioridad
5. Ordenar por prioridad (alta → baja)
6. Hacer clic en **Asignar curso** a una persona
7. Verificar confirmación de asignación
8. Verificar que la persona aparece en el módulo LMS con el curso asignado
9. Exportar lista de recomendaciones para el reporte de L&D

| Resultado Esperado                                                                                      | Resultado Real | Estado               |
| :------------------------------------------------------------------------------------------------------ | :------------- | :------------------- |
| Recomendaciones vinculadas a brechas reales · Asignación de curso refleja en LMS · Exportación funciona |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### PERF-01 — Ciclo de performance: crear, asignar reviews, calibrar

**Módulo:** Performance | **Persona:** Ana HR Leader

| Campo            | Detalle                                              |
| :--------------- | :--------------------------------------------------- |
| **ID**           | PERF-01                                              |
| **Módulo**       | Performance — Ciclos                                 |
| **Persona**      | Ana HR Leader (`hr_leader`)                          |
| **Flujo**        | Crear ciclo → asignar reviews → ejecutar calibración |
| **Precondición** | Personas en la organización con managers asignados   |

**Pasos:**

1. Navegar a la sección de Performance (desde sidebar o `/px`)
2. Crear nuevo ciclo: nombre "Q1 2026 Review", período "Ene–Mar 2026"
3. Configurar tipo de review: 360° o manager review
4. Asignar el ciclo a 3 personas de la organización de seed
5. Verificar que las personas reciben notificación (o aparecen en su dashboard)
6. Simular completar una review: ir como manager, completar el formulario de evaluación
7. Volver como Ana → Ir a **Calibración**
8. Verificar que la vista de calibración muestra distribución de ratings
9. Ajustar rating de 1 persona (arrastrar en la matriz o cambiar valor)
10. Guardar calibración y verificar que el cambio persiste
11. Cerrar el ciclo → verificar que no se pueden editar reviews cerradas

| Resultado Esperado                                                                      | Resultado Real | Estado               |
| :-------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Ciclo creado · Reviews asignadas · Calibración editable · Ciclo cerrado bloquea edición |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### PERF-02 — Insights de performance: top performers y needs attention

**Módulo:** Performance | **Persona:** Carlos CHRO

| Campo            | Detalle                                                              |
| :--------------- | :------------------------------------------------------------------- |
| **ID**           | PERF-02                                                              |
| **Módulo**       | Performance — Insights                                               |
| **Persona**      | Carlos CHRO (`observer`)                                             |
| **Flujo**        | Ver insights → identificar top performers → filtrar por departamento |
| **Precondición** | Ciclo de performance completado con ratings en BD de seed            |

**Pasos:**

1. Navegar al dashboard de Performance Insights
2. Verificar que carga lista de **Top Performers** (rating alto)
3. Verificar que carga lista de **Needs Attention** (rating bajo o sin review)
4. Confirmar que los nombres en ambas listas no se solapan
5. Filtrar por departamento "Tecnología" — verificar actualización de listas
6. Hacer clic en un top performer — ver detalle de ratings históricos
7. Verificar que Carlos (observer) no puede editar los ratings ni el ciclo
8. Verificar que la página carga en < 2 segundos

| Resultado Esperado                                                                               | Resultado Real | Estado               |
| :----------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Listas top/needs-attention correctas · Filtro por dpto funciona · Observer no edita · Carga < 2s |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### ORG-CHART-01 — Árbol de personas: ver, buscar y explorar subárbol

**Módulo:** Organigrama | **Persona:** Pedro Admin

| Campo            | Detalle                                                    |
| :--------------- | :--------------------------------------------------------- |
| **ID**           | ORG-CHART-01                                               |
| **Módulo**       | Organigrama                                                |
| **Persona**      | Pedro Admin (`admin`)                                      |
| **Flujo**        | Cargar árbol → buscar persona → ver subárbol de manager    |
| **Precondición** | Al menos 10 personas con jerarquía manager-reporte en seed |

**Pasos:**

1. Navegar a `/departments/org-chart`
2. Verificar que el árbol de organigrama carga correctamente (nodos visibles)
3. Verificar que el nodo raíz (CEO/Director) está visible
4. Expandir un nodo de nivel 2 — verificar que aparecen sus reportes directos
5. Usar el campo de búsqueda: buscar "María" (persona de seed)
6. Verificar que el árbol hace zoom/highlight al nodo de María
7. Desde el nodo de María, hacer clic en **Ver subárbol** (si existe)
8. Verificar que solo se muestra María y sus reportes (filtrado)
9. Volver a la vista completa
10. Verificar que el árbol es navegable con scroll/zoom sin errores visuales

| Resultado Esperado                                                                   | Resultado Real | Estado               |
| :----------------------------------------------------------------------------------- | :------------- | :------------------- |
| Árbol carga y es navegable · Búsqueda funciona · Subárbol muestra jerarquía correcta |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### LMS-01 — Asignar curso, ver progreso y certificado

**Módulo:** LMS | **Persona:** María L&D Manager

| Campo            | Detalle                                                 |
| :--------------- | :------------------------------------------------------ |
| **ID**           | LMS-01                                                  |
| **Módulo**       | LMS — Cursos y Certificados                             |
| **Persona**      | María L&D Manager (`talent_planner`)                    |
| **Flujo**        | Asignar curso → verificar progreso → ver certificado    |
| **Precondición** | Cursos en BD de seed · Persona "Luis Técnico" existente |

**Pasos:**

1. Navegar a `/learning-paths` o sección LMS
2. Buscar el curso "Liderazgo Ágil" (de seed)
3. Hacer clic en **Asignar** → seleccionar persona "Luis Técnico"
4. Confirmar la asignación
5. Verificar que Luis aparece en la lista de inscritos del curso
6. Simular avance: ir al perfil de Luis → actualizar progreso del curso al 100%
   (o usar endpoint: `PATCH /api/lms/enrollments/{id}` con `progress=100, status=completed`)
7. Verificar que el progreso se refleja en la vista de María
8. Verificar que se generó un certificado para Luis
9. Hacer clic en **Ver certificado** — verificar que se abre/descarga correctamente
10. Verificar que el certificado muestra nombre, curso y fecha

| Resultado Esperado                                                                               | Resultado Real | Estado               |
| :----------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Asignación exitosa · Progreso actualizado · Certificado generado y accesible con datos correctos |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### NOTIF-01 — Configurar canal Slack y recibir notificación de prueba

**Módulo:** Notificaciones | **Persona:** Pedro Admin

| Campo            | Detalle                                                                         |
| :--------------- | :------------------------------------------------------------------------------ |
| **ID**           | NOTIF-01                                                                        |
| **Módulo**       | Notificaciones — Canales                                                        |
| **Persona**      | Pedro Admin (`admin`)                                                           |
| **Flujo**        | Configurar webhook Slack → enviar notificación de prueba                        |
| **Precondición** | Pedro logueado · Workspace de Slack UAT disponible · Webhook de prueba generado |

**Pasos:**

1. Navegar a `/admin/alert-configuration` o configuración de notificaciones
2. Localizar la sección de **Canal Slack**
3. Ingresar la URL del webhook de prueba: `https://hooks.slack.com/services/TEST/...`
4. Hacer clic en **Guardar configuración**
5. Hacer clic en **Enviar notificación de prueba**
6. Verificar en el canal de Slack que el mensaje de prueba llega en < 30 segundos
7. Verificar que el mensaje contiene: nombre de la organización, tipo de evento, timestamp
8. Cambiar el webhook a una URL inválida → hacer clic en **Probar**
9. Verificar que el sistema muestra un mensaje de error claro (no 500)
10. Restaurar el webhook válido y verificar que vuelve a funcionar

| Resultado Esperado                                                                                                | Resultado Real | Estado               |
| :---------------------------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Mensaje de prueba llega a Slack en < 30s · URL inválida muestra error claro · Configuración persiste tras guardar |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

### ADMIN-01 — Audit logs con filtros y verificación de acciones

**Módulo:** Admin | **Persona:** Pedro Admin

| Campo            | Detalle                                                                               |
| :--------------- | :------------------------------------------------------------------------------------ |
| **ID**           | ADMIN-01                                                                              |
| **Módulo**       | Admin — Audit Logs                                                                    |
| **Persona**      | Pedro Admin (`admin`)                                                                 |
| **Flujo**        | Ver audit logs → aplicar filtros → verificar registro de acción propia                |
| **Precondición** | Pedro logueado · Acciones previas en la sesión UAT (login, creación de usuario, etc.) |

**Pasos:**

1. Navegar a `/admin/audit-logs`
2. Verificar que la tabla de audit logs carga con registros
3. Verificar que cada registro tiene: timestamp, usuario, acción, recurso, IP
4. Filtrar por usuario `pedro@stratos-uat.hr`
5. Verificar que aparecen las acciones de Pedro de la sesión actual (login, cambios de rol, etc.)
6. Filtrar por tipo de acción: **"user.created"** o similar
7. Verificar que el filtro muestra solo creaciones de usuario
8. Filtrar por rango de fechas: hoy
9. Aplicar filtros combinados: usuario + fecha + tipo de acción
10. Intentar acceder a `/admin/audit-logs` como Ana (hr_leader) — verificar que devuelve 403
11. Exportar audit logs filtrados (si existe la funcionalidad)

| Resultado Esperado                                                                                                             | Resultado Real | Estado               |
| :----------------------------------------------------------------------------------------------------------------------------- | :------------- | :------------------- |
| Logs muestran acciones con detalle completo · Filtros combinados funcionan · Ana no puede acceder (403) · Exportación funciona |                | ☐ PASS ☐ FAIL ☐ BLOQ |

---

## 7. Registro de ejecución

### 7.1 Tabla resumen por módulo

| Módulo           | ID           | Total casos | PASS | FAIL | BLOQ | % Éxito |
| :--------------- | :----------- | :---------: | :--: | :--: | :--: | :-----: |
| Autenticación    | AUTH-01      |      1      |      |      |      |         |
| Organización     | ORG-01       |      1      |      |      |      |         |
| Workforce Plann. | WFP-01..04   |      4      |      |      |      |         |
| Skills           | SKILL-01..02 |      2      |      |      |      |         |
| Performance      | PERF-01..02  |      2      |      |      |      |         |
| Organigrama      | ORG-CHART-01 |      1      |      |      |      |         |
| LMS              | LMS-01       |      1      |      |      |      |         |
| Notificaciones   | NOTIF-01     |      1      |      |      |      |         |
| Admin            | ADMIN-01     |      1      |      |      |      |         |
| **TOTAL**        |              |   **14**    |      |      |      |         |

**Criterio de aceptación:** ≥ 90% PASS (≥ 13 de 14 casos) · 0 Bloqueadores

### 7.2 Bugs encontrados

| ID Bug  | Descripción | Severidad | Módulo | Asignado a | Estado |
| :------ | :---------- | :-------: | :----- | :--------- | :----- |
| BUG-001 |             |           |        |            |        |
| BUG-002 |             |           |        |            |        |
| BUG-003 |             |           |        |            |        |

**Severidades:**

- 🔴 **Bloqueador** — Impide completar el flujo, sin workaround posible
- 🟠 **Crítico** — Flujo incompleto pero con workaround documentado
- 🟡 **Mayor** — Degradación funcional notable, no bloquea el flujo
- 🟢 **Menor** — Problema cosmético o de UX, no afecta funcionalidad

### 7.3 Decisión final UAT

```
FECHA DE EVALUACIÓN: ___________________________

RESULTADO:
  Total casos ejecutados:  ___ / 14
  PASS:        ___
  FAIL:        ___
  BLOQUEADOS:  ___
  % Éxito:     ___%

DECISIÓN:  [ ] GO — 90%+ PASS, 0 bloqueadores → proceder con release
           [ ] NO-GO — no cumple criterios → resolver bugs y re-ejecutar

Bugs bloqueadores pendientes (si NO-GO):
  - _______________________________________________
  - _______________________________________________

FIRMAS:
  QA Lead:        ______________________  Fecha: ___________
  Product Owner:  ______________________  Fecha: ___________
  Tech Lead:      ______________________  Fecha: ___________
```

---

## 8. Transición a Playwright (E2E automatizados)

### 8.1 Principio de mapeo UAT → Spec

Cada caso UAT que resulte en **PASS** se convierte en un spec de Playwright. La lógica de los pasos se mantiene; se reemplaza la ejecución manual por selectores con `data-testid`.

```
UAT caso AUTH-01 (PASS)
  → tests/e2e/auth/login-logout.spec.ts

UAT caso WFP-01 (PASS)
  → tests/e2e/workforce-planning/plan-lifecycle.spec.ts

UAT caso LMS-01 (PASS)
  → tests/e2e/lms/course-assignment.spec.ts
```

### 8.2 Convención de `data-testid`

Cada elemento interactuable en los flujos UAT debe tener un atributo `data-testid` estable:

```html
<!-- Botones de acción -->
<v-btn data-testid="btn-submit-plan">Enviar a revisión</v-btn>
<v-btn data-testid="btn-approve-plan">Aprobar</v-btn>
<v-btn data-testid="btn-logout">Cerrar sesión</v-btn>

<!-- Campos de formulario -->
<v-text-field data-testid="input-plan-name" />
<v-select data-testid="select-plan-period" />

<!-- Elementos de datos -->
<tr data-testid="audit-log-row-{id}">
    <div data-testid="heatmap-cell-{skill}-{dept}">
        <!-- Estados y badges -->
        <span data-testid="plan-status-badge">active</span>
        <div data-testid="traffic-light-indicator"></div>
    </div>
</tr>
```

**Reglas:**

- Usar kebab-case: `data-testid="btn-create-plan"` ✅
- Incluir contexto: `data-testid="wfp-plan-list-item-{id}"` ✅
- No usar clases CSS o texto visible como selectores en specs ❌
- Los `data-testid` son **solo para testing** — no usar en lógica de negocio

### 8.3 Estructura de archivos E2E

```
tests/e2e/
├── auth/
│   ├── login-logout.spec.ts          # AUTH-01
│   └── session-expiry.spec.ts
├── org/
│   └── invite-and-assign-role.spec.ts # ORG-01
├── workforce-planning/
│   ├── plan-lifecycle.spec.ts         # WFP-01
│   ├── recommendations.spec.ts        # WFP-02
│   ├── governance-dashboard.spec.ts   # WFP-03
│   └── scenario-comparator.spec.ts   # WFP-04
├── skills/
│   ├── gap-heatmap.spec.ts           # SKILL-01
│   └── upskilling-recommendations.spec.ts # SKILL-02
├── performance/
│   ├── review-cycle.spec.ts          # PERF-01
│   └── insights.spec.ts             # PERF-02
├── org-chart/
│   └── tree-navigation.spec.ts       # ORG-CHART-01
├── lms/
│   └── course-assignment.spec.ts     # LMS-01
├── notifications/
│   └── slack-channel.spec.ts         # NOTIF-01
├── admin/
│   └── audit-logs.spec.ts            # ADMIN-01
├── fixtures/
│   ├── users.ts                      # Credenciales de prueba
│   └── seed-data.ts                  # Referencias a datos de seed
└── support/
    ├── auth.setup.ts                 # Login compartido entre specs
    └── helpers.ts                    # Utilidades comunes
```

### 8.4 Orden de implementación recomendado

**Semana 1 — Smoke tests (máxima prioridad):**

1. `auth/login-logout.spec.ts` — gate para todos los demás specs
2. `workforce-planning/plan-lifecycle.spec.ts` — flujo core del producto
3. `admin/audit-logs.spec.ts` — compliance crítico

**Semana 2 — Critical paths:**

4. `org/invite-and-assign-role.spec.ts`
5. `skills/gap-heatmap.spec.ts`
6. `performance/review-cycle.spec.ts`
7. `lms/course-assignment.spec.ts`

**Semana 3 — Flujos secundarios:**

8. `workforce-planning/recommendations.spec.ts`
9. `workforce-planning/governance-dashboard.spec.ts`
10. `workforce-planning/scenario-comparator.spec.ts`
11. `skills/upskilling-recommendations.spec.ts`
12. `performance/insights.spec.ts`
13. `org-chart/tree-navigation.spec.ts`
14. `notifications/slack-channel.spec.ts`

### 8.5 Ejemplo de spec generado desde UAT

```typescript
// tests/e2e/auth/login-logout.spec.ts
// Generado desde UAT caso AUTH-01 (PASS - 4 Abr 2026)

import { test, expect } from '@playwright/test';

test.describe('AUTH-01: Login y logout', () => {
    test('login exitoso redirige al dashboard', async ({ page }) => {
        await page.goto('/login');
        await page.getByTestId('input-email').fill('pedro@stratos-uat.hr');
        await page.getByTestId('input-password').fill('UAT-Pedro-2026!');
        await page.getByTestId('btn-login').click();

        await expect(page).toHaveURL('/dashboard/analytics');
        await expect(page.getByTestId('sidebar-user-name')).toContainText(
            'Pedro',
        );
    });

    test('logout redirige a /login', async ({ page }) => {
        // (asume estado autenticado via auth.setup.ts)
        await page.getByTestId('nav-user-menu').click();
        await page.getByTestId('btn-logout').click();

        await expect(page).toHaveURL('/login');
    });
});
```

---

_Documento generado para Stratos v0.12.x — 4 Abr 2026_  
_Basado en personas de `NARRATIVE_TESTING_STRATEGY.md` y rutas de `routes/web.php` + `routes/api.php`_
