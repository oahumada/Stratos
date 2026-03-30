# 📐 Norma Interna de Versionado y Releases — Stratos

**Estado:** Vigente  
**Versión de la norma:** 1.1  
**Alcance:** Todo cambio que llegue a ramas de integración/release y afecte backend, frontend, infraestructura o documentación operativa.

---

## 1. Propósito

Definir una política única, auditable y repetible para:

- versionar el producto,
- generar changelog confiable,
- liberar versiones con control de calidad,
- asegurar trazabilidad desde commit hasta tag/release.

---

## 2. Estándares adoptados

Stratos adopta explícitamente:

1. **Semantic Versioning (SemVer)**: `MAJOR.MINOR.PATCH`.
2. **Conventional Commits**: estructura de commits para automatización.
3. **Keep a Changelog (adaptado)**: historial legible de cambios.

Implementación actual en el proyecto:

- Script de commits: `scripts/commit.sh`
- Script de release: `scripts/release.sh`
- Guías base: `docs/VERSIONADO_SETUP.md`, `docs/GUIA_VERSIONADO_CHANGELOG.md`
- Registro oficial: `CHANGELOG.md`

---

## 3. Reglas de versionado

## 3.1 Interpretación SemVer

- **MAJOR**: cambios incompatibles (breaking changes).
- **MINOR**: nuevas capacidades retrocompatibles.
- **PATCH**: correcciones y mejoras menores sin ruptura.

## 3.2 Mapeo de commits a bump

- `feat` → **MINOR**
- `fix`, `perf`, `refactor`, `docs`, `test`, `chore` → **PATCH**
- `BREAKING CHANGE` (en body/footer) → **MAJOR**

## 3.3 Política pre-1.0.0

Mientras el producto esté en `0.x`:

- Se permite evolución rápida, pero se mantiene disciplina semántica.
- Cambios potencialmente disruptivos deben marcarse como `BREAKING CHANGE` incluso en `0.x`.

## 3.4 Criterio para declarar `v1.0.0`

`v1.0.0` se autoriza cuando:

1. Se cumplen criterios de salida Alpha/Beta definidos en `docs/ROADMAP_TRANSICION_MVP_ALPHA_BETA_2026.md`.
2. Existen al menos 2 ciclos consecutivos sin incidentes críticos P0/P1 en flujos core.
3. La documentación operativa y de release está validada por responsables técnicos y operativos.

## 3.5 Escalera de madurez (MVP → Producción)

Stratos adopta explícitamente la siguiente secuencia:

1. `v0.x` → producto en evolución controlada (MVP/Alpha interna).
2. `v0.x-alpha.N` → validación temprana funcional.
3. `v0.x-beta.N` → estabilización y validación ampliada.
4. `v0.x-rc.N` → candidato final sin cambios estructurales grandes.
5. `v1.0.0` → release general (GA) para operación productiva.

Regla operativa:

- Desde `rc`, solo entran `fix`, `perf`, `docs` críticos y ajustes de hardening.
- `feat` en fase `rc` requiere aprobación explícita de Product + Tech Lead.

---

## 4. Convención de commits (obligatoria)

Formato:

```text
type(scope): descripción breve
```

Ejemplos válidos:

- `feat(intelligence): agregar endpoint de resumen de agregados`
- `fix(scenarios): corregir validación de rango en backfill`
- `docs(roadmap): formalizar criterio de salida v1.0.0`

Ejemplo con ruptura:

```text
feat(api): cambiar payload de respuesta de métricas

BREAKING CHANGE: se elimina campo legacy "avg_latency" y se reemplaza por "p95_duration_ms".
```

---

## 5. Flujo oficial de release

## 5.1 Flujo estándar

1. Integrar cambios con commits semánticos.
2. Verificar suite mínima de calidad (tests/lint/format según alcance).
3. Ejecutar release:

```bash
./scripts/release.sh
```

o modo automático:

```bash
./scripts/release.sh auto
```

