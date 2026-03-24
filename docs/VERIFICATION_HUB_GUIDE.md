# 🔍 Verification Hub - Guía Completa

**Última actualización:** 24 de marzo de 2026  
**Status:** ✅ Production-Ready  
**Versión:** 1.0

---

## 📋 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Acceso y Navegación](#acceso-y-navegación)
3. [Componentes del Hub](#componentes-del-hub)
4. [API Endpoints](#api-endpoints)
5. [Casos de Uso](#casos-de-uso)
6. [Guía de Uso](#guía-de-uso)
7. [Arquitectura Técnica](#arquitectura-técnica)
8. [Troubleshooting](#troubleshooting)

---

## Introducción

El **Verification Hub** es un panel centralizado de control para el sistema de verificación de transiciones de fase de la plataforma Stratos. Permite a administradores:

- ✅ **Monitorear** el estado del scheduler automático en tiempo real
- ✅ **Simular** transiciones sin aplicar cambios (dry-run mode)
- ✅ **Auditar** el historial completo de eventos del sistema
- ✅ **Generar** reportes de cumplimiento
- ✅ **Configurar** canales de notificación (Slack, Email, Database, Logs)

### Características Principales

| Característica           | Descripción                                 |
| ------------------------ | ------------------------------------------- |
| **Real-time Monitoring** | Dashboard en vivo con contadores y timers   |
| **Safety First**         | Modo simulación sin efectos secundarios     |
| **Auditoría Completa**   | Historial de todas las acciones del sistema |
| **Multi-canal**          | Notificaciones por Slack, Email, DB y Logs  |
| **Multi-tenant**         | Datos aislados por organización             |
| **Dark Mode**            | Interfaz completa con soporte dark/light    |
| **Responsive**           | Funciona en desktop, tablet y mobile        |

---

## Acceso y Navegación

### 1. Acceso desde Control Center

**Ruta:** `/controlcenter`

En el landing del Control Center, busca la tarjeta **"Verification Hub"** (con icono ✓ Teal):

```
Control Center Landing
├─ RBAC Manager
├─ AI Agent Supervisor
├─ Quality Sentinel
├─ ... otros módulos ...
└─ 🟢 Verification Hub ← Click aquí
```

### 2. Acceso Directo

**URL:** `http://localhost:8000/deployment/verification-hub`

**Requisitos:**

- ✅ Estar autenticado
- ✅ Tener rol `admin`
- ✅ Organización correcta (multi-tenant scoped)

### 3. Navegación de Tabs

Una vez en el Hub, verás 5 tabs principales:

```
Hub Overview
├─ 📊 OVERVIEW (Default)
│  ├─ Quick Status Cards
│  ├─ Scheduler Status
│  └─ Transition Readiness
│
├─ 🎮 CONTROL
│  ├─ Dry-Run Simulator (izquierda)
│  ├─ Setup Wizard (derecha)
│  ├─ Quick Actions
│  └─ System Controls
│
├─ 🔔 NOTIFICATIONS
│  ├─ Notification History
│  ├─ Filters (Type, Severity, Read)
│  └─ Pagination
│
├─ ⚙️ CONFIGURATION
│  ├─ Channel Setup (Slack, Email, DB, Log)
│  ├─ Alert Thresholds
│  └─ Test Notifications
│
└─ 📋 AUDIT
   ├─ Audit Log Explorer (top)
   └─ Compliance Report Generator (bottom)
```

---

## Componentes del Hub

### 1. **SchedulerStatus** - Estado del Scheduler

**Ubicación:** Tab Overview, Tab Control  
**Archivo:** `resources/js/components/Verification/SchedulerStatus.vue`

**¿Qué muestra?**

- ✅ Estado del scheduler (Enabled/Disabled)
- ✅ Próxima ejecución (countdown en vivo)
- ✅ Última ejecución (timestamp + estado)
- ✅ Historial de últimas 5 ejecuciones

**Interacciones:**

```
[Ejecutar Ahora] → Dispara evaluación inmediata de transiciones
```

**Datos en tiempo real:**

- Auto-refresh cada 5 minutos
- Countdown actualiza cada segundo
- Indicador de estado (verde/amarillo/rojo)

---

### 2. **NotificationCenter** - Centro de Notificaciones

**Ubicación:** Tab Notifications  
**Archivo:** `resources/js/components/Verification/NotificationCenter.vue`

**¿Qué muestra?**

- 📋 Historial completo de notificaciones
- 🔍 Filtros avanzados:
    - Por **Tipo** (Transition, Config Change, Manual, etc.)
    - Por **Gravedad** (Info, Warning, Error)
    - Por **Estado Lectura** (Read, Unread)
- 📄 Paginación (20 items/página)
- 📊 Expandable details (JSON view completo)

**Cómo usar:**

1. Selecciona filtros deseados
2. Filtra por rango de fechas
3. Click en notificación para expandir detalles
4. Exporta si necesitas (future feature)

---

### 3. **ChannelConfig** - Configuración de Canales

**Ubicación:** Tab Configuration  
**Archivo:** `resources/js/components/Verification/ChannelConfig.vue`

**Canales soportados:**

| Canal        | Configuración          | Uso                |
| ------------ | ---------------------- | ------------------ |
| **Slack**    | Webhook URL            | Real-time alertas  |
| **Email**    | Lista de destinatarios | Reportes + alertas |
| **Database** | Días de retención      | Auditoría local    |
| **Logs**     | Nivel de severidad     | Observabilidad     |

**Umbrales de alerta:**

- Confianza mínima (slider: 50-100%)
- Tasa de error máxima (slider: 10-50%)
- Tasa de reintentos (slider: 5-50%)

**Test de Canales:**

```
[Test Slack]  → Envía mensaje test a webhook
[Test Email]  → Envía email a destinatarios
[Test DB]     → Escribe evento de test en BD
[Test Logs]   → Registra en logs del sistema
```

---

### 4. **TransitionReadiness** - Readiness para Transición

**Ubicación:** Tab Overview  
**Archivo:** `resources/js/components/Verification/TransitionReadiness.vue`

**¿Qué muestra?**

- 🎯 Fase actual (badge con color)
- 📅 Días hasta próxima transición
- 📊 3 métricas vs umbral:
    - Confianza (vs 90% requerido)
    - Tasa de error (vs 40% máximo)
    - Tasa de reintentos (vs 20% máximo)
- ⚠️ Advertencias de tamaño de muestra
- 📜 Tabla de transiciones recientes

**Interpretación:**

- Verde ✓ = Métrica cumple criterio
- Rojo ✗ = Métrica no cumple
- Amarillo ⚠️ = Cerca del límite

---

### 5. **DryRunSimulator** - Simulador Sin Riesgos

**Ubicación:** Tab Control (izquierda)  
**Archivo:** `resources/js/components/Verification/DryRunSimulator.vue`

**¿Qué hace?**
Simula una transición de fase **sin aplicar cambios reales**, ideal para:

- ✅ Testing antes de activar
- ✅ Análisis de "qué pasa si..."
- ✅ Identificar gaps de mejora

**Uso:**

```
1. Ingresa a Tab "Control"
2. Panel izquierdo = DryRunSimulator
3. Ajusta sliders (umbrales opcionales)
4. Click "▶️ Ejecutar simulación"
5. Lee resultado + gaps + reporte
```

**Output:**

```json
{
  "current_phase": "flagging",
  "would_transition": true,
  "next_phase": "reject",
  "reason": "error_rate exceds threshold",
  "metrics": { ... },
  "gaps": [
    {
      "metric": "error_rate",
      "current_value": 45,
      "required_value": 40,
      "days_to_meet": 3
    }
  ],
  "summary": "..."
}
```

---

### 6. **SetupWizard** - Asistente de Configuración Inicial

**Ubicación:** Tab Control (derecha)  
**Archivo:** `resources/js/components/Verification/SetupWizard.vue`

**Pasos del Wizard:**

```
1️⃣ Seleccionar Modo
   ├─ Transiciones automáticas
   ├─ Modo híbrido (auto con aprobación)
   └─ Solo monitoreo (manual)

2️⃣ Configurar Modo
   ├─ Si auto: intervalo + max reintentos
   ├─ Si híbrido: umbral de confianza
   └─ Si monitoring: alertas de cambios

3️⃣ Canales de Notificación
   ├─ Slack (webhook)
   ├─ Email (destinatarios)
   ├─ Database (retención)
   └─ Logs (nivel)

4️⃣ Umbrales de Alerta
   ├─ Confianza mínima
   ├─ Error máximo
   ├─ Reintentos máximo
   └─ Tamaño muestra mínimo

5️⃣ Revisión & Guardado
   └─ Confirm → Save
```

**Barra de progreso visual** muestra dónde estás en el proceso.

---

### 7. **AuditLogExplorer** - Explorador de Auditoría

**Ubicación:** Tab Audit (arriba)  
**Archivo:** `resources/js/components/Verification/AuditLogExplorer.vue`

**¿Qué muestra?**

- 📊 Tabla completa de eventos de auditoría
- 🔍 Filtros:
    - Por acción (Transición, Config, Override)
    - Por usuario
    - Por rango de fechas
- 📈 Estadísticas resumidas (total eventos, transiciones, cambios, usuarios)
- 📤 Exportar JSON/CSV
- 🔎 Expandable details (JSON completo)

**Cómo auditar:**

```
1. Click tab "📋 AUDIT"
2. Top section = AuditLogExplorer
3. Usa filtros para buscar
4. Click evento para ver detalles
5. Exporta si necesario
```

---

### 8. **ComplianceReportGenerator** - Generador de Reportes

**Ubicación:** Tab Audit (abajo)  
**Archivo:** `resources/js/components/Verification/ComplianceReportGenerator.vue`

**¿Qué genera?**
Reportes de cumplimiento con:

- Rango de fechas customizable
- Filtros por acción y usuario
- Estadísticas agregadas
- Timeline de eventos
- Exportación (JSON/CSV/PDF)

**Formatos:**

- 📋 **JSON** - Para integración con sistemas
- 📊 **CSV** - Para Excel/análisis
- 📄 **PDF** - Para presentación

**Programación:**

```
Options:
├─ Semanal
├─ Mensual
└─ Trimestral

Se envían automáticamente al correo configurado
```

---

## API Endpoints

### Endpoints Phase 1 (Monitoring)

#### 1. Scheduler Status

```http
GET /api/deployment/verification/scheduler-status
Authorization: Bearer {token}
```

**Response:**

```json
{
    "data": {
        "enabled": true,
        "mode": "auto_transitions",
        "last_run": "2026-03-24T12:30:00Z",
        "next_run": "2026-03-24T13:30:00Z",
        "seconds_until_next": 3600,
        "recent_executions": [
            {
                "id": 1,
                "started_at": "2026-03-24T12:30:00Z",
                "ended_at": "2026-03-24T12:31:00Z",
                "status": "completed",
                "phase_evaluated": "flagging"
            }
        ]
    }
}
```

#### 2. Recent Transitions

```http
GET /api/deployment/verification/transitions
Authorization: Bearer {token}
Query: ?limit=5&page=1
```

**Response:**

```json
{
    "data": [
        {
            "id": 123,
            "from_phase": "silent",
            "to_phase": "flagging",
            "timestamp": "2026-03-24T10:00:00Z",
            "reason": "confidence exceeded 90%",
            "triggered_by": "automatic"
        }
    ],
    "pagination": {
        "total": 42,
        "count": 5,
        "per_page": 5,
        "current_page": 1
    }
}
```

#### 3. Notifications

```http
GET /api/deployment/verification/notifications
Authorization: Bearer {token}
Query: ?type=transition&severity=info&read=false&limit=20&page=1
```

**Response:**

```json
{
  "data": [
    {
      "id": 456,
      "type": "phase_transition",
      "severity": "info",
      "message": "Transición a fase flagging completada",
      "read": false,
      "created_at": "2026-03-24T10:00:00Z"
    }
  ],
  "pagination": { ... }
}
```

#### 4. Test Notification

```http
POST /api/deployment/verification/test-notification
Authorization: Bearer {token}
Content-Type: application/json

{
  "channel": "slack"  // or email, database, log
}
```

**Response:**

```json
{
    "success": true,
    "message": "Test message sent to Slack successfully",
    "channel": "slack"
}
```

#### 5. Configuration

```http
GET /api/deployment/verification/configuration
Authorization: Bearer {token}
```

**Response:**

```json
{
    "data": {
        "channels": {
            "slack": {
                "enabled": true,
                "webhook": "https://hooks.slack.com/..."
            },
            "email": {
                "enabled": true,
                "recipients": ["admin@example.com"]
            },
            "database": {
                "enabled": true,
                "retention_days": 90
            },
            "log": {
                "enabled": true,
                "level": "info"
            }
        },
        "thresholds": {
            "confidence_min": 90,
            "error_rate_max": 40,
            "retry_rate_max": 20,
            "sample_size_min": 100
        }
    }
}
```

---

### Endpoints Phase 2 (Advanced)

#### 6. Audit Logs

```http
GET /api/deployment/verification/audit-logs
Authorization: Bearer {token}
Query: ?action=phase_transition&user_id=1&date_from=2026-03-01&date_to=2026-03-31&limit=50&page=1
```

**Response:**

```json
{
  "data": [
    {
      "id": 789,
      "action": "phase_transition",
      "user": { "name": "Admin User" },
      "phase_from": "silent",
      "phase_to": "flagging",
      "reason": "confidence threshold met",
      "triggered_by": "automatic",
      "created_at": "2026-03-24T10:00:00Z"
    }
  ],
  "pagination": { ... }
}
```

#### 7. Dry-Run Simulation

```http
POST /api/deployment/verification/dry-run
Authorization: Bearer {token}
Content-Type: application/json

{
  "confidence_threshold": 90,
  "error_rate_threshold": 40,
  "retry_rate_threshold": 20
}
```

**Response:**

```json
{
    "data": {
        "current_phase": "flagging",
        "would_transition": true,
        "next_phase": "reject",
        "reason": "error_rate exceeds threshold",
        "summary": "Sistema listo para transición",
        "metrics": {
            "confidence": 92,
            "error_rate": 45,
            "retry_rate": 18,
            "sample_size": 250
        },
        "gaps": [
            {
                "metric": "error_rate",
                "current_value": 45,
                "required_value": 40,
                "days_to_meet": 3
            }
        ]
    }
}
```

#### 8. Compliance Report

```http
GET /api/deployment/verification/compliance-report
Authorization: Bearer {token}
Query: ?date_from=2026-03-01&date_to=2026-03-31&format=json
```

**Response:**

```json
{
    "data": {
        "summary": {
            "total_events": 245,
            "phase_transitions": 12,
            "config_changes": 35,
            "manual_overrides": 5,
            "unique_users": 3,
            "by_trigger": {
                "automatic": 200,
                "manual": 45
            }
        },
        "events": [
            {
                "timestamp": "2026-03-24T10:00:00Z",
                "action": "phase_transition",
                "user": "System",
                "phase_from": "silent",
                "phase_to": "flagging",
                "reason": "...",
                "triggered_by": "automatic"
            }
        ]
    }
}
```

---

## Casos de Uso

### Caso 1: Monitor Diario

**Objetivo:** Verificar que el sistema está funcionando correctamente

**Pasos:**

1. Navega a `/controlcenter` → "Verification Hub"
2. Revisa tab **Overview**:
    - ✅ Scheduler Status: ¿está enabled?
    - ✅ Próxima ejecución: ¿próxima está programada?
    - ✅ Transition Readiness: ¿métricas en verde?
3. Revisa tab **Notifications**: ¿hay alertas no leídas?
4. Si todo está bien → Continúa

**Indicadores de alerta:**

- ❌ Scheduler deshabilitado
- ❌ Transición fallida recientemente
- ❌ Métrica en rojo criterio

---

### Caso 2: Configurar Canales de Notificación

**Objetivo:** Recibir alertas por Slack y Email

**Pasos:**

1. Tab **Configuration**
2. **Slack**: Toggle ON, pega webhook URL
3. **Email**: Toggle ON, añade correos
4. **Database**: Toggle ON, set 90 días retención
5. **Logs**: Toggle ON, set level "warning"
6. Ajusta sliders de thresholds
7. Click **Test Slack** → Verifica en Slack
8. Click **Test Email** → Revisa inbox
9. Click **Save**

**Resultado:** Sistema notificará en múltiples canales

---

### Caso 3: Simular Transición Antes de Actualizar

**Objetivo:** Validar que la próxima transición será exitosa

**Pasos:**

1. Tab **Control**
2. Panel izquierdo: **Dry-Run Simulator**
3. Lee "Current Phase" y "Would Transition?"
4. Revisa **Gaps** (áreas de mejora necesarias)
5. Si `would_transition == true` → Sistema listo
6. Si hay gaps → Nota cuántos días faltan

**Uso avanzado:**

- Ajusta sliders para "qué pasa si bajamos confianza a 85%"
- Simula escenarios diferentes
- Exporta reporte si necesario

---

### Caso 4: Auditar Cambios del Sistema

**Objetivo:** Investigar quién cambió la configuración

**Pasos:**

1. Tab **Audit**
2. Top section: **Audit Log Explorer**
3. Filtros:
    - Action: "config_change"
    - Date Range: últimas 2 semanas
4. Click evento para ver detalles completos
5. Exporta listado como CSV/JSON si necesario

**Resultado:** Trazabilidad completa de cambios

---

### Caso 5: Generar Reporte de Cumplimiento

**Objetivo:** Documentar actividad para auditoría externa

**Pasos:**

1. Tab **Audit**
2. Bottom section: **Compliance Report Generator**
3. Rango de fechas: "Last quarter"
4. Click **📊 Generar reporte**
5. Revisa summary stats
6. Click **⬇️ Descargar** → Formato "PDF"
7. Envía a auditores

**Datos incluidos:**

- Total eventos por tipo
- Usuarios involucrados
- Timestamp de cada acción
- Razones de transiciones

---

## Guía de Uso

### Primera Vez: Setup Inicial

```
1. Accede: /deployment/verification-hub
2. Tab "Control" → Right panel "Setup Wizard"
3. Sigue los 5 pasos:
   ✓ Selecciona modo (recomendado: "monitoring_only" para start)
   ✓ Configura notificaciones (opcional inicialmente)
   ✓ Ajusta umbrales (defaults están bien)
   ✓ Revisa configuración
   ✓ Guarda
4. Tab "Configuration" → Test cada canal
5. Listo! El sistema está monitoring
```

### Operación Diaria

```
Morning:
├─ 08:00 - Revisa Overview (todo verde?)
├─ 09:00 - Revisa Notifications (hay alertas?)
└─ Si problema → Revisa Audit

During Day:
├─ Scheduler automático evalúa cada hora
├─ Notificaciones en Slack/Email si hay cambios
└─ Puedes monitorear desde Hub

Before Weekend:
├─ Tab Audit → Compliance Report
├─ Email del reporte a stakeholders
└─ Backup logs si necesario
```

### Troubleshooting Operacional

**P: Scheduler no evauló en la última hora**

```
R: 1. Revisa /api/deployment/verification/scheduler-status
   2. ¿enabled=true?
   3. Si false → Click "Ejecutar Ahora"
   4. Si problem persiste → Check Laravel queue
```

**P: No recibo notificaciones en Slack**

```
R: 1. Tab Configuration → Test Slack
   2. ¿Apareció mensaje de test?
   3. Si no → Webhook URL inválida
   4. Actualiza webhook en configuración
```

**P: Transición está "stuck" en una fase**

```
R: 1. Tab Audit → Audit Log Explorer
   2. ¿Última transición cuándo fue?
   3. Revisa "reason" de por qué no transaciona
   4. DryRun → Identifica gap
   5. Soluciona gap → Manual trigger si necesario
```

---

## Arquitectura Técnica

### Stack Tecnológico

```
Backend:
├─ Laravel 12
├─ PHP 8.4
├─ Eloquent ORM
├─ Sanctum (API Auth)
└─ Multi-tenant via organization_id

Frontend:
├─ Vue 3 + TypeScript
├─ Composition API
├─ Tailwind CSS v4
├─ Vuetify 3 (optional components)
└─ Inertia.js v2

Database:
├─ VerificationAuditLog (audit trail)
├─ VerificationNotification (notification history)
├─ Cache (scheduler state, metrics)
└─ Multi-tenant scoping: organization_id

API:
├─ 8 REST endpoints
├─ JSON response format
├─ Pagination support
├─ Error handling with status codes
└─ CSRF protection + Bearer tokens
```

### Directorio de Archivos

```
app/
├─ Http/Controllers/Deployment/
│  └─ VerificationHubController.php (430 LOC)
│     ├─ schedulerStatus()
│     ├─ recentTransitions()
│     ├─ notifications()
│     ├─ testNotification()
│     ├─ configuration()
│     ├─ auditLogs()
│     ├─ dryRunSimulation()
│     └─ complianceReport()

resources/js/
├─ Pages/Deployment/
│  └─ VerificationHub.vue (Master page + 5 tabs)
│
├─ components/Verification/
│  ├─ SchedulerStatus.vue (232 LOC)
│  ├─ NotificationCenter.vue (210 LOC)
│  ├─ ChannelConfig.vue (320 LOC)
│  ├─ TransitionReadiness.vue (260 LOC)
│  ├─ AuditLogExplorer.vue (350 LOC)
│  ├─ DryRunSimulator.vue (350 LOC)
│  ├─ SetupWizard.vue (300 LOC)
│  └─ ComplianceReportGenerator.vue (300 LOC)

routes/
├─ web.php (8 API routes + 1 web route)
└─ Middleware: auth, verified, role:admin

database/
├─ Models/VerificationAuditLog.php
├─ Models/VerificationNotification.php
└─ Migrations (existing)
```

### Flujo de Datos

```
User Action
    ↓
Vue Component (VerificationHub.vue tab)
    ↓
API Endpoint (/api/deployment/verification/*)
    ↓
VerificationHubController method
    ↓
Multi-tenant scope filter (organization_id)
    ↓
Eloquent Query / Service Logic
    ↓
Cache (if applicable)
    ↓
JSON Response
    ↓
Vue Component receives + displays
```

### Security Model

```
Authentication:
├─ Laravel Sanctum tokens
├─ Bearer token in Authorization header
├─ Session-based fallback

Authorization:
├─ Route middleware: auth, verified, role:admin
├─ Policy checks: VerificationHubPolicy (planned)
├─ Multi-tenant scope: all queries filtered by organization_id

Data Protection:
├─ No cross-tenant data leakage
├─ Sensitive config (webhooks) hashed in DB
├─ Audit trail for compliance
└─ CSRF token on form submissions
```

---

## Troubleshooting

### Problemas Comunes

#### ❌ "Access Denied" al acceder a `/deployment/verification-hub`

**Causa:** No tienes rol `admin`

**Solución:**

```
1. Verifica tu rol en usuarios/settings
2. Solicita a administrador que te asigne rol admin
3. Logout + Login para refrescar tokens
```

#### ❌ No se cargan los datos en el Hub

**Causa:** Problema con API endpoint

**Solución:**

```
1. Abre browser DevTools (F12)
2. Tab Network
3. Refres
ca página
4. Busca requests a /api/deployment/verification/*
5. Verifica que tienen status 200
6. Si 401/403 → Token issue
7. Si 500 → Check Laravel logs: storage/logs/
```

#### ❌ Dry-Run Simulator no funciona

**Causa:** Posible error en cálculo de métricas

**Solución:**

```
1. Revisa si VerificationMetricsService está disponible
2. Verifica que existen datos en base de datos
3. Check: app/Services/VerificationMetricsService.php
4. Si metric está null → Source data missing
```

#### ❌ Notificaciones de Slack no se envían

**Causa:** Webhook inválido o queda inactivo

**Solución:**

```
1. Tab Configuration → Click [Test Slack]
2. ¿Recibiste message en Slack?
3. Si no:
   a. Regenera webhook en Slack app settings
   b. Copia nuevo URL
   c. Pega en ChannelConfig.vue
   d. Test nuevamente
4. Si sí recibiste → Revisa si hay events en audit
```

#### ❌ Base de datos está creciendo demasiado

**Causa:** Retention policy excesiva

**Solución:**

```
1. Tab Configuration
2. Database section → Reduce retention_days
   (ej: 90 días en lugar de 365)
3. Ejecuta artisan command para limpiar:
   php artisan verification:cleanup-logs --days=90
4. Monitor disk space
```

### Debugging Tips

#### Ver todos los requests API

```javascript
// En DevTools Console:
fetch('/api/deployment/verification/scheduler-status', {
    headers: { Authorization: `Bearer ${localStorage.sanctum_token}` },
})
    .then((r) => r.json())
    .then((d) => console.log(d));
```

#### Revisar Laravel logs en tiempo real

```bash
tail -f storage/logs/laravel.log | grep -i verification
```

#### Verificar estado de la base de datos

```bash
php artisan tinker

>>> DB::table('verification_audit_logs')->count()
=> 1250

>>> DB::table('verification_notifications')->count()
=> 430
```

---

## Próximos Pasos / Roadmap

### Phase 3 (Planned)

- [ ] Unified Control Hub (todas las páginas en una)
- [ ] Advanced dashboards (gráficos, predicciones)
- [ ] Automated remediation (acciones automáticas)
- [ ] Machine Learning para detectar anomalías
- [ ] Mobile app nativa

### Mejoras Futuras

- [ ] Soporte para más canales (Telegram, Teams)
- [ ] Integración con PagerDuty
- [ ] Predicción de transiciones con IA
- [ ] Custom thresholds por departamento
- [ ] Rate limiting basado en usage

---

## Recursos Adicionales

- 📖 [README principal](/README.md)
- 🏗️ [Arquitectura del sistema](/docs/DIA6_ARQUITECTURA_COMPLETA_FRONTEND_BACKEND.md)
- 🔧 [API Endpoints](/docs/dia5_api_endpoints.md)
- 🎓 [Guía Frontend](/docs/DIA6_GUIA_INICIO_FRONTEND.md)

---

## Soporte

**¿Preguntas o problemas?**

1. Revisa [Troubleshooting](#troubleshooting)
2. Consulta los logs: `storage/logs/laravel.log`
3. Contacta al equipo de desarrollo
4. Abre issue en repositorio

---

**Documento mantenido por:** DevTeam Stratos  
**Última actualización:** 24.03.2026  
**Version:** 1.0.0
