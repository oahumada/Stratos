# ✅ CHECKLIST DE PRODUCCIÓN — Stratos v0.12.x

| Campo             | Valor              |
| :---------------- | :----------------- |
| **Versión**       | v0.12.x            |
| **Fecha**         | 4 Abr 2026         |
| **Responsable**   | Tech Lead / DevOps |
| **Estado**        | 🔴 Pendiente       |
| **Último update** | —                  |

> **Instrucciones:** Marca cada ítem con `[x]` al completarlo. El despliegue a producción sólo puede proceder cuando TODAS las fases estén completas y la sección Go/No-Go tenga las firmas requeridas.

---

## FASE 1 — Infraestructura del Servidor

### 1.1 PHP y servidor web

```bash
# Verificar versión de PHP (requerido: 8.3+)
php --version
# ✅ ESPERADO: PHP 8.3.x

# Verificar php-fpm activo
systemctl status php8.3-fpm
# ✅ ESPERADO: active (running)

# Verificar Nginx activo
systemctl status nginx
# ✅ ESPERADO: active (running)

# Verificar configuración de Nginx (sin errores)
nginx -t
# ✅ ESPERADO: syntax is ok / test is successful
```

- [ ] PHP 8.3+ instalado y activo
- [ ] php-fpm activo y habilitado en boot (`systemctl enable php8.3-fpm`)
- [ ] Nginx activo y habilitado en boot (`systemctl enable nginx`)
- [ ] Configuración de Nginx válida (`nginx -t`)
- [ ] `worker_processes auto;` configurado en `nginx.conf`
- [ ] `fastcgi_pass unix:/run/php/php8.3-fpm.sock;` configurado en el vhost

### 1.2 PostgreSQL y backups

```bash
# Verificar versión de PostgreSQL (requerido: 14+)
psql --version
# ✅ ESPERADO: psql (PostgreSQL) 14.x o superior

# Verificar servicio activo
systemctl status postgresql
# ✅ ESPERADO: active (running)

# Test de conexión
psql -U stratos_user -d stratos_prod -c "SELECT version();"
# ✅ ESPERADO: respuesta con versión de PG

# Verificar backup más reciente
ls -lh /var/backups/stratos/db/ | tail -5
# ✅ ESPERADO: archivo .dump de hoy o ayer

# Test de dump manual
pg_dump -U stratos_user stratos_prod -F c -f /tmp/stratos_test.dump && echo "DUMP OK"
# ✅ ESPERADO: DUMP OK
```

- [ ] PostgreSQL 14+ instalado y activo
- [ ] Base de datos `stratos_prod` creada con usuario `stratos_user`
- [ ] Backup automático diario configurado (cron + pg_dump)
- [ ] Retención de backups: mínimo 7 días
- [ ] Backup de ayer verificado y restaurable
- [ ] Directorio de backups con permisos adecuados (no world-readable)

### 1.3 Redis

```bash
# Verificar versión (requerido: 6.0+)
redis-cli --version
# ✅ ESPERADO: redis-cli 6.x o superior

# Verificar servicio activo
systemctl status redis
# ✅ ESPERADO: active (running)

# Test de ping
redis-cli ping
# ✅ ESPERADO: PONG

# Verificar uso de memoria
redis-cli info memory | grep used_memory_human
# ✅ ESPERADO: < 80% del límite configurado
```

- [ ] Redis 6.0+ instalado y activo
- [ ] Redis habilitado en boot (`systemctl enable redis`)
- [ ] Contraseña de Redis configurada en `redis.conf` (`requirepass`)
- [ ] `maxmemory` y `maxmemory-policy allkeys-lru` configurados

### 1.4 SSL / HTTPS

```bash
# Verificar certificado SSL
openssl s_client -connect app.stratos.hr:443 -servername app.stratos.hr < /dev/null 2>/dev/null | openssl x509 -noout -dates
# ✅ ESPERADO: notAfter >= 60 días desde hoy

# Verificar renovación automática (Certbot)
certbot renew --dry-run
# ✅ ESPERADO: Congratulations, all renewals succeeded
```

