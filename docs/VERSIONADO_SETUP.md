# 🚀 Sistema Completo de Versionado y Changelog

## ¡Configuración Completada!

Has obtenido un sistema completo de versionado automático:

```
Commits Semánticos → Versionado Automático → Changelog → Release
```

---

## 📊 Los 3 Pilares

### 1️⃣ **Commits Semánticos** (Ya configurado)

```bash
./scripts/commit.sh        # Script interactivo
git commit -m "feat(...)"  # O directo
```

### 2️⃣ **Versionado Automático** (NUEVO)

```bash
./scripts/release.sh       # Detecta automáticamente
```

- `feat` → MINOR version
- `fix` → PATCH version
- `BREAKING CHANGE` → MAJOR version

### 3️⃣ **Changelog Automático** (NUEVO)

```
CHANGELOG.md actualizarse automáticamente
```

---

## 🎯 Flujo Completo

```
┌─────────────────────────────────────────────────┐
│ 1. Desarrollo (días/semanas)                    │
│                                                 │
│   git add .                                     │
│   ./scripts/commit.sh  (múltiples veces)        │
│                                                 │
│   Commits de ejemplo:                           │
│   - feat(forms): agregar validación             │
│   - fix(api): corregir endpoint                 │
│   - feat(auth): agregar 2FA                     │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│ 2. Release (cuando está listo)                  │
│                                                 │
│   ./scripts/release.sh                          │
│                                                 │
│   Automáticamente:                              │
│   ✅ Analiza todos los commits                  │
│   ✅ Calcula nueva versión (0.1.0 → 0.2.0)    │
│   ✅ Genera CHANGELOG.md                        │
│   ✅ Actualiza package.json                     │
│   ✅ Crea git tag (v0.2.0)                      │
│   ✅ Push a GitHub                              │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│ 3. GitHub Release (automático)                  │
│                                                 │
│   https://github.com/oahumada/Strato/releases│
│                                                 │
│   - Changelog                                   │
│   - Comparación de commits                      │
│   - Descarga de código                          │
└─────────────────────────────────────────────────┘
```

---

## 🚀 Cómo Usar

### Release Interactivo

```bash
./scripts/release.sh
```

Pasos:

1. Selecciona tipo (patch/minor/major/auto)
2. Script analiza commits
3. Genera changelog
4. Muestra resumen
5. Confirma push

### Release Automático

```bash
./scripts/release.sh auto    # Detecta automáticamente
./scripts/release.sh patch   # Siempre patch
./scripts/release.sh minor   # Siempre minor
./scripts/release.sh major   # Siempre major
```

### Desde npm

```bash
npm run release              # Interactivo
npm run release:patch        # Patch
npm run release:minor        # Minor
npm run release:major        # Major

# También en src/
cd src && npm run release
```

---

## 📝 Ejemplo Práctico Completo

### Situación Actual

```
Versión: 0.1.0
Último release: 2025-12-28
```

### Día 1-3: Desarrollo

```bash
# Haces cambios
echo "nuevo código" > src/forms/validator.ts

# Preparas y commiteas
git add .
./scripts/commit.sh

# Output:
# 📝 CAMBIOS PREPARADOS
# feat(forms): agregar validación
```

### Día 3-5: Más desarrollo

```bash
# Cambio 2
echo "otro código" > src/api/handler.ts
git add .
./scripts/commit.sh
# fix(api): mejorar manejo de errores

# Cambio 3
echo "más código" > src/utils/helpers.ts
git add .
./scripts/commit.sh
# feat(utils): agregar utilidades de fecha
```

### Día 5: Release

```bash
# Ver commits desde último release
git log v0.1.0..HEAD --oneline

# Output:
# feat(forms): agregar validación
# fix(api): mejorar manejo de errores
# feat(utils): agregar utilidades de fecha

# Hacer release
./scripts/release.sh
# → Detecta: 2 feat + 1 fix → MINOR version
# → Nueva versión: 0.2.0

# CHANGELOG.md se actualiza:
# ## [0.2.0] - 2025-12-29
#
# ### ✨ Nuevas Funcionalidades
# - **forms**: agregar validación
# - **utils**: agregar utilidades de fecha
#
# ### 🐛 Correcciones de Bugs
# - **api**: mejorar manejo de errores
```

