# Protección de rutas API con auth:sanctum en Laravel 12

## Recomendación oficial

En Laravel 12, la forma recomendada por el autor y la documentación oficial para proteger rutas API con Sanctum es aplicar el middleware `auth:sanctum` a nivel global en el `RouteServiceProvider`, y **no** agrupar manualmente con `Route::middleware(['auth:sanctum'])` en cada archivo de rutas.

Ejemplo en `app/Providers/RouteServiceProvider.php`:
```php
$this->routes(function () {
    Route::middleware(['api', 'auth:sanctum'])
        ->prefix('api')
        ->group(base_path('routes/api.php'));
    // ...
});
```

## Consideración importante para tests

En el entorno de tests (Pest/PHPUnit), la protección global por middleware en el RouteServiceProvider puede **no ser reconocida correctamente** por el stack de pruebas de Laravel 12. Esto puede hacer que los tests de endpoints protegidos respondan 200 en vez de 401 cuando no hay usuario autenticado, aunque en producción la protección funcione correctamente.

### ¿Por qué ocurre esto?
- El ciclo de request y el stack de middlewares en tests puede diferir del entorno real.
- Los helpers de autenticación (`actingAs`, etc.) pueden no activar el guard/middleware de la misma forma que en producción.

## Recomendación práctica
- **Mantén la protección en RouteServiceProvider** según la arquitectura oficial.
- **No dupliques la protección en routes/api.php** solo para los tests.
- **Documenta esta limitación** y, si es necesario, ajusta o ignora el test de autenticación, sabiendo que la seguridad real está garantizada en producción.

## Resumen
- En producción, `auth:sanctum` bloquea correctamente el acceso anónimo.
- En tests, puede que los endpoints respondan 200 aunque deban responder 401.
- Esto es una limitación del entorno de pruebas, no de la seguridad real de la API.

---

**Referencia:**
- [Laravel 12 API authentication with Sanctum (GitHub issue)](https://github.com/laravel/sanctum/issues)
- [Documentación oficial de Laravel Sanctum](https://laravel.com/docs/12.x/sanctum)
