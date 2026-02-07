# Guía rápida: Ejecutar pruebas E2E con Playwright (local / CI)

Requisitos
- Node 18+, npm
- PHP 8.2+, Composer
- Base app configurada (migrations/seeders)

Local (rápido)

1. Desde la raíz del repo:

```bash
cd src
composer install
npm ci
npm run build
php artisan migrate:fresh --seed
npx playwright install --with-deps
npx playwright test --project=chromium --reporter=list
```

2. Si hay fallos, abre el informe localmente:

```bash
npx playwright show-report
```

CI (GitHub Actions)

- El workflow `/.github/workflows/e2e.yml` realiza:
  - Instala dependencias PHP/Node
  - Crea `sqlite` y ejecuta `migrate:fresh --seed`
  - Compila frontend (`npm run build`)
  - Arranca servidor `php artisan serve` en `127.0.0.1:8000`
  - Ejecuta `npx playwright test`
  - Sube artefactos: `playwright-report`, capturas y videos (para inspección)

Consejos
- Asegura `BASE_URL` si usas otro puerto.
- Variables E2E: `E2E_ADMIN_EMAIL` / `E2E_ADMIN_PASSWORD` (si no setea valores por defecto: `admin@example.com`/`password`).
- Para pruebas confiables en CI: usa `LLM_PROVIDER=mock` o configura `LLM_*` mocks.

Depuración rápida
- Si falla el wizard, revisa `src/tests/fixtures/llm/mock_generation_response.json` y la ruta de intercepts en `src/tests/e2e/helpers/intercepts.ts`.

Contacto
- Para cambios en el flujo E2E, actualiza `docs/GUIA_E2E.md` y `openmemory.md`.
