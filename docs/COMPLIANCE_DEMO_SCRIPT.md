# 🎬 COMPLIANCE DEMO SCRIPT

**Narrativa Palabra por Palabra | Timimg Exacto | Variantes por Audiencia**

---

## 📋 METADATA

- ⏱️ Tiempo de lectura: 20 minutos
- 🎯 Audiencia: Todos (QA, Cliente, Auditor, Dev, PM)
- 📊 Complejidad: Media-Alta
- 🔄 Última actualización: 19 Mar 2026
- ✅ Estado: Completo
- 🎤 Narración: Profesional / Técnica / Ejecutiva

---

## 📍 ANTES DE EMPEZAR

### Checklist Pre-Demo

- ✅ Dashboard cargado (sin errores console)
- ✅ Seeder ejecutado (89 personas, 2,847 eventos)
- ✅ Navegador limpio (sin pestañas extra)
- ✅ Scroll en top
- ✅ Volumen OK (si es video)
- ✅ Wifi estable
- ✅ 5 minutos de preparación mental

### Setup Técnico

```bash
# Verificar seeder ejecutado
php artisan tinker
>>> ComplianceAuditEvent::count()  # Debe ser ~2,847
>>> Person::count()                 # Debe ser ~89
>>> Role::count()                   # Debe ser ~24
```

---

## 🎯 DEMO #1: EJECUTIVO (5 MINUTOS EXACTO)

### Audiencia: CEO, CFO, Junta Directiva

### Objetivo: Decision-making en 5 minutos

### Material: Dashboard + Números clave

---

### 📍 MINUTO 0:00 - APERTURA

```
[PANTALLA] Slide título: "Compliance Audit Dashboard"
[NARRACIÓN]

"Buenos días/tardes. En los próximos 5 minutos, quiero mostrarles
cómo controlamos la gobernanza en Stratos. 4 números clave,
4 minutos. Vamos."

[PAUSA 2 segundos]
```

---

### 📍 MINUTO 0:30 - PRIMER KPI

```
[ACCIÓN] Click al dashboard
[TIEMPO ESPERADO] 1-2 segundos para cargar

[NARRACIÓN]

"Primero: Auditoría. Cada cambio en nuestros roles críticos
está registrado. Aquí ven: 2,847 eventos auditados.
Ninguno desaparecer, ninguno es invisible."

[Silencio, deja que asimilen número]

"¿Quién cambió qué? Cuando? Aquí está. Inmutable."
```

---

### 📍 MINUTO 1:30 - SEGUNDO KPI

```
[ACCIÓN] Scroll a ISO 30414 KPIs (tarjetas superiores)
[NARRACIÓN]

"Segundo: Riesgo de talento. Le importa al CFO:
costo de sustitución."

[Señala tarjeta de costo]

"$48 millones si pierdo a mi gente clave. Reemplazar
a 89 personas: eso cuesta. Promedio por persona: $150,000."

[Pausa]

"¿Por qué 150K? Porque estos no son datos entry-level.
Estos son roles que toman 18 meses en reemplazar.
Con faltas de skills."

[Apunta a skill gaps]

"Ven acá: Liderazgo Ágil. 32 de 45 personas con brecha.
71%. Data-Driven Decision: 28 de 45. 62%.
Eso magnifica el costo del reemplazo."
```

---

### 📍 MINUTO 3:00 - TERCER KPI

```
[ACCIÓN] Scroll a Internal Audit Wizard
[NARRACIÓN]

"Tercero: Gobernanza. 87.5% cumplimiento de firmas en roles críticos.
21 de 24.

3 están en riesgo:
- Uno expiró (hace 30 días).
- Uno está faltando (nunca se firmó).
- Uno vence en 45 días (acción urgente necesaria)."

[Pausa]

"Estos 3 son nuestros items abiertos. Son conocidos.
Se está actuando en ellos."
```

---

### 📍 MINUTO 4:15 - CUARTO VALOR

