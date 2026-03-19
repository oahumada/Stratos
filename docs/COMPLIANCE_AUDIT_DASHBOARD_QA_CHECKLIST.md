# 📋 Checklist QA Manual - Compliance Audit Dashboard

**Demo Ejecutiva | QA Manual Operativa**

---

## 1️⃣ CÓMO FUNCIONA LA PANTALLA

### 🎯 Propósito General

El **Compliance Audit Dashboard** es el centro de control de auditoría interna de Stratos que consolida:

- **Audit Trail**: Registro de todos los eventos de cambio en el sistema (quién, qué, cuándo)
- **ISO 30414 Metrics**: Indicadores de riesgo de talento (reemplazo, brechas de skills)
- **Internal Audit Wizard**: Cumplimiento de firmas digitales en roles críticos
- **Credential Verification**: Validación de credenciales verificables (VC/JSON-LD)

### 📊 Flujo de datos

```
Sistema (cualquier cambio)
    ↓
EventStore (almacena evento)
    ↓
API: /api/compliance/audit-events (recupera eventos)
    ↓
Dashboard (visualiza + filtra)
    ↓
Export VC (genera credencial)
    ↓
Verify VC (valida firma digital)
```

### 🔍 Cuatro Secciones Principales

| Sección                      | Datos                                               | Uso                  | Endpoint                                     |
| ---------------------------- | --------------------------------------------------- | -------------------- | -------------------------------------------- |
| **Resumen Audit Trail**      | Total eventos, últimas 24h, tipos, agregados únicos | Verificar actividad  | `/api/compliance/audit-events/summary`       |
| **ISO 30414 KPIs**           | Costo reemplazo, brechas de skills por depto        | Riesgo de talento    | `/api/compliance/iso30414/summary`           |
| **Internal Audit Wizard**    | Roles críticos, cumplimiento de firma, vigencia     | Gobernanza interna   | `/api/compliance/internal-audit-wizard`      |
| **Tabla Eventos**            | Evento, agregado, actor, fecha (_filtrable_)        | Auditar cambios      | `/api/compliance/audit-events?per_page=50`   |
| **Tablas ISO30414**          | Madurez por depto, brechas de skills                | Análisis de talento  | Subset de summary                            |
| **Credential Export/Verify** | Extrae VC, valida firma JSON-LD                     | Comprobancia externa | `/api/compliance/credentials/roles/{roleId}` |

---

## 2️⃣ CHECKLIST DE QA MANUAL - OPERATIVA

### ✅ SECCIÓN A: CARGA Y RENDERING

**Objetivo**: Verificar que el dashboard carga sin errores y muestra datos correctos

| #   | Caso de Prueba                            | Pasos                                                     | Resultado Esperado                                         | Observación              |
| --- | ----------------------------------------- | --------------------------------------------------------- | ---------------------------------------------------------- | ------------------------ |
| A1  | **Dashboard carga en primer acceso**      | 1. Navegar a `/quality/compliance-audit` 2. Esperar carga | ✅ Se muestra "Cargando eventos..." y luego desaparece     | Con datos en BD          |
| A2  | **KPIs resumen visibles y correctos**     | 1. Observar bloque superior (4 tarjetas)                  | ✅ Muestra: Eventos Totales, Eventos 24h, Tipos, Agregados | Números = filtro en DB   |
| A3  | **Bloque ISO30414 se renderiza**          | 1. Scroll a sección ISO 2. Verificar 3 tarjetas KPI       | ✅ Muestra: Costo Total, Costo Promedio, Skills con Brecha | Si iso30414 ≠ null       |
| A4  | **Bloque Internal Audit Wizard presente** | 1. Scroll a sección 3 2. Verificar 3 tarjetas             | ✅ Muestra: Roles Críticos, Cumpliendo, % Cumplimiento     | Si internalAudit ≠ null  |
| A5  | **Botón "Actualizar" funciona**           | 1. Click botón superior derecha 2. Observar spinner       | ✅ Dashboard recarga datos, spinner muestra 1-2 seg        | XHR a /api/compliance/\* |
| A6  | **No hay errores en consola**             | 1. Abrir DevTools (F12) 2. Ir a Console tab               | ✅ Ningún error rojo                                       | Si hay errores: fallar   |

---

### ✅ SECCIÓN B: INPUTS Y FILTROS

**Objetivo**: Verificar que los filtros funcionan correctamente

