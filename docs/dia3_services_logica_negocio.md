# Día 3: Lógica de Negocio - Services ✅ EN PROGRESO

**Fecha:** 2025-12-27  
**Objetivo:** Implementar 3 services core con los algoritmos de TalentIA  
**Tiempo estimado:** 6-8 horas

---

## 📋 Services a Implementar

### 1. GapAnalysisService (PRIORITARIO)

**Ubicación:** `app/Services/GapAnalysisService.php`

**Función:** Calcular brecha entre competencias de una peoplea y las requeridas por un rol

**Algoritmo (memories.md 5.2):**

```
Para cada skill requerida en el rol:
  - Obtener nivel actual de la peoplea (default 0 si no tiene)
  - Obtener nivel requerido del rol
  - Calcular gap = max(0, required - current)
  - Clasificar como: ok (gap=0), developing (gap≤1), critical (gap>1)

Calcular match_percentage = (skills_ok / total_skills) * 100

Retornar:
  {
    match_percentage: float (0-100),
    gaps: [
      {
        skill_id, skill_name,
        current_level, required_level,
        gap, status
      }
    ]
  }
```

**Método:**

```php
public function calculate(People $people, Role $role): array
```

**Uso:**

```php
$service = new GapAnalysisService();
$analysis = $service->calculate($people, $role);
// {match_percentage: 72.5, gaps: [...]}
```

---

### 2. DevelopmentPathService

**Ubicación:** `app/Services/DevelopmentPathService.php`

**Función:** Generar ruta de desarrollo automática basada en brechas

**Algoritmo (memories.md 5.3):**

```
1. Calcular brechas (usar GapAnalysisService)
2. Priorizar skills:
   - Críticas (is_critical=true en rol)
   - Alto impacto (gap > 2 niveles)
   - Rápidas (gap=1 y cursos cortos)
3. Para cada skill, generar step:
   - action_type: course | mentoring | project | certification | job_shadowing
   - Buscar resource (curso online, mentor interno, proyecto, etc)
   - Estimar duration_hours
4. Calcular total estimated_duration_months
5. Validación: max 3 rutas activas, duración ≤ 12 meses (warning)
```

**Método:**

```php
public function generate(People $people, Role $targetRole): DevelopmentPath
```

**Retorna:** DevelopmentPath creado con status='draft' y steps JSON

---

### 3. MatchingService

**Ubicación:** `app/Services/MatchingService.php`

**Función:** Rankear candidatos internos para una vacante

**Algoritmo:**

```
Para cada people en la organización:
  1. Calcular gap analysis vs job_opening.role
  2. Calcular match_percentage
  3. Estimar "time_to_productivity" en meses
     - base: 1 mes
     + gap_count * 0.5 (cada brecha suma 0.5 meses)
  4. Estimar "risk_factor" (0-100)
     - skills with gap>2: high risk
     - skills with gap=1-2: medium risk
     - skills perfect: no risk

Retornar array ordenado por match_percentage DESC:
  [
    {
      people_id, name, role,
      match_percentage, missing_skills[],
      time_to_productivity_months, risk_factor
    }
  ]
```

**Método:**

```php
public function rankCandidatesForOpening(JobOpening $jobOpening): Collection
```

**Uso:**

```php
$service = new MatchingService();
$candidates = $service->rankCandidatesForOpening($jobOpening);
// Collection de candidatos rankeados
```

---

## 🗂️ Estructura de Carpetas

```
app/Services/
├── GapAnalysisService.php      ← Cálculo de brechas
├── DevelopmentPathService.php  ← Generación de rutas
└── MatchingService.php         ← Ranking de candidatos
```

---

## 📝 Archivos a Crear

### GapAnalysisService.php

```php
<?php

namespace App\Services;

use App\Models\People;
use App\Models\Role;

class GapAnalysisService
{
    /**
     * Calcular brecha entre competencias de peoplea y rol
     */
    public function calculate(People $people, Role $role): array
    {
        $gaps = [];
        $skillsOk = 0;
        $totalSkills = $role->skills()->count();

        foreach ($role->skills as $roleSkill) {
            $peopleSkill = $people->skills()
                ->where('skill_id', $roleSkill->id)
                ->first();

            $currentLevel = $peopleSkill?->pivot->level ?? 0;
            $requiredLevel = $roleSkill->pivot->required_level;
            $gap = max(0, $requiredLevel - $currentLevel);

            if ($gap === 0) {
                $skillsOk++;
                $status = 'ok';
            } elseif ($gap <= 1) {
                $status = 'developing';
            } else {
                $status = 'critical';
            }

            $gaps[] = [
                'skill_id' => $roleSkill->id,
                'skill_name' => $roleSkill->name,
                'current_level' => $currentLevel,
                'required_level' => $requiredLevel,
                'gap' => $gap,
                'status' => $status,
            ];
        }

        $matchPercentage = $totalSkills > 0
            ? ($skillsOk / $totalSkills) * 100
            : 0;

        return [
            'match_percentage' => round($matchPercentage, 2),
            'gaps' => $gaps,
        ];
    }
}
```

---

## 🧪 Pruebas Manuales

Después de implementar, probar con Tinker:

```php
$people = People::first();
$role = Role::where('name', 'Senior Full Stack Developer')->first();

$gapService = new GapAnalysisService();
$analysis = $gapService->calculate($people, $role);
dd($analysis);
// Debe mostrar: match_percentage, gaps con skill details
```

---

## ✅ Checklist

- [ ] Crear `app/Services/GapAnalysisService.php`
    - [ ] Implementar método `calculate()`
    - [ ] Manejar peopleas sin skills
    - [ ] Calcular correctamente gap y status
- [ ] Crear `app/Services/DevelopmentPathService.php`
    - [ ] Implementar método `generate()`
    - [ ] Priorizar skills correctamente
    - [ ] Generar steps con action_type
    - [ ] Crear DevelopmentPath en BD

- [ ] Crear `app/Services/MatchingService.php`
    - [ ] Implementar método `rankCandidatesForOpening()`
    - [ ] Calcular time_to_productivity
    - [ ] Calcular risk_factor
    - [ ] Retornar Collection ordenada

- [ ] Pruebas manuales con Tinker
    - [ ] GapAnalysisService
    - [ ] DevelopmentPathService
    - [ ] MatchingService

---

## 📊 Integración con Controllers (Día 4)

Los services se usarán en:

```
GapAnalysisService    → GapAnalysisController@analyze
DevelopmentPathService → DevelopmentPathController@generate
MatchingService       → JobOpeningController@candidates
```

---

## ⏱️ Timeline Estimado

- GapAnalysisService: 1-1.5 horas
- DevelopmentPathService: 1.5-2 horas
- MatchingService: 1-1.5 horas
- Testing y fixing: 1-2 horas

**Total esperado:** 5-7 horas

---

**Estado:** 🔄 LISTO PARA EMPEZAR
