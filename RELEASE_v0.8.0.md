# 🚀 Release v0.8.0 - Workforce + LMS Integration Snapshot

**Fecha:** 2026-04-03  
**Versión:** v0.8.0  
**Tag:** `v0.8.0`  
**Estado:** ✅ Listo para publicar

---

## Resumen

Este release consolida un snapshot amplio de integración entre Workforce Planning y LMS, incluyendo:

- endpoints y servicios nuevos para Workforce y LMS,
- mejoras de gobernanza, seguridad y observabilidad,
- cobertura de pruebas funcionales y de autorización,
- documentación operativa de Go Live para Workforce.

---

## Cambios destacados

### Workforce Planning

- API para demand lines, action plan, baseline/comparativos, monitoreo y resumen enterprise.
- Reglas de transición de estado y guardas por estado (`409`) en operaciones bloqueadas.
- Umbrales configurables por organización y trazabilidad de auditoría.
- Guía de Go Live actualizada con evidencia DEV y acta Go/No-Go condicional.

### LMS

- Endpoints y servicios para analytics, políticas de curso, intervenciones y sincronización.
- Comandos de consola para chequeo/monitor de sync.
- Validaciones de permisos/tenant y pruebas de seguridad para endpoints LMS.

### Calidad y pruebas

- Nuevas suites Feature/Console/Browser en Workforce y LMS.
- Cobertura de auth (`401`), permisos (`403`), tenant isolation (`404`) y validación (`422`).

---

## Versionado

- `package.json`: mantiene `0.8.0` (alineado con `main`).
- `package-lock.json`: mantiene `0.8.0` (alineado con `main`).

---

## Commits incluidos en el corte

- `8b4d2208` — `chore(release): snapshot integración workforce y lms`
- `be30f004` — `chore(release): v0.7.2` (ajustado a `v0.8.0` por sincronización con remoto)