| #   | Caso de Prueba                                          | Pasos                                                                               | Resultado Esperado                          | Observación                             |
| --- | ------------------------------------------------------- | ----------------------------------------------------------------------------------- | ------------------------------------------- | --------------------------------------- |
| B1  | **Input "Filtrar por event_name" visible y escribible** | 1. Buscar campo de filtro arriba de tabla eventos 2. Escribir "user.created"        | ✅ Texto aparece en campo, sin errores      | Color: blanco                           |
| B2  | **Input "Filtrar por aggregate_type" escribible**       | 1. Click en segundo campo 2. Escribir "User"                                        | ✅ Texto aparece, sin errores               | Color: blanco                           |
| B3  | **Botón "Aplicar filtros" funciona**                    | 1. Ingresar "user.created" en event_name 2. Click "Aplicar filtros"                 | ✅ Tabla filtra a solo ese evento           | XHR con params ?event_name=user.created |
| B4  | **Filtrar retorna resultados correctos**                | 1. Navegar a un evento que existe 2. Ingresar evento 3. Click verificar             | ✅ Rows en tabla = eventos coincidentes     | Si es 0: OK si evento no existe         |
| B5  | **Limpiar filtros**                                     | 1. Borrar ambos campos 2. Click "Aplicar filtros"                                   | ✅ Tabla muestra todos los eventos          | Sin parámetros en XHR                   |
| B6  | **Campo "Vigencia (días)" para Audit Wizard funciona**  | 1. Scroll a sección Internal Audit 2. Cambiar valor (ej: 180) 3. Click "Recalcular" | ✅ Tabla filtra roles con firma <= 180 días | API recalcula con parámetro             |

---

### ✅ SECCIÓN C: TABLAS Y PRESENTACIÓN

**Objetivo**: Verificar que las tablas muestran datos legibles y con estructura correcta

| #   | Caso de Prueba                                           | Pasos                                                           | Resultado Esperado                                                             | Observación                    |
| --- | -------------------------------------------------------- | --------------------------------------------------------------- | ------------------------------------------------------------------------------ | ------------------------------ |
| C1  | **Tabla "Eventos" muestra 4 columnas correctas**         | 1. Scroll a tabla eventos 2. Verificar encabezados              | ✅ Evento, Agregado, Actor, Fecha                                              | Headers visibles en blanco/70% |
| C2  | **Datos en tabla eventos legibles**                      | 1. Observar filas de datos 2. Verificar texto blanco (no negro) | ✅ Texto blanco en fondo oscuro                                                | Sin texto invisible            |
| C3  | **Tabla "Madurez de Talento por Departamento" presente** | 1. Scroll sección ISO 2. Buscar tabla 2                         | ✅ 6 columnas: Depto, Headcount, Readiness, Nivel Actual, Requerido, Brechas   | Scroll horizontal en mobile    |
| C4  | **Tabla "Brechas de Capacidades" presente**              | 1. Scroll sección ISO 2. Buscar tabla 3                         | ✅ 5 columnas: Skill, Dominio, Personas Evaluadas, Con Brecha, Brecha Promedio | Datos de ISO 30414             |
| C5  | **Tabla "Internal Audit Wizard - Roles" presente**       | 1. Scroll a sección 3 2. Buscar tabla                           | ✅ 5 columnas: Role, Depto, Skills críticas, Estado firma, Edad firma          | Vigencia configurable          |
| C6  | **Hover efecto en filas de tabla**                       | 1. Pasar mouse sobre una fila 2. Observar cambio visual         | ✅ Fila se ilumina levemente (hover:bg-white/5)                                | Feedback visual                |

---

### ✅ SECCIÓN D: CONTRASTE Y LEGIBILIDAD

**Objetivo**: Garantizar que todo el texto es visible (no hay texto oscuro/negro oculto)

| #   | Caso de Prueba                                     | Pasos                                                        | Resultado Esperado                                    | Observación         |
| --- | -------------------------------------------------- | ------------------------------------------------------------ | ----------------------------------------------------- | ------------------- |
| D1  | **Título "Compliance Audit Dashboard" visible**    | 1. Observar encabezado superior izq                          | ✅ Texto blanco grande, legible                       | text-white text-3xl |
| D2  | **Subtítulo "Visión centralizada..." visible**     | 1. Línea bajo título                                         | ✅ Gris (text-white/60), legible                      | Describe propósito  |
| D3  | **Labels en inputs de filtro visible**             | 1. Ir a área de filtros 2. Observar "Filtrar por event_name" | ✅ Label visible sobre input                          | text-white color    |
| D4  | **Headers de tabla con opacity 70% legible**       | 1. Observar encabezados (Evento, Agregado...)                | ✅ Visible pero menos intenso que datos               | text-white/70       |
| D5  | **Valores numéricos en KPI cards brillan**         | 1. Observar números grandes (ej: eventos totales)            | ✅ text-white font-bold, muy legible                  | text-3xl            |
| D6  | **Texto en breadcrumb "Vigencia (días)" visible**  | 1. Scroll a Internal Audit 2. Observar label input           | ✅ Blanco, legible, no desaparece                     | text-white          |
| D7  | **Validar contraste en modo claro (si aplicable)** | 1. Si hay toggle dark/light: cambiar a light                 | ✅ Texto sigue siendo legible (verificar con cliente) | Depende de tema     |

