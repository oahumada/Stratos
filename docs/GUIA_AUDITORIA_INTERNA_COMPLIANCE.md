# 🛡️ Guía Operativa de Auditoría Interna de Compliance - Stratos

Esta guía orienta al equipo para ejecutar auditorías internas de compliance de forma **expedita, transparente, repetible y auditable**, usando las capacidades ya implementadas en Stratos.

---

## 1. Objetivo

Verificar periódicamente que los controles de cumplimiento de Stratos estén operando como fueron diseñados y que exista evidencia suficiente para sostener una revisión formal interna o una auditoría externa posterior.

### Objetivos específicos

- Confirmar trazabilidad de eventos relevantes en `EventStore`.
- Verificar vigencia de firmas digitales en roles críticos.
- Validar controles de privacidad, consentimiento y purga GDPR.
- Revisar integridad y verificabilidad de credenciales VC/JSON-LD.
- Detectar desviaciones antes de una revisión por terceros.

---

## 2. Alcance sugerido

La auditoría interna debe cubrir, como mínimo:

1. **Gobernanza y Audit Trail**
    - Dashboard de auditoría
    - Eventos de cumplimiento
2. **Capital Humano / ISO 30414**
    - Métricas agregadas y brechas
3. **Seguridad y Privacidad**
    - Consentimiento IA
    - Purga GDPR
    - Encriptación at-rest
4. **Certificación Externa / Fase 4**
    - Internal Audit Wizard
    - VC/JSON-LD
    - DID document
    - Verificación pública de credenciales

---

## 3. Frecuencia recomendada

- **Mensual:** revisión operativa rápida (30-60 min)
- **Trimestral:** auditoría interna completa (2-4 horas)
- **Previo a auditoría externa:** dry-run formal con checklist completo

---

## 4. Roles mínimos

| Rol                           | Responsabilidad                                  |
| :---------------------------- | :----------------------------------------------- |
| Compliance Lead / ISO Manager | Coordina la auditoría y firma hallazgos          |
| Product Owner / HR Tech Lead  | Aporta contexto funcional y valida desvíos       |
| Responsable Técnico           | Extrae evidencia del sistema y explica controles |
| Observador / Stakeholder      | Valida transparencia del proceso                 |

---

## 5. Evidencia mínima requerida

Antes de iniciar, reunir:

- Acceso al dashboard [resources/js/pages/Quality/ComplianceAuditDashboard.vue](resources/js/pages/Quality/ComplianceAuditDashboard.vue)
- Acceso autenticado a endpoints de compliance
- Copia vigente de [docs/quality_compliance_standards.md](docs/quality_compliance_standards.md)
- Evidencia de pruebas recientes de compliance
- Lista de roles críticos y responsables funcionales
- Configuración del issuer DID y versionado de políticas

---

## 6. Preparación previa (T-3 a T-1 días)

### Paso 1. Congelar alcance

Definir explícitamente:

- período auditado
- módulos incluidos
- organizaciones/tenants incluidos
- tipo de revisión: operativa, trimestral o pre-externa

### Paso 2. Preparar evidencia base

Recolectar:

- resumen de eventos de auditoría
- reporte de `Internal Audit Wizard`
- al menos 1 VC exportada de rol crítico
- verificación pública de una credencial válida
- verificación pública de un caso tampered

### Paso 3. Preparar muestra

Seleccionar una muestra mínima:

- 3 roles críticos firmados
- 1 rol crítico con firma faltante o expirada (si existe)
- 1 consentimiento IA registrado
- 1 caso GDPR `dry-run`
- 1 caso GDPR ejecutado
- 1 ejemplo de perfil psicométrico cifrado

---

## 7. Ejecución paso a paso

## 7.1 Apertura de auditoría

1. Registrar fecha, hora y responsables.
2. Confirmar alcance y período.
3. Nombrar dueño de cada bloque de evidencia.
4. Abrir acta de auditoría interna.

### Acta mínima

- fecha
- auditor responsable
- participantes
- objetivo
- alcance
- hallazgos
- acciones correctivas
- fecha compromiso

---

## 7.2 Revisión de Audit Trail

Validar:

- que existan eventos en `/api/compliance/audit-events`
- que el resumen `/api/compliance/audit-events/summary` sea consistente
- que el filtrado por evento y agregado funcione
- que el scope por organización esté preservado

### Evidencia a capturar

- screenshot del dashboard
- export del resumen
- ejemplo de 3 eventos críticos

### Preguntas de control

- ¿Quién ejecutó la acción?
- ¿Cuándo ocurrió?
- ¿Sobre qué agregado?
- ¿La evidencia es suficiente para reconstruir el hecho?

