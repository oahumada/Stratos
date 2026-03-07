# 🧪 Plan de QA y Cierre de Módulos (Wave 2 - Bloque A)

Este documento detalla la ronda de aseguramiento de calidad necesaria para cerrar formalmente los módulos implementados en la **Wave 2 (Bloque A)**.

## 🏁 Objetivos

- Garantizar una cobertura de tests > 80% en servicios críticos.
- Validar la integridad de datos entre el Core (Postgres) y el Córtex de IA.
- Asegurar que el sistema de permisos (RBAC) es impenetrable.
- Verificar la experiencia de usuario (UX) en el Portal Personal.

---

## 🛠️ Suite de Pruebas por Módulo

### 1. Mi Stratos (Portal Personal)

- [ ] **Unit Test:** `MiStratosController@dashboard` — Verificar agregación de datos (KPIs, Brechas, Rutas).
- [ ] **Integration Test:** Flujo completo desde la asignación de un rol hasta la visualización del Match % en el portal.
- [ ] **Acceptance (E2E):** El colaborador puede ver sus misiones (Quests) y el progreso de sus rutas de aprendizaje.

### 2. Talento 360 & Comando

- [ ] **Unit Test:** `AssessmentCycleSchedulerService` — Verificar que los ciclos se activan en la fecha correcta.
- [ ] **Feature Test:** Triangulación de la verdad — Comparar resultados de entrevista IA vs feedback externo.
- [ ] **Integration Test:** Generación automática de solicitudes de feedback tras finalizar una entrevista.

### 3. Roles y Competencias (Cubo)

- [ ] **Unit Test:** `RoleTemplateService` — Validar que las plantillas BARS se generan correctamente según el arquetipo.
- [ ] **Feature Test:** Importación de competencias desde el módulo de Escenarios (Radar) al catálogo maestro.

### 4. Sistema RBAC & Seguridad

- [ ] **Security Audit:** Probar acceso cruzado entre tenants (Multi-tenancy check).
- [ ] **Permission Matrix:** Validar los 18 permisos base sobre los 5 roles del sistema.
- [ ] **API Hardening:** Implementar rate-limiting específico para los endpoints de IA (costosos).

---

## 📈 Métricas de Aceptación

- 🟢 **Tests de Pasada:** 100% de la suite ejecutada con éxito.
- 🟢 **Cobertura:** > 85% en el directorio `app/Services`.
- 🟢 **Lighthouse:** Score de Accesibilidad y Best Practices > 90 en el portal.

---

_Iniciando fase de QA..._
