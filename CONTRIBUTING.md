# Contributing

Gracias por contribuir a Stratos. Este archivo explica el flujo TDD recomendado y cómo ejecutar pruebas localmente antes de abrir un PR.

1) Preparación del entorno

```bash
# Instalar deps backend
cd src
composer install

# Instalar deps frontend
npm ci

# Preparar DB sqlite rápida
cp .env.example .env
php -r "file_exists('database/database.sqlite') || mkdir('database') && touch('database/database.sqlite');"
php artisan key:generate
php artisan migrate --force
```

2) Flujo TDD local rápido

- Ejecuta pruebas unitarias de backend durante el desarrollo:

```bash
cd src
vendor/bin/pest --watch
```

- Ejecuta pruebas unitarias de frontend en modo watch:

```bash
cd src
npx vitest --watch resources/js
```

3) Pre-push hooks

El repositorio incluye hooks Husky para prevenir pushes con tests rotos. Instálalos con:

```bash
cd src
npm run prepare
```

El hook `pre-push` ejecuta `composer test` y `npm run test:unit`. Si las pruebas fallan, el push se bloqueará.

4) Recomendaciones de PR

- Ejecuta `composer test` y `npm run test:unit` antes de abrir PR.
- Describe los pasos para reproducir cualquier cambio visual (screenshots, pasos manuales).
- Añade tests unitarios cuando agregues lógica (composables, helpers) y E2E cuando cambios afectan flujos críticos.