```
[ACCIÓN] Scroll a Credencial Verificable (si no viste antes)
[NARRACIÓN]

"Cuarto: Verificabilidad. Un auditor externo puede validar
que todo es auténtico. Sin confiar en nosotros.
Confía en la criptografía.

Cada firma es un JSON criptograficamente verificable.
Issuer: nosotros. Signature: válida. Role data: correcta.

¿Alguien tampered? Los 4 checks fallan inmediatamente.
Transparencia radical."
```

---

### 📍 MINUTO 5:00 - CIERRE

```
[NARRACIÓN]

"En resumen:
✅ Auditoría: 100% de cobertura. Cada cambio grabado.
✅ Riesgo: Cuantificado. $48M. Visible.
✅ Gobernanza: 87.5% cumplimiento. 3 items abiertos.
✅ Verificabilidad: Auditable externamente.

Preguntas?"

[FIN DEL SCRIPT]
```

---

## 🎯 DEMO #2: COMPLIANCE OFFICER (15 MINUTOS)

### Audiencia: Compliance Officer, Internal Auditor, Risk Manager

### Objetivo: Confianza técnica en trazabilidad + gobernanza

### Material: Dashboard completo + Exports + Q&A

---

### 📍 MINUTO 0:00 - INTRODUCTION

```
[NARRACIÓN]

"Hola. Voy a mostrarles cómo auditamos cambios en gobernanza.
Tres áreas:
1. Auditoría de cambios (Event Sourcing).
2. Gobernanza de firmas (Internal Audit Wizard).
3. Credenciales verificables (Verifiable Credentials).

Tiempo: 15 minutos. Luego preguntas. Vamos."
```

---

### 📍 MINUTO 1:00 - PRESENTACIÓN

```
[ACCIÓN] Abre dashboard
[PAUSA] 2 segundos para cargar
[NARRACIÓN]

"El dashboard tiene 6 bloques. Veamos cada uno brevemente."

[Apunta a bloques en orden]

"1. Resumen de auditoría: números generales.
2. Audit Trail: tabla de eventos detallados.
3. ISO 30414 KPIs: riesgo de talento (nos importa por costo).
4. Internal Audit Wizard: gobernanza de firmas.
5. Exportar credencial verificable: para auditoría externa.
6. Comparación antes/después: cambios recientes.

Focalizarse en: 2, 4, 5. Esos son core."
```

---

### 📍 MINUTO 2:30 - AUDIT TRAIL (TÉCNICO)

```
[ACCIÓN] Scroll a tabla "Audit Trail"
[NARRACIÓN]

"Aquí está el corazón. Audit Trail. Cada cambio es un evento.

Estructura de cada evento:
- Event ID: UUID único (ej: 8f4b-9e2a-11ed-a1eb)
- Aggregate Type: ¿Qué se cambió? (User, Role, Skill, etc.)
- Event Name: ¿Qué pasó? (created, updated, deleted, approved)
- Aggregate ID: ID de la cosa que cambió
- Actor: ¿Quién lo hizo? (user email + rol)
- Timestamp: Cuándo exactamente
- Payload: ¿Qué cambió? (campos antes/después)
- Metadata: Contexto adicional (IP, browser, etc.)

Esto es Event Sourcing. Inmutable. Append-only.
¿Saben Event Sourcing? ¿No? No importa. Idea:
como blockchain, pero para cambios internos."

[Pausa para preguntas técnicas]

"Bonus: Cada evento tiene proof_at_storage.
Timestamp con microsegundos. No puedes fingir el tiempo."
```

---

### 📍 MINUTO 5:00 - AUDIT TRAIL (CASOS)

```
[ACCIÓN] Filtra eventos por aggregate_type = "Role"
[NARRACIÓN]

"Caso 1: Alguien cambió el Role 'VP Talento'.
¿Quién? ¿Cuándo? ¿Qué cambió?

Buscamos: event_name = 'role.updated', Role ID = 5"

[Expande el evento]

"Miren:
- Aggregate: Role#5 (VP Talento)
- Event: role.updated
- Actor: admin:omar
- Timestamp: 2026-03-19T14:31:42.123456Z exacto
- Payload:
```

{
'changes': {
'description': {'old': 'VP de Talento', 'new': 'VP Talento Senior'},
'required_skills': {'old': ['L1','L2'], 'new': ['L1','L2','L3']},
'approval_country': {'old': null, 'new': 'ARG'}
}
}

