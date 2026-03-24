# 🆘 Verification Hub - Troubleshooting & FAQ

**Última actualización:** 24.03.2026  
**Versión:** 1.0

---

## 🔥 Problemas Críticos

### ❌ Error: "Verification Hub no carga"

**Síntomas:**

- Página en blanco o loading infinito
- Error en consola del navegador

**Diagnóstico:**

```javascript
// Abre DevTools (F12) → Console → copia esto:
console.log('URL:', window.location.href);
console.log('Token:', localStorage.getItem('sanctum_token'));
fetch('/api/deployment/verification/scheduler-status', {
    headers: {
        Authorization: `Bearer ${localStorage.getItem('sanctum_token')}`,
    },
})
    .then((r) => r.json())
    .then(console.log)
    .catch(console.error);
```

**Soluciones (en orden):**

1. **¿Estás logueado?**

    ```
    → Si no: Login primero
    → Si sí: Continúa
    ```

2. **¿Tienes rol admin?**

    ```
    Verifica en: /settings/profile
    → Si no tienes role "admin": Solicita al administrador
    → Si tienes: Continúa
    ```

3. **¿El API responde?**

    ```bash
    curl -X GET "http://localhost:8000/api/deployment/verification/scheduler-status" \
      -H "Authorization: Bearer YOUR_TOKEN"

    → Si error 500: Revisa Laravel logs (ver abajo)
    → Si error 401: Token inválido/expirado → re-login
    → Si error 404: URL mal → verifica rutas
    ```

4. **Revisa Laravel logs:**

    ```bash
    # Terminal en proyecto:
    tail -f storage/logs/laravel.log | grep -i verification

    # O en archivo:
    cat storage/logs/laravel.log | grep -A5 "Verification"
    ```

5. **Limpia cache del navegador:**
    ```
    DevTools → Application → Clear Site Data
    → Cierra tab
    → Abre nueva tab
    → Intenta nuevamente
    ```

**Si sigue sin funcionar:** Contacta al equipo de desarrollo con el output del paso 3 y 4.

---

### ❌ Error: "Access Denied / 403 Forbidden"

**Síntomas:**

- Mensaje: "User does not have admin role"
- API retorna 403

**Solución:**

1. Abre `/settings/profile`
2. Busca tu "Role"
3. Si no dice "admin" → Solicita al administrador que te asigne
4. Espera 5 minutos (caché)
5. Logout + Login
6. Intenta nuevamente

**Para administrador (agregar rol admin):**

```bash
php artisan tinker

>>> $user = User::find(YOUR_USER_ID)
>>> $user->assignRole('admin')  // o similar en tu sistema

# Verifica:
>>> $user->roles  // debe incluir 'admin'
```

---

### ❌ Error: "Scheduler nunca ejecuta"

**Síntomas:**

- `SchedulerStatus.vue` dice "Never run"
- `next_run` No existe o es en el pasado
- Transiciones no ocurren automáticamente

**Diagnóstico:**

1. **¿El scheduler está enabled?**

    ```bash
    php artisan tinker
    >>> DB::table('verification_scheduler')->first()

    # Busca: enabled = true
    ```

2. **¿Está el Laravel queue en vivo?**

    ```bash
    # En terminal, revisa si está corriendo:
    ps aux | grep queue

    # Debería haber un proceso:
    "php artisan queue:work"
    ```

3. **¿Hay errores en queue?**
    ```bash
    # Terminal:
    tail -f storage/logs/laravel.log | grep -i "queue\|job"
    ```

**Solución:**

Si queue no está corriendo:

```bash
# Terminal en proyecto (detener si está corriendo):
# Ctrl+C

# Iniciar queue worker:
php artisan queue:work

# O en background:
php artisan queue:work &
```

Si con `composer run dev` (debería incluir queue):

```bash
# Verifica que queue está en procfile o script
cat Procfile  # o package.json scripts

# Busca línea similar a:
"queue: php artisan queue:work"

# Si no está → Añádelo
```

---

### ❌ Error: "Notifications en Slack no llegan"

**Síntomas:**

- `ChannelConfig.vue` Test OK, pero no recibe mensajes reales
- Audit log muestra "notification_sent" pero no hay mensaje

**Diagnóstico:**

1. **Test Slack funciona?**

    ```
    Tab Configuration → Test Slack
    → ¿Recibes mensaje en Slack?
    ```

    Si NO:
    - Webhook URL incorrecta
    - Webhook expiró
    - Canal de Slack incorrecto

2. **¿Webhook URL es válida?**

    ```bash
    # En Terminal, test directo:
    curl -X POST "https://hooks.slack.com/services/YOUR/WEBHOOK/URL" \
      -H 'Content-type: application/json' \
      -d '{"text":"Test message"}'

    # Debería responder con: ok
    ```

3. **¿El canal de Slack existe?**
    - Abre Slack
    - Ve al canal donde debería ir el mensaje
    - Revisa permisos (debe permitir apps)

**Soluciones:**

**Si webhook expiró:**

1. Ve a Slack app settings
2. Regenera webhook URL
3. Copia nueva URL
4. En Hub: Tab Configuration → Pega URL nuevo
5. Click [Test Slack]

