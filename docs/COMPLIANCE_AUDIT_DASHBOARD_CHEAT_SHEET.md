# ⚡ CHEAT SHEET - Compliance Audit Dashboard

**Referencia Rápida - 1 Página para Demostración**

---
## 📋 METADATA
- ⏱️ Tiempo de lectura: 3 minutos
- ⏱️ Tiempo de demo: 5 minutos (con esta guía)
- 🎯 Audiencia: Ejecutivos, Sales, Demo Teams
- 📊 Complejidad: Muy Baja
- 🔄 Última actualización: 19 Mar 2026
- ✅ Estado: Listo para Producción

---
## 🎯 USAR CUANDO:
- ✅ Demo rápida a ejecutivo (5 min)
- ✅ Presentación a cliente nuevo
- ✅ Necesitas recordar números clave
- ✅ Prep antes de salida a cliente
- ✅ Sales pitch urgente

---

## 📺 LO QUE VES EN PANTALLA

```
┌─────────────────────────────────────────────────────────────────────┐
│ COMPLIANCE AUDIT DASHBOARD                          [ACTUALIZAR]    │
├─────────────────────────────────────────────────────────────────────┤
│                                                                      │
│ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐ │
│ │ Evt Totales  │ │ Evt 24h      │ │ Tipos Evento │ │ Agregados    │ │
│ │   2,847      │ │   156        │ │   12         │ │   34         │ │
│ └──────────────┘ └──────────────┘ └──────────────┘ └──────────────┘ │
│                                                                      │
│ TABLA: Evento | Agregado | Actor | Fecha                           │
│ [Filtros: event_name, aggregate_type] [Aplicar]                    │
│                                                                      │
│ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐                 │
│ │ Costo Total  │ │ Costo Prom   │ │ Skills Gap   │ ISO 30414       │
│ │ $48.2M       │ │ $150K        │ │ 8            │                 │
│ └──────────────┘ └──────────────┘ └──────────────┘                 │
│ TABLA: Matuez por Depto | TABLA: Brechas de Skills                 │
│                                                                      │
│ ┌──────────────┐ ┌──────────────┐ ┌──────────────┐                 │
│ │ Roles Crítico│ │ Roles Cumpl. │ │ % Cumpl.     │ Audit Wizard    │
│ │ 24           │ │ 21           │ │ 87.5%        │                 │
│ └──────────────┘ └──────────────┘ └──────────────┘                 │
│ TABLA: Roles + Vigencia Firma                                       │
│ [Vigencia: ___] [Recalcular]                                         │
│                                                                      │
│ CREDENTIAL VERIFICATION                                             │
│ [Role ID: ___] [Exportar VC] [Verificar VC]                        │
│ ✅ VC Válida | Checks: ok/ok/ok/ok                                  │
│ {JSON-LD preview}                                                   │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 6 BLOQUES → 6 MENSAJES CLAVE

| Bloque                 | Tarjetas      | Mensaje en 10 seg                                                                                        |
| ---------------------- | ------------- | -------------------------------------------------------------------------------------------------------- |
| **🔍 Audit Trail**     | 4 KPIs        | "2,847 eventos registrados. **Evidencia de quién cambió qué, cuándo.** Cualquiera puede auditar."        |
| **📊 Tabla Eventos**   | Filtrable     | "Filtro: role.updated → muestra cambios de roles. **Trazabilidad 100%.** Timestamp exacto."              |
| **💰 ISO 30414**       | 3 KPIs        | "Riesgo financiero: $48M si pierdes talento. **8 skills tienen brechas** en todo el equipo."             |
| **📈 Madurez x Depto** | Tabla         | "Innovación: 65% lista, Ops: 72% lista. **Visualiza riesgo por unidad organizacional.**"                 |
| **✅ Audit Wizard**    | 3 KPIs        | "87.5% cumplimiento firma. **21 de 24 roles críticos tienen gobernanza vigente.** CRO: expired."         |
| **🔐 Credencial VC**   | Export/Verify | "Rol VP Talento → exporta JSON-LD → verifica 4 checks → **auditable externamente. Sin intermediarios.**" |

---

## 🔥 TOP 3 PUNTOS DE VENTA

### 1. **Auditoría Completa (Bloque: Audit Trail + Tabla)**

✅ 2,847 eventos = cobertura total  
✅ Quién, Qué, Cuándo, Dónde  
✅ Reproducible: filtros activos  
**Pitch**: "Stratos no oculta. Cada cambio está registrado. Si auditor pregunta, tenemos respuesta."

### 2. **Riesgo Cuantificado (Bloque: ISO 30414)**

✅ $48.2M=valor en riesgo  
✅ 8 gaps transversales = vulnerabilidad  
✅ Departamientos ranked por readiness  
**Pitch**: "No es intuición. Ves exactamente dónde el talento es más frágil."

### 3. **Gobernanza Verificable (Bloque: Credential VC)**

✅ Firmas digitales en roles críticos  
✅ 87.5% cumplimiento  
✅ JSON-LD verificable externamente  
**Pitch**: "Cualquier auditor externo valida sin pedir permiso. Transparencia total."

---

## ⏱️ DEMO EN 5 MINUTOS

```
00-30s: "Auditoría completa: cada cambio registrado. Mira: 2,847 eventos."
        → Señala bloque Audit Trail

