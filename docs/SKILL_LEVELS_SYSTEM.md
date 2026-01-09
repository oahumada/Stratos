# Sistema de Niveles de Competencia (Skill Levels)

## 📋 Resumen Ejecutivo

Se ha implementado un **sistema de 5 niveles de competencia genéricos** aplicables a todas las skills del sistema Strato. Esta es una solución de **Fase 1 (MVP)** que establece definiciones claras de lo que significa cada nivel (1-5), facilitando evaluaciones consistentes y análisis de brechas de habilidades.

## 🎯 Arquitectura Implementada

### Opción 1: Niveles Genéricos (IMPLEMENTADO)
- ✅ **Tabla única**: `skill_level_definitions` 
- ✅ **5 niveles universales**: Básico → Intermedio → Avanzado → Experto → Maestro
- ✅ **Sistema de puntos**: 10, 25, 50, 100, 200 (progresión exponencial)
- ✅ **Tres dimensiones de progresión**: Autonomía, Complejidad, Responsabilidad

### Opción 2: Niveles Personalizados (ROADMAP - FASE 2)
- ⏳ **Deferred**: Learning Paths / Certificaciones
- ⏳ **Tabla futura**: `skill_specific_level_definitions` 
- ⏳ **Uso**: Skills técnicas avanzadas con niveles específicos (ej. AWS Certifications)

## 📊 Estructura de Datos

### Tabla: `skill_level_definitions`

```sql
CREATE TABLE skill_level_definitions (
    id INTEGER PRIMARY KEY,
    level INTEGER NOT NULL UNIQUE,  -- 1 a 5
    name TEXT NOT NULL,              -- Básico, Intermedio, etc.
    description TEXT NOT NULL,       -- Qué significa cada nivel
    points INTEGER NOT NULL,         -- Sistema de puntos (gamificación)
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Modelo: `SkillLevelDefinition.php`

```php
class SkillLevelDefinition extends Model
{
    protected $fillable = ['level', 'name', 'description', 'points'];
    
    // Helper: "1 - Básico"
    public function getDisplayLabelAttribute(): string 
    {
        return "{$this->level} - {$this->name}";
    }
}
```

## 🎓 Los 5 Niveles Definidos

| Nivel | Nombre | Puntos | Características Clave |
|-------|--------|--------|----------------------|
| **1** | Básico | 10 | Conocimiento teórico, supervisión constante, tareas simples |
| **2** | Intermedio | 25 | Supervisión ocasional, conceptos intermedios, validación periódica |
| **3** | Avanzado | 50 | **Autonomía plena**, resuelve problemas complejos sin supervisión |
| **4** | Experto | 100 | Referente interno, mentoriza, lidera iniciativas técnicas |
| **5** | Maestro | 200 | Autoridad reconocida, define estándares, influencia estratégica |

### 📏 Dimensiones de Progresión

Cada nivel representa incrementos en:

1. **Autonomía**: 
   - Nivel 1: Requiere supervisión constante
   - Nivel 5: Máxima autonomía y autodirección

2. **Complejidad**: 
   - Nivel 1: Tareas simples y repetitivas
   - Nivel 5: Diseño de soluciones complejas e innovadoras

3. **Responsabilidad**: 
   - Nivel 1: Responsabilidad individual sobre tareas asignadas
   - Nivel 5: Responsabilidad estratégica organizacional

## 🔌 Integración API

### Endpoint de Catálogos

```http
GET /api/catalogs?catalogs[]=skill_levels
```

**Response:**
```json
{
  "skill_levels": [
    {
      "id": 1,
      "level": 1,
      "name": "Básico",
      "description": "Conocimiento teórico fundamental...",
      "points": 10,
      "display_label": "1 - Básico"
    },
    // ... 4 more levels
  ]
}
```

### Uso en Repositorio

```php
// CatalogsRepository.php
'skill_levels' => fn() => SkillLevelDefinition::orderBy('level')->get()
```

## 🎨 Componente Frontend

### `SkillLevelChip.vue`

Componente reutilizable con tooltip que muestra:
- Display label: "3 - Avanzado"
- Tooltip con descripción completa
- Puntos asociados
- Colores personalizables

**Uso:**
```vue
<SkillLevelChip 
  :level="3" 
  :skill-levels="skillLevels"
  color="primary"
  show-tooltip