```

Cada línea del payload cuenta una historia:
qué cambió, de qué a qué, cuándo, quién."

[Apunta a valores específicos]

"Si alguien dice 'nunca cambié eso', aquí está la prueba de que sí.
Si alguien pregunta 'quién lo autorizó', vemos: admin:omar.
Si preguntan 'cuándo exactamente', tenemos microsegundos.

¿Preguntas sobre auditoría de cambios?"

[Pausa para preguntas]
```

---

### 📍 MINUTO 8:00 - INTERNAL AUDIT WIZARD

```
[ACCIÓN] Scroll a Internal Audit Wizard
[NARRACIÓN]

"Ahora: Gobernanza.

Regla: Cada rol crítico necesita firma verificada.
¿Quién firma? El responsable del rol.
¿Cuánto dura la firma? 365 días (renovable).

Estado actual:
- 24 roles críticos identificados
- 21 con firma vigente ✅
- 2 con firma expirada ⚠️
- 1 sin firma ❌

21 / 24 = 87.5% cumplimiento.

¿Está bien? Depende de regulación. En ISO 26000,
esperamos 95%+. Entonces: estamos bajo. Action items:"

[Apunta a roles en riesgo]

"- Role: CRO. Status: Expired. Días: -30 (hace 30 días pasó)
  → Acción: Re-firmar inmediatamente

- Role: Data Science Lead. Status: Missing. Días: N/A
  → Acción: Solicitar firma al responsable

- Role: CFO. Status: Current. Días: +45 (vence en 45 días)
  → Acción: Renovar en próximas 2 semanas"

[Pausa]

"Este wizard es dinámico. Si cambio 'Vigencia requerida'
a 180 días, recalcula y dice: '3 roles vencerán en 180 días'."

[Edita campo, muestra recalculo]

"Eso permite planificación. Saben en avance qué vence."
```

---

### 📍 MINUTO 11:00 - VERIFIABLE CREDENTIALS

```
[ACCIÓN] Scroll a Exportar Credencial Verificable
[NARRACIÓN]

"Parte técnica: Credenciales Verificables (VC).

W3C standard. JSON-LD. Criptografía.

Un role, cuando se firma, genera una VC.
La VC es auditable sin confiar en nosotros.

Vamos a ver un ejemplo:"

[Ingresa Role ID: 1 (VP Talento)]
[Click: Exportar VC]

"Se descargó un JSON. Veamos qué contiene:"

[Abre JSON en editor]

"Estructura:
```

{
'@context': 'https://www.w3.org/2018/credentials/v1',
'type': ['VerifiableCredential'],
'issuer': 'did:web:stratos.local',
'issuanceDate': '2026-03-19T...',
'expirationDate': '2027-03-19T...',
'credentialSubject': {
'id': 'role:5',
'name': 'VP Talento',
'governanceSignature': '...(firma DER)',
'signatureProof': '...(proof that signature matches role)'
},
'proof': {
'type': 'RsaSignature2018',
'created': '...',
'verificationMethod': 'did:web:stratos.local#key-1',
'signatureValue': '...(criptografía RSA-SHA256)'
}
}

```

4 cosas críticas aquí:
1. Issuer: Identificación criptográfica de nosotros.
2. Credential Subject: El role que estamos certificando.
3. Signature: Firma RSA-2048 sobre el contenido.
4. Verification Method: Dónde verificar (nuestra clave pública).

Auditor toma este JSON y verifica offline.
Sin internet hacia nosotros. Sin confiar en UI."

[Pausa]

"Veremos cómo se verifica..."
```

---

### 📍 MINUTO 13:00 - VERIFICACIÓN