o pre-release por fase:

```bash
./scripts/release.sh alpha
./scripts/release.sh beta
./scripts/release.sh rc
```

4. Validar que se actualizó `CHANGELOG.md` y se creó tag `vX.Y.Z`.
5. Publicar tag y release notes.

## 5.2 Release gates (mínimos obligatorios)

Antes de publicar una versión:

- Build/test de alcance en verde.
- No hay cambios críticos sin documentar.
- `CHANGELOG.md` actualizado y coherente con los commits.
- Validación de seguridad operativa para cambios sensibles.

## 5.3 Automatización CI (implementada)

Esta norma se aplica automáticamente en CI mediante:

- Workflow: `.github/workflows/release-governance.yml`
- Controles incluidos:
    - Validación de **Conventional Commits** en PR/push.
    - Integridad de release en tags `v*`:
        - `tag` coincide con `package.json.version`.
        - `CHANGELOG.md` contiene la versión liberada.
        - el `tag` apunta al commit esperado.

## 5.4 Branch protection (bloqueo de merge)

Para bloquear merges cuando falle el workflow de gobernanza:

- Payload de protección: `.github/branch-protection-main-release-governance.json`
- Script de aplicación: `scripts/apply-branch-protection.sh`

Ejecución:

```bash
export GITHUB_TOKEN=<token_con_permiso_admin_repo>
./scripts/apply-branch-protection.sh
```

Resultado esperado:

- Rama `main` protegida.
- Merge bloqueado si falla el check requerido de Release Governance:
    - `Commit Convention (Conventional Commits)`.

---

## 6. Cadencia recomendada

- **PATCH**: bajo demanda (hotfix o corrección rápida).
- **MINOR**: quincenal o mensual (bloques de valor).
- **MAJOR**: por hito estratégico, no por calendario.

### 6.1 Activación inmediata (vigente desde 2026-03-29)

Esta política entra en vigor inmediatamente con las siguientes reglas de ejecución:

1. Para bloques de funcionalidad grandes previos a GA usar `alpha`/`beta`/`rc`.
2. Para correcciones urgentes usar `patch`.
3. No elevar a `v1.0.0` hasta cumplir criterios de 3.4.

Comandos oficiales desde hoy:

```bash
npm run release:alpha
npm run release:beta
npm run release:rc
npm run release:patch
npm run release:minor
npm run release:major
```

---

## 7. Gobernanza y responsabilidades (RACI mínimo)

- **Responsible (R):** Tech Lead / Maintainer ejecuta release.
- **Accountable (A):** Owner de producto/arquitectura aprueba MAJOR y salida `v1.0.0`.
- **Consulted (C):** QA/Security en cambios de alto impacto.
- **Informed (I):** Equipo de desarrollo y stakeholders internos.

---

## 8. Estructura mínima de changelog por release

Cada release debe incluir secciones (cuando aplique):

- ✨ Nuevas Funcionalidades
- 🐛 Correcciones
- ♻️ Refactorización
- ⚡ Rendimiento
- 🔒 Seguridad
- 📚 Documentación

---

## 9. Manejo de excepciones

Se permite “release de emergencia” solo para incidentes críticos.  
Debe incluir post-acción obligatoria:

1. registro en changelog,
2. nota de causa raíz,
3. acción preventiva para evitar recurrencia.

---

## 10. Checklist rápido de aplicación

- [ ] Commits cumplen convención semántica.
- [ ] Bump de versión correcto según impacto.
- [ ] Changelog refleja cambios reales.
- [ ] Tests/gates mínimos aprobados.
- [ ] Tag y release publicados.
- [ ] Documentación operativa actualizada.

---

## 11. Documentos relacionados

- `docs/VERSIONADO_SETUP.md`
- `docs/GUIA_VERSIONADO_CHANGELOG.md`
- `docs/ROADMAP_TRANSICION_MVP_ALPHA_BETA_2026.md`
- `CHANGELOG.md`
