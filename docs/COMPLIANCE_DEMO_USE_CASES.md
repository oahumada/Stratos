# 📌 COMPLIANCE DEMO USE CASES

**Casos de Uso Reales | Narrativas Prácticas | Demostraciones Específicas**

---

## 📋 METADATA

- ⏱️ Tiempo de lectura: 15 minutos
- 🎯 Audiencia: Todos (QA, Cliente, Auditor, Dev)
- 📊 Complejidad: Media
- 🔄 Última actualización: 19 Mar 2026
- ✅ Estado: Completo

---

## 🎯 10 CASOS DE USO PRÁCTICOS

### CASO 1: "¿Quién Cambió el Rol sin Autorizar?"

**Perfil**: Auditor / Compliance Officer  
**Pregunta**: "¿Cómo pruebo que alguien modificó un rol sin autorización?"  
**Duración Demo**: 5 minutos

#### Cómo Demostrar

1. Navega a **Audit Trail** → **Tabla de Eventos**
2. Filtro: `aggregate_type = "Role"` → `event_name = "role.updated"`
3. Verifica: Ver cambio específico
    - **Evento**: role.updated
    - **Agregado**: Role #5 (VP Talento)
    - **Actor**: admin:omar
    - **Timestamp**: 19/03 14:31:42 exacto
4. Payload muestra: qué campos cambiaron, valores antes/después

#### Narrativa

> "Aquí tenemos la prueba: Role VP Talento fue actualizado por Omar el 19 de marzo a las 14:31:42. El evento está registrado inmutablemente. Si quieres, expandimos el payload y ves exactamente qué cambió, quién lo autorizó, y cuándo."

#### Q&A Anticipa

- **"¿Y si borraron el evento?"** → "Los eventos son append-only en EventStore. No se pueden borrar, solo agregar nuevos."
- **"¿Cuánto hacia atrás puedo auditar?"** → "Hasta dondetengas retención (en este demo: 30 días históricos)."

---

### CASO 2: "¿Cuál es Nuestro Riesgo Financiero de Talento?"

**Perfil**: CFO / CEO / HR Director  
**Pregunta**: "¿Si pierdo a mis mejores talentos, cuánto me cuesta reemplazarlos?"  
**Duración Demo**: 5 minutos

#### Cómo Demostrar

1. Abre **ISO 30414 KPIs** (tarjetas superiores)
2. Muestra:
    - **Costo Total de Sustitución**: $48,200,000
    - **Costo Promedio/Persona**: $150,000
    - **Headcount Evaluado**: 89
3. Explica: "Si pierdes a los 89, cuesta $48.2M. Por persona promedio: $150K."
4. Navega a **Tabla: Madurez de Talento por Departamento**
    - Innovación: 65% lista (más riesgo)
    - Operaciones: 72% lista
    - RRHH: 58% lista (más riesgo)
5. Muestra: **Top Brechas de Skills Transversales**
    - Liderazgo Ágil: 32 de 45 con brecha
    - Data-Driven Decision: 28 de 45 con brecha
    - Comunicación: 25 de 45 con brecha

#### Narrativa

> "El riesgo es real y cuantificable. $48 millones si pierdes todos. Pero hay más: 8 skills transversales tienen brechas. Liderazgo Ágil: 71% del equipo no lo domina. Eso magnifica el riesgo. No solo pierdes personas, pierdes expertise que falta reproducar."

#### Q&A Anticipa

- **"¿De dónde salen estos costos?"** → "Basado en complejidad del rol + skills requeridas. Roles con skills únicas = reemplazo más caro."
- **"¿Puedo cambiar los números?"** → "Sí, se pueden parameterizar según salario real de tu org."

---

### CASO 3: "¿Todos Nuestros Roles Críticos Tienen Firma Vigente?"

**Perfil**: Compliance Officer / Internal Auditor  
**Pregunta**: "¿Cuál es nuestro cumplimiento de gobernanza?"  
**Duración Demo**: 5 minutos

#### Cómo Demostrar

1. Abre **Internal Audit Wizard** (3 tarjetas)
    - **Roles Críticos**: 24
    - **Roles Cumpliendo**: 21
    - **% Cumplimiento Firma**: 87.5%
2. Navega a **Tabla: Internal Audit Wizard - Roles**
3. Muestra 3 estados:
    - ✅ **VP Talento**: current, vence en 45 días (OK)
    - ⚠️ **CRO**: expired, -30 días (ACCIÓN REQUERIDA)
    - ❌ **Data Science Lead**: missing (CRÍTICO)
4. Ajusta **"Vigencia (días)"** a 180
    - Se recalcula: muestra roles que vencerán en < 180 días
5. Resultado: "3 roles vencerán en próximos 6 meses"

#### Narrativa

> "Cumplimiento: 87.5%. Significa 21 de 24 roles están gobernados. Pero 3 están en riesgo: el CRO expiró hace 30 días (acción inmediata), Data Science Lead no tiene firma (critico), y 1 vence en 45 días (renovar pronto). Estos son nuestros 3 items abiertos."

#### Q&A Anticipa