```
[ACCIÓN] Click: Verificar VC (o copia JSON en verificador)
[NARRACIÓN]

"Sistema verifica 4 checks:

✅ Check 1: model_signature_valid
   → ¿Es válida la firma RSA en el payload?
   → Resultado: OK. Firma es auténtica.

✅ Check 2: proof_matches_role_signature
   → ¿Coincide el proof del VC con la firma del Role en DB?
   → Resultado: OK. El VC refleja exactamente lo que está guardado.

✅ Check 3: issuer_matches_expected
   → ¿Es el issuer realmente Stratos (nuestra DID)?
   → Resultado: OK. DID es did:web:stratos.local.

✅ Check 4: credential_subject_role_matches
   → ¿El Role ID en el VC coincide con lo que pedimos?
   → Resultado: OK. Role 5 = VP Talento.

RESULTADO FINAL: ✅ Credencial válida y verificable.

¿Qué pasa si alguien intenta tamper?"

[Abre VC tampered (demo data)]

"Acá cambié:
'name': 'VP Talento' → 'name': 'VP Talento - Fake'

Verifica..."

[Verifica, checks fallan]

"❌ Check 2: proof_matches_role_signature - FAILED
   Razón: El proof no coincide con el cambio.
   Alteración detectada.

Auditor ve esto y sabe: alguien trató de falsificar.
Sistema rechaza la VC. Transparencia total."
```

---

### 📍 MINUTO 14:30 - CIERRE

```
[NARRACIÓN]

"Resumiendo gobernanza:

1. Auditoría: 2,847 eventos, cada uno inmutable.
2. Firmas: 87.5% cumplimiento, 3 items en acción.
3. Verificabilidad: VC auditable externamente con 4 checks.

Preguntas sobre técnica, timings, o casos de uso?"

[Esperan preguntas, responden 5-10 minutos]

"Gracias. ¿Qué más necesitan?"
```

---

## 🎯 DEMO #3: AUDITOR EXTERNO (30+ MINUTOS)

### Audiencia: Auditor Externo, Certificador, Regulador

### Objetivo: Confianza completa en trazabilidad + verificabilidad externa

### Material: Dashboard + Exports + Reporte + Q&A técnico

---

### 📍 SECCIÓN 1: OVERVIEW (5 minutos)

```
[NARRACIÓN]

"Bienvenida, [Nombre del Auditor].

Hoy voy a mostrar cómo Stratos implementa
auditoría inmutable de cambios en gobernanza.

Plan:
- Parte 1 (5m): Qué es el dashboard y datos
- Parte 2 (5m): Event Sourcing (cómo guardamos cambios)
- Parte 3 (5m): Internal Audit Wizard (gobernanza)
- Parte 4 (5m): Verifiable Credentials (verificación externa)
- Parte 5 (5m): Q&A técnico
- Parte 6 (5m): Acceso a exportaciones

Total: 30 minutos. Luego preguntas. Vamos."
```

---

### 📍 SECCIÓN 2: DATOS SNAPSHOT (5 minutos)

```
[ACCIÓN] Abre dashboard
[NARRACIÓN]

"Snapshot actual del sistema (19 de marzo, 2026):
- Organizaciones: 1 (Stratos Demo Corporation)
- Departamentos: 6
- Personas: 89
- Roles críticos identificados: 24
- Skills: 12
- Eventos auditados: 2,847
- Eventos últimas 24h: 156
- Tipos de evento: 12
- Agregados únicos modificados: 34

Años de data: simulados 30 días históricos.
Relevancia: suficiente para demostrar auditoría a escala."
```

---

### 📍 SECCIÓN 3: EVENT SOURCING (5 minutos)

```
[ACCIÓN] Abre tabla Audit Trail con todos los filtros limpios
[NARRACIÓN]

"Cómo guardamos cambios: Event Sourcing.

Paradigma alternativo a sobrescritura:
- Tradicional: UPDATE role SET description = X WHERE id = 5
- Event Sourcing: INSERT event 'role.updated' { role_id: 5, changes: {...} }

Beneficio: Historia completa. Nunca se pierde estado anterior.

La tabla tiene 2,847 filas. Cada fila es un evento.
Orden: ID (secuencial) + Timestamp (secuencial).

¿Pueden borrarse? No. ADD ONLY.
¿Pueden editarse? No. Inmutable.
¿Pueden reordenarse? No. Blockchain-like."

[Scroll por table, muestra eventos]

"Ven: cada evento tiene sequencial incremental (id).
Timestamp es descendente (últimos primero).

Tomar cualquier evento random. Tienen acceso completo
a payload, actor, timestamp.
¿Necesitan un CSV de todos los eventos?"

[Muestra Export botón]

"Aquí pueden exportar todos a CSV. Lleva 2-3 segundos.
Archivo contiene todas las 2,847 filas.
Auditable offline. Verifiable signature en metadata."
```

