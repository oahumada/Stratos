# 📖 Guía de Interpretación - Compliance Audit Dashboard

**Explicación operativa de cada datos para Demo**

---

## 🎯 INICIO RÁPIDO: QUÉ VE EL Cliente

Cuando el cliente llega por primera vez, verá **6 bloques de información**. Aquí está qué significa cada uno y **cómo explicarlo en menos de 2 minutos**.

---

## 1️⃣ BLOQUE: RESUMEN DEL AUDIT TRAIL (4 tarjetas superiores)

### Vista del cliente

```
┌─────────────────┬──────────────┬────────────────┬──────────────┐
│ Eventos Totales │ Eventos (24h) │ Tipos de Evento│Agregados Únicos│
│     2,847       │     156      │      12        │      34       │
└─────────────────┴──────────────┴────────────────┴──────────────┘
```

### 🔍 Interpretación Por Tarjeta

| Tarjeta              | Qué significa                                                                    | Por qué importa                                           | Ejemplo de explicación al cliente                                                    |
| -------------------- | -------------------------------------------------------------------------------- | --------------------------------------------------------- | ------------------------------------------------------------------------------------ |
| **Eventos Totales**  | Número de cambios registrados desde que Stratos comenzó                          | Base de auditoría: cuánto se ha modificado                | "2,847 cambios auditados. Cada línea = un cambio verificable de quién, qué, cuándo"  |
| **Eventos (24h)**    | Cambios en las últimas 24 horas                                                  | Actividad reciente: sistema está vivo y registrando       | "156 cambios hoy. Significa 156 acciones registradas (crear user, editar rol, etc.)" |
| **Tipos de Evento**  | Cuántas categorías diferentes de eventos hay                                     | Cobertura de auditoria: qué tipos de acciones se capturan | "12 tipos: user.created, role.updated, skill.deleted, etc. Cubre toda la gobernanza" |
| **Agregados Únicos** | Cuántos "objetos" principales fueron modificados (roles, usuarios, skills, etc.) | Alcance: qué entidades están siendo auditadas             | "34 entidades diferentes fueron tocadas (3 roles, 10 usuarios, 21 skills, etc.)"     |

### 💡 Cómo Explicarlo

**En 30 segundos:**

> "Stratos registra CADA cambio. Ves 2,847 eventos totales: significa que hay 2,847 cambios auditables en el sistema. En las últimas 24 horas hubo 156 cambios. Son 12 tipos diferentes de cambios, atravesando 34 entidades diferentes. Esto es EVIDENCIA: si dicen 'alguien cambió esto', podemos probarlo."

---

## 2️⃣ BLOQUE: TABLA DE EVENTOS (Audit Trail Detallado)

### Vista del cliente

```
┌──────────────────┬─────────────────┬────────────┬──────────────────────┐
│ Evento           │ Agregado        │ Actor      │ Fecha                │
├──────────────────┼─────────────────┼────────────┼──────────────────────┤
│ user.created     │ User #2851      │ admin:omar │ 19/03 14:32:15       │
│ role.updated     │ Role #5         │ admin:omar │ 19/03 14:31:42       │
│ scenario.deleted │ Scenario #112   │ Sistema    │ 19/03 14:15:33       │
│ ...              │ ...             │ ...        │ ...                  │
```

### 🔍 Interpretación Por Columna

| Columna      | Qué significa                               | Ejemplos                                                              | Por qué importa               |
| ------------ | ------------------------------------------- | --------------------------------------------------------------------- | ----------------------------- |
| **Evento**   | Tipo de acción realizada                    | `user.created`, `role.updated`, `skill.deleted`, `scenario_published` | Identifica QUÉ pasó           |
| **Agregado** | El objeto que fue modificado (ID + tipo)    | `User #2851` = Usuario con ID 2851, `Role #5` = Rol con ID 5          | Identifica QUIÉN fue afectado |
| **Actor**    | Quién ejecutó el cambio                     | `admin:omar` = Usuario Omar, `Sistema` = Cambio automático            | Identifica QUIÉN lo hizo      |
| **Fecha**    | Cuándo ocurrió el cambio (timestamp exacto) | `19/03 14:32:15` = 19 marzo 2026, 14:32:15 UTC                        | Identifica CUÁNDO             |

### 💡 Narrative Example

**Client pregunta:** "¿Puedo saber si alguien cambió el rol de VP de Talento?"
**Respuesta con tabla:**

