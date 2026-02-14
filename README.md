# 🚀 Strato - Plataforma de Gestión de Talento Basada en Skills

> **SaaS + Consultoría** para mapeo estratégico de talento, identificación de brechas y diseño de rutas de desarrollo con IA.

![MVP Status](https://img.shields.io/badge/MVP-v0.2.0-blue)
![Backend](https://img.shields.io/badge/Backend-✅-green)
![Frontend](https://img.shields.io/badge/Frontend-🚀_In%20Progress-yellow)
![Documentation](https://img.shields.io/badge/Docs-45%2B_files-brightgreen)

---

## 📚 Documentación

**Accede a toda la documentación en:** [`docs/INDEX.md`](docs/INDEX.md)

### Inicio Rápido

- **5 minutos?** → [`QUICK_START.md`](docs/QUICK_START.md)
- **¿Primer día?** → [`memories.md`](docs/memories.md)
- **¿Visión Técnica?** → [`PLAN_DE_ATAQUE_EXCELENCIA.md`](docs/PLAN_DE_ATAQUE_EXCELENCIA.md)
- **¿Estado actual?** → [`estado_actual_mvp.md`](docs/estado_actual_mvp.md)

---

## 🎯 Status Actual

### ✅ Completado (v0.2.0)

```
Backend:
├── 17 endpoints API funcionales ✅
├── 15+ migraciones de BD ✅
├── Seeders con datos demo (TechCorp) ✅
├── Algoritmos de brechas, rutas, matching ✅
├── Tests pasando ✅
└── Documentación técnica completa ✅

DevOps:
├── Commits semánticos con validación ✅
├── Versionado automático ✅
├── Changelog generado automáticamente ✅
├── Git tags y releases ✅
└── Scripts de release interactivos ✅
```

### 🚀 En Progreso (Próximos 5-7 días)

```
Frontend:
├── FormSchema.vue (CRUD base) - 🏗️
├── Dashboard ejecutivo - 🏗️
├── Vistas por rol (CHRO, Manager, Recruiter, Employee) - 🏗️
├── Tests frontend - 🏗️
└── Integración con API - 🏗️
```

---

## 🏗️ Stack Técnico

### Backend

- **Framework:** Laravel 11
- **Database:** PostgreSQL
- **API:** RESTful (17 endpoints)
- **Testing:** Pest/PHPUnit

### Frontend

- **Framework:** Vue 3
- **TypeScript:** ✅ Configurado
- **UI:** Vuetify 3
- **State:** Pinia
- **Build:** Vite

### DevOps

- **VCS:** Git + GitHub
- **Commits:** Conventional Commits (commitlint)
- **Versioning:** Semantic Versioning (standard-version)
- **Docs:** 45+ archivos Markdown

---

## 🚀 Cómo Comenzar

### Opción 1: Rápido (5 minutos)

```bash
# Lee la guía rápida
cat docs/QUICK_START.md

# Verifica estado
git log --oneline -5
git tag
```

### Opción 2: Completo (30 minutos)

```bash
# Entiende el proyecto
cat docs/memories.md

# Revisa la arquitectura
cat docs/DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md

# Mira el plan
cat docs/DIA6_PLAN_ACCION.md
```

---

## 📋 Estructura del Proyecto

```
Strato/
├── docs/                      (📚 45+ archivos de documentación)
│   ├── INDEX.md              (Índice principal)
│   ├── memories.md           (Memoria de contexto)
│   ├── QUICK_START.md        (Inicio rápido)
│   └── ... (más)
├── src/                       (💻 Código fuente)
│   ├── app/                  (Backend Laravel)
│   ├── resources/            (Frontend Vue 3)
│   ├── routes/               (API routes)
│   ├── database/             (Migrations + Seeders)
│   └── ...
├── scripts/                   (🛠️ Scripts útiles)
│   ├── commit.sh             (Asistente de commits)
│   └── release.sh            (Asistente de releases)
├── package.json              (Dependencies raíz)
├── .versionrc.json           (Config de versionado)
├── .gitmessage               (Template de commits)
├── commitlint.config.js      (Config de commitlint)
├── CHANGELOG.md              (Historial de cambios)
└── README.md                 (Este archivo)
```

---

## 🔄 Flujo de Desarrollo

```
┌─────────────────────────────────────────────────┐
│ 1. Desarrollo                                   │
│    $ ./scripts/commit.sh  (múltiples veces)    │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│ 2. Release (cuando esté listo)                 │
│    $ ./scripts/release.sh                       │
│    → Calcula versión automáticamente            │
│    → Genera CHANGELOG.md                        │
│    → Crea git tag                               │
│    → Push a GitHub                              │
└─────────────────────────────────────────────────┘
                        ↓
┌─────────────────────────────────────────────────┐
│ 3. GitHub Release (automático)                 │
│    https://github.com/oahumada/Strato/releases
└─────────────────────────────────────────────────┘
```

---

## 📊 Comandos Útiles

### Commits (Semánticos)

```bash
# Interactivo con análisis de cambios
./scripts/commit.sh

# O directo
git commit -m "feat(forms): agregar validación"

# Ver commits
git log --oneline
```

### Releases

```bash
# Interactivo
./scripts/release.sh

# O específico
./scripts/release.sh minor  # 0.1.0 → 0.2.0
./scripts/release.sh patch  # 0.2.0 → 0.2.1

# O con npm
npm run release
npm run release:minor
```

### Backend (Laravel)

```bash
cd src

# Migrations
php artisan migrate
php artisan migrate:fresh --seed

# Tests
php artisan test

# Server
php artisan serve
```

### Frontend (Vue 3)

```bash
cd src

# Dev server
npm run dev

# Build
npm run build

# Lint
npm run lint
```

---

## 🎯 Próximas Prioridades

### Inmediato (Días 8-14)

- [ ] FormSchema.vue - CRUD genérico
- [ ] Dashboard ejecutivo
- [ ] Vistas por rol (4 perspectivas)
- [ ] Integración API ↔️ Frontend

### Post-MVP (Semana 3+)

- [ ] Tests frontend
- [ ] Roles y permisos reales
- [ ] Autenticación completa
- [ ] Integraciones externas

---

## 📖 Documentación Especial

### Para Desarrolladores

- [`GUIA_COMMITS_SEMANTICOS.md`](docs/GUIA_COMMITS_SEMANTICOS.md) - Cómo commitear
- [`GUIA_VERSIONADO_CHANGELOG.md`](docs/GUIA_VERSIONADO_CHANGELOG.md) - Cómo hacer releases
- [`CHEATSHEET_COMANDOS.md`](docs/CHEATSHEET_COMANDOS.md) - Comandos útiles

### Para Entender el Proyecto

- [`memories.md`](docs/memories.md) - Contexto completo del proyecto
- [`DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md`](docs/DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md) - Arquitectura full-stack
- [`DIAGRAMA_FLUJO.md`](docs/DIAGRAMA_FLUJO.md) - Flujos principales

### Para Troubleshooting

- [`TROUBLESHOOTING.md`](docs/TROUBLESHOOTING.md) - Problemas comunes
- [`ULTRA_RESUMEN.md`](docs/ULTRA_RESUMEN.md) - Resumen de 2 minutos

---

## 🔐 Status de Releases

| Versión | Fecha       | Status         | Features         |
| ------- | ----------- | -------------- | ---------------- |
| v0.2.0  | 28 Dec 2025 | ✅ Released    | Backend + DevOps |
| v0.3.0  | TBD         | 🚀 In Progress | Frontend base    |
| v1.0.0  | TBD         | 📋 Planned     | MVP Completo     |

Ver todos: [`CHANGELOG.md`](CHANGELOG.md)

---

## 🤝 Contribución

El proyecto usa **commits semánticos** y **versionado automático**.

Guía de contribución: [`GUIA_COMMITS_SEMANTICOS.md`](docs/GUIA_COMMITS_SEMANTICOS.md)

---

## 📞 Contacto y Support

- **Documentación:** [`docs/INDEX.md`](docs/INDEX.md)
- **Issues:** GitHub Issues
- **Releases:** [`CHANGELOG.md`](CHANGELOG.md)

---

## 📄 Licencia

MIT

---

## 🎬 Storytelling - TechCorp Demo

El MVP incluye datos demo de **TechCorp**, una startup tech ficticia con:

- 20 empleados
- 8 roles
- 30 skills
- 5 vacantes internas

→ Ver datos en [`docs/memories.md`](docs/memories.md#11-datos-de-demo-historia-de-techcorp)

---

**Última actualización:** 28 de Diciembre, 2025  
**Versión:** v0.2.0  
**Status:** ✅ Backend Completo | 🚀 Frontend en Progreso