---

### ✅ SECCIÓN E: CREDENTIAL EXPORT & VERIFICATION

**Objetivo**: Verificar flujo de exportación y validación de credenciales verificables

| #   | Caso de Prueba                                  | Pasos                                                                      | Resultado Esperado                                                                                                                 | Observación                               |
| --- | ----------------------------------------------- | -------------------------------------------------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------- |
| E1  | **Input "Role ID" escribible**                  | 1. Scroll a sección Credential Export 2. Escribir un rol ID válido (ej: 5) | ✅ Número aparece en campo                                                                                                         | color="white" class="text-white"          |
| E2  | **Botón "Exportar VC" sin errores**             | 1. Ingresar Role ID válido (ej: 5) 2. Click "Exportar VC"                  | ✅ No hay error console, muestra "Exportando..."                                                                                   | XHR a /api/compliance/credentials/roles/5 |
| E3  | **VC JSON-LD se visualiza en <pre>**            | 1. Luego de E2 2. Observar bloque gris oscuro con JSON                     | ✅ Muestra JSON con estructura @context, type, issuer, credentialSubject                                                           | Fondo: bg-white/5                         |
| E4  | **Botón "Verificar VC" se activa**              | 1. Después de exportar (E3) 2. Botón está habilitado                       | ✅ Botón clickeable (no disabled)                                                                                                  | disabled="false"                          |
| E5  | **Verificación VC ejecuta y muestra resultado** | 1. Click "Verificar VC"                                                    | ✅ Badge verde "VC Válida" O roja "VC Inválida" + checks                                                                           | XHR POST a /verify                        |
| E6  | **Checks de verificación se muestran**          | 1. Después de E5 2. Observar texto pequeño con validaciones                | ✅ Muestra 4 checks: model_signature_valid, proof_matches_role_signature, issuer_matches_expected, credential_subject_role_matches | ok/fail                                   |
| E7  | **VC inválido se maneja gracefully**            | 1. Role ID no existe (ej: 99999) 2. "Exportar VC"                          | ✅ Mensaje de error NO quiebra UI, limpia datos                                                                                    | Error handling                            |

---

### ✅ SECCIÓN F: RENDERING RESPONSIVO

**Objetivo**: Verificar que el dashboard funciona en diferentes tamaños de pantalla

| #   | Caso de Prueba                                     | Pasos                                                                 | Resultado Esperado                                    | Observación              |
| --- | -------------------------------------------------- | --------------------------------------------------------------------- | ----------------------------------------------------- | ------------------------ |
| F1  | **Mobile (< 768px): Tarjetas en 1 columna**        | 1. DevTools → móvil (375px ancho) 2. Observar KPI cards               | ✅ 4 tarjetas stacked verticalmente                   | grid-cols-1              |
| F2  | **Tablet (≥768px): Tarjetas en 2 columnas**        | 1. DevTools → tablet (768px) 2. Observar KPI cards                    | ✅ 2 tarjetas por fila                                | md:grid-cols-2           |
| F3  | **Desktop (≥1280px): Tarjetas en máxima densidad** | 1. DevTools → desktop (1400px) 2. Observar KPI cards                  | ✅ Resumen: 4 cols, ISO: 3 cols, Audit: 3 cols        | xl:grid-cols-4/3/3       |
| F4  | **Tablas con scroll horizontal en mobile**         | 1. Mobile (375px) 2. Ir a tabla eventos 3. Intentar scroll horizontal | ✅ Tabla scrollable horizontalmente, sin cortar datos | overflow-x-auto          |
| F5  | **Padding y gap escalan apropiadamente**           | 1. Medir en diferentes resoluciones 2. Verificar px-12 en grids       | ✅ Espaciado consistente en mobile, tablet, desktop   | px-12, gap-x-10, gap-y-8 |

---

### ✅ SECCIÓN G: PERFORMANCE Y ERRORES

**Objetivo**: Verificar que el dashboard es performante y sin errores

