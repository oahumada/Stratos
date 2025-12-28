# Commits Semánticos - Configuración TalentIA

✅ Configuración completada para commits semánticos en tu proyecto.

## ¿Qué se configuró?

### 1. **CommitLint** (`commitlint.config.js`)
- Valida automáticamente el formato de tus mensajes de commit
- Asegura consistencia en el equipo
- Rechaza commits que no sigan el estándar

### 2. **Husky** (`.husky/commit-msg`)
- Hook automático antes de hacer commit
- Ejecuta CommitLint para validar el mensaje
- Previene commits inválidos

### 3. **Git Template** (`.gitmessage`)
- Template de mensaje con instrucciones
- Se muestra al abrir el editor de git
- Guía el formato correcto

### 4. **Documentación** (`docs/GUIA_COMMITS_SEMANTICOS.md`)
- Guía completa sobre commits semánticos
- Ejemplos válidos e inválidos
- Beneficios del estándar

### 5. **Script Helper** (`scripts/commit.sh`)
- Asistente interactivo para crear commits
- Valida datos en tiempo real
- Facilita el proceso

## Cómo Usar

### Opción 1: Editor Interactivo (Recomendado)
```bash
git commit
# Se abrirá tu editor con el template de instrucciones
# Sigue el formato: type(scope): subject
```

### Opción 2: Línea de Comandos
```bash
git commit -m "feat(auth): agregar autenticación de dos factores"
```

### Opción 3: Script Interactivo
```bash
./scripts/commit.sh
# Responde las preguntas del asistente
```

## Formato Básico

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Tipos Disponibles:
- **feat**: Nueva funcionalidad
- **fix**: Corrección de bugs
- **docs**: Cambios en documentación
- **style**: Cambios de estilo
- **refactor**: Refactoring
- **perf**: Mejora de rendimiento
- **test**: Tests
- **chore**: Cambios en build/dependencias
- **ci**: Cambios en CI/CD
- **revert**: Revertir commit

### Scopes Comunes:
- `auth` - Autenticación
- `forms` - Formularios
- `api` - API endpoints
- `models` - Modelos de datos
- `search` - Búsqueda
- `validation` - Validaciones
- `ui` - Interfaz de usuario

## Ejemplos

### ✅ Válido
```
feat(forms): agregar validación de email en tiempo real
```

### ✅ Válido (con body y footer)
```
fix(search): corregir filtro por rango de fechas

Se corrigió la comparación de timestamps que causaba
resultados incorrectos cuando se seleccionaba un rango.

Fixes #234
```

### ❌ Inválido
```
updated code                    # Sin format
FIX(API): error                 # Mayúsculas
fix: añadir nueva feature.      # Subject vago + punto
```

## Validación Automática

CommitLint valida automáticamente cada commit:

```bash
$ git commit -m "wrong format"
⧗ input: "wrong format"
✖ type must be one of [feat, fix, docs, style, refactor, perf, test, chore, ci, revert]
```

Simplemente intenta de nuevo con el formato correcto.

## Comandos Útiles

```bash
# Ver últimos 10 commits
git log --oneline -10

# Ver commits de un tipo específico
git log --oneline | grep "^feat"
git log --oneline | grep "^fix"

# Ver commits de un scope
git log --oneline | grep "(forms)"

# Ver historial detallado
git log --oneline --all --graph
```

## Beneficios

1. **Automatizar Changelogs** - Se pueden generar automáticamente
2. **Versionamiento Semántico** - Major, minor, patch automático
3. **Historial Limpio** - Fácil de entender cambios
4. **Búsqueda Efectiva** - Encontrar commits rápidamente
5. **Code Review** - Entender intención del cambio

## Documentación

Para más detalles, consulta:
- [`docs/GUIA_COMMITS_SEMANTICOS.md`](docs/GUIA_COMMITS_SEMANTICOS.md) - Guía completa
- [`commitlint.config.js`](commitlint.config.js) - Configuración
- [`scripts/commit.sh`](scripts/commit.sh) - Script interactivo

---

**Estándar**: [Conventional Commits](https://www.conventionalcommits.org/)
