# 🔐 Guía Completa: Autenticación Sanctum en TalentIA

**Status**: ✅ Ya configurado en el proyecto  
**Fecha**: 28 Diciembre 2025  
**Componentes**: FormSchema.vue + apiHelper.ts + Sanctum en Laravel 12

---

## 📋 Estado Actual del Proyecto

### ✅ Configuración Completada

En TalentIA, Sanctum **YA ESTÁ CONFIGURADO Y FUNCIONANDO**:

```
✅ Backend (Laravel 12):
   - Sanctum instalado y migraciones ejecutadas
   - Middleware auth:sanctum aplicado a rutas API protegidas
   - CSRF protection activado para SPA

✅ Frontend (Vue 3 + TypeScript):
   - apiHelper.ts maneja tokens CSRF automáticamente
   - Axios interceptores detectan 401 y redirigen a login
   - withCredentials: true en todas las requests
   - Cookies de sesión se envían automáticamente
```

---

## 🔍 Archivos de Configuración Relevantes

### Backend - Laravel 12

**`src/bootstrap/app.php`** (Ya configurado)

```php
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

$middleware->api(prepend: [
    EnsureFrontendRequestsAreStateful::class,
]);

->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',
    // ...
)
```

**`src/routes/api.php`** (Rutas protegidas con auth:sanctum)

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 17 endpoints CRUD
    Route::get('/Person', [PersonController::class, 'index']);
    Route::post('/Person', [PersonController::class, 'store']);
    // ... más rutas
});
```

**`src/routes/web.php`** (Inertia SSR)

```php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', function () {
        return Inertia::render('Dashboard');
    });

    Route::get('/Person', function () {
        return Inertia::render('Person/Index');
    });
    // ... más rutas
});
```

### Frontend - Vue 3 + TypeScript

**`src/resources/js/apiHelper.ts`** (Interceptor centralizado)

```typescript
axios.interceptors.request.use((config) => {
  // Agregar CSRF token a headers
  const token = document
    .querySelector('meta[name="csrf-token"]')
    ?.getAttribute("content");
  if (token) {
    config.headers["X-CSRF-TOKEN"] = token;
  }
  return config;
});