| #   | Caso de Prueba                          | Pasos                                                           | Resultado Esperado                                 | Observación                   |
| --- | --------------------------------------- | --------------------------------------------------------------- | -------------------------------------------------- | ----------------------------- |
| G1  | **Dashboard carga en < 3 segundos**     | 1. Network tab en DevTools 2. Navegar a dashboard               | ✅ DOM ready < 3s, APIs responden < 2s each        | Con datos en BD               |
| G2  | **No hay N+1 queries**                  | 1. Backend logs 2. Hacer llamada a /api/compliance/audit-events | ✅ 1 query a eventos, eager load rels              | Si es > 1: revisión necesaria |
| G3  | **Sin errores de CORS**                 | 1. Console tab en DevTools 2. Ejecutar E2                       | ✅ Sin mensajes "No 'Access-Control-Allow-Origin'" | Si hay CORS: error            |
| G4  | **Validación de Role ID: solo números** | 1. Ir a Credential Export 2. Escribir "abc" en Role ID          | ✅ Campo rechaza entrada (type="number")           | Validación HTML5              |
| G5  | **Manejo de API timeout (si aplica)**   | 1. Simular red lenta: DevTools throttle 2. Click "Actualizar"   | ✅ Spinner muestra, se reintenta O muestra error   | No se cuelga UI               |
| G6  | **Console sin warnings de Vue/Vuetify** | 1. DevTools → Console 2. Esperar a que componente montes        | ✅ Sin yellowish warnings (deprecated props)       | Si hay: revisar v-text-field  |

---

### ✅ SECCIÓN H: SEGURIDAD Y PERMISOS

**Objetivo**: Verificar que la pantalla respeta permisos y no expone datos cross-tenant

| #   | Caso de Prueba                                   | Pasos                                                                         | Resultado Esperado                                           | Observación             |
| --- | ------------------------------------------------ | ----------------------------------------------------------------------------- | ------------------------------------------------------------ | ----------------------- |
| H1  | **Solo admin/hr_leader pueden acceder**          | 1. Logout y login como usuario regular 2. Navegar a /quality/compliance-audit | ✅ 403 Forbidden O redirige a home                           | Policy check en backend |
| H2  | **Eventos son filtrados por organization_id**    | 1. Estar en Org A 2. Ver EventStore 3. Cambiar a Org B                        | ✅ Eventos diferentes por org (no aparecen eventos de Org A) | tenant middleware       |
| H3  | **Datos ISO30414 pertenecen a org actual**       | 1. Ir sección ISO30414 2. Verificar que datos = org actual                    | ✅ KPIs reflejan solo talento de esta org                    | organization_id scope   |
| H4  | **Roles en Audit Wizard = roles de org actual**  | 1. Scroll a Internal Audit 2. Verificar rol IDs                               | ✅ Solo roles de esta org                                    | FK + scope              |
| H5  | **No hay exposición de datos en error messages** | 1. Ingresar Role ID inválido 2. Exportar VC 3. Ver error                      | ✅ Error genérico (sin SQL, sin paths internos)              | Error handling          |

---

## 3️⃣ SCORECARD QUICK CHECK (PARA DEMO)

Use esta tabla de verificación rápida **antes de presentar al cliente**:

| Aspecto                                   | Estado | Observación                        |
| ----------------------------------------- | ------ | ---------------------------------- |
| ✅ Dashboard carga sin errores            | ☐ Pasa | Console clean                      |
| ✅ Todos los números son correctos        | ☐ Pasa | Verificar vs DB                    |
| ✅ Filtros funcionan                      | ☐ Pasa | Aplicar filtros muestra diferencia |
| ✅ Texto visible (no negro, no duplicado) | ☐ Pasa | Revisar sección D                  |
| ✅ Tablas responsivas en mobile           | ☐ Pasa | Probar en 375px                    |
| ✅ Credencial export funciona             | ☐ Pasa | E2-E3, muestra JSON                |
| ✅ Seguridad: permisos respetados         | ☐ Pasa | H1, H3                             |
| ✅ Performance: < 3 seg carga             | ☐ Pasa | Network tab                        |

---

## 4️⃣ NOTAS OPERATIVAS

### Para Auditor Externo

- Exportar VC de un rol para mostrar credencial verificable
- Validar firma y mostrar que los 4 checks pasan
- Explicar que VC es reproducible: cualquiera puede verificar con DID público

### Para Cliente

- Mostrar Audit Trail como evidencia de "quién cambió qué y cuándo"
- Explicar KPIs ISO30414 como controles de riesgo de talento
- Internal Audit Wizard = validación de firmas en roles críticos
- Destacar que credenciales son externamente verificables

### Para Demo Interna

- Preparar data ficticia: 100+ eventos en últimas 24h
- Tener un rol crítico con VC vigente
- Tener un rol con vigencia vencida (para mostrar "no compliant")
- Mostrar skip de datos inválidos gracefully

---

## 5️⃣ CRITERIO DE ÉXITO

**Dashboard está LISTO para demo si:**

1. ✅ Todos los casos A1-G6 pasan (excepto los que no aplican)
2. ✅ Scorecard Quick Check: 8/8 ☐ Pasa
3. ✅ Auditor/Cliente puede navegar sin ayuda
4. ✅ Performance < 3s
5. ✅ Cero errores console

---

**Última actualización**: 19 de marzo 2026  
**Versión**: 1.0  
**Autor**: QA Manual - Stratos Compliance Team
