# Stratos k6 — Stress Testing

Suite de pruebas de carga y estrés para la API de Stratos.

## Estructura

```
tests/k6/
├── scenarios/
│   ├── smoke.js   — Sanity check (1 VU, 1 iter) — < 30s
│   ├── load.js    — Carga realista (20-30 VUs, 5 min)
│   └── stress.js  — Spike test (60 VUs, punto de quiebre)
├── utils/
│   └── auth.js    — Fortify session login helper
├── results/       — Output JSON (git-ignored)
└── README.md
```

## Requisitos

- [k6](https://k6.io/docs/get-started/installation/) instalado localmente
- Server Laravel corriendo en `http://localhost:8000`

**Instalación de k6 (Linux):**

```bash
sudo gpg --no-default-keyring --keyring /usr/share/keyrings/k6-archive-keyring.gpg \
  --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys C5AD17C747E3415A3642D57D77C6C491D6AC1D69
echo "deb [signed-by=/usr/share/keyrings/k6-archive-keyring.gpg] https://dl.k6.io/deb stable main" \
  | sudo tee /etc/apt/sources.list.d/k6.list
sudo apt-get update && sudo apt-get install k6
```

## Variables de entorno

| Variable        | Default                 | Descripción               |
| --------------- | ----------------------- | ------------------------- |
| `K6_BASE_URL`   | `http://localhost:8000` | URL base de la app        |
| `K6_USER_EMAIL` | `admin@stratos.test`    | Email del usuario de test |
| `K6_USER_PASS`  | `password`              | Contraseña del usuario    |

## Uso

```bash
# Smoke test (sanity check rápido)
k6 run tests/k6/scenarios/smoke.js

# Load test (carga realista)
k6 run tests/k6/scenarios/load.js

# Stress test (punto de quiebre)
k6 run tests/k6/scenarios/stress.js

# Contra entorno staging
k6 run -e K6_BASE_URL=https://staging.stratos.app \
       -e K6_USER_EMAIL=k6@stratos.app \
       -e K6_USER_PASS=supersecret \
       tests/k6/scenarios/load.js
```

## Umbrales (SLOs)

| Escenario        | p(95) objetivo | Error rate máx |
| ---------------- | -------------- | -------------- |
| Read endpoints   | < 2s           | < 1%           |
| Scenario preview | < 5s           | < 1%           |
| RAGAS metrics    | < 1.5s         | < 1%           |
| Stress (global)  | < 4s           | < 5%           |

## CI/CD

El workflow `.github/workflows/k6-stress.yml` ejecuta:

- **Smoke test** automáticamente en cada PR que toque la API o rutas
- **Load test** cada lunes a las 3 AM (schedule)
- **Cualquier escenario** vía `workflow_dispatch` apuntando a cualquier entorno