> "Sí. Buscamos 'role.updated' → ves que Role #5 fue actualizado por admin:omar a las 14:31:42. Aquí hay el evento. Si quieres el detalle (qué campos cambiaron), expandimos el JSON del payload."

### 🎮 Filtros

- **"Filtrar por event_name"**: Escribe `user.created` para ver solo nuevos usuarios
- **"Filtrar por aggregate_type"**: Escribe `Role` para ver solo cambios de roles
- **Aplicar**: Refresca la tabla

---

## 3️⃣ BLOQUE: ISO 30414 - RIESGO DE TALENTO (3 tarjetas KPI)

### Vista del cliente

```
┌─────────────────────────────┬──────────────────────┬──────────────────────┐
│ Costo Total de Sustitución  │ Costo Promedio/Persona│ Skills Transversales │
│ $48,200,000                 │ $150,000              │ con Brecha: 8        │
│ (89 headcount evaluados)    │                       │                      │
└─────────────────────────────┴──────────────────────┴──────────────────────┘
```

### 🔍 Interpretación Por Tarjeta

| Tarjeta                             | Qué significa                                                          | Cálculo                                        | Por qué importa                                                                    |
| ----------------------------------- | ---------------------------------------------------------------------- | ---------------------------------------------- | ---------------------------------------------------------------------------------- |
| **Costo Total de Sustitución**      | Si perdieras TODO el talento evaluado, cuánto te costaría reemplazarlo | Suma de (headcount × replacement_cost_by_role) | Identifica riesgo financiero de pérdida de talento                                 |
| **Costo Promedio/Persona**          | Promedio que cuesta reemplazar a UN empleado                           | Total / Headcount                              | Prioriza roles más caros. Si alguien tiene skills únicas, su reemplazo es más caro |
| **Skills Transversales con Brecha** | Cuántas competencias transversales faltan en el equipo                 | Count de skills donde avg_gap > umbral         | Identifica vulnerabilidades: si 8 skills tienen brecha generalizada, hay riesgo    |

### 💡 Narrative Example

**Client ejecutivo:** "¿Qué riesgo tenemos si se va una persona clave?"
**Respuesta:**

> "Mira: el costo promedio de reemplazar a alguien en tu organización es $150,000. Si perdieras a los 89 talentos evaluados, sería $48.2M. Además, hay 8 skills transversales donde el equipo tiene brechas: Liderazgo Ágil, Comunicación Intercultural, etc. Esto magnifica el riesgo: no solo pierdes a la persona, sino expertise que falta en otros."

### 📊 Tablas Detalladas (Debajo de KPIs)

#### Tabla: Madurez de Talento por Departamento

```
┌────────────────┬──────────┬──────────┬─────────────┬──────────────┬────────┐
│ Departamento   │ Headcount│ Readiness│ Nivel Actual│ Nivel Requer.│ Brechas│
├────────────────┼──────────┼──────────┼─────────────┼──────────────┼────────┤
│ Innovación     │ 12       │ 65%      │ 3.2         │ 4.5          │ 14     │
│ Operaciones    │ 23       │ 72%      │ 3.8         │ 4.1          │ 8      │
│ RRHH           │ 8        │ 58%      │ 2.9         │ 4.2          │ 12     │
```

**Cómo leerlo:**

- **Innovación // Readiness 65%**: "Innovación está 65% lista para el nivel requerido. Hay 14 brechas de skills."
- **Operaciones // Nivel Actual 3.8 vs Requerido 4.1**: "Operaciones está 0.3 niveles por debajo del promedio requerido."

#### Tabla: Top Brechas de Capacidades Transversales

```
┌─────────────────────┬──────────┬───────────────┬──────────────┬──────────────┐
│ Skill               │ Dominio  │ Personas Eval.│ Con Brecha   │ Brecha Prom. │
├─────────────────────┼──────────┼───────────────┼──────────────┼──────────────┤
│ Liderazgo Ágil      │ Liderazgo│ 45            │ 32           │ 1.8          │
│ Data-Driven Decision│ Analytics│ 45            │ 28           │ 1.6          │
│ Comunicación Cultura│ Soft     │ 45            │ 25           │ 1.4          │
```

**Cómo leerlo:**

- **Liderazgo Ágil // 32 de 45 con brecha**: "71% del equipo ha sido evaluado en Liderazgo Ágil y el 71% tiene brecha."
- **Brecha Promedio 1.8**: "En promedio, les falta 1.8 niveles para llegar al requerido."

---

## 4️⃣ BLOQUE: INTERNAL AUDIT WIZARD (3 tarjetas KPI)