/>
```

### Integración en `Skills/Index.vue`

✅ Pestaña **Roles**: Muestra nivel requerido con chip + tooltip
✅ Pestaña **Personas**: Compara nivel actual vs. requerido con indicadores de gap

## 📁 Archivos Creados/Modificados

### Backend
```
✅ database/migrations/2026_01_02_010210_create_skill_level_definitions_table.php
✅ app/Models/SkillLevelDefinition.php
✅ database/seeders/SkillLevelDefinitionSeeder.php
✅ database/seeders/DemoSeeder.php (orchestration)
✅ app/Repository/CatalogsRepository.php (nuevo catálogo)
```

### Frontend
```
✅ resources/js/components/SkillLevelChip.vue (NUEVO)
✅ resources/js/pages/Skills/Index.vue (integración niveles)
```

### Documentación
```
✅ docs/SKILL_LEVELS_ARCHITECTURE_DECISION.md
✅ test-skill-levels.sh (script de verificación)
```

## 🚀 Implementación en 3 Pasos

### 1. Migración
```bash
php artisan migrate
# Creates skill_level_definitions table
```

### 2. Seeding
```bash
php artisan db:seed --class=SkillLevelDefinitionSeeder
# ✅ 5 skill level definitions creados
```

### 3. Frontend Build
```bash
npm run build
# Compiles SkillLevelChip component
```

## 💡 Casos de Uso

### Caso 1: Evaluación de Empleado
```
Empleado: Juan Pérez
Skill: PHP
Nivel Actual: 2 - Intermedio (25 pts)
Nivel Requerido: 4 - Experto (100 pts)
Gap: 2 niveles (75 pts)
```

### Caso 2: Requisitos de Rol
```
Rol: Senior Backend Developer
Skill Crítica: Laravel
Nivel Requerido: 4 - Experto
Descripción: "Referente interno, mentoriza a otros, lidera iniciativas técnicas"
```

### Caso 3: Plan de Desarrollo
```
Objetivo: Pasar de Intermedio (2) a Avanzado (3) en React
Requisitos para Nivel 3:
  ✓ Ejecuta de forma autónoma
  ✓ Resuelve problemas complejos sin supervisión
  ✓ Toma decisiones técnicas con criterio
  ✓ Dominio práctico consolidado
```

## 🎮 Sistema de Puntos (Gamificación)

Los puntos permiten:
- **Ranking de empleados** por skill total
- **Objetivos cuantificables**: "Alcanzar 500 pts en Frontend"
- **Badges**: Por ejemplo, 1000+ pts = "Polyglot Developer"
- **Progresión visible**: Barra de progreso hacia siguiente nivel

**Progresión Exponencial:**
- L1→L2: +15 pts (incremento 150%)
- L2→L3: +25 pts (incremento 100%)
- L3→L4: +50 pts (incremento 100%)
- L4→L5: +100 pts (incremento 100%)

## 🔮 Roadmap - Fase 2 (Option 2)

### Learning Paths Module
Cuando se implemente el módulo de capacitación:

```sql
-- Futura tabla para niveles personalizados
CREATE TABLE skill_specific_level_definitions (
    id INTEGER PRIMARY KEY,
    skill_id INTEGER NOT NULL,
    level INTEGER NOT NULL,
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    certification_url TEXT,
    FOREIGN KEY (skill_id) REFERENCES skills(id)
);
```

**Ejemplo:**
```
Skill: AWS
  Level 1: Cloud Practitioner (certification required)
  Level 2: Solutions Architect Associate
  Level 3: Solutions Architect Professional
  Level 4: Specialty Certifications
  Level 5: AWS Hero / Community Leader
```

### Lógica Híbrida
```php
// Prioridad a niveles específicos si existen
function getLevelDefinition($skillId, $level) {
    $specific = SkillSpecificLevelDefinition::where([
        'skill_id' => $skillId, 
        'level' => $level
    ])->first();
    
    return $specific ?? SkillLevelDefinition::where('level', $level)->first();
}
```

## ✅ Validación del Sistema

### Script de Pruebas
```bash
./test-skill-levels.sh
```

**Output esperado:**
```
✓ Migración: skill_level_definitions presente
✓ Total skill levels: 5
✓ Modelo funcional: "3 - Avanzado"
✓ Skills con roles tienen required_level configurado
```

### Checklist de Integración
- [x] Migración ejecutada
- [x] Seeder poblado (5 niveles)
- [x] Modelo con helper `display_label`
- [x] Endpoint API `/catalogs?catalogs[]=skill_levels`
- [x] Componente `SkillLevelChip.vue`
- [x] Integración en Skills/Index.vue
- [x] Tooltips con descripciones completas
- [x] Frontend compilado sin errores

## 📚 Referencias

- **Decisión Arquitectónica**: [SKILL_LEVELS_ARCHITECTURE_DECISION.md](./SKILL_LEVELS_ARCHITECTURE_DECISION.md)
- **Migration**: `2026_01_02_010210_create_skill_level_definitions_table.php`
- **Seeder**: `SkillLevelDefinitionSeeder.php`
- **Componente**: `resources/js/components/SkillLevelChip.vue`

## 🎯 Conclusión

Este sistema proporciona:
1. ✅ **Claridad**: Cada nivel tiene definición explícita
2. ✅ **Consistencia**: Mismos criterios para todas las skills
3. ✅ **Escalabilidad**: Preparado para Option 2 en Fase 2
4. ✅ **UX mejorada**: Tooltips informativos en toda la UI
5. ✅ **Gamificación**: Sistema de puntos implementado

**Estado**: ✅ Production Ready
**Próximos pasos**: Integrar en módulos People y Roles para evaluaciones