---

## 7.3 Revisión de roles críticos y firmas

Usar `Internal Audit Wizard` para revisar:

- total de roles críticos
- roles conformes
- roles no conformes
- tasa de cumplimiento
- firmas `current`, `expired`, `missing`

### Criterio de aceptación

- Todo rol crítico debe tener firma vigente o plan de remediación aprobado.

### Hallazgos típicos

- firma vencida
- firma ausente
- rol crítico no identificado como tal
- desfase entre owner funcional y control técnico

---

## 7.4 Revisión de consentimiento IA

Validar en eventos:

- `consent.ai_processing.accepted`
- `consent.ai_processing.revoked`

### Revisar

- actor
- fecha/hora
- versión de política
- persona afectada
- notas relevantes

### Criterio de aceptación

Debe existir trazabilidad suficiente para demostrar aceptación o revocación por sujeto/actor.

---

## 7.5 Revisión GDPR

Ejecutar dos comprobaciones:

1. `dry-run` de purga
2. caso ejecutado de purga

### Verificar

- impacto estimado
- anonimización aplicada
- soft delete realizado
- evento `gdpr.purge.executed`

### Criterio de aceptación

La purga debe dejar evidencia, no debe romper trazabilidad y debe remover/anonimizar PII según diseño.

---

## 7.6 Revisión de cifrado at-rest

Confirmar por evidencia técnica/documental que existe cifrado y backcompat en:

- `Roles`
- `LLMEvaluation`
- `PsychometricProfile`

### Revisar específicamente

- presencia de mutators/accessors de cifrado
- fallback legacy plaintext
- tests verdes asociados

### Criterio de aceptación

Los campos sensibles definidos deben estar protegidos sin romper lecturas históricas.

---

## 7.7 Revisión de credenciales verificables

Para al menos un rol crítico:

1. exportar VC
2. verificar VC autenticada
3. verificar VC pública
4. probar un caso con `proof.jws` alterado

### Validar checks

- `proof_matches_role_signature`
- `issuer_matches_expected`
- `credential_subject_role_matches`
- `credential_subject_organization_matches`

### Criterio de aceptación

- credencial original válida
- credencial adulterada inválida
- DID y metadata accesibles públicamente

---

## 8. Criterios de salida

La auditoría interna se considera cerrada si:

- se ejecutó la revisión completa
- existe acta con hallazgos
- cada hallazgo tiene responsable y fecha compromiso
- la evidencia quedó archivada
- el resultado fue comunicado a stakeholders

---

## 9. Clasificación de hallazgos

| Nivel   | Descripción                                      | Acción esperada    |
| :------ | :----------------------------------------------- | :----------------- |
| Crítico | Rompe trazabilidad, privacidad o verificabilidad | Corregir inmediato |
| Alto    | Riesgo material antes de auditoría externa       | Corregir < 7 días  |
| Medio   | Desvío controlado                                | Plan < 30 días     |
| Bajo    | Mejora documental/operativa                      | Backlog priorizado |

---

## 10. Entregables mínimos

Al cierre, producir:

1. Acta de auditoría interna
2. Lista de hallazgos
3. Plan de remediación
4. Evidencia adjunta o indexada
5. Recomendación: `apto`, `apto con acciones`, o `no apto`

---

## 11. Checklist expedito

### Preparación

- [ ] Alcance definido
- [ ] Período auditado definido
- [ ] Roles definidos
- [ ] Evidencia base reunida
- [ ] Muestra seleccionada

### Ejecución

- [ ] Audit Trail revisado
- [ ] Internal Audit Wizard revisado
- [ ] Consentimiento IA revisado
- [ ] GDPR revisado
- [ ] Cifrado at-rest revisado
- [ ] VC exportada y verificada
- [ ] Caso tampered verificado

### Cierre

- [ ] Hallazgos clasificados
- [ ] Acciones correctivas asignadas
- [ ] Evidencia archivada
- [ ] Stakeholders notificados

---

## 12. Recomendación práctica

Para mantener el proceso **rápido y transparente**:

- usar siempre la misma plantilla de acta
- no auditar todo manualmente si ya existe evidencia automática
- registrar cada desviación con dueño y fecha
- usar el dashboard de compliance como punto único de verdad
- correr la auditoría interna antes de cualquier visita de terceros

---

## 13. Resultado esperado

Si esta guía se sigue correctamente, Stratos llega a auditoría externa con:

- evidencia ordenada
- hallazgos conocidos
- remediaciones trazadas
- narrativa clara de control
- mínima fricción operativa