30-90s: "¿Quién cambió el rol VP Talento? Filtro → role.updated → ves nombre,
         fecha, hora exacta. Evidencia."
        → Ejecuta filtro en tabla

90-120s: "Riesgo de talento: $48.2M si pierdes gente. 8 gaps en skills críticas.
          Innovación es 65% ready. Ves dónde reforzar."
        → Señala KPIs ISO + Tabla madurez

120-180s: "Gobernanza: 24 roles críticos, 21 tienen firma vigente. CRO está expired,
           necesita renovar. VP está bien: vence en 45 días."
         → Señala tabla Internal Audit

180-240s: "¿Prueba independiente? Exporto VC del VP Talento. Verifico: firma válida?
           ✅ Issuer correcto? ✅ Datos del rol? ✅ Auditable externamente."
         → Export + Verify VC

240-300s: "Preguntas?"
```

---

## 🎬 FRASES GANADORES

| Situación                            | Frase                                                                                       |
| ------------------------------------ | ------------------------------------------------------------------------------------------- |
| Auditor pregunta por cobertura       | "2,847 eventos auditables. Cero ocultos. Quién, qué, cuándo, por qué."                      |
| Cliente pregunta por riesgo          | "87.5% gobernanza vigente. 8 skills con brecha crítica. $48M en riesgo si pierdes talento." |
| Partner pregunta por verificabilidad | "JSON-LD. Firma criptográfica. Auditor externo verifica sin intermediarios."                |
| Regulador pregunta por trazabilidad  | "100% trazable. Evento → timestamp → actor → agregado → payload ¯\ (ツ) /¯"                 |

---

## 🔧 COSAS QUE PUEDES HACER EN DIRECTO (DEMO)

| Acción                   | Botones                                | Tiempo | Efecto                              |
| ------------------------ | -------------------------------------- | ------ | ----------------------------------- |
| **Recarga datos**        | "Actualizar" (superior)                | 2 seg  | Actualiza todas las APIs live       |
| **Filtra eventos**       | event_name: "user.created" → "Aplicar" | 1 seg  | Tabla muestra solo nuevos usuarios  |
| **Busca rol específico** | aggregate_type: "Role" → "Aplicar"     | 1 seg  | Tabla muestra solo cambios de roles |
| **Recalcula vigencia**   | Vigencia: "180" → "Recalcular"         | 2 seg  | Muestra roles expiran en < 180 días |
| **Exporta credencial**   | Role ID: "5" → "Exportar VC"           | 2 seg  | Muestra JSON-LD                     |
| **Valida firma**         | Click "Verificar VC"                   | 1 seg  | ✅ o ❌ según validez               |

---

## ⚠️ GOTCHAS (Si algo no funciona)

| Problema                | Causa                    | Solución                           |
| ----------------------- | ------------------------ | ---------------------------------- |
| "Sin datos en tabla"    | Filtros muy restrictivos | Borrar filtros → "Aplicar"         |
| "Números no cambian"    | API no actualiza         | Click "Actualizar" (arriba)        |
| "VC no verifica"        | Role ID no existe        | Ingresar Role ID válido (ej: 1-10) |
| "Texto invisible/negro" | Bug de tema              | F5 (recargar página)               |
| "Consola tiene errores" | Conexión lenta           | Revisar network tab                |

---

## 📋 PRE-DEMO CHECKLIST

```
☐ BD tiene ≥ 100 eventos (si no, seed con scripts/generate_events.sh)
☐ Al menos 1 rol crítico con firma vigente
☐ Al menos 1 rol con firma expirada (para mostrar riesgo)
☐ ISO 30414 tiene datos (departamentos, skills gaps)
☐ Network tab DevTools: APIs < 1s cada una
☐ Console: cero errores rojos
☐ Responsivo: probado en mobile (375px)
☐ Todos los textos legibles (sección D del QA checklist)
```

---

## 🎯 KPIs A MEMORIZAR

| Métrica            | Rango ✅ | Rango ⚠️  | Rango ❌ |
| ------------------ | -------- | --------- | -------- |
| Eventos Totales    | > 1,000  | 500-1,000 | < 500    |
| Eventos 24h        | > 50     | 10-50     | < 10     |
| Cumplimiento Firma | > 85%    | 70-85%    | < 70%    |
| Costo Reemplazo    | < $50M   | $50-100M  | > $100M  |
| Skills con Brecha  | < 5      | 5-10      | > 10     |

---

## 🔗 ENDPOINTS IMPORTANTES

```
GET /api/compliance/audit-events/summary     → 4 KPIs audit trail
GET /api/compliance/audit-events             → 50 eventos filtrados
GET /api/compliance/iso30414/summary         → Riesgo talento
GET /api/compliance/internal-audit-wizard    → Roles críticos
GET /api/compliance/credentials/roles/{id}   → Exporta VC
POST /api/compliance/credentials/roles/{id}/verify → Valida VC
```

---

## 📞 CONTACTO RÁPIDO

- **Si algo no funciona**: Check console (F12) → Network tab → ver respuesta de API
- **Si client pregunta técnico**: "Ver docs en docs/ folder + QA checklist"
- **Si client pregunta de negocio**: "Ver Guía de Interpretación"

---

**Última actualización**: 19 de marzo 2026  
**Imprime esto**: ✅ Si, lleva en la demo  
**Comparte antes de demo**: ✅ Sí, con cliente
