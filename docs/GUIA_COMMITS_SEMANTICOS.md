# Guía de Commits Semánticos - TalentIA

## ¿Qué son los Commits Semánticos?

Los commits semánticos son mensajes estructurados que permiten:
- Identificar rápidamente el tipo de cambio
- Automatizar la generación de changelogs
- Facilitar el entendimiento del historial
- Mantener consistencia en el equipo

## Formato

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Components:

#### 1. **Type** (Tipo) - REQUERIDO
- `feat`: Nueva funcionalidad
- `fix`: Corrección de bugs
- `docs`: Cambios en documentación
- `style`: Cambios de estilo (CSS, formato, etc - sin lógica)
- `refactor`: Refactoring de código
- `perf`: Mejoras de rendimiento
- `test`: Agregar o actualizar tests
- `chore`: Cambios en build, dependencias, configuración
- `ci`: Cambios en CI/CD
- `revert`: Revertir un commit anterior

#### 2. **Scope** (Alcance) - OPCIONAL
Área del proyecto afectada. Ejemplos:
- `auth`: Sistema de autenticación
- `forms`: Componentes de formularios
- `models`: Modelos de datos
- `api`: API endpoints
- `search`: Búsqueda y filtros
- `validation`: Validaciones
- `ui`: Interfaz de usuario
- `database`: Base de datos

#### 3. **Subject** (Asunto) - REQUERIDO
- Máximo 100 caracteres
- Usar minúsculas
- Usar modo imperativo ("agregar" no "agregado")
- No terminar con punto

#### 4. **Body** (Cuerpo) - OPCIONAL
- Explicar **qué** y **por qué**, no solo **cómo**
- Separado del subject por línea en blanco
- Máximo 100 caracteres por línea
- Incluir cambios significativos

#### 5. **Footer** (Pie) - OPCIONAL
- Referencia a issues: `Fixes #123` o `Closes #456`
- Breaking changes: `BREAKING CHANGE: descripción`

## Ejemplos

### ✅ Commit Válido - Nueva Funcionalidad
```
feat(forms): agregar validación de email en tiempo real

Se agregó validación asincrónica de emails para detectar
direcciones duplicadas. Utiliza debounce para evitar
múltiples requests al servidor.

Closes #152
```

### ✅ Commit Válido - Bug Fix
```
fix(search): corregir filtro de búsqueda por rango de fechas

El filtro no funcionaba correctamente cuando se seleccionaba
un rango de fechas. Se corrigió la comparación de timestamps.

Fixes #234
```

### ✅ Commit Válido - Refactoring
```
refactor(models): simplificar lógica del repositorio

Se extrajo la lógica de joins a un método separado
para mejorar la legibilidad y reutilización.
```

### ✅ Commit Válido - Documentación
```
docs(readme): actualizar instrucciones de instalación y setup

Se agregaron pasos detallados para instalar dependencias
tanto del frontend como del backend.
```

### ✅ Commit Válido - Chore
```
chore(deps): actualizar dependencias de desarrollo

- Actualizar eslint a 9.19.0
- Actualizar prettier a 3.4.2
```

## ❌ Commits Inválidos

```
updated code                          # Vago, sin type
fix: error                            # Subject muy corto
FEAT(FORMS): ADD NEW FIELDS           # Type/scope sin minúsculas
fix(api): arreglar el endpoint.       # Subject termina con punto
added new feature for user            # Sin type ni estructura
```

## Usar desde Git

### 1. Commitear con validación
```bash
git commit -m "feat(auth): agregar login de dos factores"
```

### 2. Usar editor interactivo (recomendado)
```bash
git commit
# Se abrirá tu editor con el template
```

### 3. Ver historial de commits
```bash
git log --oneline
```

## Validación Automática

CommitLint automáticamente validará tus mensajes y rechazará
los que no cumplan con el formato.

Si recibís un error:
```
⧗ input: "updated code"
✖ type must be one of [feat, fix, docs, style, refactor, perf, test, chore, ci, revert]
```

Simplemente intenta de nuevo con el formato correcto.

## Beneficios en el Proyecto

Con commits semánticos podemos:

1. **Generar Changelogs** automáticamente agrupando cambios por type
2. **Determinar versión** (major, minor, patch) según los tipos
3. **Historial limpio** - Fácil de entender qué cambió y por qué
4. **Búsqueda efectiva** - Encontrar commits por tipo o scope
5. **Code Review** - Entender rápidamente la intención del cambio

## Comandos Útiles

```bash
# Ver commits de un tipo específico
git log --oneline --grep="^feat" --grep="^fix" -E

# Ver commits por scope
git log --oneline | grep "(forms)"

# Ver commits sin contar merges
git log --oneline --no-merges

# Ver cambios detallados
git log --format=fuller
```

---

Para más información: https://www.conventionalcommits.org/