### Vista del cliente

```
┌──────────────────┬────────────────┬─────────────────────┐
│ Roles Críticos   │ Roles Cumpliendo│ % Cumplimiento Firma│
│ 24               │ 21              │ 87.5%              │
└──────────────────┴────────────────┴─────────────────────┘
```

### 🔍 Interpretación Por Tarjeta

| Tarjeta                  | Qué significa                           | Importancia                            | Threshold        |
| ------------------------ | --------------------------------------- | -------------------------------------- | ---------------- |
| **Roles Críticos**       | Roles donde el reemplazo/riesgo es alto | Estos roles REQUIEREN firma/validación | 24 = ejemplo     |
| **Roles Cumpliendo**     | Roles que tienen firma VIGENTE          | Gobernanza verificada                  | 21 de 24 = 87.5% |
| **% Cumplimiento Firma** | Porcentaje de roles con firma vigente   | KPI: > 85% es buen cumplimiento        | 87.5% ✅         |

### 💡 Baseline de Éxito

- **> 90%**: Excelente cumplimiento
- **80-89%**: Cumplimiento normal, revisar los 15-20% faltantes
- **< 80%**: Riesgo alto, requiere acción
- **A6 meses (180 días vigencia)**: Firmas vencidas pronto deben renovarse

### 📊 Tabla: Roles Críticos y Vigencia de Firma

```
┌──────────────────────┬────────┬───────────────┬──────────────┬──────────────┐
│ Rol                  │ Depto  │ Skills críticas│ Estado firma │ Edad firma   │
├──────────────────────┼────────┼───────────────┼──────────────┼──────────────┤
│ VP Talento           │ RRHH   │ 5             │ current      │ 45 días      │
│ CRO                  │ Ventas │ 8             │ expired      │ -30 días ⚠️   │
│ Head Data Engineering│ Tech   │ 6             │ missing      │ N/A ⚠️        │
│ Controller           │ Finance│ 4             │ current      │ 120 días     │
```

**Cómo leerlo:**

- **VP Talento // current // 45 días**: "Su firma es válida y vence en 45 días. ✅ OK, pero será necesario renovar en < 1 mes."
- **CRO // expired // -30 días**: "⚠️ RIESGO: Su firma expiró hace 30 días. Acción requerida: renovar firma."
- **Head Engineering // missing**: "⚠️ CRÍTICO: No tiene firma. Debe firmar immediatamente."

**Cómo filtrar:**

- Cambiar **"Vigencia (días)"** a 90 → muestra solo roles con firma vigencia < 90 días (próximos a vencer)

---

## 5️⃣ BLOQUE: EXPORTACIÓN DE CREDENCIAL VERIFICABLE (VC/JSON-LD)

### Vista del cliente

```
╔═══════════════════════════════════════════════════════════════╗
║ Exportación Verifiable Credential (VC/JSON-LD)               ║
║                                                               ║
║ [Role ID: _____] [Exportar VC] [Verificar VC] ← Botones      ║
║                                                               ║
║ Badge: ✅ VC Válida                                           ║
║ Checks:                                                       ║
║  - model_signature_valid: ok                                  ║
║  - proof_matches_role_signature: ok                           ║
║  - issuer_matches_expected: ok                                ║
║  - credential_subject_role_matches: ok                        ║
║                                                               ║
║ {                      ← JSON-LD legible                       ║
║   "@context": [...],                                          ║
║   "type": ["VerifiableCredential", "RoleCredential"],         ║
║   "issuer": {...},                                            ║
║   "credentialSubject": {...}                                  ║
║ }                                                              ║
╚═══════════════════════════════════════════════════════════════╝
```

### 🔍 ¿Qué es una Credencial Verificable (VC)?

**Para el cliente:**

> "Una credencial verificable es como un certificado digital con firma criptográfica. Prueba que el rol fue validado internamente y que la firma es auténtica. Cualquier tercero (auditor, partner) puede verificarla sin preguntar."

### 📋 Pasos: Exportar y Validar una VC

1. **Ingresa Role ID** (ej: `5` = VP Talento)
2. **Click "Exportar VC"**
    - Backend genera JSON-LD con firma digital
    - Se muestra en el bloque `<pre>` (fondo gris)
3. **Click "Verificar VC"**
    - Backend valida 4 checks:
        - `model_signature_valid`: ¿Firma es auténtica?
        - `proof_matches_role_signature`: ¿Firma del rol coincide con la prueba?
        - `issuer_matches_expected`: ¿Emisor es Stratos?
        - `credential_subject_role_matches`: ¿Datos del rol están correctos?