- **"¿Qué pasa si no renuevo?"** → "El rol sigue funcionando, pero pierde auditoría verificada. Regulador lo marca como no-compliant."
- **"¿Cuánto cuesta renovar?"** → "Operativamente: 15 minutos. Solo re-firmar con clave digital."

---

### CASO 4: "¿Puedo Probar Esta Gobernanza Externamente?"

**Perfil**: Auditor Externo / Partner  
**Pregunta**: "¿Cómo verifico que la firma es auténtica sin confiar en Stratos?"  
**Duración Demo**: 8 minutos

#### Cómo Demostrar

1. Navega a **Exportación de Credencial Verificable**
2. Ingresa **Role ID**: 1 (VP Talento)
3. Click **"Exportar VC"**
    - Muestra JSON-LD con estructura completa
    - Issuer: did:web:stratos.local
    - @context: W3C credentials standard
4. Click **"Verificar VC"**
    - Sistema verifica 4 checks:
      ✅ model_signature_valid: ok
      ✅ proof_matches_role_signature: ok
      ✅ issuer_matches_expected: ok
      ✅ credential_subject_role_matches: ok
5. Badge: "✅ VC Válida"
6. Explica: "El auditor puede tomar este JSON, ir a nuestra clave pública, y verificar de forma independiente."

#### Narrativa

> "Aquí tenemos la credencial. Cualquiera puede verificarla. No confía en nosotros, confía en la criptografía. Los 4 checks pasan: firma válida, issuer es Stratos, datos del rol correctos. Es auditable externamente, sin intermediarios."

#### Q&A Anticipa

- **"¿Qué pasa si alguien tamper el JSON?"** → "Los 4 checks fallan inmediatamente. Ves cuál check falló."
- **"¿Dónde está tu clave pública?"** → "En nuestro DID document público: /.well-known/did.json"

---

### CASO 5: "¿Cuántos Cambios Hubo en las Últimas 24 Horas?"

**Perfil**: Operations Manager  
**Pregunta**: "¿Cuál es el nivel de actividad en governance?"  
**Duración Demo**: 3 minutos

#### Cómo Demostrar

1. Abre **Resumen Audit Trail**
2. Muestra tarjetas:
    - Eventos Totales (históricos): 2,847
    - **Eventos 24h**: 156 ← Aquí está la actividad reciente
3. Explica: "En 24 horas: 156 cambios. Promedio: ~6.5 cambios/hora."
4. Navega a **Tabla de Eventos**
5. Filtra últimas (tabla muestra descendente por fecha)
6. Muestra: "Aquí están ordenados por recientes. La mayoría de cambios son en últimas horas."

#### Narrativa

> "Actividad: 156 cambios en 24 horas. Sistema está vivo y se usa. Cada cambio está auditado. No hay 'cambios fantasma' que no se vean."

#### Q&A Anticipa

- **"¿Eso es mucho o poco?"** → "Depende de tu org. 156/día = ~6/hora es normal en governance activa."
- **"¿Puedo ver picos de actividad?"** → "Sí, filtrando por fecha/hora específica."

---

### CASO 6: "¿Qué Tipos de Cambios Estamos Rastreando?"

**Perfil**: CTO / Tech Lead  
**Pregunta**: "¿Cuál es la cobertura de auditoría?"  
**Duración Demo**: 3 minutos

#### Cómo Demostrar

1. Abre **Resumen Audit Trail**
2. Tarjeta: **Tipos de Evento**: 12
3. Explica: "12 tipos diferentes de cambios auditados"
4. Navega a **Tabla de Eventos**
5. Muestra ejemplos:
    - user.created
    - user.updated
    - role.updated
    - role.deleted
    - skill.created
    - governance.approved
    - signature.created
    - people_role_skills.updated
    - scenario_published
    - etc.

#### Narrativa

> "Cobertura: 12 tipos de eventos. Usuarios, roles, skills, gobernanza, firmas. Todo lo importante está auditado. Si quieres agregar más tipos, podemos."

---

### CASO 7: "¿Cuántas Entidades Diferentes Fueron Modificadas?"

**Perfil**: Data Analyst  
**Pregunta**: "¿Cuál es el alcance de cambios?"  
**Duración Demo**: 2 minutos

#### Cómo Demostrar

1. **Agregados Únicos**: 34
2. Explica: "34 entidades diferentes fueron tocadas"
3. Desglose (aproximado):
    - 10 usuarios
    - 8 roles
    - 7 skills
    - 6 departamentos
    - 3 otras

#### Narrativa

> "Alcance: 34 entidades tocadas. Usuarios, roles, skills, etc. Eso significa cambios distribuidos en múltiples áreas, no solo en una."

---

### CASO 8: "¿Cuál Departamento Está Más Maduro?"

**Perfil**: HR Director / Talent Manager  
**Pregunta**: "¿Dónde debo enfocar capacitación?"  
**Duración Demo**: 5 minutos

#### Cómo Demostrar

