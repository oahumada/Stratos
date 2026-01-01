# 🔧 Guía de Visualización de Base de Datos - TalentIA

## Métodos para Visualizar el Schema

Dado que no tienes acceso a interface gráfica (sin servidor X11/Wayland), aquí hay las opciones disponibles:

---

## 1. ✅ CLI interactivo con sqlite3

### Conexión

```bash
sqlite3 /home/omar/TalentIA/src/database/database.sqlite
```

### Comandos útiles dentro de sqlite3

```sql
-- Ver todas las tablas
.tables

-- Ver estructura de una tabla
PRAGMA table_info(role_skills);

-- Ver todas las relaciones de una tabla
PRAGMA foreign_key_list(role_skills);

-- Contar registros
SELECT COUNT(*) FROM role_skills;

-- Ver datos con formato
.mode column
.headers on
SELECT * FROM role_skills LIMIT 5;

-- Exportar a CSV
.mode csv
.output /tmp/role_skills.csv
SELECT * FROM role_skills;
.output stdout

-- Ver índices
.indexes role_skills

-- Salir
.quit
```

---

## 2. 📊 Exportar Diagrama en SQL

### Generar script de schema

```bash
sqlite3 /home/omar/TalentIA/src/database/database.sqlite ".schema" > /tmp/schema.sql
cat /tmp/schema.sql
```

### Ver solo structure CREATE TABLE

```bash
sqlite3 /home/omar/TalentIA/src/database/database.sqlite << 'EOF'
.mode list
SELECT sql FROM sqlite_master WHERE type='table' AND name IN ('roles', 'skills', 'role_skills', 'people_skills') ORDER BY name;
EOF
```

---

## 3. 🎯 Generar Diagrama en PlantUML

```bash
cat > /tmp/talentia_db.puml << 'EOF'
@startuml TalentIA_Database
!theme plain

entity "roles" as roles {
  * id: int <<PK>>
  organization_id: int
  name: varchar
  level: varchar
  department: varchar
  description: text
  created_at: datetime
  updated_at: datetime
}

entity "skills" as skills {
  * id: int <<PK>>
  organization_id: int
  name: varchar
  category: varchar
  is_critical: boolean
  description: text
  created_at: datetime
  updated_at: datetime
}

entity "role_skills" as role_skills {
  * id: int <<PK>>
  role_id: int <<FK>>
  skill_id: int <<FK>>
  required_level: int
  is_critical: boolean
  created_at: datetime
  updated_at: datetime
}

entity "people" as people {
  * id: int <<PK>>
  organization_id: int
  name: varchar
  email: varchar
  role_id: int
  created_at: datetime
  updated_at: datetime
}

entity "people_skills" as people_skills {
  * id: int <<PK>>
  people_id: int <<FK>>
  skill_id: int <<FK>>
  level: int
  last_evaluated_at: datetime
  evaluated_by: int
  created_at: datetime
  updated_at: datetime
}

' Relaciones
roles ||--o{ role_skills : has
skills ||--o{ role_skills : has
skills ||--o{ people_skills : has
people ||--o{ people_skills : has

@enduml
EOF

cat /tmp/talentia_db.puml
```

**Nota:** Para convertir a PNG/SVG, necesitarías `plantuml`:
```bash
plantuml /tmp/talentia_db.puml -o /tmp -tpng
```

---

## 4. 📈 Generar Diagrama en Mermaid

```bash
cat > /tmp/talentia_db.mmd << 'EOF'
erDiagram
    ORGANIZATIONS ||--o{ ROLES : has
    ORGANIZATIONS ||--o{ SKILLS : has
    ORGANIZATIONS ||--o{ PEOPLE : has
    
    ROLES ||--o{ ROLE_SKILLS : has
    SKILLS ||--o{ ROLE_SKILLS : uses
    
    PEOPLE ||--o{ PEOPLE_SKILLS : has
    SKILLS ||--o{ PEOPLE_SKILLS : evaluates
    
    ROLES ||--o{ JOB_OPENINGS : defines
    PEOPLE ||--o{ APPLICATIONS : submits
    JOB_OPENINGS ||--o{ APPLICATIONS : receives
    
    ROLES ||--o{ DEVELOPMENT_PATHS : source
    ROLES ||--o{ DEVELOPMENT_PATHS : target

    ORGANIZATIONS {
        int id PK
        string name
    }
    
    ROLES {
        int id PK
        int organization_id FK
        string name
        string level
        text description
    }
    
    SKILLS {
        int id PK
        int organization_id FK
        string name
        string category
        boolean is_critical
    }
    
    ROLE_SKILLS {
        int id PK
        int role_id FK
        int skill_id FK
        int required_level
        boolean is_critical
    }
    
    PEOPLE {
        int id PK
        int organization_id FK
        string name
        string email
        int role_id FK
    }
    
    PEOPLE_SKILLS {
        int id PK
        int people_id FK
        int skill_id FK
        int level
        datetime last_evaluated_at
    }
    
    JOB_OPENINGS {
        int id PK
        int role_id FK
        string title
    }
    
    APPLICATIONS {
        int id PK
        int job_opening_id FK
        int people_id FK
        string status
    }
    
    DEVELOPMENT_PATHS {
        int id PK
        int source_role_id FK
        int target_role_id FK
        int estimated_duration
    }
EOF

cat /tmp/talentia_db.mmd
```