### Resultado Final

```
✅ Versión actualizada: 0.1.0 → 0.2.0
✅ CHANGELOG.md actualizado
✅ package.json actualizado
✅ Git tag creado: v0.2.0
✅ Pushed a GitHub
✅ Release visible en https://github.com/oahumada/Strato/releases
```

---

## 📊 Qué Cambios de Versión

```
fix, perf, refactor, test, docs, style   →  PATCH (1.0.0 → 1.0.1)
feat                                     →  MINOR (1.0.0 → 1.1.0)
BREAKING CHANGE                          →  MAJOR (1.0.0 → 2.0.0)
```

### Ejemplo Breaking Change

```bash
git commit -m "feat(api): cambiar estructura de response

BREAKING CHANGE: response ahora es array en lugar de objeto"
```

→ Release automático a 2.0.0

---

## 🎨 Changelog Generado

El changelog agrupa automáticamente:

```markdown
## [0.2.0] - 2025-12-29

### ✨ Nuevas Funcionalidades

- **forms**: agregar validación de email
- **api**: mejorar endpoints

### 🐛 Correcciones de Bugs

- **search**: corregir filtro por fechas

### ⚡ Mejoras de Rendimiento

- **database**: optimizar queries

### ♻️ Refactorización

- **models**: simplificar lógica

### 📚 Documentación

- **readme**: actualizar instrucciones

### ✅ Tests

- **validation**: agregar test cases

### 🎨 Estilos

- ajustes de estilos CSS

### 🔧 Mantenimiento

- actualizar dependencias
```

---

## 🔗 GitHub Integration

Automáticamente en:

```
https://github.com/oahumada/Strato/releases
```

Cada release tiene:

- ✅ Changelog
- ✅ Commits incluidos
- ✅ Comparación con release anterior
- ✅ Opción de descargar ZIP/tar

---

## 📋 Archivos Nuevos

```
.versionrc.json                      - Configuración de versioning
scripts/release.sh                   - Script de releases
CHANGELOG.md                         - Historial de cambios
docs/GUIA_VERSIONADO_CHANGELOG.md   - Documentación completa
package.json (raíz)                 - npm scripts para releases
```

---

## 🔍 Monitorear Versiones

```bash
# Ver versión actual
cat package.json | grep version

# Ver todos los tags
git tag

# Ver cambios desde último tag
git log v0.1.0..HEAD --oneline

# Ver información de un tag
git show v0.2.0

# Crear release sin push
npm run release  # Sin "-y" flag
```

---

## ⚠️ Notas Importantes

1. **Commits semánticos son obligatorios** - Sin ellos, no hay versionado automático
2. **Un release a la vez** - Espera a que se complete antes de otro
3. **Siempre review** - Verifica el changelog antes de confirmar
4. **Sincronizar versiones** - Ambos package.json se actualizan
5. **Breaking changes** - Sé explícito con `BREAKING CHANGE:`

---

## 📚 Documentación

Consulta:

- [`docs/GUIA_COMMITS_SEMANTICOS.md`](docs/GUIA_COMMITS_SEMANTICOS.md) - Commits
- [`docs/GUIA_VERSIONADO_CHANGELOG.md`](docs/GUIA_VERSIONADO_CHANGELOG.md) - Versionado
- [`CHANGELOG.md`](CHANGELOG.md) - Historial de cambios

---

## 🎯 Resumen Rápido

```bash
# Commit
./scripts/commit.sh

# Release
./scripts/release.sh

# Ver resultados
cat CHANGELOG.md
git log --oneline
git tag
```

**¡Listo para releases profesionales!** 🚀🎉