**Si canal incorrecto:**

1. En Slack app settings: revisa tu webhook
2. Asegúrate que apunta al canal correcto
3. Regener if needed

**Si webhook invalidó:**

1. Slack Web UI → Your Apps → Verification Hub
2. Incoming Webhooks → Add New Webhook to Workspace
3. Selecciona canal
4. Copia nueva URL
5. Actualiza en Hub → Test

---

### ❌ Error: "Database llena / Disk space"

**Síntomas:**

- Error al intentar crear audit logs
- Página lenta
- "Disk full" error

**Diagnóstico:**

```bash
# Ver tamaño de BD:
df -h | grep postgres  # Si PostgreSQL

# Ver tabla de audit logs:
php artisan tinker
>>> DB::table('verification_audit_logs')->count()
=> 125000  # Muchos registros!

>>> DB::table('verification_audit_logs')->size()
```

**Solución:**

1. **Reducir retention policy:**

    ```
    Tab Configuration → Database section
    Cambiar retention_days: 365 → 90
    ```

2. **Ejecutar cleanup:**

    ```bash
    # Artisan command (crear si no existe):
    php artisan verification:cleanup-logs --days=90

    # O borrar manualmente:
    php artisan tinker
    >>> DB::table('verification_audit_logs')
         ->where('created_at', '<', now()->subDays(90))
         ->delete()
    ```

3. **Archivar datos viejos:**
    ```bash
    # Exportar a CSV antes de borrar:
    # (desde ComplianceReportGenerator)
    1. Date Range: 1 año atrás
    2. Click "Descargar CSV"
    3. Guarda archivo
    4. Luego ejecuta cleanup
    ```

---

## ⚠️ Problemas Comunes

### P: Dry-Run Simulator retorna valores NULL

**Problema:**

```json
{
    "error": "Cannot evaluate metrics - insufficient data",
    "metrics": null
}
```

**Causa:** No hay datos de tests en la base de datos

**Solución:**

1. Asegúrate que el sistema tiene datos históricos
2. Ejecuta tests primero:
    ```bash
    php artisan test  # O tu test suite
    ```
3. Espera a que se populen métricas (puede tomar 5-10 min)
4. Intenta dry-run nuevamente

---

### P: Transitions nunca ocurren aunque métricas son buenas

**Problema:**

- `DryRunSimulator` says "would_transition": true
- Pero sistema nunca transaciona

**Causa Potencial:**

- Scheduler no está checking
- Cuota de transiciones alcanzada
- Sistema en modo "manual-only"

**Solución:**

1. **Verifica modo del sistema:**

    ```
    Tab Configuration → Mode: debe ser "auto_transitions"
    Si no → Cambiar a auto
    ```

2. **Revisa historial de transiciones:**

    ```
    Tab Overview → TransitionReadiness
    ¿Cuándo fue la última transición?

    Si fue hace 10 minutos → Espera a siguiente hora
    (Scheduler corre cada hora)
    ```

3. **Fuerza evaluación:**
    ```
    SchedulerStatus.vue → Click [Ejecutar Ahora]
    Verifica si transaciona
    ```

---

### P: Audit Log Explorer no muestra logs antiguos

**Problema:**

- Filtro por fecha antigua retorna vacío
- Pero ComplianceReport SÍ muestra datos

**Causa:** Logs fueron archivados/borrados

**Solución:**

1. Revisa fecha de retention en Configuration
    ```
    Si fue set a 30 dias → Logs más viejos fueron borrados
    ```
2. Para datos históricos: usa ComplianceReport (tiene caché)
3. Para futuros: reduce rotation speed si necesario

---

### P: SetupWizard se queda en paso 2

**Problema:**

- Wizard no avanza al paso 3
- Botón "Next" no responde
- Errores de validación no claros

**Causa:** Página no se renderizó completamente

**Solución:**

1. Reload: F5
2. Limpia cache: Ctrl+Shift+Delete
3. Abre nuevamente Hub
4. Intenta SetupWizard nuevamente

---

## 🎯 Casos de Uso (solucionador de problemas)

### "Necesito saber qué pasó en el sistema el jueves pasado"

**Solución:**

```
1. Tab Audit → Audit Log Explorer
2. Date Filter: "last_thursday"
3. Exporta como CSV si necesitas analyrar
```

---

### "Quiero tener alertas por email, no Slack"

**Solución:**

```
1. Tab Configuration → Email section
2. Toggle ON
3. Añade emails: admin@company.com; ops@company.com
4. Click [Test Email]
5. Verifica recibas email
6. Listo!
```

---

### "Sistema pasó a fase "reject" pero no debería"

**Solución:**

```
1. Tab Audit → AuditLogExplorer
2. Busca transición a "reject"
3. Click para ver detalles
4. Nota el "reason"
5. Intenta DryRun con sliders
6. Identifica qué métrica está fuera
7. Resuelve (ej: reduce error rate)
8. Si es error del sistema → Tab Control → [Override to Flagging]
```

---

### "Quiero crear reporte para auditoría externa"

**Solución:**

