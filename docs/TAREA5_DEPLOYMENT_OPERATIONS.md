# Tarea 5: Guía Operativa de Despliegue de Phases

## 📖 Introducción

Esta guía le ayudará a desplegar los 4 phases de verificación de forma segura y gradual en su entorno de producción.

---

## 🚀 Inicio Rápido

```bash
# Ver fase actual
./scripts/verification-phase-deploy.sh status

# Cambiar a Phase 1 (Silent)
./scripts/verification-phase-deploy.sh silent

# Ver métricas
php artisan verification:metrics

# Cambiar a siguiente fase
./scripts/verification-phase-deploy.sh flagging
```

---

## 📋 Paso a Paso

### Día 1: Phase 1 (Silent) - Observación sin acción

#### 1.1 Preparación (5 min)

```bash
# Asegurar que verificación está habilitada
echo "VERIFICATION_ENABLED=true" >> .env
echo "VERIFICATION_PHASE=silent" >> .env

# Actualizar configuración
php artisan config:cache
```

#### 1.2 Desplegar Phase 1 (5 min)

```bash
# Ejecutar script de despliegue
./scripts/verification-phase-deploy.sh silent

# Confirmar que está en modo silent
php artisan verification:metrics
```

**Lo que verás:**

- Sistema detecta errores pero NO rechaza
- Usuarios NO ven cambios
- Logs internos registran todas las violaciones

#### 1.3 Monitoreo (1h - durante el día)

Deja el sistema funcionando. La verificación silenciosa está registrando todos los errores:

```bash
# Ver métricas cada hora
watch -n 3600 "php artisan verification:metrics"

# O manualmente
php artisan verification:metrics
```

**Qué buscar:**

- Total de eventos procesados
- Distribución de tipos de violación
- Agentes con más errores

#### 1.4 Análisis (1h - al final del día)

Después de 24h, analiza los datos:

```bash
# Exportar métricas a JSON para análisis
php artisan verification:metrics --json > storage/metrics-day1.json
```

**Preguntas a responder:**

- ¿Cuál es el error_rate total? (% solicitudes con errores)
- ¿Cuáles son los 3 errores más comunes?
- ¿Hay algún agente que falla desproporcionadamente?
- ¿Hay patrones por hora o tipo de datos?

---

### Día 2: Decisión - ¿Continuar a Phase 2?

#### Matriz de Decisión:

```
SI error_rate < 5%:
  ✅ CONTINUAR a Phase 2
  Nota: Muy pocos errores, el sistema funciona bien

SI 5% <= error_rate <= 15%:
  ✅ CONTINUAR a Phase 2
  Advertencia: Hay errores moderados, observar de cerca

SI error_rate > 15%:
  ⚠️  PAUSAR
  Acción: Revisar y ajustar reglas antes de continuar
```

#### 2.1 Si error_rate < 15%: Desplegar Phase 2

```bash
./scripts/verification-phase-deploy.sh flagging
```

**Lo que ocurre:**

- Usuarios ven ⚠️ banderas en respuestas problemáticas
- Las respuestas AÚN se entregan
- Usuarios pueden revisar manualmente

#### 2.2 Monitoreo Phase 2 (24h)

```bash
php artisan verification:metrics --window=24
```

**Qué buscar:**

- % de solicitudes con bandera (debería ser similar a error_rate)
- Feedback de usuarios: "¿Esta bandera tiene sentido?"
- Tasa de falsos positivos (debería ser <10%)

#### 2.3 Análisis de False Positives

Si tasa_falsos_positivos > 10%:

```bash
# Revisar errores específicos
php artisan verification:metrics

# Posible acción: Volver a Phase 1 para ajustar reglas
./scripts/verification-phase-deploy.sh rollback silent
```

---

### Día 3: Phase 3 (Reject) - Garantía de Calidad

#### 3.1 Pregunta crítica:

**"¿Confío que las 9 reglas de validación están correctamente calibradas?"**

- SI → Ir a 3.2
- NO → Volver a Phase 1 y revisar reglas

#### 3.2 Desplegar Phase 3

```bash
./scripts/verification-phase-deploy.sh reject
```

**Lo que ocurre:**

- Solicitudes inválidas que Phase 2 flagueaba, ahora son RECHAZADAS
- Usuarios reciben error 422: "Tu solicitud no es válida"
- Usuarios pueden reintentar

#### 3.3 Monitoreo CRÍTICO (24h)

```bash
php artisan verification:metrics --window=24
```

**Métrica CRÍTICA: retry_rate**

```
retry_rate = (solicitudes rechazadas) / (total solicitudes)
```

**¿Cuál es el retry_rate?**

```
SI retry_rate < 5%:
  ✅ EXCELENTE
  Phase 3 está bien calibrada
  Phase 4 (Tuning) es OPCIONAL

SI 5% <= retry_rate <= 10%:
  ⚠️  MODERADO
  Algo de fricción pero aceptable
  Phase 4 (Tuning) es RECOMENDADA

SI retry_rate > 15%:
  🚨 CRÍTICO
  Demasiados usuarios reciben rechazo
  Phase 4 (Tuning) es URGENTE
```

---

### Día 4: Phase 4 (Tuning) - Auto-Mejora (Condicional)

#### 4.1 Análisis de Decisión

**¿Debería desplegar Phase 4?**

Responde estas preguntas:

1. **¿Es el retry_rate > 5-10%?**
    - SI → Fase 4 es RECOMENDADA
    - NO → Fase 4 es OPCIONAL

2. **¿Tolero +2-4 segundos de latencia?**
    - SI → Fase 4 es VIABLE
    - NO → Mantener Fase 3

3. **¿Es el consumo de tokens un problema?**
    - SI → No hacer Fase 4
    - NO → Fase 4 es viable

