# Día 2: Seeders y Datos de Demo - ✅ COMPLETADO

**Fecha:** 2025-12-27  
**Tiempo empleado:** ~3 horas  
**Status:** ✅ COMPLETO

---

## ✅ DemoSeeder Creado

**Ubicación:** `database/seeders/DemoSeeder.php`

**Función:** Poblar la base de datos con datos de demo de TechCorp para testing y desarrollo

---

## 📊 Datos Creados

### 1. Organización (1)

| Campo     | Valor      |
| --------- | ---------- |
| name      | TechCorp   |
| subdomain | techcorp   |
| industry  | Technology |
| size      | large      |

---

### 2. Usuario Administrador (1)

| Campo    | Valor                   |
| -------- | ----------------------- |
| name     | Juan Pérez              |
| email    | juan.perez@techcorp.com |
| role     | admin                   |
| password | `password` (hash)       |

**Uso:** Login y operaciones administrativas

---

### 3. Skills (30 total)

#### Técnicas (12)

- PHP, Laravel, Vue.js, React, TypeScript
- Database Design, PostgreSQL, REST APIs
- Docker, Git, CSS/Tailwind, Testing (PHPUnit)

#### Soft Skills (9)

- Communication, Leadership, Problem Solving
- Team Work, Critical Thinking, Adaptability
- Project Management, Mentoring, Agile Methodology

#### Business Skills (9)

- Business Analysis, Product Strategy, Requirements Gathering
- Client Management, Budget Planning, Market Research
- Stakeholder Management, Sales Skills, Data Analysis

**Estructura:**

```
skills table:
├── id (PK)
├── name
├── category (technical|soft|business)
├── is_critical (boolean)
└── organization_id (FK → TechCorp)
```

---

### 4. Roles (8)

| #   | Nombre                      | Departamento      | Nivel  | Skills Requeridas                 |
| --- | --------------------------- | ----------------- | ------ | --------------------------------- |
| 1   | Backend Developer           | Engineering       | mid    | 9 (PHP, Laravel, DB, etc)         |
| 2   | Frontend Developer          | Engineering       | mid    | 7 (Vue.js, TypeScript, CSS, etc)  |
| 3   | Senior Full Stack Developer | Engineering       | senior | 13 (completo)                     |
| 4   | QA Engineer                 | Quality Assurance | mid    | 5 (Testing, problem solving, etc) |
| 5   | Product Manager             | Product           | senior | 7 (Strategy, análisis, etc)       |
| 6   | DevOps Engineer             | Infrastructure    | senior | 6 (Docker, DB, git, etc)          |
| 7   | Technical Lead              | Engineering       | lead   | 7 (arquitectura, liderazgo)       |
| 8   | Business Analyst            | Business          | mid    | 5 (análisis, comunicación)        |

**Estructura:**

```
roles table:
├── id (PK)
├── name
├── department
├── level (junior|mid|senior|lead|principal)
├── organization_id (FK)
└── timestamps

role_skills (pivot):
├── role_id (FK)
├── skill_id (FK)
├── required_level (1-5)
├── is_critical (boolean)
└── unique(role_id, skill_id)
```

---

### 5. Personas (20)

**Estructura:**

- Nombres realistas: Carlos, María, Juan, Ana, Pedro, Laura, etc.
- Email: firstname.lastname@techcorp.com
- Cada persona tiene un rol actual asignado
- 4-7 skills por persona (algunas del rol, otras para crear gaps)
- Hire dates: 3-60 meses atrás
- Avatar: generado con DiceBear API (por email)

**Ejemplo:**

```
Person {
  first_name: "Carlos",
  last_name: "García",
  email: "carlos.garcia@techcorp.com",
  current_role_id: 1 (Backend Developer),
  department: "Engineering",
  hire_date: "2023-05-15",
  photo_url: "https://api.dicebear.com/7.x/avataaars/svg?seed=carlos.garcia@techcorp.com",
  skills: [
    {skill: "PHP", level: 4},
    {skill: "Laravel", level: 3},
    {skill: "Vue.js", level: 2},
    {skill: "Problem Solving", level: 5},
    {skill: "Communication", level: 3},
    {skill: "Docker", level: 1}
  ]
}
```

**Relaciones:**

```
person_skills (pivot):
├── person_id (FK)
├── skill_id (FK)
├── level (1-5, default: 1-3)
├── last_evaluated_at (fecha aleatoria)
└── unique(person_id, skill_id)
```

---