---

### 📍 SECCIÓN 4: GOBERNANZA (5 minutos)

```
[ACCIÓN] Scroll a Internal Audit Wizard
[NARRACIÓN]

"Gobernanza de firmas en roles críticos.

Estándar: cada rol crítico necesita verificación.
Mecanismo: Firma digital del responsable.
Validez: 365 días. Renovable.

Estado actual: 87.5% cumplimiento (21/24).

Roles en riesgo:
1. CRO: Expirado hace 30 días - CRITICO
2. Data Science Lead: Sin firma - CRITICO
3. CFO: Vence en 45 días - ACTION ITEM

¿Pueden explorar cada rol? Claro."

[Click en CRO]

"Rol: CRO
Descripción: Chief Risk Officer
Required Skills: Risk Management, Compliance, Board Interaction
Last Signature: 2025-12-17 (Omar Admin)
Vence en: 2026-03-17 (ya pasó)
Status: EXPIRED

¿Quién puede renovar? El CRO o un admin autorizado.
¿Qué pasa si no renueva? Regulador lo ve como non-compliant.

Aquí está el escalation: esto está documentado,
comunicado, y siendo actado. No es sorpresa."
```

---

### 📍 SECCIÓN 5: VERIFIABLE CREDENTIALS (10 minutos)

```
[ACCIÓN] Exportar VC de 1 role (ej: VP Talento)
[NARRACIÓN]

"Parte central para auditoría externa:
Verifiable Credentials (W3C standard).

Cuando un role se firma, genera una VC.
La VC es:
- Signed: Criptografía RSA-2048
- Verifiable: Sin confiar en nosotros
- Machine-readable: JSON-LD
- Time-stamped: Exacto
- Revocable: Si revocamos, VC queda inválida

Exporté la VC de VP Talento. JSON que ven aquí."

[Muestra JSON]

"Estructura completa. Veamos 3 partes clave:

PARTE 1: Credential Subject (El role que certificamos)
```

'credentialSubject': {
'id': 'role:5',
'name': 'VP Talento',
'department': 'RRHH',
'required_skills': ['L1-Liderazgo', 'L2-Comunicación'],
'approval_country': 'ARG',
'criticality': 'HIGH',
'governanceSignature': 'LS0tLS1CRUdJ...(base64 DER)'
}

```
Esto identifica unívocamente el rol.

PARTE 2: Issuer (Quién emite)
```

'issuer': 'did:web:stratos.local'

```
DID es una identidad criptográfica descentralizada.
Pueden verificar quiénes somos yendo a:
/.well-known/did.json en nuestro dominio.

PARTE 3: Proof (Firma criptográfica)
```

'proof': {
'type': 'RsaSignature2018',
'verificationMethod': 'did:web:stratos.local#key-1',
'signatureValue': 'MIIEowIBAA...(firma RSA-SHA256 de 256 bytes)'
}

```
Esta es la firma. Auditor usa nuestra clave pública
(del DID document) y verifica que la firma es válida.

¿Cómo se verifica?"

[Click: Verificar VC]

"Sistema ejecuta 4 checks:

Check 1: ¿Es la firma RSA válida para el payload?
  resultado: ✅ OK - Firma auténtica

Check 2: ¿Coincide con la firma guardada del role en BD?
  resultado: ✅ OK - VC refleja exactamente lo guardado

Check 3: ¿Es el issuer realmente nosotros?
  resultado: ✅ OK - Issuer es did:web:stratos.local

Check 4: ¿Coincide el role ID del VC con lo esperado?
  resultado: ✅ OK - Role 5 es VP Talento

RESULTADO: 4/4 checks pasan → VC válida ✅

¿Qué pasa si alguien intenta falsificar?"

[Abre VC tampered]

"Tomé el JSON y cambié:
  'name': 'VP Talento' → 'name': 'VP Talento - FAKE PAY RAISE APPROVED'

Verifica..."

[Verifica, Check 2 y 3 fallan]

"Check 2: proof_matches_role_signature - ❌ FAILED
  Razón: El payload fue alterado después de la firma.
  Signature no coincide con contenido.

Check 3: (si cambié issuer) - ❌ FAILED
  Razón: El issuer no es nuestro.

Auditor ve: VC falsificada. Rechazada.
Sistema automaticamente lo identifica y rechaza.
Transparencia + Seguridad."

[Pausa]

"¿Pueden exportar cualquier VC y verificar offline? Sí.
Archivo JSON que llevarse. Clave pública pública.
No necesitan volver a nosotros. Verifican independiente."
```

