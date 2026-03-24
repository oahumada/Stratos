# Interfaces de Configuración de Verificación - README

## 📦 Archivos Nuevos Agregados

### 1. Comando CLI Interactivo

**Ubicación:** `app/Console/Commands/VerificationConfigureCommand.php`

**Uso:**

```bash
php artisan verification:configure
```

**Características:**

- ✅ Preguntas interactivas paso a paso
- ✅ Configuración visual con colores
- ✅ Resumen al final de la configuración
- ✅ Actualización automática de `.env` y config files

**Perfil de usuario:** Técnicos, DevOps, Administradores

---

### 2. Página Web de Configuración

**Ubicación:** `resources/js/Pages/Deployment/VerificationConfiguration.vue`

**Acceso:**

```
http://miapp.local/deployment/verification-config
```

**Características:**

- ✅ Interfaz visual moderna con Tailwind CSS
- ✅ Selección de modo con cards interactivas
- ✅ Sliders y dropdowns para parámetros
- ✅ Live preview de configuración
- ✅ Soporte Dark Mode
- ✅ Info boxes explicativas

**Requiere:** `role:admin` (middleware)

**Perfil de usuario:** Business Users, Product Managers, No-técnicos

---

### 3. Controlador de Configuración

**Ubicación:** `app/Http/Controllers/Deployment/VerificationConfigurationController.php`

**Métodos:**

- `show()` - Mostrar página de configuración
- `store()` - Guardar configuración (POST)
- `status()` - Obtener estado actual (API)

**Endpoints:**

```
GET    /deployment/verification-config       → Mostrar formulario
POST   /deployment/verification-config       → Guardar configuración
GET    /api/deployment/verification-status   → Estado actual (JSON)
```

---

### 4. Archivo de Configuración

**Ubicación:** `config/verification-deployment.php`

**Contenido:**

- Todos los parámetros de cada opción
- Documentación inline
- Valores por defecto
- Configuración compartida

**Acceso en código:**

```php
$mode = config('verification-deployment.deployment_mode');
$config = config('verification-deployment.auto_transitions');
```

---

### 5. Rutas Agregadas

**Ubicación:** `routes/web.php`

```php
// Rutas protegidas (solo admin)
Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::get('/deployment/verification-config', ...)->name('deployment.verification-config');
    Route::post('/deployment/verification-config', ...)->name('deployment.verification-config.store');
    Route::get('/api/deployment/verification-status', ...)->name('deployment.verification-status');
});
```

---

## 🎯 Opciones Configurables

### 1. AUTO-TRANSITIONS (Opción 1)

**Sistema de autopiloto automático**

Parámetros configurables:

- `error_rate_threshold_phase2` (%) - Umbral para cambiar a Phase 2
- `retry_rate_threshold_phase4` (%) - Umbral para cambiar a Phase 4
- `check_interval_minutes` - Cada cuánto verificar criterios
- `data_window_hours` - Ventana de datos para análisis
- `enable_notifications` - Notificaciones de cambios
- `notification_channel` - Dónde enviar (log/slack/email)

**Ejemplo de flujo:**

1. Sistema monitorea error_rate cada hora
2. Si error_rate < 15%, cambia automáticamente a Phase 2
3. Después, monitorea retry_rate
4. Si retry_rate < 10%, mantiene Phase 3
5. Si retry_rate > 10%, sube automáticamente a Phase 4

---

### 2. HYBRID (Opción 3)

**Recolecta automáticamente + decisiones manuales**

Parámetros configurables:

- `metrics_collection_interval` - Cada cuántos minutos recolectar
- `alert_threshold_percent` - Alertar si sube más del X%
- `enable_suggestions` - Sugerencias automáticas
- `enable_web_dashboard` - Dashboard de métricas
- `suggestion_channel` - Dónde mostrar (cli/web/both)

**Ejemplo de flujo:**

1. Sistema recolecta métricas cada hora
2. Muestra sugerencias: "Error_rate = 3%, listo para Phase 2"
3. **TÚ DECIDES** cuándo cambiar
4. Ejecutas manualmente: `./scripts/verification-phase-deploy.sh flagging`

---

### 3. MONITORING ONLY (Opción 2)

**Solo recolecta datos**

Parámetros configurables:

- `metrics_collection_interval` - Cada cuántos minutos recolectar
- `metrics_retention_days` - Cuántos días guardar datos

**Ejemplo de flujo:**

1. Sistema recolecta métricas
2. No hace nada automáticamente
3. Tú analizas y tomas decisiones manuales
4. Útil para análisis profundo antes de implementar

---

## 🚀 Flujo de Uso Típico

### Para Usuario No-Técnico (Web UI)

```bash
# 1. Acceder a la página web
http://miapp.local/deployment/verification-config

# 2. Seleccionar modo (ej: "Hybrid")

# 3. Ajustar parámetros con sliders y dropdowns

# 4. Click en "Guardar Configuración"

# 5. Sistema actualiza automáticamente

# 6. El dev ejecuta en terminal:
php artisan config:cache
./scripts/verification-phase-deploy.sh silent
```

### Para Usuario Técnico (CLI)

```bash
# 1. Ejecutar comando interactivo
php artisan verification:configure

# 2. Responder preguntas interactivas
# - ¿Qué modo prefieres?
# - ¿Umbral error_rate?
# - ¿Intervalo de verificación?
# - etc.

# 3. Sistema muestra resumen y guarda

# 4. Continuar con despliegue
./scripts/verification-phase-deploy.sh silent
```

---

## 🔄 Sincronización Web ↔ CLI

Ambas interfaces actualizan los mismos archivos:

- `.env` - Variables de entorno
- `config/verification-deployment.php` - Archivo de config

**Resultado:** Puedes cambiar entre web y CLI sin conflictos

---

## 📊 Archivos de Configuración Guardados

```
.env
    └─ VERIFICATION_DEPLOYMENT_MODE=hybrid

config/verification-deployment.php
    ├─ deployment_mode: "hybrid"
    ├─ auto_transitions: {...}
    ├─ hybrid: {...}
    └─ monitoring_only: {...}
```

---

## ✅ Testing de Configuración

```bash
# Ver configuración actual
php artisan config:show verification-deployment

# Ver en formato JSON
php artisan tinker
>>> config('verification-deployment')

# Ver en dashboard
curl http://miapp.local/api/deployment/verification-status
```

---

## 🚨 Consideraciones de Seguridad

- ✅ Solo usuarios con `role:admin` pueden acceder a la web
- ✅ CLI comando también requiere autenticación Laravel
- ✅ Cambios se registran en logs
- ✅ Configuración se almacena en archivos (no en DB)
- ✅ No hay secrets en la configuración visible

---

## 📝 Próximos Pasos

1. Ejecutar uno de los comandos de configuración
2. Revisar el archivo `docs/TAREA5_DEPLOYMENT_START.md`
3. Comenzar despliegue: `./scripts/verification-phase-deploy.sh silent`

---

**Creado:** Marzo 2026
**Soporte:** Tarea 5 - Pasos Opcionales de Verificación
