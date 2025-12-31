# Cheat Sheet - Comandos Útiles TalentIA

**Referencia rápida para desarrollo**

---

## 🚀 Iniciar el Proyecto

```bash
# 1. Navegar a la carpeta
cd /workspaces/talentia/src

# 2. Instalar dependencias (si es primera vez)
composer install
npm install

# 3. Preparar BD
php artisan migrate:fresh --seed

# 4. Iniciar servidor API
php artisan serve --port=8000

# 5. En otra terminal: Iniciar Vite (frontend)
npm run dev

# 6. Abrir en navegador
http://localhost:5173
```

---

## 🔍 Ver Datos

```bash
# Contar registros
php artisan tinker
>>> App\Models\People::count()          # 20
>>> App\Models\Role::count()            # 8
>>> App\Models\Skill::count()           # 30
>>> App\Models\JobOpening::count()      # 5
>>> App\Models\Application::count()     # 10

# Ver una peoplea con skills
>>> $people = App\Models\People::first();
>>> $people->skills->pluck('name');

# Ver un rol con skills requeridas
>>> $role = App\Models\Role::first();
>>> $role->skills()->pluck('name');
```

---

## 📡 API Testing

### Artisan Commands

```bash
# Análisis de brecha
php artisan gap:analyze 1 "Backend Developer"
php artisan gap:analyze 2 "Business Analyst"

# Generar ruta de desarrollo
php artisan devpath:generate 1 "Senior Developer"

# Ranking de candidatos para vacante
php artisan candidates:rank 1
php artisan candidates:rank 2
```

### cURL Requests

```bash
# GET - Lista de peopleas
curl http://localhost:8000/api/People

# GET - Detalle de peoplea
curl http://localhost:8000/api/People/1

# POST - Analizar brecha
curl -X POST http://localhost:8000/api/gap-analysis \
  -H "Content-Type: application/json" \
  -d '{
    "people_id": 1,
    "role_name": "Backend Developer"
  }'

# POST - Crear postulación
curl -X POST http://localhost:8000/api/applications \
  -H "Content-Type: application/json" \
  -d '{
    "people_id": 1,
    "job_opening_id": 1,
    "message": "Me interesa"
  }'

# PATCH - Actualizar estado de postulación
curl -X PATCH http://localhost:8000/api/applications/1 \
  -H "Content-Type: application/json" \
  -d '{"status": "accepted"}'

# GET - Marketplace de oportunidades
curl http://localhost:8000/api/People/1/marketplace

# GET - Candidatos para vacante
curl http://localhost:8000/api/job-openings/1/candidates
```

### Postman

```bash
# Importar colección
# 1. Abrir Postman
# 2. Import → File → docs/TalentIA_API_Postman.json
# 3. Cambiar base_url si necesario
# 4. Ejecutar requests
```

---

## 🗄️ Base de Datos

```bash
# Resetear BD con datos frescos
php artisan migrate:fresh --seed

# Ver estructura de tablas
php artisan migrate:status

# Rollback última migración
php artisan migrate:rollback

# Ver migraciones pendientes
php artisan migrate

# Abrir tinker (REPL de PHP)
php artisan tinker
```

---

## 🧪 Testing

```bash
# Ejecutar todos los tests
php artisan test

# Ejecutar solo tests de features
php artisan test --filter Feature

# Ejecutar un test específico
php artisan test tests/Feature/GapAnalysisServiceTest.php

# Con coverage
php artisan test --coverage
```

---

## 🛠️ Desarrollo

```bash
# Limpiar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver rutas registradas
php artisan route:list
php artisan route:list | grep api

# Generar un nuevo migration
php artisan make:migration create_table_name

# Generar controller
php artisan make:controller Api/ControllerName

# Generar modelo
php artisan make:model ModelName -m

# Generar comando Artisan
php artisan make:command CommandName

# Generar test
php artisan make:test FeatureTestName --feature
```

---

## 📁 Estructura Importante

