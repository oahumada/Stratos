# 🗂️ BACKLOG V2.0 OPERATIVO - Stratos

**Última actualización:** 1 Abr, 2026 (plan del día)  
**Fuente:** `docs/PENDIENTES_2026_03_26.md` (cierres por transferencia a backlog)

## Objetivo

Convertir iniciativas diferidas en trabajo ejecutable semanal con prioridad, owner y criterio de cierre.

## Backlog priorizado

| ID    | Iniciativa                                                               | Prioridad | Owner sugerido    | Fecha objetivo | Estado      | Criterio de cierre                                                                               |
| ----- | ------------------------------------------------------------------------ | --------- | ----------------- | -------------- | ----------- | ------------------------------------------------------------------------------------------------ |
| V2-01 | Pulido frontend LMS (UX + validaciones cliente + selector de plantillas) | Alta      | Frontend Lead     | 3 Abr, 2026    | Cerrado     | Flujo completo de edición/guardado validado + feedback visual + accesibilidad básica aplicada    |
| V2-02 | Notificaciones LMS extendidas (plantillas + canal Slack/In-app)          | Media     | Backend Lead      | 11 Abr, 2026   | Pendiente   | Plantillas revisadas + canal adicional integrado + prueba funcional por canal                    |
| V2-03 | Runbook operativo `lms:sync-progress` en producción                      | Alta      | DevOps Lead       | 5 Abr, 2026    | En progreso | Cron verificado en entorno objetivo + monitoreo y alerta activa + checklist de operación firmado |
| V2-04 | SSO LMS con proveedores externos (LinkedIn Learning / SuccessFactors)    | Media     | Integrations Lead | 22 Abr, 2026   | Pendiente   | Diseño de integración aprobado + PoC funcional de al menos 1 proveedor                           |
| V2-05 | Analytics LMS (eventos/KPIs + tasa de certificación por curso)           | Alta      | Data/BI Lead      | 15 Abr, 2026   | En progreso | Taxonomía de eventos definida + tablero base con KPIs mínimos visibles                           |
| V2-06 | Integración People Experience en Scenario Planning                       | Media     | Scenario Lead     | 18 Abr, 2026   | Pendiente   | Contrato de datos definido + endpoint integrado + validación funcional de extremo a extremo      |

## Plan de hoy (1 Abr, 2026)

### Foco operativo

1. **V2-03** — Runbook operativo `lms:sync-progress`
2. **V2-01** — Pulido frontend LMS (validaciones y feedback UX)
3. **V2-05** — Definición inicial de eventos/KPIs de analytics LMS

### Entregables del día

- **V2-03:** checklist operativo + validación de cron objetivo + propuesta de alerta mínima
- **V2-01:** lista cerrada de validaciones cliente + criterios de feedback de guardado
- **V2-05:** borrador de taxonomía de eventos + 5 KPIs mínimos

### Criterio de cierre del día

- Estados actualizados en este backlog
- Evidencia documental enlazable (checklist/notas)
- Traspaso claro de pendientes al siguiente día

## Cadencia de seguimiento

- **Ritmo:** revisión semanal (lunes) + corte de avance (viernes)
- **Regla de estado:** `Pendiente` → `En progreso` → `Bloqueado` o `Cerrado`
- **Definición de “Cerrado”:** evidencia técnica (PR/commit), validación funcional y actualización documental

## Riesgos de ejecución

- Dependencias de terceros para SSO pueden mover fechas objetivo.
- Disponibilidad de canal In-app/Slack puede requerir alineación con plataforma de mensajería.
- Instrumentación de analytics depende de definición de eventos transversales.