```
1. Tab Audit → ComplianceReportGenerator (bottom)
2. Date Range: sel quarter que necesitas
3. Filtros: aplica si necesario
4. Click [📊 Generar reporte]
5. Click [⬇️ Descargar]
6. Formato: PDF para presentación, CSV para datos
```

---

## 🔧 Debug Mode

### Enable Verbos Logging

```bash
# En .env:
APP_DEBUG=true
LOG_LEVEL=debug

# Luego:
php artisan cache:clear
php artisan config:cache
```

### Verificar Estado del Sistema

```bash
# Terminal:
php artisan tinker

# Ver estado scheduler:
>>> $latest = DB::table('verification_scheduler')->latest()->first()
>>> $latest

# Ver últimas transiciones:
>>> DB::table('verification_transitions')->latest()->limit(5)->get()

# Ver audit logs:
>>> DB::table('verification_audit_logs')->latest()->limit(10)->get()

# Ver notificationes no leídas:
>>> DB::table('verification_notifications')->where('read', false)->count()

# Ver caché:
>>> cache()->get('verification_scheduler_last_run_' . auth()->user()->organization_id)
```

### Monitorear en Tiempo Real

```bash
# Terminal 1 - Laravel logs:
tail -f storage/logs/laravel.log | grep -i verification

# Terminal 2 - Queue:
php artisan queue:work

# Terminal 3 - Dev server:
composer run dev  # Vite + PHP server
```

---

## ❓ FAQ

### P: ¿Cada cuánto se evalúa si hay que transacionar?

**R:** Cada hora. El scheduler corre automáticamente cada 60 minutos. Puedes verlo en `SchedulerStatus` → "Next Run" countdown.

---

### P: ¿Puedo hacer transición manual?

**R:** Sí, pero está limitado:

```
1. Tab Control → DryRunSimulator
2. Verifica que "would_transition" es true
3. Si quieres forzar: Contacta a admin (manual override)
```

---

### P: ¿Qué es la "fase" exactamente?

**R:** El sistema tiene 4 fases de aceptación de cambios:

- **Silent**: Recolecta datos, no reporta
- **Flagging**: Reporta issues pero no rechaza
- **Reject**: Rechaza cambios que no pasen
- **Tuning**: Está en "reject mode" y se optimiza continuamente

---

### P: ¿Puedo ver diferencia entre lo que SERÍA vs lo que ES?

**R:** Sí, usa DryRunSimulator:

```
1. Tab Control → Left panel (DryRunSimulator)
2. Ajusta sliders para simular
3. Lee "Would Transition?" - eso es lo que SERÍA
4. Compara con "Current Phase" - eso es lo que ES
```

---

### P: ¿Qué hacer si un audit log tiene error?

**R:** Los logs solo leen, no escriben. Si hay errores en lectura:

```
1. Verifica DB connection
2. Revisa si tabla tiene datos
3. Si DB corrompida: contact DBA
```

---

### P: ¿Cómo exporto datos para análisis?

**R:** Varios formatos:

```
1. AuditLogExplorer: Exporta CSV con tabla completa
2. ComplianceReport: Exporta JSON/CSV/PDF con resumen
3. DryRunSimulator: Exporta PDF con resultado simulación
```

---

### P: ¿Qué sucede si pierdo conexión a Internet mientras uso el Hub?

**R:** Depende:

- **Si estás leyendo**: Perderás datos no cargados
- **Si estás escribiendo**: Cambio no se guarda
- **Recomendación**: Recarga página después de conexión restaurada

---

### P: ¿Hay límite a cuántar datos puedo ver?

**R:** Sí:

- **Notificaciones**: Default 20 por página
- **Audit Logs**: Default 50 por página
- **Rate limit**: 60 requests/minuto

---

### P: ¿Puedo integrar el Hub con mis propias herramientas?

**R:** Sí, todos los endpoints son REST JSON:

```
Ver: VERIFICATION_HUB_API_REFERENCE.md para
integración con:
- Postman
- Zapier
- Custom scripts
- Webhooks (coming soon)
```

---

## 📞 Escalar a Soporte

Si después de estos pasos el problema persiste:

1. **Recolecta información:**

    ```bash
    # Copia output de:
    php artisan --version
    php --version
    npm --version

    # Logs:
    cat storage/logs/laravel.log | tail -50

    # DevTools Console error
    F12 → Console → Screenshot
    ```

2. **Documenta:**
    - ¿Qué intentaste hacer?
    - ¿Qué error ves?
    - ¿Cuándo empezó?
    - ¿Otros usuarios afectados?

3. **Contacta:**
    - Slack: #tech-support
    - Email: support@company.com
    - Issue: GitHub repo

---

## 📚 Recursos Adicionales

- [Verification Hub Guide](VERIFICATION_HUB_GUIDE.md)
- [Architecture](VERIFICATION_HUB_ARCHITECTURE.md)
- [API Reference](VERIFICATION_HUB_API_REFERENCE.md)
- [Laravel Official Docs](https://laravel.com/docs)
- [Vue 3 Docs](https://vuejs.org/guide/introduction.html)

---