```
src/
├── app/
│   ├── Http/Controllers/Api/       ← Controllers REST (8)
│   ├── Models/                     ← Modelos Eloquent (7)
│   ├── Services/                   ← Servicios de negocio (3)
│   └── Console/Commands/           ← Artisan commands (3)
├── database/
│   ├── migrations/                 ← Migraciones (10)
│   └── seeders/                    ← DemoSeeder
├── routes/
│   └── web.php                     ← Rutas API (17 endpoints)
├── tests/
│   └── Feature/                    ← Tests (2 Pest)
├── docs/                           ← Documentación
│   ├── dia1_migraciones_*.md
│   ├── dia2_seeders_*.md
│   ├── dia3_servicios_*.md
│   ├── dia3_comandos_*.md
│   ├── dia5_api_endpoints.md
│   ├── TalentIA_API_Postman.json
│   ├── STATUS_EJECUTIVO_DIA5.md
│   ├── DIA6_GUIA_INICIO_FRONTEND.md
│   └── CHECKLIST_MVP_COMPLETION.md
└── resources/js/                   ← Frontend (próximo)
    ├── pages/                      ← Páginas (por crear)
    ├── components/                 ← Componentes
    └── composables/                ← Hooks (useApi)
```

---

## 🔗 URLs Importantes

```
API Base:       http://localhost:8000/api
Frontend:       http://localhost:5173
Admin Docs:     /docs (si está configurado)
Routes List:    php artisan route:list
```

---

## 📝 Endpoints Quick Ref

| Método | Endpoint                        | Descripción             |
| ------ | ------------------------------- | ----------------------- |
| POST   | /api/gap-analysis               | Brecha de competencias  |
| POST   | /api/development-paths/generate | Generar ruta desarrollo |
| GET    | /api/People                     | Lista peopleas          |
| GET    | /api/People/{id}                | Detalle peoplea         |
| GET    | /api/roles                      | Lista roles             |
| GET    | /api/skills                     | Lista skills            |
| GET    | /api/job-openings               | Lista vacantes          |
| POST   | /api/applications               | Crear postulación       |
| PATCH  | /api/applications/{id}          | Actualizar postulación  |
| GET    | /api/People/{id}/marketplace    | Oportunidades internas  |
| GET    | /api/dashboard/metrics          | Métricas dashboard      |

---

## ⚡ Tips Productividad

1. **Terminal Múltiples:**
    - Terminal 1: `php artisan serve`
    - Terminal 2: `npm run dev`
    - Terminal 3: Para comandos

2. **Watch/Rebuild:**

    ```bash
    npm run dev  # Vite en modo watch
    ```

3. **Debugging con Tinker:**

    ```bash
    php artisan tinker
    >>> \App\Services\GapAnalysisService::class
    >>> (new \App\Services\GapAnalysisService())->calculate($people, $role)
    ```

4. **Postman Tips:**
    - Usar variables de entorno ({{base_url}})
    - Pre-request scripts para auth (si es necesario)
    - Collection runner para tests

5. **IDE Helper (Laravel):**
    ```bash
    composer require --dev barryvdh/laravel-ide-helper
    php artisan ide-helper:generate
    ```

---

## 🆘 Troubleshooting

**Error: CSRF token mismatch**

- Normal en POST sin autenticación
- Agregar header: `'X-Requested-With': 'XMLHttpRequest'`

**Error: Port 8000 ya en uso**

```bash
php artisan serve --port=8001
```

**BD sin datos**

```bash
php artisan migrate:fresh --seed
```

**Cache corrupto**

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

**Node modules problemas**

```bash
rm -rf node_modules package-lock.json
npm install
```

---

## 📞 Documentación de Referencia

| Archivo                                 | Propósito                       |
| --------------------------------------- | ------------------------------- |
| `docs/STATUS_EJECUTIVO_DIA5.md`         | Resumen actual y próximos pasos |
| `docs/dia5_api_endpoints.md`            | Especificación completa de API  |
| `docs/DIA6_GUIA_INICIO_FRONTEND.md`     | Cómo empezar frontend           |
| `docs/TalentIA_API_Postman.json`        | Colección Postman para testing  |
| `docs/CHECKLIST_MVP_COMPLETION.md`      | Verificación de completitud     |
| `docs/dia3_servicios_logica_negocio.md` | Especificación de servicios     |

---

**Última actualización:** 2025-12-31  
**Próxima:** Día 6 Frontend