**Para visualizar en línea:** Copia el contenido a https://mermaid.live

---

## 5. 🌐 Exportar como HTML Interactivo

```bash
cat > /tmp/db_visualization.html << 'EOF'
<!DOCTYPE html>
<html>
<head>
    <title>TalentIA Database Schema</title>
    <script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
    <style>
        body { font-family: Arial; margin: 20px; }
        .mermaid { border: 1px solid #ccc; padding: 10px; }
    </style>
</head>
<body>
    <h1>TalentIA Database Schema</h1>
    <div class="mermaid">
erDiagram
    ROLES ||--o{ ROLE_SKILLS : has
    SKILLS ||--o{ ROLE_SKILLS : uses
    PEOPLE ||--o{ PEOPLE_SKILLS : has
    SKILLS ||--o{ PEOPLE_SKILLS : evaluates
    ROLES ||--o{ JOB_OPENINGS : defines
    PEOPLE ||--o{ APPLICATIONS : submits
    JOB_OPENINGS ||--o{ APPLICATIONS : receives
    </div>
</body>
</html>
EOF

echo "✅ Archivo creado: /tmp/db_visualization.html"
```

---

## 6. 📋 Generar reporte JSON

```bash
sqlite3 /home/omar/TalentIA/src/database/database.sqlite << 'EOF' > /tmp/db_schema.json
.mode json

SELECT 
  (SELECT json_group_array(json_object('id', id, 'name', name, 'level', level)) FROM roles) as roles,
  (SELECT json_group_array(json_object('id', id, 'name', name, 'category', category)) FROM skills) as skills,
  (SELECT json_group_array(json_object('role_id', role_id, 'skill_id', skill_id, 'required_level', required_level)) FROM role_skills) as role_skills;
EOF

cat /tmp/db_schema.json | python3 -m json.tool
```

---

## 7. 🔗 Análisis de Relaciones

```sql
-- Ver todas las FK de la base de datos
SELECT 
  m1.name as table_name,
  m2.name as referenced_table,
  pragma_foreign_key_list.from as from_col,
  pragma_foreign_key_list.to as to_col
FROM sqlite_master m1
JOIN pragma_foreign_key_list(m1.name) 
  ON m1.name = pragma_foreign_key_list.table
LEFT JOIN sqlite_master m2 
  ON m2.name = pragma_foreign_key_list.table;
```

---

## 8. 📊 Estadísticas Detalladas

```bash
sqlite3 /home/omar/TalentIA/src/database/database.sqlite << 'EOF'
.mode column
.headers on

-- Tamaño de cada tabla
SELECT 
  name,
  (SELECT COUNT(*) FROM role_skills WHERE role_id IS NOT NULL) as records
FROM (SELECT 'role_skills' as name UNION SELECT 'roles' UNION SELECT 'skills' UNION SELECT 'people_skills' UNION SELECT 'people');

-- Espacio en disco
SELECT 
  'Base de datos' as item,
  ROUND(page_count * page_size / 1024.0 / 1024.0, 2) as size_mb
FROM pragma_page_count(), pragma_page_size();
EOF
```

---

## 🚀 Recomendación para Visualización Final

**Mejor opción para tu caso:** Usar **Mermaid online** (diagramas ER interactivos)

```bash
# 1. Generar diagrama Mermaid
cat > /tmp/db.mmd << 'EOF'
erDiagram
    ROLES ||--o{ ROLE_SKILLS : ""
    SKILLS ||--o{ ROLE_SKILLS : ""
    PEOPLE ||--o{ PEOPLE_SKILLS : ""
    SKILLS ||--o{ PEOPLE_SKILLS : ""
EOF

# 2. Copiar contenido
cat /tmp/db.mmd

# 3. Ir a https://mermaid.live
# 4. Pegar contenido
# 5. Ver diagrama interactivo
```

---

## 💾 Backup de la Base de Datos

```bash
# Crear backup
cp /home/omar/TalentIA/src/database/database.sqlite /tmp/database.sqlite.backup

# Dump SQL completo
sqlite3 /home/omar/TalentIA/src/database/database.sqlite .dump > /tmp/database.sql

# Restaurar desde dump
sqlite3 /tmp/database_restored.sqlite < /tmp/database.sql
```

---

## ✅ Resumen Rápido

| Herramienta | Comando | Salida |
|-------------|---------|--------|
| SQLite3 CLI | `sqlite3 [db] ".tables"` | Terminal |
| Exportar CSV | `sqlite3 [db] ".mode csv" "SELECT * FROM tabla;"` | CSV |
| Exportar JSON | `sqlite3 [db] ".mode json" "SELECT * FROM tabla;"` | JSON |
| Diagrama Mermaid | Ver sección 4 | HTML/SVG |
| PlantUML | Ver sección 3 | PNG/SVG/PDF |
| Dump SQL | `sqlite3 [db] .dump` | SQL |

---

**Documentación creada:** 1 Enero 2026