#### 4.2 Si todas son SÍ: Desplegar Phase 4

```bash
./scripts/verification-phase-deploy.sh tuning
```

**Lo que ocurre:**

- Sistema rechaza automaticamente
- Pero REINTENTA automáticamente (máx 2 intentos)
- Usuario no VE el rechazo inicial
- Pero solicitud tarda +2-4s

#### 4.3 Monitoreo intensivo (48h iniciales)

```bash
# Ver métricas de tuning
php artisan verification:metrics --window=48

# Observar:
# - retry_success_rate: % de reintentos que funcionan
# - average_latency: tiempo promedio de respuesta
# - token_consumption: aumento de tokens
```

**Indicadores de problema:**

- Si retry_success_rate < 30%: Las mejoras no están funcionando
- Si latencia > 10s: Demasiado lenta
- Si usuarios se quejan del tiempo: Posiblemente reverting Fase 4

---

## 🚨 Rollback - Volver Atrás

Si algo sale mal, deshacer es FÁCIL:

```bash
# Volver a fase anterior
./scripts/verification-phase-deploy.sh rollback silent

# O ir a una fase específica
./scripts/verification-phase-deploy.sh flagging
```

**Tiempo de rollback:** 5 minutos
**Downtime de usuarios:** 0 minutos (reversible sin impacto)

### Causas Comunes de Rollback:

1. **Fase 2: Falsos positivos > 10%**

    ```bash
    ./scripts/verification-phase-deploy.sh rollback silent
    # Acción: Ajustar reglas en Tarea 4
    ```

2. **Fase 3: Retry rate > 20%**

    ```bash
    ./scripts/verification-phase-deploy.sh rollback flagging
    # Acción: Activar Phase 4 Tuning
    ```

3. **Fase 4: Latencia > 10s o falla retry**
    ```bash
    ./scripts/verification-phase-deploy.sh rollback reject
    # Acción: Revisar configuración de retry
    ```

---

## 📊 Monitoreo Continuo

### Comandos Útiles

```bash
# Ver estado actual
./scripts/verification-phase-deploy.sh status

# Métricas últimas 24 horas
php artisan verification:metrics --window=24

# Métricas como JSON (para scripts)
php artisan verification:metrics --json

# Métricas últimas 7 días
php artisan verification:metrics --window=168
```

### Alertas Recomendadas

Configure alertas automáticas para:

```yaml
Alert 1: "Error rate > 20%"
  Action: Notificar al team

Alert 2: "Retry rate > 15%"
  Action: Considerar Phase 4 Tuning

Alert 3: "Phase cambió sin autorización"
  Action: Investigar inmediatamente

Alert 4: "Latencia promedio > 8s (en Phase 4)"
  Action: Possible performance issue
```

---

## ✅ Checklist de Despliegue

```
PHASE 1 (SILENT):
  ☐ Configurable .env con VERIFICATION_ENABLED=true
  ☐ Deployado con ./scripts/verification-phase-deploy.sh silent
  ☐ Monitoreo activo 24h
  ☐ Datos analizados, error_rate conocido

PHASE 2 (FLAGGING):
  ☐ error_rate < 15% confirmado
  ☐ Deployado con ./scripts/verification-phase-deploy.sh flagging
  ☐ Usuarios ven banderas correctamente
  ☐ Falsos positivos < 10% medido

PHASE 3 (REJECT):
  ☐ Reglas validadas y calibradas
  ☐ Deployado con ./scripts/verification-phase-deploy.sh reject
  ☐ Retry rate medido por 24h
  ☐ Retry rate < 10% (o decidida otra opción)

PHASE 4 (TUNING - OPCIONAL):
  ☐ Retry rate > 5-10% justifica Phase 4
  ☐ Latencia +2s es aceptable
  ☐ Token cost es viable
  ☐ Deployado con ./scripts/verification-phase-deploy.sh tuning
  ☐ Success rate de reintentos > 30%
```

---

## 🆘 Troubleshooting

### Problema: Script dice "Environment check failed"

```bash
# Solución
php artisan config:cache
./scripts/verification-phase-deploy.sh status
```

### Problema: Config no cambia después de deploy

```bash
# Solución
php artisan config:cache
php artisan config:clear
./scripts/verification-phase-deploy.sh status
```

### Problema: Métricas no se actualizan

```bash
# Verificar que event_store tiene datos
php artisan tinker
>>> DB::table('event_store')->count()
```

### Problema: No puedo hacer rollback

```bash
# Rollback manual
VERIFICATION_PHASE=silent php artisan config:cache

# O editar .env directamente
vim .env
# Cambiar VERIFICATION_PHASE a fase anterior
php artisan config:cache
```

---

## 📞 Soporte

Para problemas o preguntas:

1. Ver [TAREA5_DEPLOYMENT_ROADMAP.md](TAREA5_DEPLOYMENT_ROADMAP.md) para análisis detallado
2. Ver [TAREA5_VERIFICATION_INTEGRATION.md](TAREA5_VERIFICATION_INTEGRATION.md) para especificación técnica
3. Revisar logs: `storage/logs/verification-phase-deployment.log`

---

## 🎯 Resumen de Fases

| Fase | Modo     | Impacto Usuarios        | Latencia      | Tokens  |
| ---- | -------- | ----------------------- | ------------- | ------- |
| 1    | Silent   | Ninguno                 | Mínimo (<1ms) | +0%     |
| 2    | Flagging | Ven ⚠️ banderas         | Mínimo (<1ms) | +0%     |
| 3    | Reject   | Rechazo 422             | Normal        | +0%     |
| 4    | Tuning   | Recuperación automática | +2-4s         | +10-20% |

---

**Última actualización:** 2026-03-24  
**Versión:** 1.0  
**Status:** Production Ready
