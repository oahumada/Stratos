# Autenticación de Rutas API con Sanctum en Laravel 12

## ¿Qué es `auth:sanctum`?

`auth:sanctum` es un middleware de Laravel que protege rutas para que solo usuarios autenticados puedan acceder a ellas, utilizando el sistema de autenticación de Laravel Sanctum. Es la forma recomendada para proteger APIs en aplicaciones modernas (SPA, móviles, etc.).

---

## ¿Cómo funciona Sanctum?

Sanctum permite dos modos de autenticación para APIs:

### 1. Autenticación por Token Personal (Bearer Token)
- El usuario obtiene un token personal (por ejemplo, usando un endpoint como `/login` o `/api/token`).
- El frontend debe enviar ese token en el header de cada request:
  ```
  Authorization: Bearer TU_TOKEN
  ```
- Es ideal para apps móviles, clientes externos o cuando no hay sesión/cookies compartidas.

### 2. Autenticación por Sesión (SPA)
- El usuario inicia sesión (por ejemplo, con `/login` usando email y password).
- Laravel responde con cookies de sesión y XSRF-TOKEN.
- El frontend (SPA) debe enviar estas cookies en cada request (con `withCredentials: true`).
- Es ideal para aplicaciones Vue, React, etc. que comparten dominio con el backend.

---

## ¿Cómo proteger rutas con Sanctum?

En `routes/api.php`:

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/catalogs', [CatalogsController::class, 'getCatalogs']);
    // otras rutas protegidas...
});
```

Esto significa que solo los usuarios autenticados podrán acceder a esas rutas. Si no lo están, Laravel devolverá un error 401 (Unauthorized).

---

## Requisitos de configuración en Laravel 12

1. **Instalar Sanctum**
   ```bash
   composer require laravel/sanctum
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   php artisan migrate
   ```

2. **Configurar el guard en `config/auth.php`**
   ```php
   'guards' => [
       'web' => [
           'driver' => 'session',
           'provider' => 'users',
       ],
       'sanctum' => [
           'driver' => 'sanctum',
           'provider' => 'users',
       ],
   ],
   ```

3. **Agregar el middleware en `bootstrap/app.php`**
   ```php
   use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

   $middleware->api(prepend: [
       EnsureFrontendRequestsAreStateful::class,
   ]);
   ```

4. **Registrar el archivo de rutas API en `bootstrap/app.php`**
   ```php
   ->withRouting(
       web: __DIR__.'/../routes/web.php',
       api: __DIR__.'/../routes/api.php',
       // ...
   )
   ```

---

## ¿Cómo autenticarse desde el frontend?

### a) Por sesión/cookie (SPA)
- El frontend hace un POST a `/login` con email y password.
- Laravel responde con cookies de sesión y XSRF-TOKEN.
- El frontend debe enviar las cookies en cada request (con `withCredentials: true`).
- Las rutas protegidas con `auth:sanctum` funcionarán mientras la sesión esté activa.

### b) Por token personal
- El usuario obtiene un token (por ejemplo, llamando a un endpoint que use `$user->createToken('nombre')->plainTextToken`).
- El frontend guarda el token y lo envía en cada request en el header `Authorization`.

---

## ¿Qué pasa si no estás autenticado?

- Laravel responderá con un error 401 (Unauthorized).
- El frontend debe redirigir al usuario a login o mostrar un mensaje de autenticación requerida.

---

## Resumen visual

| Middleware en ruta      | ¿Qué hace?                                 | ¿Cómo autenticarse?         |
|------------------------|---------------------------------------------|-----------------------------|
| `auth:sanctum`         | Solo usuarios autenticados acceden          | Cookie de sesión o token    |
| `auth:api`             | Solo usuarios autenticados por Passport/API | Bearer token                |
| `api`                  | Aplica middlewares de grupo, NO autentica   | -                           |

---

## Recomendaciones

- Usa `auth:sanctum` para proteger APIs en proyectos nuevos con SPA o apps móviles.
- Si tu frontend es Vue/React y comparte dominio, usa autenticación por sesión.
- Si tienes clientes externos, usa tokens Bearer.
- Siempre prueba `/api/user` para verificar si la autenticación funciona.
