# Versionado Semántico y Changelog - Strato

## 📊 Visión General

Con los commits semánticos configurados, ahora puedes:

1. **Versionado automático** basado en tipos de commits
2. **Changelog automático** generado desde los commits
3. **Git tags** automáticos para cada release
4. **Release notes** en GitHub

## 🔄 Cómo Funciona

### Semantic Versioning (MAJOR.MINOR.PATCH)

```
MAJOR - Breaking changes (feat con BREAKING CHANGE)
MINOR - Nuevas funcionalidades (feat)
PATCH - Fixes y mejoras menores (fix, perf, refactor, etc)
```

### Ejemplo de Evolución

```
1.0.0 → Release inicial
   ↓ (Se agrega feat)
1.1.0 → Nueva funcionalidad
   ↓ (Se agrega fix)
1.1.1 → Corrección de bug
   ↓ (Se agrega BREAKING CHANGE en feat)
2.0.0 → Breaking change
```

## 🚀 Cómo Hacer un Release

### Opción 1: Script Interactivo (Recomendado)

```bash
./scripts/release.sh
```

Ejemplo:

```
═══════════════════════════════════════════════════════════════
  🚀 Asistente de Releases - Strato
═══════════════════════════════════════════════════════════════

📌 Versión actual: 1.0.0

1. ¿Qué tipo de release?
   1) patch  - Fixes (1.0.0 → 1.0.1)
   2) minor  - Nuevas features (1.0.0 → 1.1.0)
   3) major  - Breaking changes (1.0.0 → 2.0.0)
   4) auto   - Detectar automáticamente

Elige (1-4): 2

✓ Tipo de release: minor

📊 Analizando commits...
[Actualiza CHANGELOG.md y package.json]

═══════════════════════════════════════════════════════════════
✨ RESUMEN DEL RELEASE:
═══════════════════════════════════════════════════════════════

Versión anterior:    1.0.0
Nueva versión:       1.1.0
Tag:                 v1.1.0
Changelog:           CHANGELOG.md

📝 Últimos cambios en CHANGELOG.md:

## [1.1.0] - 2025-12-28

### ✨ Nuevas Funcionalidades
- **forms**: agregar validación de email
- **api**: mejorar manejo de errores

### 🐛 Correcciones de Bugs
- **search**: corregir filtro por fechas

¿Hacer push de los cambios y tags? (y/n): y

📤 Haciendo push...
✅ Release completado exitosamente!

Git tag creado: v1.1.0
Rama: MVP
CHANGELOG actualizado: CHANGELOG.md

📎 Enlace del release:
https://github.com/oahumada/Strato/releases/tag/v1.1.0

🎉 ¡Release listo!
```

### Opción 2: Línea de Comandos

```bash
# Detectar automáticamente
./scripts/release.sh auto

# Detectar automáticamente y confirmar push (no interactivo)
./scripts/release.sh auto --yes

# O especificar explícitamente
./scripts/release.sh patch    # Fix
./scripts/release.sh minor    # Feature
./scripts/release.sh major    # Breaking change
```

### Preflight automático (recomendado)

El script de release aplica validaciones automáticas antes de versionar:

1. ejecuta `git pull --rebase origin <rama>` (salvo `--no-sync`),
2. valida que el árbol de trabajo esté limpio,
3. bloquea release si `package.json.version` local está por detrás de remoto o del último tag semver.

Esto evita bucles de versionado y releases sobre ramas desactualizadas.

### Opción 3: Con npm scripts (agregar a package.json)

```bash
npm run release
npm run release:auto
npm run release:auto:yes
npm run release:patch
npm run release:minor
npm run release:major
npm run release:alpha
npm run release:beta
npm run release:rc
```

### Nota CI anti-recursión

El workflow de `auto-release` ignora commits `chore(release): ...` y no usa fallback por “últimos N commits”, para evitar bumps en cascada.

## 📝 Archivo CHANGELOG

Se genera automáticamente con la estructura:

```markdown
## [1.1.0] - 2025-12-28

### ✨ Nuevas Funcionalidades

- **forms**: descripción del cambio
- **api**: otra descripción

### 🐛 Correcciones de Bugs

- **search**: descripción del fix

### ⚡ Mejoras de Rendimiento

- **db**: optimización de queries

### ♻️ Refactorización

- **models**: cambio de estructura

### 📚 Documentación

- **readme**: actualización

### 🔧 Mantenimiento

- actualización de dependencias
```

