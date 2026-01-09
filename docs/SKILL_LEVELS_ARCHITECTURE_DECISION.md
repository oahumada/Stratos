# Sistema de Niveles de Skills - Decisión Arquitectónica

**Fecha:** 1 Enero 2026  
**Status:** ✅ Implementado  
**Versión:** 1.0

---

## 📋 Contexto

El sistema Strato maneja skills (habilidades) que las personas poseen en diferentes niveles de dominio. Inicialmente, los niveles eran numéricos (1-5) sin definición clara de qué significa cada número.

---

## 🎯 Decisión: Sistema de Niveles Genéricos (Opción 1)

**Decisión:** Implementar tabla `skill_level_definitions` con **5 niveles genéricos** aplicables a todas las skills del sistema.

### Tabla: skill_level_definitions

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | Primary Key |
| level | TINYINT | Nivel numérico (1-5), único |
| name | VARCHAR(50) | Nombre del nivel |
| description | TEXT | Descripción detallada |
| points | SMALLINT | Puntos de scoring |

### Los 5 Niveles Definidos

| Nivel | Nombre | Descripción | Puntos |
|-------|--------|-------------|--------|
| **1** | **Básico** | Conocimiento teórico fundamental. Requiere supervisión constante. Ejecuta tareas simples siguiendo instrucciones detalladas. Mínima autonomía. | 10 |
| **2** | **Intermedio** | Puede ejecutar tareas con supervisión ocasional. Comprende conceptos intermedios. Resuelve problemas conocidos. Requiere validación periódica. | 25 |
| **3** | **Avanzado** | Ejecuta de forma autónoma. Resuelve problemas complejos sin supervisión. Toma decisiones técnicas con criterio. Dominio práctico consolidado. | 50 |
| **4** | **Experto** | Referente interno en la materia. Mentorea a otros. Diseña soluciones complejas. Lidera iniciativas técnicas. Alta responsabilidad en decisiones críticas. | 100 |
| **5** | **Maestro** | Autoridad reconocida. Innova y define estándares organizacionales. Influencia estratégica. Máximo nivel de autonomía, complejidad y responsabilidad. | 200 |

---

## 🎓 Criterios de Progresión

Los niveles progresan en **tres dimensiones simultáneas**:

### 1. Autonomía Funcional
- **Nivel 1:** Supervisión constante
- **Nivel 2:** Supervisión ocasional
- **Nivel 3:** Trabajo autónomo
- **Nivel 4:** Guía a otros
- **Nivel 5:** Define estándares

### 2. Complejidad de Tareas
- **Nivel 1:** Tareas simples y rutinarias
- **Nivel 2:** Problemas conocidos
- **Nivel 3:** Problemas complejos
- **Nivel 4:** Diseño de soluciones complejas
- **Nivel 5:** Innovación y definición de estándares

### 3. Responsabilidad
- **Nivel 1:** Mínima responsabilidad
- **Nivel 2:** Responsabilidad individual
- **Nivel 3:** Decisiones técnicas con criterio
- **Nivel 4:** Decisiones críticas, mentoría
- **Nivel 5:** Influencia estratégica organizacional

---

## 💡 Ejemplos de Aplicación

### PHP (Skill Técnica)
- **Nivel 1:** Conoce sintaxis básica, variables, loops
- **Nivel 2:** Usa frameworks como Laravel de forma básica
- **Nivel 3:** Implementa patrones SOLID, Repository, arquitecturas
- **Nivel 4:** Diseña arquitecturas complejas, mentorea developers
- **Nivel 5:** Contribuye a estándares PHP-FIG, autoridad reconocida

### Leadership (Skill Soft)
- **Nivel 1:** Miembro de equipo que sigue instrucciones
- **Nivel 2:** Líder informal que motiva al equipo
- **Nivel 3:** Team Lead que gestiona equipos pequeños
- **Nivel 4:** Manager que gestiona múltiples equipos
- **Nivel 5:** Executive Leader que define estrategia organizacional

---

## 📊 Sistema de Puntos

Cada nivel tiene puntos asignados exponencialmente:

```
Nivel 1: 10 puntos
Nivel 2: 25 puntos   (2.5x)
Nivel 3: 50 puntos   (2x)
Nivel 4: 100 puntos  (2x)
Nivel 5: 200 puntos  (2x)
```

