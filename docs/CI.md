# CI & Testing - Flujo y cómo ejecutar localmente

Este documento describe el pipeline CI que usamos y cómo ejecutar las pruebas localmente.

Resumen del pipeline (GitHub Actions):

- Job `php-tests`: instala dependencias PHP, prepara `.env` y `database/database.sqlite`, corre migraciones y ejecuta `Pest` (suit `Feature`).
- Job `frontend-unit`: instala Node, `npm ci` en `/src` y ejecuta `Vitest` (tests unitarios en `resources/js`).
- Job `e2e`: instala Playwright, levanta servidor backend y frontend y ejecuta tests E2E. Se ejecuta en `main`, en `pull_request` y por `workflow_dispatch`.
- Artifacts: reportes HTML/JUnit y capturas Playwright se suben como artifacts cuando el job E2E termina.

Rutas relevantes:

- Workflow: `.github/workflows/ci.yml`
- Playwright tests: `src/tests/e2e/*.ts` y `tests/e2e/*.ts`
- Frontend unit tests: `src/resources/js/**/__tests__` y `src/resources/js/**/?(*.)spec.ts`
- Backend tests: `src/tests/Feature` (Pest)

Variables / Secrets recomendadas en GitHub:

- `APP_KEY` (opcional si se genera en CI)
- `DB_CONNECTION` / `DB_*` si prefieres usar un servicio de DB real en CI
- `SANCTUM_*` si tus E2E requieren autenticar con Sanctum
- `PLAYWRIGHT_BROWSERS` (si quieres controlar la instalación de navegadores)

Comandos locales (desde la raíz del repo):

1. Backend (Laravel / Pest)

```bash
cd src
# Instala deps PHP
composer install

# Prepara .env y DB sqlite rápida
cp .env.example .env
php -r "file_exists('database/database.sqlite') || mkdir('database') && touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate --force

# Ejecutar Pest (estándar del repo)
vendor/bin/pest
```

2. Frontend unit tests (Vitest)

```bash
cd src
npm ci
npx vitest run resources/js
```

3. Playwright E2E (local)

```bash
cd src
npm ci
npx playwright install --with-deps

# Levantar backend y frontend (en dos terminales o en background)
php artisan serve --host=127.0.0.1 --port=8000
npm run dev

# Ejecutar E2E
npx playwright test tests/e2e --headed
```

Notas y recomendaciones:

- En CI usamos SQLite por velocidad y simplicidad; si tus tests requieren funcionalidades específicas de Postgres/MySQL, configura servicios en el workflow y añade las variables de entorno.
- Playwright puede grabar videos/capturas; usa las rutas `src/playwright-report` y `src/test-results` para revisar artifacts subidos desde CI.
- Para TDD local rápido: ejecuta `npx vitest --watch` y `vendor/bin/pest --watch` en terminales separados.

Smoke tests Playwright:

- Marca las pruebas rápidas que deben ejecutarse en PRs con la etiqueta `@smoke` en la descripción del test. Ejemplo:

```ts
test("@smoke smoke: capability expands", async ({ page }) => {
  // ...
});
```

- El job E2E en PRs ejecuta `npx playwright test --grep @smoke` (solo las pruebas con esa etiqueta). En `main`/`dispatch` se ejecuta la suite completa.

Husky pre-push:

- El repositorio incluye un hook Husky (`.husky/pre-push`) que ejecuta una verificación rápida de tests antes de permitir un push.
- Instálalo localmente desde `src` con `npm run prepare`.
- El hook ejecuta `composer test` (Pest) y `npm run test:unit` (Vitest); si alguna falla, el push se cancela.

Soporte y mantenimiento:

- Si quieres que Playwright corra sólo en `main` (evitar coste en PRs), modifica `.github/workflows/ci.yml` en el job `e2e`.
- Para fallos intermitentes en E2E, añade reintentos (`retry`) o ejecuta E2E en nightly.

Contacto: abre un issue en el repo o menciona al equipo en PRs para ajustes de CI.