### 6. Vacantes Internas (5)

| #   | Título                   | Rol                   | Status | Deadline |
| --- | ------------------------ | --------------------- | ------ | -------- |
| 1   | Senior Backend Developer | Senior Full Stack Dev | open   | +1 mes   |
| 2   | Frontend Developer       | Frontend Developer    | open   | +1 mes   |
| 3   | QA Engineer              | QA Engineer           | open   | +1 mes   |
| 4   | Product Manager          | Product Manager       | open   | +1 mes   |
| 5   | DevOps Engineer          | DevOps Engineer       | open   | +1 mes   |

**Estructura:**

```
job_openings table:
├── id (PK)
├── title
├── role_id (FK)
├── department
├── status (draft|open|closed|filled)
├── deadline
├── created_by (FK → users)
├── organization_id (FK)
└── timestamps
```

---

### 7. Postulaciones (10)

**Distribución:**

- 2 postulantes por vacante
- Total: 10 postulaciones
- Estados variados: pending, under_review, accepted

**Estructura:**

```
applications table:
├── id (PK)
├── job_opening_id (FK)
├── person_id (FK)
├── status (pending|under_review|accepted|rejected)
├── message (motivo de postulación)
├── applied_at (timestamp)
├── unique(job_opening_id, person_id)
└── timestamps
```

**Ejemplo:**

```
Application {
  job_opening_id: 1,
  person_id: 5,
  status: "pending",
  message: "Interested in this opportunity. I believe...",
  applied_at: "2025-12-20 14:30:00"
}
```

---

### 8. Ruta de Desarrollo (1 de ejemplo)

**Persona objetivo:** Carlos García (primer empleado)  
**Rol objetivo:** Senior Full Stack Developer

**Steps:**

1. **Vue.js course** (40 horas)
    - Resource: "Vue.js - The Complete Guide" (Udemy)
    - Action: course
2. **TypeScript course** (30 horas)
    - Resource: "TypeScript Pro Course" (Udemy)
    - Action: course

3. **Docker mentoring** (20 horas)
    - Resource: "Work with DevOps team"
    - Action: mentoring

**Estructura:**

```
development_paths table:
├── id (PK)
├── person_id (FK)
├── target_role_id (FK)
├── status: "draft"
├── estimated_duration_months: 6
├── steps: JSON array
│  ├── [0] {skill_id, action_type, resource_name, duration_hours, completed}
│  ├── [1] {...}
│  └── [2] {...}
├── started_at: NULL
├── completed_at: NULL
└── organization_id (FK)
```

---

## 🔄 Flujo de Ejecución del Seeder

```
1. Crear Organization (TechCorp)
   ↓
2. Crear User (admin)
   ↓
3. Crear 30 Skills
   ↓
4. Crear 8 Roles + role_skills (pivot)
   ↓
5. Crear 20 Personas + person_skills (pivot)
   ↓
6. Crear 5 Job Openings
   ↓
7. Crear 10 Applications (2 por vacante)
   ↓
8. Crear 1 Development Path (ejemplo)
```

---

## 📝 Código del Seeder

**Archivo:** `database/seeders/DemoSeeder.php`

### Características principales:

1. **Crear Organización:**

    ```php
    $org = Organization::create([
        'name' => 'TechCorp',
        'subdomain' => 'techcorp',
        'industry' => 'Technology',
        'size' => 'large',
    ]);
    ```

2. **Crear Skills dinámicamente:**

    ```php
    $skillsData = [
        ['name' => 'PHP', 'category' => 'technical', 'is_critical' => true],
        // ... 29 más
    ];

    $skills = collect($skillsData)->map(function ($skill) use ($org) {
        return Skill::create([
            'organization_id' => $org->id,
            ...$skill,
        ]);
    });
    ```

3. **Asociar Skills a Roles:**

    ```php
    foreach ($roleData['required_skills'] as $skillName) {
        $skill = $skills->firstWhere('name', $skillName);
        if ($skill) {
            $skillsToAttach[$skill->id] = [
                'required_level' => rand(2, 5),
                'is_critical' => $skill->is_critical,
            ];
        }
    }

    $role->skills()->attach($skillsToAttach);
    ```

4. **Crear Personas con Skills variadas:**

    ```php
    for ($i = 0; $i < 20; $i++) {
        $person = Person::create([
            'organization_id' => $org->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'current_role_id' => $currentRole->id,
            // ...
        ]);

        // Asignar skills (subconjunto del rol + adicionales)
        $person->skills()->attach($skillsForPerson);
    }
    ```

