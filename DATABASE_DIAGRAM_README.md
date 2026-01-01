# 📊 DATABASE VISUALIZATION - TalentIA

## ✅ Diagrama Entidad-Relación (ER) Completado

Este documento proporciona acceso a los diagramas de base de datos del proyecto TalentIA, que implementa un módulo de **Skills y Roles** con relaciones N:M.

---

## 🎯 Acceso Rápido

### HTML Interactivo (RECOMENDADO) ⭐
```
📁 Ubicación: docs/DATABASE_ER_DIAGRAM.html

✨ Características:
   • Diagrama Mermaid interactivo
   • Estadísticas visuales en tiempo real
   • Tabla de roles y categorías de skills
   • Ejemplos de consultas SQL
   • Diseño responsive
   • Colores y estilos modernos

🌐 Cómo abrir:
   • Doble clic en el archivo
   • O arrastra a navegador
   • O: open docs/DATABASE_ER_DIAGRAM.html
```

### Markdown Completo
```
📁 Ubicación: docs/DATABASE_ER_DIAGRAM.md

📄 Contenido:
   • Diagrama ASCII art
   • Estructura detallada de 10 tablas
   • Relaciones y constraints
   • Estadísticas de datos
   • Ejemplos de consultas SQL
   • Instrucciones de acceso

🔍 Cómo leer:
   • cat docs/DATABASE_ER_DIAGRAM.md | less
   • Abre en VS Code
   • Previsualización en GitHub
```

### Guía de Visualización
```
📁 Ubicación: docs/DATABASE_VISUALIZATION_GUIDE.md

🔧 Métodos Disponibles (8 opciones):
   1. CLI interactivo (sqlite3)
   2. Exportar a CSV/JSON
   3. Diagrama PlantUML
   4. Diagrama Mermaid
   5. HTML embebido
   6. Análisis de relaciones
   7. Estadísticas detalladas
   8. Backup de BD

✨ Incluye: comandos, sintaxis y ejemplos
```

### Estado Actual
```
📁 Ubicación: docs/STATUS_CURRENT_STATE.md

📋 Contiene:
   • Checklist de completitud
   • Logros principales
   • Estructura de carpetas
   • Roadmap (próximas fases)
   • Stack técnico
   • Métricas de calidad
   • Comandos útiles
```

---

## 📊 Datos Verificados

### Tablas Principales
```
ROLES ........................... 8 registros
SKILLS ......................... 30 registros
ROLE_SKILLS (Nueva) ............ 48 registros
PEOPLE ......................... 20 registros
PEOPLE_SKILLS ................. 129 registros
```

### Distribución
```
Relaciones Role-Skill: 6 skills por rol
Roles disponibles:     Backend Dev, Frontend Dev, Senior FS,
                       QA Eng, PM, DevOps, Tech Lead, BA

Skills por categoría:  12 Technical, 9 Soft, 9 Business
```

---

## 🔧 Comandos Rápidos (CLI)

### Ver todas las tablas
```bash
sqlite3 src/database/database.sqlite ".tables"
```

### Ver estructura de role_skills
```bash
sqlite3 src/database/database.sqlite "PRAGMA table_info(role_skills);"
```

### Ver datos con ejemplo
```bash
sqlite3 src/database/database.sqlite << 'EOF'
.mode column
.headers on
SELECT 
  r.name AS role,
  s.name AS skill,
  rs.required_level,
  rs.is_critical
FROM role_skills rs
LEFT JOIN roles r ON rs.role_id = r.id
LEFT JOIN skills s ON rs.skill_id = s.id
LIMIT 12;
EOF
```

### Conectar a la BD interactivamente
```bash
sqlite3 src/database/database.sqlite

# Dentro de sqlite3:
.tables              # Ver todas las tablas
.schema roles        # Ver estructura de tabla
SELECT * FROM roles; # Ver datos
.quit                # Salir
```

---

## 📁 Ubicación Base de Datos

```
/home/omar/TalentIA/src/database/database.sqlite
```

### Información
- **Tipo:** SQLite 3
- **Tamaño:** ~5-10 MB
- **Tablas:** 14
- **Registros:** 400+
- **Última actualización:** 1 Enero 2026
- **Integridad:** ✅ Verificada

---

## 🌐 Visuailzación Web

