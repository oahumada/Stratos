# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

## [Unreleased]

### ✨ Nuevas Funcionalidades

- **skills:** implementar sistema de niveles de competencia (Skill Levels)
  - Tabla `skill_level_definitions` con 5 niveles genéricos
  - Niveles: Básico (10pts) → Intermedio (25pts) → Avanzado (50pts) → Experto (100pts) → Maestro (200pts)
  - Sistema de puntos para gamificación
  - Modelo `SkillLevelDefinition` con helper `display_label`
  - Endpoint API `/catalogs?catalogs[]=skill_levels`
  - Componente frontend `SkillLevelChip.vue` con tooltips informativos
  - Integración en Skills/Index.vue para mostrar niveles en lugar de números
  - Documentación arquitectónica: SKILL_LEVELS_ARCHITECTURE_DECISION.md
  - Tres dimensiones de progresión: Autonomía, Complejidad, Responsabilidad

### 📚 Documentación

- **skills:** agregar SKILL_LEVELS_SYSTEM.md con guía completa del sistema de niveles
- **skills:** documentar decisión arquitectónica entre niveles genéricos vs. específicos
- Actualizar INDEX.md con nueva sección "Skill Levels System"

## 0.2.0 (2025-12-28)


### 📚 Documentación

* actualizar guía de commits semánticos con nuevas secciones y ejemplos ([dd6ecb0](https://github.com/oahumada/Strato/commit/dd6ecb06888e50d0e61f86b468a0d2a683aa0938))


### ♻️ Refactorización

* **scripts:** mejorar script de commits con análisis automático de cambios ([c83602b](https://github.com/oahumada/Strato/commit/c83602b83c2979c0df1c9cfed8b8862d0e1f4d42))


### ✨ Nuevas Funcionalidades

* add .gitignore files for storage and testing directories ([371b374](https://github.com/oahumada/Strato/commit/371b3744510d6083715bd4a1f48d17255b782cc8))
* add initial MVP documentation for Strato project (estado_actual_mvp.md) ([241f3a4](https://github.com/oahumada/Strato/commit/241f3a4abb4ad5222162820bda1b2e1ecdee9009))
* agregar soporte para Vuetify y configurar el tema predeterminado ([0e16a7a](https://github.com/oahumada/Strato/commit/0e16a7ac56481f4b8155e239a21f46ff28b7f4e7))
* Implement form schema and CRUD functionality for Alergia model ([34f12a7](https://github.com/oahumada/Strato/commit/34f12a7888ca643ca10e2b95a43e9fe38bf88734))
* **release:** agregar sistema de versionado y changelog automático ([9f28673](https://github.com/oahumada/Strato/commit/9f2867315b0ecfd3b3627fbab1ed0106d73ebeb8))

## [0.1.0] - 2025-12-28

### ✨ Nuevas Funcionalidades

- **chore**: Configurar commits semánticos con commitlint y husky
- **refactor**: Mejorar script de commits con análisis automático de cambios
- **feat**: Sistema de versionado automático basado en commits semánticos
- **feat**: Generación automática de changelog desde commits convencionales
- **feat**: Script de releases interactivo

### 📚 Documentación

- **docs**: Guía completa de commits semánticos
- **docs**: Documentación de versionado y changelog
- **docs**: Setup inicial de herramientas de desarrollo

### 🔧 Mantenimiento

- Instalación de commitlint y husky
- Instalación de standard-version para versionado
- Configuración de hooks de git

---

## Cómo Leer este Changelog

- **✨ Nuevas Funcionalidades**: Funcionalidades nuevas agregadas
- **🐛 Correcciones de Bugs**: Bugs corregidos
- **⚡ Mejoras de Rendimiento**: Optimizaciones y mejoras de rendimiento
- **♻️ Refactorización**: Cambios de código sin afectar funcionalidad
- **✅ Tests**: Cambios relacionados con tests
- **📚 Documentación**: Cambios en documentación
- **🎨 Estilos**: Cambios cosméticos (CSS, formato, etc)
- **⏮️ Reversiones**: Commits revertidos
- **🔧 Mantenimiento**: Cambios en build, dependencias, etc

---

## Cómo Contribuir

Para mantener un changelog limpio y útil:

1. **Usa commits semánticos** - `feat()`, `fix()`, etc.
2. **Agrupa cambios relacionados** - Múltiples commits del mismo tipo se agrupan
3. **Sé descriptivo** - El subject del commit se usa en el changelog
4. **Referencia issues** - Usa `Fixes #123` en el footer

Ejemplo:

```
feat(forms): agregar validación de email en tiempo real

Se agregó validación asincrónica para detectar
emails duplicados. Incluye debounce para
evitar múltiples requests.

Fixes #152
```

Aparecerá en changelog como:

```
### ✨ Nuevas Funcionalidades
- **forms**: agregar validación de email en tiempo real
```

---

## Releases

Los releases se hacen con:

```bash
./scripts/release.sh
```

Esto:

- Calcula nueva versión automáticamente
- Actualiza este archivo
- Crea git tag
- Actualiza package.json

---

**Última actualización**: 2025-12-28