5. **Crear Vacantes e Postulaciones:**

    ```php
    // Crear vacantes
    foreach ($jobTitles as $jobData) {
        $jobOpening = JobOpening::create([...]);
        $jobOpenings[] = $jobOpening;
    }

    // Crear postulaciones (2 por vacante)
    foreach ($jobOpenings as $jobOpening) {
        $candidates = collect($people)->random(2);
        foreach ($candidates as $candidate) {
            Application::create([
                'job_opening_id' => $jobOpening->id,
                'person_id' => $candidate->id,
                'status' => $applicationStatuses[rand(0, 2)],
                // ...
            ]);
        }
    }
    ```

6. **Crear Ruta de Desarrollo de ejemplo:**
    ```php
    DevelopmentPath::create([
        'organization_id' => $org->id,
        'person_id' => $person->id,
        'target_role_id' => $targetRole->id,
        'status' => 'draft',
        'estimated_duration_months' => 6,
        'steps' => [
            [
                'skill_id' => $skill->id,
                'skill_name' => 'Vue.js',
                'action_type' => 'course',
                'resource_name' => 'Vue.js - The Complete Guide',
                'resource_url' => 'https://www.udemy.com/...',
                'duration_hours' => 40,
                'completed' => false,
            ],
            // ... más steps
        ],
    ]);
    ```

---

## ✅ Verificación

### Ejecución:

```bash
php artisan migrate:fresh --force
php artisan db:seed --class=DemoSeeder
```

### Output esperado:

```
✅ Demo seeder completado:
   • 1 Organización (TechCorp)
   • 1 Usuario admin
   • 30 Skills
   • 8 Roles
   • 20 Personas
   • 5 Vacantes
   • 10 Postulaciones
   • 1 Ruta de desarrollo
```

### Queries de validación:

```php
// Verificar números
Organization::count()        // 1
User::count()               // 1
Skill::count()              // 30
Role::count()               // 8
Person::count()             // 20
JobOpening::count()         // 5
Application::count()        // 10
DevelopmentPath::count()    // 1

// Verificar relaciones
Role::first()->skills()->count()      // ~7-13
Person::first()->skills()->count()    // ~4-7
JobOpening::first()->applications()   // 2 per opening
```

---

## 🎯 Propósito del Seeder

El seeder de TechCorp proporciona:

1. **Data de Testing:**
    - Datos realistas para testing manual
    - Base para pruebas de API
    - Datos para frontend development

2. **Demo para Clientes:**
    - Ejemplos de cómo funciona el sistema
    - Datos visuales atractivos (avatares, nombres reales)
    - Casos de uso realistas

3. **Development Aid:**
    - Facilita testing sin crear datos manualmente
    - Global scopes funcionales (multi-tenant)
    - Relaciones pre-pobladas para exploración

---

## 🔐 Multi-Tenant Validation

Todos los datos están bajo `organization_id` de TechCorp:

```php
// Global scopes funcionando
Skill::count()          // 30 (filtrados por org)
Person::count()         // 20 (filtrados por org)
Role::count()           // 8 (filtrados por org)
DevelopmentPath::count() // 1 (filtrados por org)

// Válido solo para TechCorp
User::first()->organization_id  // 1
Skill::first()->organization_id // 1
```

---

## 📁 Archivos Creados/Modificados

| Archivo                               | Estado                                |
| ------------------------------------- | ------------------------------------- |
| `database/seeders/DemoSeeder.php`     | ✅ CREADO                             |
| `database/seeders/DatabaseSeeder.php` | ℹ️ (opcional modificar para auto-run) |

---

## 📋 Próximos Pasos (Día 3)

Los servicios de Día 3 usarán estos datos:

1. **GapAnalysisService**
    - Comparará personas vs roles
    - Ejemplo: Carlos García vs Senior Full Stack Developer
    - Output: match_percentage, gaps array

2. **DevelopmentPathService**
    - Usará gaps para generar rutas automáticas
    - Ejemplo: crear ruta para Carlos basada en brechas

3. **MatchingService**
    - Rankeará personas para vacantes
    - Ejemplo: ranking de candidatos para "Senior Backend Developer"

---

**Estado:** ✅ DÍA 2 COMPLETADO  
**Base de datos:** Lista con 30 skills, 8 roles, 20 personas, datos de prueba  
**Próximo:** Día 3 - Services (GapAnalysisService, DevelopmentPathService, MatchingService)