### Opción 1: Ver HTML en Navegador
```bash
# Ubícate en la carpeta del proyecto
cd /home/omar/TalentIA

# Abre el archivo HTML
open docs/DATABASE_ER_DIAGRAM.html

# O desde línea de comandos:
xdg-open docs/DATABASE_ER_DIAGRAM.html    # Linux
open docs/DATABASE_ER_DIAGRAM.html         # macOS
start docs/DATABASE_ER_DIAGRAM.html        # Windows
```

### Opción 2: Ver en VS Code
```bash
# Abre VS Code en la carpeta
code .

# Luego abre: docs/DATABASE_ER_DIAGRAM.md
# O abre: docs/DATABASE_ER_DIAGRAM.html
```

### Opción 3: Ver en GitHub
```
Si subes a GitHub, ve a:
https://github.com/tu-usuario/TalentIA/blob/main/docs/DATABASE_ER_DIAGRAM.md
```

---

## 🏗️ Estructura del Proyecto

```
/home/omar/TalentIA/
├── docs/
│   ├── DATABASE_ER_DIAGRAM.md ................... 📄 Markdown
│   ├── DATABASE_ER_DIAGRAM.html ................ 🌐 HTML interactivo ⭐
│   ├── DATABASE_VISUALIZATION_GUIDE.md ........ 📚 Guía completa
│   ├── STATUS_CURRENT_STATE.md ................ 📋 Estado + Roadmap
│   └── [+ 40 documentos de arquitectura]
├── src/
│   └── database/
│       └── database.sqlite ..................... 🗄️ Base de datos
├── app/
│   ├── Models/
│   │   ├── RoleSkill.php ....................... ✨ Nuevo modelo pivot
│   │   ├── Roles.php
│   │   └── Skills.php
│   └── Repository/
│       └── RoleSkillRepository.php ............ ✨ Nuevo repository
├── database/
│   └── seeders/
│       ├── DemoSeeder.php ...................... ✨ Orquestador
│       ├── OrganizationSeeder.php ............ ✨ Nuevo
│       ├── SkillSeeder.php .................... ✨ Nuevo
│       ├── RoleSeeder.php ..................... ✨ Nuevo
│       ├── RoleSkillSeeder.php ............... ✨ Nuevo (48 relaciones)
│       └── [+ 4 seeders más]
└── VIEW_DATABASE_DIAGRAM.sh ................... 🔧 Script CLI
```

---

## ✨ Características del Diagrama HTML

### Elementos Interactivos
- 🎯 Diagrama Mermaid zoomable
- 📊 Estadísticas en tiempo real
- 📋 Tabla de roles con conteos
- 🏷️ Categorías de skills
- 🔗 Visualización de relaciones
- 💡 Ejemplos de consultas SQL

### Información Incluida
```
Diagrama ER
├── Todas las entidades (10 tablas)
├── Relaciones (FK)
├── Tipos de datos
├── Constraints
└── Cardinalidad (1:N, N:M)

Estadísticas
├── Conteos por tabla
├── Distribución de datos
├── Ejemplos de registros
└── Información de integridad

Referencia
├── Estructura de tabla ROLES
├── Estructura de tabla SKILLS
├── Estructura de tabla ROLE_SKILLS (NEW)
├── Estructura de tabla PEOPLE_SKILLS
└── Ejemplos de consultas SQL
```

---

## 🔍 Consultas Ejemplo

### 1. Ver todas las skills de un rol
```sql
SELECT 
  r.name AS role,
  s.name AS skill,
  rs.required_level,
  rs.is_critical
FROM role_skills rs
LEFT JOIN roles r ON rs.role_id = r.id
LEFT JOIN skills s ON rs.skill_id = s.id
WHERE r.name = 'Backend Developer'
ORDER BY rs.is_critical DESC, rs.required_level DESC;
```

### 2. Contar skills por rol
```sql
SELECT 
  r.name AS role,
  COUNT(rs.skill_id) AS total_skills,
  SUM(CASE WHEN rs.is_critical = 1 THEN 1 ELSE 0 END) AS critical_skills
FROM roles r
LEFT JOIN role_skills rs ON r.id = rs.role_id
GROUP BY r.id
ORDER BY r.name;
```