- [ ] Certificado SSL válido (Let's Encrypt o CA corporativa)
- [ ] Certificado con vigencia > 60 días
- [ ] Renovación automática configurada y funcionando (`certbot renew --dry-run`)
- [ ] Redirección HTTP → HTTPS activa en Nginx
- [ ] HSTS header configurado (`add_header Strict-Transport-Security "max-age=31536000" always;`)

### 1.5 Supervisor (Queue Workers)

```bash
# Verificar Supervisor instalado y activo
supervisorctl status
# ✅ ESPERADO: stratos-worker:stratos-worker_00   RUNNING

# Ver configuración del worker
cat /etc/supervisor/conf.d/stratos-worker.conf
```

Configuración mínima de Supervisor:

```ini
[program:stratos-worker]
command=php /var/www/stratos/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
directory=/var/www/stratos
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/log/supervisor/stratos-worker.log
stopwaitsecs=3600
```

- [ ] Supervisor instalado y activo
- [ ] Configuración de worker creada en `/etc/supervisor/conf.d/stratos-worker.conf`
- [ ] `numprocs=2` (o más según carga esperada)
- [ ] Worker con `RUNNING` status
- [ ] Logs de Supervisor en `/var/log/supervisor/stratos-worker.log`

### 1.6 Cron Scheduler

```bash
# Verificar entrada de cron para el scheduler de Laravel
crontab -l -u www-data
# ✅ ESPERADO: * * * * * cd /var/www/stratos && php artisan schedule:run >> /dev/null 2>&1

# Test manual del scheduler
php artisan schedule:list
# ✅ ESPERADO: lista de tareas programadas
```

- [ ] Cron de Laravel añadido al crontab de `www-data`
- [ ] `php artisan schedule:list` muestra tareas registradas
- [ ] `php artisan schedule:run` ejecuta sin errores

---

## FASE 2 — Configuración de Entorno (.env producción)

> ⚠️ **Nunca commitear el `.env` de producción al repositorio.** Gestionar vía secrets manager o acceso SSH directo.

### 2.1 Variables de aplicación

```bash
# Verificar valores críticos
grep -E "^APP_(ENV|DEBUG|KEY|URL)" /var/www/stratos/.env
```

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_KEY=base64:...` (generado con `php artisan key:generate`, no el del `.env.example`)
- [ ] `APP_URL=https://app.stratos.hr` (sin trailing slash)

### 2.2 Base de datos

```bash
grep -E "^DB_" /var/www/stratos/.env
```

- [ ] `DB_CONNECTION=pgsql`
- [ ] `DB_HOST=127.0.0.1` (o IP del servidor PG)
- [ ] `DB_PORT=5432`
- [ ] `DB_DATABASE=stratos_prod`
- [ ] `DB_USERNAME=stratos_user`
- [ ] `DB_PASSWORD=` (contraseña fuerte, >20 chars, nunca la de desarrollo)

### 2.3 Redis y colas

```bash
grep -E "^(REDIS|QUEUE|CACHE|SESSION)" /var/www/stratos/.env
```

- [ ] `REDIS_CLIENT=predis`
- [ ] `REDIS_HOST=127.0.0.1`
- [ ] `REDIS_PASSWORD=` (misma que en `redis.conf`)
- [ ] `REDIS_PORT=6379`
- [ ] `QUEUE_CONNECTION=redis`
- [ ] `CACHE_STORE=redis`
- [ ] `SESSION_DRIVER=redis`
- [ ] `SESSION_LIFETIME=120`

### 2.4 Seguridad de sesión y cookies

```bash
grep -E "^SESSION_" /var/www/stratos/.env
```

- [ ] `SESSION_SECURE_COOKIE=true`
- [ ] `SESSION_ENCRYPT=true`
- [ ] `SESSION_DOMAIN=.stratos.hr` (con punto inicial para subdominios)

### 2.5 CORS y Sanctum

```bash
grep -E "^(SANCTUM|CORS|FRONTEND)" /var/www/stratos/.env
```

- [ ] `SANCTUM_STATEFUL_DOMAINS=app.stratos.hr,staging.stratos.hr`
- [ ] `FRONTEND_URL=https://app.stratos.hr`
- [ ] CORS configurado en `config/cors.php`: `allowed_origins` sólo dominios propios

### 2.6 Canales de notificación reales

```bash
grep -E "^(SLACK|TELEGRAM|MAIL)" /var/www/stratos/.env
```

- [ ] `SLACK_WEBHOOK_URL=https://hooks.slack.com/services/...` (canal `#stratos-alerts`)
- [ ] `TELEGRAM_BOT_TOKEN=...` (bot de producción, no el de dev)
- [ ] `TELEGRAM_CHAT_ID=...` (grupo de alertas de producción)
- [ ] `MAIL_MAILER=smtp` (no `log`)
- [ ] `MAIL_HOST=` / `MAIL_PORT=` / `MAIL_USERNAME=` / `MAIL_PASSWORD=` configurados
- [ ] `MAIL_FROM_ADDRESS=no-reply@stratos.hr`

### 2.7 Logging

- [ ] `LOG_CHANNEL=stack`
- [ ] `LOG_LEVEL=warning` (no `debug` en producción)
- [ ] Rotación de logs configurada (`daily` con `LOG_DAILY_DAYS=30`)

---

## FASE 3 — Build y Deploy

```bash
# En el servidor de producción — ejecutar en orden

# 1. Poner aplicación en modo mantenimiento
php artisan down --secret="stratos-deploy-token-2026"
# ✅ ESPERADO: Application is now in maintenance mode.

# 2. Pull del código
git pull origin main
# ✅ ESPERADO: Fast-forward / Already up to date

# 3. Instalar dependencias PHP sin devDependencies
composer install --no-dev --optimize-autoloader --no-interaction
# ✅ ESPERADO: Generating optimized autoload files / Nothing to install

# 4. Build de assets de frontend
npm ci --prefix . && npm run build --prefix .
# ✅ ESPERADO: build/assets generados en public/build/

# 5. Correr migraciones
php artisan migrate --force
# ✅ ESPERADO: Running migrations... (o "Nothing to migrate.")

# 6. Optimizar el framework
php artisan optimize
# ✅ ESPERADO: INFO Caching framework bootstrap, configuration, and metadata.

# 7. Crear symlink de storage (solo primera vez o si se perdió)
php artisan storage:link
# ✅ ESPERADO: The [public/storage] link has been connected to [storage/app/public].

# 8. Reiniciar queue workers
php artisan queue:restart
# ✅ ESPERADO: Broadcasting queue restart signal.
supervisorctl restart stratos-worker:*

# 9. Quitar modo mantenimiento
php artisan up
# ✅ ESPERADO: Application is now live.
```

- [ ] `php artisan down` ejecutado antes del deploy
- [ ] `git pull origin main` — sin conflictos
- [ ] `composer install --no-dev --optimize-autoloader` — sin errores
- [ ] `npm run build` — assets generados en `public/build/`
- [ ] `php artisan migrate --force` — sin errores ni rollbacks inesperados
- [ ] `php artisan optimize` — cache de config, rutas y vistas generada
- [ ] `php artisan storage:link` — symlink verificado
- [ ] `php artisan queue:restart` + `supervisorctl restart` — workers reiniciados
- [ ] `php artisan up` — aplicación disponible

---

## FASE 4 — Verificación Post-Deploy

### 4.1 Suite de tests

```bash
# Correr suite completa en modo compacto
php artisan test --compact
# ✅ ESPERADO: 1219+ tests passed, 0 failed
```

- [ ] `php artisan test --compact` — 1219+ tests PASS, 0 FAIL
- [ ] No hay tests marcados como `risky` o `incomplete` en módulos críticos

### 4.2 Smoke test de carga (k6)

```bash
# Instalar k6 si no está disponible: https://k6.io/docs/get-started/installation/
k6 run --env BASE_URL=https://app.stratos.hr scripts/k6-smoke.js
# ✅ ESPERADO: http_req_failed: 0.00%, p95 < 200ms
```

- [ ] k6 smoke test ejecutado contra URL de producción
- [ ] `http_req_failed` = 0%
- [ ] p95 de respuesta < 200ms
- [ ] Sin errores 5xx en el log de Nginx durante la prueba

### 4.3 Verificación de homepage y health

```bash
# Verificar homepage responde 200
curl -o /dev/null -s -w "%{http_code}" https://app.stratos.hr/
# ✅ ESPERADO: 200

# Verificar que no redirige a login cuando está autenticado
curl -I https://app.stratos.hr/dashboard/analytics \
  -H "Cookie: stratos_session=<session_de_prueba>"
# ✅ ESPERADO: 200

# Verificar headers de seguridad
curl -I https://app.stratos.hr/ | grep -E "(X-Frame|X-Content|Strict-Transport|Content-Security)"
```

- [ ] Homepage devuelve HTTP 200
- [ ] Dashboard carga sin errores tras login
- [ ] Headers de seguridad presentes (`X-Frame-Options`, `X-Content-Type-Options`, `Strict-Transport-Security`)

### 4.4 Queue workers activos y procesando

```bash
# Verificar que los workers están corriendo
supervisorctl status stratos-worker:*
# ✅ ESPERADO: RUNNING

# Enviar job de prueba y verificar procesamiento
php artisan tinker --execute="dispatch(new App\Jobs\TestQueueJob())->onQueue('default');"
# Verificar en logs que el job se procesó
tail -20 storage/logs/laravel.log | grep "TestQueueJob"
# ✅ ESPERADO: Processed App\Jobs\TestQueueJob

# Ver failed jobs (debe ser 0)
php artisan queue:failed
# ✅ ESPERADO: No failed jobs found.
```

- [ ] `supervisorctl status` — workers en `RUNNING`
- [ ] Job de prueba despachado y procesado correctamente
- [ ] `php artisan queue:failed` — 0 jobs fallidos tras el deploy

### 4.5 Scheduler funcionando

```bash
# Verificar que el cron está registrado
crontab -l -u www-data | grep schedule:run
# ✅ ESPERADO: * * * * * cd /var/www/stratos && php artisan schedule:run >> /dev/null 2>&1

# Ejecutar manualmente para verificar
php artisan schedule:run
# ✅ ESPERADO: sin errores (puede decir "No scheduled commands are ready to run.")
```

- [ ] Entrada de cron confirmada en crontab de `www-data`
- [ ] `php artisan schedule:run` ejecuta sin errores

---

## FASE 5 — Seguridad

### 5.1 Rate limiting

```bash
# Verificar que rate limiting está activo en API
for i in {1..15}; do curl -o /dev/null -s -w "%{http_code}\n" https://app.stratos.hr/api/catalogs; done
# ✅ ESPERADO: 200 200 200 ... 429 (Too Many Requests)
```

- [ ] Rate limiting activo en rutas de API (responde 429 tras umbral)
- [ ] Rate limiting activo en rutas de login (previene brute force)

### 5.2 APP_DEBUG desactivado

```bash
# Verificar que errores no exponen stack traces
curl https://app.stratos.hr/ruta-que-no-existe
# ✅ ESPERADO: página 404 sin stack trace ni paths del servidor
```

- [ ] `APP_DEBUG=false` confirmado en `.env` de producción
- [ ] Errores devuelven páginas genéricas (sin paths, sin stack traces)

### 5.3 Archivo .env no expuesto

```bash
# Verificar que .env no es accesible vía HTTP
curl -o /dev/null -s -w "%{http_code}" https://app.stratos.hr/.env
# ✅ ESPERADO: 403 o 404 (NUNCA 200)
```

- [ ] `.env` devuelve 403/404 — nunca 200
- [ ] `.env.example` también inaccesible vía HTTP
- [ ] Regla en Nginx: `location ~ /\. { deny all; }`

### 5.4 HTTPS forzado

```bash
# Verificar redirección HTTP → HTTPS
curl -I http://app.stratos.hr/
# ✅ ESPERADO: 301/302 Location: https://app.stratos.hr/
```

- [ ] Todo tráfico HTTP redirige a HTTPS (301)
- [ ] No hay recursos mixtos (mixed content) en el frontend

### 5.5 Audit logs

```bash
# Verificar que las acciones se registran en audit_logs
php artisan tinker --execute="echo App\Models\AuditLog::count();"
# ✅ ESPERADO: número > 0 (al menos los eventos del deploy)
```

- [ ] Audit logs almacenando eventos de sistema
- [ ] Endpoint `/admin/audit-logs` accesible para admins y protegido para otros roles

---

## FASE 6 — Observabilidad

### 6.1 Rotación de logs

```bash
# Verificar configuración de logrotate
cat /etc/logrotate.d/stratos
# ✅ ESPERADO: configuración con daily/weekly, compress, dateext

# Test de logrotate
logrotate --debug /etc/logrotate.d/stratos
# ✅ ESPERADO: sin errores críticos
```

Configuración mínima de logrotate:

```
/var/www/stratos/storage/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 0640 www-data www-data
    sharedscripts
    postrotate
        [ -f /run/php/php8.3-fpm.pid ] && kill -USR1 $(cat /run/php/php8.3-fpm.pid)
    endscript
}
```

- [ ] Logrotate configurado para `storage/logs/*.log`
- [ ] Retención de 30 días
- [ ] Compresión habilitada (`compress`)

### 6.2 Alertas de errores

```bash
# Verificar integración con sistema de alertas (Sentry/Flare/webhook)
grep -E "^(SENTRY|FLARE|LOG_SLACK)" /var/www/stratos/.env

# Test de alerta manual (si usa Slack webhook)
curl -X POST -H 'Content-type: application/json' \
  --data '{"text":"🚀 Stratos v0.12.x desplegado — test de alertas OK"}' \
  $SLACK_WEBHOOK_URL
# ✅ ESPERADO: ok (mensaje aparece en canal)
```

- [ ] Sistema de alertas de errores configurado (Sentry, Flare, o webhook propio)
- [ ] Mensaje de prueba recibido en canal de alertas (`#stratos-alerts`)
- [ ] Alertas configuradas para errores nivel `error` y `critical`
- [ ] Notificaciones de Telegram/Slack funcionando para alertas críticas

### 6.3 Monitoreo de performance

```bash
# Baseline de performance con k6
k6 run --env BASE_URL=https://app.stratos.hr \
  --out json=/tmp/k6-baseline.json \
  scripts/k6-smoke.js

# Extraer p95
cat /tmp/k6-baseline.json | python3 -c "
import json, sys
data = [json.loads(l) for l in sys.stdin if l.strip()]
p95s = [d['data']['value'] for d in data if d.get('metric') == 'http_req_duration' and d.get('type') == 'Point']
if p95s: print(f'p95: {sorted(p95s)[int(len(p95s)*0.95)]:.0f}ms')
"
# ✅ ESPERADO: p95 < 200ms
```

- [ ] Baseline de performance registrado (p95, p99, RPS)
- [ ] p95 de latencia < 200ms en condiciones normales
- [ ] Herramienta de APM configurada (New Relic, Datadog, o similar) — _opcional pero recomendado_
- [ ] Dashboard de métricas de servidor (CPU, RAM, I/O) disponible

---

## FASE 7 — Decisión Go / No-Go

> **Criterio de aceptación:** Todas las fases 1–6 completadas, 0 ítems bloqueadores pendientes.

### 7.1 Resumen de estado

| Fase | Descripción              | Estado | Ítems Pendientes |
| :--: | :----------------------- | :----: | :--------------: |
|  1   | Infraestructura          |   ⬜   |        —         |
|  2   | Configuración de entorno |   ⬜   |        —         |
|  3   | Build y deploy           |   ⬜   |        —         |
|  4   | Verificación post-deploy |   ⬜   |        —         |
|  5   | Seguridad                |   ⬜   |        —         |
|  6   | Observabilidad           |   ⬜   |        —         |

**Leyenda:** ✅ Completo | ⚠️ Con observaciones | ❌ Bloqueador | ⬜ Pendiente

### 7.2 Tabla de sign-off

| Rol               | Nombre | Firma | Fecha | Decisión    |
| :---------------- | :----- | :---- | :---- | :---------- |
| **Tech Lead**     |        |       |       | ☐ GO ☐ NOGO |
| **QA Lead**       |        |       |       | ☐ GO ☐ NOGO |
| **Product Owner** |        |       |       | ☐ GO ☐ NOGO |
| **DevOps**        |        |       |       | ☐ GO ☐ NOGO |

### 7.3 Decisión final

```
DECISIÓN:  [ ] GO — Proceder al despliegue en producción
           [ ] NO-GO — Resolver ítems bloqueadores antes de proceder

RAZÓN (si NO-GO):
______________________________________________________________
______________________________________________________________

PRÓXIMO INTENTO: ___________________________
```

---

_Documento generado para Stratos v0.12.x — 4 Abr 2026_