**Usos futuros del sistema de puntos:**
- Scoring total de perfiles de personas
- Ranking de candidatos para vacantes
- Métricas de desarrollo organizacional
- Gamificación de learning paths
- Comparación objetiva entre equipos/departamentos

---

## 🔄 Roadmap: Opción 2 (Futuro)

### Sistema de Niveles Específicos por Skill

**Cuando implementar:** Fase 2 - Learning Paths y Planificación Curricular

**Tabla futura:** `skill_levels` (relación 1:N con `skills`)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | INT | Primary Key |
| skill_id | INT | FK → skills |
| level | TINYINT | Nivel (1-5) |
| name | VARCHAR(100) | Nombre específico del nivel |
| description | TEXT | Qué debe saber/hacer en este nivel |
| learning_objectives | JSON | Objetivos de aprendizaje específicos |

**Ejemplo de uso:**

```json
{
  "skill_id": 1,
  "level": 3,
  "name": "Pattern Implementer",
  "description": "Implementa patrones de diseño SOLID, Repository, Strategy",
  "learning_objectives": [
    "Identificar cuándo aplicar cada patrón",
    "Implementar Repository Pattern en Laravel",
    "Aplicar SOLID principles en código real",
    "Refactorizar código legacy usando patrones"
  ]
}
```

**Casos de uso de Opción 2:**
1. **Learning Paths personalizados:** Definir qué aprender para pasar del nivel 2 al 3 en PHP
2. **Planificación curricular:** Malla de contenidos por skill
3. **Evaluaciones específicas:** Tests técnicos alineados a objetivos de aprendizaje
4. **Certificaciones internas:** Validar conocimientos específicos por nivel
5. **Onboarding estructurado:** Rutas claras de qué aprender

---

## ✅ Ventajas de Opción 1 (Implementada)

| Ventaja | Descripción |
|---------|-------------|
| **Simplicidad** | 5 registros fijos, fácil de mantener |
| **Consistencia** | Todos entienden lo mismo por "Nivel 3" |
| **Rapidez de implementación** | No requiere definir 5 niveles × 30 skills |
| **Flexibilidad inicial** | Permite evolucionar sin bloquear MVP |
| **Menos mantenimiento** | Cambios globales en un solo lugar |

---

## ⚠️ Limitaciones de Opción 1

| Limitación | Mitigación |
|------------|------------|
| Niveles genéricos pueden ser ambiguos | Documentación clara + ejemplos por skill |
| No permite personalización por skill | Opción 2 se implementará en Fase 2 |
| Dificulta learning paths muy específicos | Se compensará con metadata adicional |

---

## 🚀 Plan de Migración a Opción 2 (Futuro)

**Cuándo migrar:**
- Cuando se implemente módulo de Learning Paths
- Cuando se requiera planificación curricular detallada
- Cuando existan >50 skills y se necesite diferenciación

**Estrategia de migración:**
1. Mantener `skill_level_definitions` como fallback
2. Crear `skill_levels` con niveles específicos
3. Sistema consulta primero `skill_levels`, si no existe usa `skill_level_definitions`
4. Migración gradual skill por skill

---

## 📚 Referencias

- [memories.md líneas 656-661](/docs/memories.md) - Definición original de niveles
- [dia1_migraciones_modelos_completados.md](/docs/dia1_migraciones_modelos_completados.md) - Estructura de tablas
- [RoleSkill migration](/src/database/migrations/2025_12_27_162333_create_role_skills_table.php) - required_level
- [PeopleRoleSkills migration](/src/database/migrations/2026_01_01_171617_create_people_role_skills_table.php) - current_level

---

## 🔧 Implementación Técnica

### Modelo
```php
App\Models\SkillLevelDefinition
```

### Migración
```php
database/migrations/2026_01_02_010210_create_skill_level_definitions_table.php
```

### Seeder
```php
database/seeders/SkillLevelDefinitionSeeder.php
```

### Orden de ejecución
```bash
php artisan migrate
php artisan db:seed --class=SkillLevelDefinitionSeeder
```

---

## 📝 Notas Adicionales

- Esta decisión es **reversible** - podemos implementar Opción 2 sin afectar Opción 1
- El sistema de puntos permite métricas cuantitativas desde el inicio
- Los niveles se alinean con frameworks de competencias HR estándares
- Compatible con futuras integraciones (LinkedIn, competency frameworks, etc.)

---

**Aprobado por:** Equipo Strato  
**Próxima revisión:** Al iniciar Fase 2 - Learning Paths