---

### 📍 SECCIÓN 6: AUDITORÍA DE CAMBIOS (5 minutos)

```
[ACCIÓN] Abre tabla Audit Trail, filtra por role.updated
[NARRACIÓN]

"Auditoría de cambios: trazabilidad completa.

Caso: 'Quiero ver todos los cambios a un role específico en últimos 30 días.'

Filtramos:
- aggregate_type = 'Role'
- event_name = 'role.updated'
- date >= 2026-02-18 (hace 30 días)

Resultado: 12 eventos que tocan este role.

Veamos uno:"

[Expand evento]

"Evento #1847:
- ID: 8f4b-9e2a-11ed-a1eb
- Event: role.updated
- Role: VP Talento (ID: 5)
- Actor: admin:omar (IP: 192.168.1.105)
- Timestamp: 2026-03-15 14:31:42.123456Z
- Cambios payload:
  {
    'description': {
      'old': 'VP de Experto en Talento',
      'new': 'VP Estrategia Talento'
    },
    'required_skills': {
      'old': ['L1', 'L2'],
      'new': ['L1', 'L2', 'L3', 'Strategy']
    }
  }

Cada cambio documentado. No hay sorpresas.
¿Quién lo hizo? Admin Omar.
¿Cuándo? 14:31:42 exactamente.
¿Qué cambió? Description y skills.
¿Fue autorizado? Sí (admin role puede).

Para auditor: trazabilidad de A-Z.
Si alguien dice 'nunca cambié descripción',
aquí está probado que sí."

[Pausa]

"¿Pueden exportar audit trail completo a CSV? Sí.
Click aquí, descarga CSV, verifica offline.
Cada fila es un evento. Puede ser importado en auditoría software."
```

---

### 📍 SECCIÓN 7: CIERRE + Q&A (5 minutos)

```
[NARRACIÓN]

"Conclusión:

Auditoría en Stratos se basa en:
1. Event Sourcing: Cada cambio es un evento inmutable.
2. Criptografía: Cada firma es verificable externamente.
3. Estándares: W3C Verifiable Credentials.
4. Transparencia: Exportable, verificable sin confiar en nosotros.

Capacidades de auditor:
- ✅ Exportar todos los eventos (CSV)
- ✅ Exportar todas las VC (JSON)
- ✅ Verificar VC offline
- ✅ Detectar alteraciones automaticamente
- ✅ Rastrear cambios por role/user/date
- ✅ Acceso completo al dashboard (sin credenciales si es demo)

Cualquier pregunta técnica, metodológica, o de gobernanza
estoy disponible. ¿Preguntas?"

[Esperan q&A, pueden durar 10-15 minutos]
```

---

## 📊 VARIANTES RÁPIDAS POR CONTEXTO

### Variante: AUDITOR VIRTUAL (via Zoom/Teams)

```
Cambios:
- Dashboard: Compartir pantalla, máx 1200x800 resolución
- VC Verification: Demo en navegador separado, zoom 120%
- CSV Exports: Enviar archivo luego, audit trail en email
- Q&A: Prep todas las preguntas técnicas posibles
```

### Variante: DEMO MULTIDIOMA (Spanish/English)

```
Dashboard: Multiidioma soportado en config
Scripts: Preparar 2 versiones (here provided en Spanish)
Technical Terms: Mantener consistencia (Event Sourcing = Event Sourcing)
```