1. Abre **Tabla: Madurez por Departamento**
2. Muestra:
    ```
    Operaciones   72% readiness ✅ (mejor)
    Engineering   65% readiness ⚠️
    Finance       62% readiness ⚠️
    Innovation    58% readiness ❌ (peor)
    RRHH          55% readiness ❌ (peor)
    Ventas        60% readiness ⚠️
    ```
3. Explica: "Operaciones está en mejor shape. Innovation y RRHH necesitan apoyo."
4. Navega a **Brechas por Departamento**
5. Muestra específico:
    - Innovation: 14 brechas, Nivel Actual 3.2, Requerido 4.5
    - RRHH: 12 brechas, Nivel Actual 2.9, Requerido 4.2

#### Narrativa

> "Madurez: Operaciones lidera con 72%. Innovation rezagada con 58%. En Innovation, el nivel actual es 3.2 pero requerimos 4.5. Eso es 0.7 puntos de brecha. Necesitamos enfocarnos ahí."

#### Q&A Anticipa

- **"¿Puedo entrenar específico a Innovation?"** → "Sí. Podemos diseñar planes de desarrollo dirigidos a los gaps."
- **"¿Cuánto mejoraría con capacitación?"** → "Estimado: 0.3-0.5 puntos por trimestre."

---

### CASO 9: "Demostración Rápida para Ejecutivos (5 min)"

**Perfil**: CEO / Junta Directiva  
**Pregunta**: "¿Cómo va nuestro cumplimiento general?"  
**Duración Demo**: 5 minutos EXACTO

#### Script Ejecutivo

```
T0-30s:  "Auditoría completa: 2,847 eventos registrados.
          Cada cambio está grabado. ¿Quién cambió qué? Aquí."
         → Muestra tablaEventos

T30-90s: "Riesgo de talento: $48 millones si pierdes gente.
          8 skills con brechas. Eso es nuestro riesgo."
         → Señala KPIs ISO 30414

T90-150s: "Gobernanza: 87.5% cumplimiento. 21 de 24 roles
           críticos tienen firma vigente. 3 están en riesgo."
         → Tabla Internal Audit Wizard

T150-210s: "Verificabilidad: Cualquier auditor externo puede
            validar. Firma criptográfica. Transparencia total."
         → Exporta + Verifica VC

T210-300s: "Preguntas?"
```

#### Narrativa

> "En resumen: Cobertura 100%, riesgo $48M, cumplimiento 87.5%, verificable externamente. Esos son nuestros 4 pilares de gobernanza."

---

### CASO 10: "Full Demo para Auditor Externo (30 min)"

**Perfil**: Auditor Externo / Certificador  
**Pregunta**: "¿Cómo demostramos que todo es auditable?"  
**Duración Demo**: 30 minutos

#### Agenda Completa

```
00-05m: Overview (Qué es el dashboard)
05-10m: Audit Trail (Quién cambió qué)
10-15m: ISO 30414 (Riesgo de talento)
15-20m: Internal Audit (Gobernanza de firmas)
20-25m: Credenciales VC (Auditoría externa)
25-30m: Q&A / Preguntas del auditor
```

#### Pre-Demo Audit Pack

Preparar:

- ✅ Snapshot de eventos recientes (CSV)
- ✅ 1 VC exportada (JSON)
- ✅ 1 VC verificada (screenshot)
- ✅ 1 VC tampered (screenshot con fallos)
- ✅ Reporte de cumplimiento

#### Durante Demo

1. Abre todos los bloques
2. Filtra eventos por tipo (muestra coverage)
3. Exporta credencial en vivo
4. Verifica en vivo
5. Muestra tampered credential (demo de detección)
6. Responde preguntas técnicas

---

## 📊 COMPARATIVA RÁPIDA: QUÉ DEMOSTRAS EN CADA CASO

| Caso | Demo           | Propósito       | Duración |
| ---- | -------------- | --------------- | -------- |
| 1    | Audit Trail    | Trazabilidad    | 5 min    |
| 2    | ISO 30414 KPIs | Riesgo          | 5 min    |
| 3    | Audit Wizard   | Cumplimiento    | 5 min    |
| 4    | Credencial VC  | Verificabilidad | 8 min    |
| 5    | Actividad 24h  | Operativa       | 3 min    |
| 6    | Tipos eventos  | Cobertura       | 3 min    |
| 7    | Agregados      | Alcance         | 2 min    |
| 8    | Madurez depto  | Desarrollo      | 5 min    |
| 9    | EJecutivo      | Resumen         | 5 min    |
| 10   | Auditor        | Completo        | 30 min   |

---

## 🎯 CÓMO ELEGIR EL CASO APROPIADO

```
Cliente es...          Usa Caso...           Duración
CEO/CFO                #2 + #9               5-10 min
Compliance Officer     #3 + #4 + #9          15-20 min
Auditor Externo        #10                   30 min
HR Director            #8 + #2               10 min
CTO/Tech Lead          #1 + #6 + #4          20 min
Operaciones            #5 + #1               10 min
CFO                    #2                    5 min
```

---

**Última actualización**: 19 de marzo 2026  
**Versión**: 1.0  
**Status**: ✅ Completo y Testeado
