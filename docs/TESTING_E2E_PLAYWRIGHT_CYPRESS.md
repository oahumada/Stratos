**E2E Testing — Playwright (CI) & Cypress (local acceptance)**

Resumen rápido

- Playwright: integrado en CI (workflows/.github/workflows/playwright-e2e.yml). Usa `E2E_BASE_URL` + credenciales para ejecutar pruebas contra un entorno disponible.
- Cypress: instalado para pruebas de aceptación locales (no forma parte del flujo CI por decisión del equipo). Usar `src/.env.cypress.example` para valores de ejemplo.

Preparación de entorno

- No guardes credenciales en el repositorio. Copia los ejemplos y gestiona secretos en tu entorno o en GitHub Secrets.

Archivos de ejemplo

- `src/.env.playwright.example` — variables para Playwright: `E2E_ADMIN_EMAIL`, `E2E_ADMIN_PASSWORD`.
- `src/.env.cypress.example` — variables para Cypress: `CYPRESS_BASE_URL`, `E2E_ADMIN_EMAIL`, `E2E_ADMIN_PASSWORD`.

Variables/Secrets para CI

- Añade estos Secrets en GitHub repo → Settings → Secrets:
  - `E2E_BASE_URL` — URL público accesible por GitHub Actions (ej: https://staging.example.com)
  - `E2E_ADMIN_EMAIL` — (opcional) email de usuario de pruebas
  - `E2E_ADMIN_PASSWORD` — (opcional) password del usuario de pruebas

Playwright — ejecución local

1. Desde la raíz del frontend:

```bash
cd src
cp .env.playwright.example .env.playwright   # opcional, para referencia local
npm ci             # o npm install si necesitas actualizar package-lock
npx playwright install --with-deps
```

2. Ejecutar el test smoke (usa las vars de entorno si las exportas):

```bash
# exporta variables (ejemplo)
export E2E_ADMIN_EMAIL=admin@example.com
export E2E_ADMIN_PASSWORD=password
export BASE_URL=http://localhost:8000

# ejecutar
npx playwright test tests/e2e/planning.spec.ts
```

3. Notas:

- El test `src/tests/e2e/planning.spec.ts` intentará autenticarse programáticamente usando `E2E_ADMIN_EMAIL`/`E2E_ADMIN_PASSWORD`.
- En CI la workflow `.github/workflows/playwright-e2e.yml` usa el secret `E2E_BASE_URL` para dirigir las pruebas.
- Playwright guarda artefactos en `src/test-results/` cuando falla; revisa screenshots y video.

Playwright — ejecución en CI

- Sube `E2E_BASE_URL` (y opcionalmente credenciales) a los Secrets del repo. El workflow ejecutará `npx playwright test` y publicará el reporte.

Cypress — ejecución local (aceptación, fuera de CI)

1. Desde `src` instala dependencias (si no lo hiciste):

```bash
cd src
npm install
npx cypress verify
```

2. Ejecutar pruebas de aceptación programáticas (headless) o abrir UI:

```bash
# headless (CI-like local run)
npx cypress run --env E2E_ADMIN_EMAIL=admin@example.com,E2E_ADMIN_PASSWORD=password

# abrir la UI interactiva
npx cypress open
```

3. Headless en Linux sin X server

- Si estás en un servidor sin DISPLAY, usa Xvfb:

```bash
sudo apt-get install -y xvfb libgtk-3-0 libx11-xcb1 libxcomposite1 libxdamage1 libxrandr2 libgbm1
xvfb-run -s "-screen 0 1280x720x24" npx cypress run
```

Seguridad y buenas prácticas

- No añadas `.env.playwright` o `.env.cypress` con credenciales al repo. Usa GitHub Secrets para CI.
- Mantén las credenciales de prueba separadas de cuentas reales.
- Si necesitas estabilidad en CI, pre-seed datos (fixtures) en el entorno `E2E_BASE_URL` o usa cuentas dedicadas.

Dónde están los archivos añadidos

- Playwright smoke test: `src/tests/e2e/planning.spec.ts`
- Playwright config: `src/playwright.config.ts`
- Playwright env example: `src/.env.playwright.example`
- GitHub workflow: `.github/workflows/playwright-e2e.yml`
- Cypress test (local): `src/cypress/e2e/planning.cy.ts`
- Cypress env example: `src/.env.cypress.example`

Preguntas rápidas

- ¿Quieres que haga un job de CI para Cypress en un repo privado separado? — Puedo preparar un job separado (no recomendado en este repo por ahora).
- ¿Quieres que añada instrucciones para generar baselines de snapshot visual? — Puedo añadir pasos para `npx playwright screenshot` y almacenar las imágenes en `tests/e2e/__snapshots__`.