### Variante: DEMO SIN SEEDER (Datos mínimos)

```
Si seeder no execute:
- Crear manualmente 5-10 eventos
- 2-3 roles con firmas en diferentes estados
- Explicar: "Este es set mínimo. Con seeder: 2,847 eventos"
- Usar Case ID en URL para pre-filter
```

---

## 🎯 RESPUESTAS A PREGUNTAS COMUNES

### "¿Por qué Event Sourcing y no just auditoría tradicional?"

```
TRADICIONAL:
UPDATE role SET description = X WHERE id = 5;
SELECT * FROM audit_log WHERE table = 'role' AND row_id = 5;

PROBLEMAS:
- Si audit_log se corrompe, sin historial
- Si descarto old values, no sirve para análisis
- Perfs débil con muchos eventos
- Difícil de paralelizar

EVENT SOURCING:
INSERT INTO event_store (aggregate_type, event_name, payload);

BENEFICIOS:
- Eventos inmutables (append-only)
- Historia completa preservada siempre
- Parallelizable (múltiples readers)
- Temporal queries (¿qué había el 19 de marzo a las 3pm?)
- Event replay (reconstruir estado en cualquier punto)
```

### "¿Qué pasa si evento se corrompe?"

```
RESPUESTA:
En Event Sourcing, un evento corrupto afecta ese evento solo.
Los otros 2,846 son independientes.

Sistema detectaría:
1. Hash/signature no coinciden
2. Payload JSON inválido
3. Actor user_id no existe

Mitigación:
- Backups diarios
- Replicas en múltiples DBs
- Checksums por evento
- Alerts si detecta corrupción
```

### "¿Cuánto cuesta guardar 2,847 eventos?"

```
RESPUESTA:
Por evento: ~500 bytes average (ID, timestamp, payload, metadata)
2,847 × 500 = 1.4 MB

Costo DB (PostgreSQL): Negligible. <$1/month en AWS RDS
Costo storage: ~negligible
Costo CPU: Negligible queries (índices en event_name + aggregate_id)
```

### "¿Pueden hacer querying rápido con 2,847 eventos?"

```
RESPUESTA:
Sí. Con índices apropiados: <100ms

Índices optimizados:
- idx_aggregate_type_id: (aggregate_type, aggregate_id)
- idx_event_name: (event_name)
- idx_created_at: (created_at) DESC
- idx_actor: (actor_id)

Queries típicas:
- "Todos los eventos de Role 5" → <50ms
- "Últimos 100 eventos 24h" → <30ms
- "Cambios por User en mes" → <100ms
```

### "¿Se pueden revocar eventos?"

```
RESPUESTA:
No se revocan (no se borran), pero se pueden anotar.

INSERT INTO event_revocation (original_event_id, reason);

Auditor ve:
- Evento original: action = role.updated, timestamp = 3/19 14:31
- Revocation: reason = "Accidental, please ignore", timestamp = 3/19 15:00

Pero el evento original siempre está visible. Transparencia.
```

---

## 📋 MATERIAL DE APOYO PARA AUDITOR

### A Enviar Pre-Demo

```
1. Esta guía de script (este archivo)
2. CSV de eventos (export completo)
3. Diagrama ERD de Event Store
4. DID document (/.well-known/did.json)
5. Sample VC (1 role, exportado)
6. Comunicación de riesgos abiertos (CRO expired, etc.)
```

### A Tener Disponible Durante

```
1. Dashboard (live)
2. DB access (read-only, si es necesario)
3. VC verification tool (web)
4. CSV export tool
5. Preguntas técnicas preparadas
```

### A Enviar Post-Demo

```
1. Full audit trail CSV
2. Full VC export
3. Compliance checklist (filled)
4. Contact para escalations
5. Schedule para follow-up preguntas
```

---

## ✅ VALIDACIÓN DE SCRIPT

- ✅ Timing exacto
- ✅ Variantes por audiencia
- ✅ Q&A preparadas
- ✅ Respuestas técnicas
- ✅ Material de apoyo

Última actualización: 19 de marzo 2026  
Versión: 1.0  
Status: ✅ Completo y listo para producción
