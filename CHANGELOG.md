# Changelog

Todos los cambios notables en este proyecto están documentados en este archivo.

El formato está basado en [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
y este proyecto adhiere a [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### 🔄 En Desarrollo

- Cambios pendientes de release...

---

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