### 3. Encontrar skills faltantes
```sql
SELECT 
  s.name AS skill,
  rs.required_level AS required,
  COALESCE(ps.level, 0) AS current_level,
  (rs.required_level - COALESCE(ps.level, 0)) AS gap
FROM role_skills rs
LEFT JOIN skills s ON rs.skill_id = s.id
LEFT JOIN people_skills ps ON s.id = ps.skill_id AND ps.people_id = 1
WHERE rs.role_id = 3
AND (COALESCE(ps.level, 0) < rs.required_level OR ps.id IS NULL)
ORDER BY gap DESC;
```

---

## 📚 Documentación Relacionada

| Documento | Ubicación | Descripción |
|-----------|-----------|-------------|
| ER Diagram Markdown | `/docs/DATABASE_ER_DIAGRAM.md` | Documentación completa |
| ER Diagram HTML | `/docs/DATABASE_ER_DIAGRAM.html` | Diagrama interactivo |
| Visualization Guide | `/docs/DATABASE_VISUALIZATION_GUIDE.md` | 8 métodos diferentes |
| Current State | `/docs/STATUS_CURRENT_STATE.md` | Checklist + Roadmap |
| Architecture | `/docs/DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md` | Visión general |
| Quick Guide | `/docs/GUIA_RAPIDA_CRUD_GENERICO.md` | Implementación rápida |

---

## ✅ Verificación de Datos

### Estado de las Tablas
```
Base de Datos: ✅ Operacional
Tablas:        ✅ 14 creadas
Registros:     ✅ 400+ insertados
Integridad:    ✅ Verificada
FK Cascade:    ✅ Activas
Seeds:         ✅ Completados
```

### Relaciones Verificadas
```
Organizations:    ✅ 1
Users:             ✅ 1
Roles:             ✅ 8
Skills:            ✅ 30
Role-Skills:       ✅ 48 (6 por rol)
People:            ✅ 20
People-Skills:     ✅ 129
Job Openings:      ✅ 5
Applications:      ✅ 10
Development Paths: ✅ 1
```

---

## 🚀 Próximas Fases

### Fase 2: API REST (Próxima semana)
- [ ] RoleSkillController
- [ ] Validations & FormRequests
- [ ] Fractal Transformers
- [ ] Rutas API

### Fase 3: Frontend CRUD
- [ ] Vue 3 Components
- [ ] FormSchema integration
- [ ] Modal dialogs
- [ ] Formularios de gestión

### Fase 4: Testing
- [ ] Unit Tests (PHPUnit)
- [ ] Integration Tests
- [ ] API Tests
- [ ] Coverage >80%

---

## 💡 Tips Útiles

### Para ver cambios en BD rápidamente
```bash
# Abre una terminal y ejecuta
watch -n 1 'sqlite3 src/database/database.sqlite ".tables"'
```

### Para hacer backup
```bash
cp src/database/database.sqlite src/database/database.sqlite.backup
```

### Para restaurar desde backup
```bash
cp src/database/database.sqlite.backup src/database/database.sqlite
```

### Para ver tamaño de BD
```bash
ls -lh src/database/database.sqlite
```

---

## 🎓 Recursos Adicionales

### SQLite
- [SQLite Official](https://www.sqlite.org/)
- [SQLite Docs](https://www.sqlite.org/docs.html)
- [SQLite CLI](https://www.sqlite.org/cli.html)

### Mermaid (Diagramas)
- [Mermaid Live](https://mermaid.live)
- [Mermaid Docs](https://mermaid.js.org/)

### Laravel
- [Laravel Docs](https://laravel.com/docs)
- [Eloquent ORM](https://laravel.com/docs/eloquent)

---

## 📞 Soporte

Si tienes dudas:

1. **Consulta el archivo HTML:** `docs/DATABASE_ER_DIAGRAM.html`
2. **Lee la documentación:** `docs/DATABASE_ER_DIAGRAM.md`
3. **Ejecuta comandos CLI:** `sqlite3 src/database/database.sqlite`
4. **Revisa el roadmap:** `docs/STATUS_CURRENT_STATE.md`

---

## 📄 Información del Documento

- **Creado:** 1 Enero 2026
- **Proyecto:** TalentIA MVP
- **Versión:** 0.2.0
- **Stack:** Laravel 12 + Vue 3 + SQLite
- **Status:** ✅ Completado

---

## 🎉 ¡Listo!

Tu base de datos está completamente documentada y lista para usar. El diagrama ER interactivo es tu mejor recurso para entender la estructura.

**Próximo paso:** Implementar API REST endpoints.

---