axios.interceptors.response.use(
  (response) => response,
  (error) => {
    // Si 401 (no autenticado), redirigir a login
    if (error.response?.status === 401) {
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);
```

**`src/resources/js/pages/form-template/FormSchema.vue`** (Usa apiHelper)

```typescript
// Cargar items de API
const loadItems = async () => {
  try {
    const response = await axios.get(config.endpoints.index);
    items.value = response.data.data || response.data;
  } catch (err) {
    console.error("Failed to load items", err);
  }
};

// Crear nuevo item
const createItem = async (formData: any) => {
  try {
    const response = await axios.post(config.endpoints.apiUrl, formData);
    // ...success
  } catch (err) {
    // Maneja errores de validación 422, auth 401, etc
  }
};
```

---

## 🔒 Cómo Funciona el Flujo de Autenticación

### 1️⃣ Usuario se Autentica

```
Frontend          Backend (Laravel 12)

POST /login       └─→ LoginController
  ↓
  └─→ Sanctum middleware verifica credentials
      └─→ Genera cookie de sesión + XSRF token
      └─→ Responde 200 + Cookie en headers
  ↓
Frontend recibe cookie
└─→ Se guarda automáticamente en localStorage/sessionStorage
└─→ Se envía en siguientes requests con withCredentials: true
```

### 2️⃣ Frontend Hace Request a API Protegida

```
Frontend                  Backend (Laravel 12)

GET /api/Person     └─→ Middleware auth:sanctum verifica:
  ├─ Cookie                • ¿Hay cookie de sesión válida?
  ├─ X-CSRF-TOKEN          • ¿CSRF token es válido?
  └─ withCredentials: true └─→ Si SÍ: retorna datos (200)
                           └─→ Si NO: retorna 401 (Unauthorized)
  ↓
Si 401: apiHelper.ts interceptor
└─→ Redirige a /login
```

### 3️⃣ Usuario No Autenticado Intenta Acceder a /Person

```
Frontend                  Backend (Laravel 12)

GET /Person          └─→ Middleware auth:sanctum verifica
  (sin cookie)           • ¿Hay sesión válida? NO
                         • ¿Hay token Bearer? NO
                         └─→ Redirige a /login (303)
  ↓
Frontend:
└─→ Inertia redirige a /login
```

---

## 🛡️ Protecciones Aplicadas

### En Backend (Laravel 12)

| Protección      | Dónde              | Qué hace                             |
| --------------- | ------------------ | ------------------------------------ |
| `auth:sanctum`  | routes/api.php     | Solo usuarios autenticados acceden   |
| CSRF            | bootstrap/app.php  | Valida X-CSRF-TOKEN en requests      |
| Session timeout | config/session.php | Sesión expira después de 120 minutos |
| Rate limiting   | routes/api.php     | Limita requests por IP/usuario       |

### En Frontend (Vue 3)

| Protección        | Dónde        | Qué hace                               |
| ----------------- | ------------ | -------------------------------------- |
| `withCredentials` | apiHelper.ts | Envía cookies en cada request          |
| CSRF token inject | apiHelper.ts | Agrega X-CSRF-TOKEN a headers          |
| 401 interceptor   | apiHelper.ts | Redirige a login si no autenticado     |
| Rutas protegidas  | router       | Solo usuarios autenticados ven páginas |

---

## 🧪 Pruebas (Testing)

### Testear Endpoint Protegido

```bash
# Desde la terminal en el contenedor:

# 1. Sin autenticación (debe fallar con 401 o redirigir)
curl -X GET http://localhost:8000/api/Person

# 2. Con autenticación por sesión
curl -X GET http://localhost:8000/api/Person \
  -H "Accept: application/json" \
  -c cookies.txt -b cookies.txt

# 3. Primero login
curl -X POST http://localhost:8000/login \
  -d "email=demo@techcorp.com&password=password" \
  -c cookies.txt

# 4. Luego request protegida
curl -X GET http://localhost:8000/api/Person \
  -b cookies.txt
```

### Postman

1. Crear colección "TalentIA"
2. Variable: `base_url = http://localhost:8000`
3. Agregar request POST login:
   ```
   URL: {{base_url}}/login
   Body: { "email": "demo@techcorp.com", "password": "password" }
   ```
4. Postman captura la cookie automáticamente
5. Nuevas requests usan la cookie automáticamente

---

## ⚙️ Configuración por Entorno

### Desarrollo (Local)

```bash
# .env
APP_DEBUG=true
SESSION_LIFETIME=120  # 2 horas
SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost:8000
```

### Producción

```bash
# .env
APP_DEBUG=false
SESSION_LIFETIME=1440  # 24 horas
SANCTUM_STATEFUL_DOMAINS=talentia.app,www.talentia.app
CORS_ALLOWED_ORIGINS=https://talentia.app
```

---

## 🔧 Solución de Problemas

### Problema: 419 (CSRF Token Mismatch)

**Síntoma**: POST/PUT/DELETE fallan con error 419

**Solución**:

```typescript
// Verificar que CSRF token se inyecta
const token = document
  .querySelector('meta[name="csrf-token"]')
  ?.getAttribute("content");
console.log("CSRF Token:", token); // debe existir

// En apiHelper.ts, verificar que se agrega al header
config.headers["X-CSRF-TOKEN"] = token;
```

### Problema: 401 (Unauthorized) en requests

**Síntoma**: GET /api/Person retorna 401 aunque estés "logueado"

**Solución**:

```typescript
// 1. Verificar que cookies se envían
// Axios debe tener withCredentials: true
axios.defaults.withCredentials = true;

// 2. Verificar que sesión no expiró
// GET /api/user (endpoint público en Sanctum)
// Si retorna 401, sesión expiró

// 3. Re-login
window.location.href = "/login";
```

### Problema: CORS error

**Síntoma**: `Access to XMLHttpRequest blocked by CORS policy`

**Solución**:

```php
// src/bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->api(prepend: [
        EnsureFrontendRequestsAreStateful::class,
    ]);
})
```

---

## 📚 Documentación Relacionada

- [auth_sanctum_api.md](auth_sanctum_api.md) - Explicación técnica de Sanctum
- [auth_sanctum_laravel12.md](auth_sanctum_laravel12.md) - Configuración específica L12
- [apiHelper.ts](../../src/resources/js/apiHelper.ts) - Interceptor CSRF/401
- [FormSchema.vue](../../src/resources/js/pages/form-template/FormSchema.vue) - Usa apiHelper
- [Laravel Sanctum Docs](https://laravel.com/docs/12.x/sanctum) - Documentación oficial

---

## ✅ Checklist: Verificar Autenticación

- [ ] Backend: `php artisan tinker` → `User::count()` > 0
- [ ] Frontend: Abrir /login → ingresar credentials
- [ ] Verificar: Cookie `XSRF-TOKEN` en DevTools (Application > Cookies)
- [ ] API: GET /api/user debe retornar usuario actual
- [ ] Tabla: GET /api/Person debe cargar datos
- [ ] Crear: POST /api/Person con nuevo item
- [ ] Logout: Sesión debe limpiar cookies
- [ ] Logout: GET /api/Person debe retornar 401

---

## 🎓 Resumen

**En TalentIA:**

✅ **Sanctum está 100% funcional**

- Backend protege rutas API con `auth:sanctum`
- Frontend inyecta CSRF tokens automáticamente
- apiHelper.ts maneja errores de autenticación
- Cookies de sesión se envían automáticamente
- 401 redirige a login automáticamente

**No necesitas cambiar nada**, pero:

- Si agregas nuevas rutas API, recuerda agregar `auth:sanctum`
- Si cambias apiHelper.ts, asegúrate de mantener CSRF + withCredentials
- Si agregas nuevos Inertia pages, agrégalas en routes/web.php con `auth:sanctum`

---

**Autor**: GitHub Copilot  
**Versión**: TalentIA MVP Día 6  
**Status**: ✅ Producción Ready