## 🏷️ Git Tags

Los tags se crean automáticamente en cada release:

```bash
# Ver todos los tags
git tag

# Ver información de un tag
git show v1.1.0

# Ver commits entre tags
git log v1.0.0..v1.1.0

# Checkout a un tag específico
git checkout v1.0.0
```

## 🔗 GitHub Releases

Los releases aparecen en:

```
https://github.com/oahumada/Strato/releases
```

Cada tag automáticamente crea un "Release" en GitHub con:

- Changelog
- Comparación de commits
- Descarga de código

## 📋 Flujo Completo de Desarrollo

```
1. Hacer cambios
   ↓
2. git add .
   ↓
3. ./scripts/commit.sh          (commit semántico)
   ↓
4. Repetir 1-3 múltiples veces
   ↓
5. ./scripts/release.sh         (release automático)
   │
   ├─ Analiza todos los commits desde el último tag
   ├─ Calcula nueva versión (major/minor/patch)
   ├─ Genera CHANGELOG.md
   ├─ Actualiza package.json
   ├─ Crea git tag
   └─ Push a GitHub
   ↓
6. GitHub crea Release automáticamente 🎉
```

## 💡 Ejemplos Prácticos

### Release Minor (Nuevas Features)

```bash
# Commits desde última release:
# feat(forms): agregar validación
# feat(api): mejorar endpoints
# fix(search): corregir filtro

./scripts/release.sh
# → Detecta: 2 feat + 1 fix
# → Tipo: minor
# → 1.0.0 → 1.1.0 ✅
```

### Release Patch (Solo Fixes)

```bash
# Commits desde última release:
# fix(search): corregir filtro
# fix(auth): mejorar validación

./scripts/release.sh
# → Detecta: 2 fix
# → Tipo: patch
# → 1.1.0 → 1.1.1 ✅
```

### Release Major (Breaking Changes)

```bash
# Commits desde última release:
# feat(api): cambiar estructura de response

# BREAKING CHANGE: response format changed from {...} to [...]

./scripts/release.sh
# → Detecta: feat con BREAKING CHANGE
# → Tipo: major
# → 1.1.0 → 2.0.0 ✅
```

## 🔧 Configuración

### `.versionrc.json`

Define cómo se genera el changelog:

```json
{
  "types": [
    {
      "type": "feat",
      "section": "✨ Nuevas Funcionalidades",
      "hidden": false
    },
    {
      "type": "fix",
      "section": "🐛 Correcciones de Bugs",
      "hidden": false
    }
    // ... más tipos
  ],
  "tagPrefix": "v"
}
```

## 📚 Versiones en Proyecto

Versiones están definidas en:

- `src/package.json` - Frontend
- `package.json` (raíz) - Backend/general

Para mantener sincronizadas, ambas se actualizan con cada release.

## 🆘 Troubleshooting

### "No hay cambios que hacer release"

```bash
# Solo commitea cambios con commits semánticos correctos
# El script busca commits desde el último tag
```

### "Cambios sin commitear"

```bash
git status
git add .
git commit -m "tu mensaje semántico"
./scripts/release.sh
```

### "Quiero revertir un release"

```bash
# Revertir solo el commit de release (cuidado)
git revert HEAD
git push

# O restaurar a versión anterior
git checkout v1.0.0
```

### "Cambiar versión manualmente"

```bash
# Editar package.json
nano package.json

# Hacer commit
git commit -m "chore(release): actualizar versión a X.Y.Z"

# Crear tag manualmente
git tag vX.Y.Z
git push --tags
```

## 🎯 Mejores Prácticas

1. **Commits semánticos siempre** - Sin ellos, no hay versionado automático
2. **Review antes de release** - Verifica changelog con `./scripts/release.sh` (sin push)
3. **Uno release por sesión** - Evita releases demasiado frecuentes
4. **Documentar breaking changes** - Usa `BREAKING CHANGE:` en commit message
5. **Sincronizar versiones** - Mantén ambos package.json actualizados

## 📖 Documentación Adicional

- [Semantic Versioning](https://semver.org/)
- [Conventional Commits](https://www.conventionalcommits.org/)
- [Standard Version](https://github.com/conventional-changelog/standard-version)
- [GitHub Releases](https://docs.github.com/en/repositories/releasing-projects-on-github/about-releases)

---

**Ahora tienes versionado y changelog automáticos desde tus commits semánticos!** 🚀
