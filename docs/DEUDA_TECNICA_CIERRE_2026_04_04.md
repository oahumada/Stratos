# Cierre de Deuda Técnica — 4 de Abril de 2026

Documento formal de cierre de los ítems de deuda técnica pendientes identificados en el sprint de marzo 2026. Fecha de cierre: **4 Abr 2026**.

---

## 1. Mobile Viewport + Dark Mode Testing

### Estado: **Diferido — Fuera de alcance del sprint actual**

### Contexto

Durante el sprint de marzo 2026 se identificó que la cobertura de pruebas UI cubre exclusivamente escritorio (resolución 1280px+). Los casos de prueba para viewport móvil y dark mode quedaron pendientes.

### Decisión

Queda **fuera del alcance del sprint actual**. Se difiere a un sprint dedicado:

- **Sprint objetivo:** "Mobile & Accessibility Sprint"
- **Planificación estimada:** Q3 2026

### Justificación técnica

- La aplicación utiliza **Vuetify 3** con grid responsive, lo que proporciona responsividad de línea base sin cambios de código adicionales. No se han detectado roturas críticas en resoluciones móviles.
- El soporte completo de **dark mode** requiere un esfuerzo dedicado de theming con CSS custom properties; no puede resolverse de forma incremental sin riesgo de regresión visual.
- El impacto de negocio en usuarios móviles es bajo en la audiencia actual (usuarios enterprise en desktop).

### Cobertura actual

| Escenario             | Estado                                   |
| --------------------- | ---------------------------------------- |
| Desktop (1280px+)     | ✅ Cubierto                              |
| Tablet (768px–1279px) | ⚠️ Cobertura parcial (Vuetify grid)      |
| Mobile (<768px)       | 🔲 Diferido a Q3 2026                    |
| Dark mode             | 🔲 Diferido (requiere sprint de theming) |

### Criterios de aceptación (Q3 2026)

- Tests Playwright con viewport 375×812 (iPhone) y 768×1024 (iPad).
- Implementación de CSS custom properties para theming.
- Cobertura de los 10 componentes críticos en ambos modos.

---

## 2. Staging K6 — Load Testing

### Estado: **Bloqueado — Dependencia externa (DevOps)**

### Contexto

Los tests de carga con K6 fueron completados y ejecutados exitosamente en entorno local. La ejecución en staging equivalente a producción (php-fpm) está bloqueada por falta de aprovisionamiento del entorno.

### Resultados locales

Los tests corrieron satisfactoriamente:

```
Resultados disponibles en:
  scripts/load-testing-*-local-2026-04-04.json
```

### Comando listo para staging

Una vez provisionado el entorno, el test se ejecuta con:

```bash
k6 run scripts/load-testing-smoke.js --env BASE_URL=https://staging.stratos.app
```

Para la suite completa:

```bash
k6 run scripts/load-testing-full.js --env BASE_URL=https://staging.stratos.app
```

### Bloqueo actual

| Ítem                      | Estado              |
| ------------------------- | ------------------- |
| Tests K6 desarrollados    | ✅ Completo         |
| Ejecución en local        | ✅ Exitosa          |
| Entorno staging (php-fpm) | 🔲 Pendiente DevOps |
| Ejecución en staging      | 🔲 Bloqueada        |

**Dependencia:** Aprovisionamiento del entorno de staging con php-fpm por parte del equipo de DevOps/Infraestructura. No hay bloqueantes técnicos en el código.

### Plan de acción

- Los tests de carga se ejecutarán como parte de la **ventana de QA pre-PROD**.
- Responsable de seguimiento: equipo de infraestructura.
- Ticket de seguimiento: crear en el gestor de proyectos con etiqueta `devops/staging`.

---

## Resumen ejecutivo

| Ítem de deuda           | Cierre              | Observación                                                  |
| ----------------------- | ------------------- | ------------------------------------------------------------ |
| Mobile viewport testing | 🔲 Diferido Q3 2026 | Sin impacto crítico actual; Vuetify provee base              |
| Dark mode testing       | 🔲 Diferido Q3 2026 | Requiere sprint de theming dedicado                          |
| K6 local                | ✅ Completado       | Resultados en `scripts/load-testing-*-local-2026-04-04.json` |
| K6 staging              | 🔲 Bloqueado        | Dependencia externa DevOps; listo técnicamente               |

---

_Documento generado: 4 Abr 2026_
