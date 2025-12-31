# Día 3: Guía de uso de comandos (Services)

Fecha: 2025-12-27  
Objetivo: Ejecutar y validar los servicios core vía Artisan

---

## Prerrequisitos

- Base de datos preparada y poblada con datos de demo.
- PHP/Laravel disponibles en el proyecto.

Inicializa entorno limpio y datos demo:

```bash
cd src
php artisan migrate:fresh --force
php artisan db:seed --class=DemoSeeder
```

---

## Comando 1: gap:analyze

Analiza la brecha de competencias entre una peoplea y un rol.

- Ubicación: [routes/console.php](routes/console.php)
- Servicio: [app/Services/GapAnalysisService.php](app/Services/GapAnalysisService.php)
- Firma: `gap:analyze {people_id} {role_name}`

Parámetros:

- `people_id`: ID de la peoplea (entero).
- `role_name`: Nombre exacto del rol (usar comillas si tiene espacios).

Ejemplos:

```bash
php artisan gap:analyze 1 "Senior Full Stack Developer"
php artisan gap:analyze 5 "Frontend Developer"
```

Salida esperada (ejemplo):

```
Match: 15.38% (not-recommended)
Skills OK: 2 / 13
- PHP: actual=0, requerido=5, gap=5, status=critical (critical)
- Laravel: actual=1, requerido=5, gap=4, status=critical (critical)
...
```

---

## Comando 2: devpath:generate

Genera una ruta de desarrollo desde las brechas para un rol objetivo.

- Ubicación: [routes/console.php](routes/console.php)
- Servicio: [app/Services/DevelopmentPathService.php](app/Services/DevelopmentPathService.php)
- Firma: `devpath:generate {people_id} {role_name}`

Parámetros:

- `people_id`: ID de la peoplea (entero).
- `role_name`: Nombre exacto del rol.

Ejemplos:

```bash
php artisan devpath:generate 1 "Senior Full Stack Developer"
```

Salida esperada (ejemplo):

```
DevelopmentPath generado: #2 (status=draft)
Duración estimada (meses): 4
1) [mentoring] Mentoría avanzada en PHP - 60h (skill: PHP)
2) [course] Curso intensivo de Docker - 60h (skill: Docker)
...
```

Notas:

- Crea un registro en `development_paths` con `steps` (JSON) y `status = draft`.
- La duración estimada se calcula con heurística (160h ≈ 1 mes).

---

## Comando 3: candidates:rank

Lista candidatos internos rankeados para una vacante.

- Ubicación: [routes/console.php](routes/console.php)
- Servicio: [app/Services/MatchingService.php](app/Services/MatchingService.php)
- Firma: `candidates:rank {job_opening_id}`

Parámetros:

- `job_opening_id`: ID de la vacante (entero).

Ejemplo:

```bash
php artisan candidates:rank 1
```

Salida esperada (ejemplo):

```
Candidatos para: Senior Backend Developer (rol: Senior Full Stack Developer)
1) Ricardo Ramírez  — match=46.15%, time=4.5 meses, risk=100, missing=[PHP, Laravel, ...]
2) Claudia García  — match=23.08%, time=6.0 meses, risk=100, missing=[PHP, TypeScript, ...]
...
```

Notas:

- Ordena por `match_percentage` DESC.
- Muestra métricas: `time_to_productivity_months`, `risk_factor`, y `missing_skills`.

---

## Guía rápida (copy/paste)

```bash
cd src
php artisan migrate:fresh --force
php artisan db:seed --class=DemoSeeder
php artisan gap:analyze 1 "Senior Full Stack Developer"
php artisan devpath:generate 1 "Senior Full Stack Developer"
php artisan candidates:rank 1
```

---

## ¿Dónde encuentro IDs y nombres?

- Peopleas: pobladas por `DemoSeeder` (IDs típicamente del 1 al 20).
- Vacantes: `DemoSeeder` crea 5 vacantes (IDs típicamente 1 a 5).
- Roles: usa el `name` exacto del rol; en el demo existen (ejemplos):
    - "Senior Full Stack Developer"
    - "Frontend Developer"
    - "Product Manager"

---

## Troubleshooting

- Error `UNIQUE constraint failed: organizations.subdomain` al seedear:
    - Solución: `php artisan migrate:fresh --force` y luego `php artisan db:seed --class=DemoSeeder`.
- `Rol no encontrado por nombre`: verifica el nombre exacto y usa comillas.
- `Peoplea/Vacante no encontrada`: confirma el ID tras un `migrate:fresh` (IDs se reinician).
- Si usas Windows/WSL, asegúrate de estar dentro de `src` al ejecutar comandos.

---

## Archivos relacionados

- Services:
    - [app/Services/GapAnalysisService.php](app/Services/GapAnalysisService.php)
    - [app/Services/DevelopmentPathService.php](app/Services/DevelopmentPathService.php)
    - [app/Services/MatchingService.php](app/Services/MatchingService.php)
- Comandos Artisan: [routes/console.php](routes/console.php)
- Seeder de demo: [database/seeders/DemoSeeder.php](database/seeders/DemoSeeder.php)

---

## Próximo paso

- Integrar estos servicios en controllers (Día 4): `GapAnalysisController`, `DevelopmentPathController`, `JobOpeningController` (candidates).