4. **Resultado**
    - ✅ **VC Válida** = Todos 4 checks = OK
    - ❌ **VC Inválida** = Al menos 1 check = FAIL

### 💡 Narrative para Demo

**Auditor:** "¿Cómo pruebas que un rol está certificado?"

**Respuesta:**

> "Exportamos la credencial del rol. Ves el JSON-LD aquí. Luego verificamos: ¿La firma es válida? ✅ ¿Pertenece a Stratos? ✅ ¿Los datos del rol son correctos? ✅ Si todos los checks pasan, la credencial es verificable. Y el auditor puede tomar este JSON y verificarlo de forma independiente con nuestra clave pública."

---

## 6️⃣ BLOQUE: FILTROS Y RECÁLCULO

### Parámetros Configurables

| Parámetro           | Dónde                 | Efecto                                       | Uso en Demo                            |
| ------------------- | --------------------- | -------------------------------------------- | -------------------------------------- |
| **event_name**      | Filtros Audit Trail   | Filtra eventos por tipo (ej: `user.created`) | "Muestra solo nuevos usuarios creados" |
| **aggregate_type**  | Filtros Audit Trail   | Filtra por entidad (ej: `Role`)              | "Muestra solo cambios de roles"        |
| **Vigencia (días)** | Internal Audit Wizard | Recalcula roles con firma próxima a vencer   | "¿Roles próximos a vencer en 90 días?" |

### Botones de Acción

| Botón               | Se Encuentra En     | Efecto                                     |
| ------------------- | ------------------- | ------------------------------------------ |
| **Actualizar**      | Encabezado superior | Refresca todos los datos desde BD          |
| **Aplicar filtros** | Area de filtros     | Refresca tabla con filtros aplicados       |
| **Recalcular**      | Internal Audit      | Recalcula roles según vigencia configurada |
| **Exportar VC**     | Credential section  | Genera JSON-LD para un Role ID             |
| **Verificar VC**    | Credential section  | Valida firma de la VC exportada            |

---

## 🎬 DEMO NARRATIVE (3 MINUTOS)

Aquí está cómo presentar esto a un cliente ejecutivo:

### Apertura (30 seg)

> "Este es el Compliance Audit Dashboard. Stratos registra CADA cambio en el sistema. Objetivo: prueba de quién cambió qué, cuándo y por qué."

### Punto 1: Audit Trail (45 seg)

> "Ves 2,847 eventos en total. Los últimos 156 cambios en 24 horas. Cuando dices 'alguien cambió una política', aquí está la evidencia. Evento: `policy.updated`. Rol #5. Actor: admin. Fecha: 19/03 14:31. Reproducible."

### Punto 2: Riesgo de Talento (45 seg)

> "ISO 30414 nos dice: si pierdes talento crítico, ¿cuál es el costo? $48M en reemplazo. Pero hay más: 8 skills tienen brechas en todo el equipo. Innovación está 65% lista, Operaciones 72%. Ves exactamente dónde reforzar."

### Punto 3: Gobernanza Verificada (45 seg)

> "Los roles críticos necesitan firma vigente. 24 roles críticos, 21 tienen firma válida = 87.5% cumplimiento. El CRO está expired. Aquí exportamos su credencial y verificamos: ¿Es válida? Los 4 checks pasan → ✅ Auditable."

---

## 📝 TABLA RÁPIDA: QUÉ SIGNIFICA CADA NÚMERO

| Número                | Nombre                 | Rango "Bueno"         | Rango "Malo" | Acción                         |
| --------------------- | ---------------------- | --------------------- | ------------ | ------------------------------ |
| Eventos Totales       | Cobertura de auditoria | > 1,000               | < 100        | Si < 100: sistema subregistro  |
| Eventos 24h           | Actividad reciente     | > 50                  | < 10         | Si < 10: poco cambio (posible) |
| Tipos de Evento       | Cobertura de tipos     | > 10                  | < 5          | Si < 5: revisar qué se pierde  |
| Costo Total Reemplazo | Riesgo financiero      | Vs benchmark industry | N/A          | Semanal review si > 10M        |
| % Cumplimiento Firma  | Gobernanza verificada  | > 85%                 | < 70%        | Si < 70%: acción inmediata     |

---

**Última actualización**: 19 de marzo 2026  
**Versión**: 1.0  
**Para**: Demos internas, auditorías externas, presentaciones ejecutivas
