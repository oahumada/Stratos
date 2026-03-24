# 🔌 Verification Hub - API Reference

**Base URL:** `http://localhost:8000`  
**API Prefix:** `/api/deployment/verification`  
**Authentication:** `Bearer {sanctum_token}`  
**Content-Type:** `application/json`  

---

## 📋 Tabla de Endpoints

| Método | Endpoint | Descripción | Fase |
|--------|----------|-------------|------|
| GET | `/scheduler-status` | Estado del scheduler automático | 1 |
| GET | `/transitions` | Historial de transiciones de fase | 1 |
| GET | `/notifications` | Notificaciones con filtros | 1 |
| POST | `/test-notification` | Envía mensaje de test a canal | 1 |
| GET | `/configuration` | Configuración de sistema | 1 |
| GET | `/audit-logs` | Historial de auditoría | 2 |
| POST | `/dry-run` | Simula transición sin cambios | 2 |
| GET | `/compliance-report` | Genera reportes de cumplimiento | 2 |

---

## Phase 1 Endpoints

### 1. GET /scheduler-status

**Descripción:** Obtiene el estado actual del scheduler automático

**Autenticación:** ✅ Requerida (admin)

**Parámetros:** Ninguno

**Ejemplo de Request:**
```bash
curl -X GET "http://localhost:8000/api/deployment/verification/scheduler-status" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response 200 OK:**
```json
{
  "data": {
    "enabled": true,
    "mode": "auto_transitions",
    "last_run": "2026-03-24T12:30:00Z",
    "last_run_status": "completed",
    "next_run": "2026-03-24T13:30:00Z",
    "seconds_until_next": 3599,
    "recent_executions": [
      {
        "id": 1,
        "started_at": "2026-03-24T12:30:00Z",
        "ended_at": "2026-03-24T12:31:00Z",
        "duration_seconds": 60,
        "status": "completed",
        "phase_evaluated": "flagging",
        "phase_changed": false,
        "reason": "Metrics don't meet transition criteria"
      },
      {
        "id": 2,
        "started_at": "2026-03-24T11:30:00Z",
        "ended_at": "2026-03-24T11:31:00Z",
        "duration_seconds": 45,
        "status": "completed",
        "phase_evaluated": "flagging",
        "phase_changed": false,
        "reason": "Metrics don't meet transition criteria"
      }
    ]
  }
}
```

**Response 401 Unauthorized:**
```json
{
  "message": "Unauthenticated."
}
```

**Response 403 Forbidden:**
```json
{
  "message": "User does not have required permissions."
}
```

**Vue Component Usage:**
```typescript
// SchedulerStatus.vue
const { data: schedulerData } = await fetch(
  '/api/deployment/verification/scheduler-status',
  { headers: { 'Authorization': `Bearer ${token}` } }
).then(r => r.json())

// Format for display
const nextRunIn = calculateCountdown(schedulerData.seconds_until_next)
const lastRunStatus = schedulerData.last_run_status // to determine icon color
```

---

### 2. GET /transitions

**Descripción:** Obtiene historial de transiciones de fase (puede ser muy largo)

**Autenticación:** ✅ Requerida (admin)

**Parámetros Query:**
```
limit=10        // Items por página (default: 10, max: 50)
page=1          // Número de página (default: 1)
```

**Ejemplo de Request:**
```bash
curl -X GET "http://localhost:8000/api/deployment/verification/transitions?limit=5&page=1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response 200 OK:**
```json
{
  "data": [
    {
      "id": 1001,
      "from_phase": "silent",
      "to_phase": "flagging",
      "timestamp": "2026-03-24T10:00:00Z",
      "reason": "confidence exceeded 90% threshold (current: 92%)",
      "triggered_by": "automatic",
      "executed_by": null,
      "duration_seconds": 5
    },
    {
      "id": 1000,
      "from_phase": "hidden",
      "to_phase": "silent",
      "timestamp": "2026-03-23T15:30:00Z",
      "reason": "system initialized, starting at silent phase",
      "triggered_by": "system",
      "executed_by": "System",
      "duration_seconds": 2
    }
  ],
  "pagination": {
    "total": 42,
    "count": 5,
    "per_page": 5,
    "current_page": 1,
    "last_page": 9,
    "from": 1,
    "to": 5,
    "path": "http://localhost:8000/api/deployment/verification/transitions",
    "more_pages": true
  }
}
```

**Response 200 OK (Last page):**
```json
{
  "data": [
    {
      "id": 3,
      "from_phase": "verify",
      "to_phase": "flagging",
      "timestamp": "2026-03-01T09:00:00Z",
      "reason": "manual override by admin to test",
      "triggered_by": "manual",
      "executed_by": "Admin User",
      "duration_seconds": 8
    }
  ],
  "pagination": {
    "total": 42,
    "count": 1,
    "per_page": 5,
    "current_page": 9,
    "last_page": 9,
    "from": 41,
    "to": 42,
    "more_pages": false
  }
}
```

**Manejo en Frontend:**
```typescript
// TransitionReadiness.vue
const loadTransitions = async (page = 1) => {
  const response = await fetch(
    `/api/deployment/verification/transitions?limit=5&page=${page}`
  ).then(r => r.json())
  
  transitions.value = response.data
  pagination.value = response.pagination
  
  // Mostrar paginación solo si hay más de 1 página
  if (response.pagination.last_page > 1) {
    showPagination.value = true
  }
}
```

---

### 3. GET /notifications

**Descripción:** Obtiene notificaciones del sistema con soporte para filtros avanzados

**Autenticación:** ✅ Requerida (admin)

**Parámetros Query:**
```
type=transition           // Tipo: transition, config_change, warning, error
severity=info            // Severidad: info, warning, error
read=false               // Estado lectura: true, false, all (default: all)
limit=20                 // Items per page (max: 100)
page=1                   // Página
date_from=2026-03-01     // ISO 8601
date_to=2026-03-31       // ISO 8601
```

**Ejemplo de Request:**
```bash
curl -X GET "http://localhost:8000/api/deployment/verification/notifications?type=transition&read=false&limit=10" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response 200 OK:**
```json
{
  "data": [
    {
      "id": 5001,
      "type": "phase_transition",
      "severity": "info",
      "title": "Phase Transition: Silent → Flagging",
      "message": "System transitioned to Flagging phase. Confidence: 92%",
      "read": false,
      "read_at": null,
      "channel_sent": ["slack", "email", "database"],
      "created_at": "2026-03-24T10:00:00Z",
      "metadata": {
        "phase_from": "silent",
        "phase_to": "flagging",
        "confidence": 92,
        "reason": "confidence exceeded threshold"
      }
    },
    {
      "id": 5000,
      "type": "configuration_changed",
      "severity": "warning",
      "title": "Configuration Updated",
      "message": "Slack webhook URL changed by Admin",
      "read": true,
      "read_at": "2026-03-24T10:05:00Z",
      "channel_sent": ["database"],
      "created_at": "2026-03-24T10:00:30Z",
      "metadata": {
        "changed_by": "Admin User",
        "field": "slack_webhook_url",
        "old_value": "***",
        "new_value": "***"
      }
    }
  ],
  "pagination": {
    "total": 247,
    "count": 10,
    "per_page": 10,
    "current_page": 1,
    "last_page": 25
  }
}
```

**Frontend Integration:**
```typescript
// NotificationCenter.vue
const filters = reactive({
  type: 'all',
  severity: 'all',
  read: 'all'
})

const applyFilters = async () => {
  const queryParams = new URLSearchParams()
  if (filters.type !== 'all') queryParams.append('type', filters.type)
  if (filters.severity !== 'all') queryParams.append('severity', filters.severity)
  if (filters.read !== 'all') queryParams.append('read', filters.read)
  queryParams.append('limit', '20')
  
  const response = await fetch(
    `/api/deployment/verification/notifications?${queryParams}`
  ).then(r => r.json())
  
  notifications.value = response.data
}
```

---

### 4. POST /test-notification

**Descripción:** Envía un mensaje de prueba a un canal específico

**Autenticación:** ✅ Requerida (admin)

**Body (JSON):**
```json
{
  "channel": "slack"  // or: email, database, log
}
```

**Ejemplo de Request:**
```bash
curl -X POST "http://localhost:8000/api/deployment/verification/test-notification" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "channel": "slack"
  }'
```

**Response 200 OK:**
```json
{
  "success": true,
  "message": "Test notification sent to Slack successfully",
  "channel": "slack",
  "timestamp": "2026-03-24T12:00:00Z"
}
```

**Response 400 Bad Request (Invalid channel):**
```json
{
  "message": "Invalid channel. Supported: slack, email, database, log",
  "errors": {
    "channel": ["The channel must be one of: slack, email, database, log"]
  }
}
```

**Response 422 Unprocessable Entity (Missing channel):**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "channel": ["The channel field is required."]
  }
}
```

**Vue Component Usage:**
```typescript
// ChannelConfig.vue
const testChannel = async (channel: string) => {
  try {
    const response = await fetch(
      '/api/deployment/verification/test-notification',
      {
        method: 'POST',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ channel })
      }
    ).then(r => r.json())
    
    if (response.success) {
      showToast(`✅ Test message sent to ${channel}`)
    }
  } catch (error) {
    showToast(`❌ Failed to send test to ${channel}`, 'error')
  }
}
```

---

### 5. GET /configuration

**Descripción:** Obtiene la configuración completa del sistema

**Autenticación:** ✅ Requerida (admin)

**Parámetros:** Ninguno

**Ejemplo de Request:**
```bash
curl -X GET "http://localhost:8000/api/deployment/verification/configuration" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response 200 OK:**
```json
{
  "data": {
    "mode": "auto_transitions",
    "enabled": true,
    "channels": {
      "slack": {
        "enabled": true,
        "webhook_url": "https://hooks.slack.com/services/T****/B****/****",
        "webhook_url_masked": "https://hooks.slack.com/services/T.../B.../...",
        "last_tested": "2026-03-24T11:59:00Z",
        "test_status": "success"
      },
      "email": {
        "enabled": true,
        "recipients": ["admin@company.com", "ops@company.com"],
        "last_tested": "2026-03-24T11:58:00Z",
        "test_status": "success"
      },
      "database": {
        "enabled": true,
        "retention_days": 90,
        "current_records": 1240
      },
      "log": {
        "enabled": true,
        "level": "info",
        "file": "storage/logs/verification.log"
      }
    },
    "thresholds": {
      "confidence_min": 90,
      "error_rate_max": 40,
      "retry_rate_max": 20,
      "sample_size_min": 100
    },
    "auto_retry": {
      "enabled": true,
      "max_attempts": 3,
      "backoff_seconds": 300
    },
    "organization": {
      "id": 1,
      "name": "Acme Corp"
    }
  }
}
```

**Frontend Integration:**
```typescript
// ChannelConfig.vue
onMounted(async () => {
  const response = await fetch(
    '/api/deployment/verification/configuration'
  ).then(r => r.json())
  
  // Populate form with current settings
  channels.value = response.data.channels
  thresholds.value = response.data.thresholds
})
```

---

## Phase 2 Endpoints

### 6. GET /audit-logs

**Descripción:** Obtiene logs de auditoría con filtros avanzados

**Autenticación:** ✅ Requerida (admin)

**Parámetros Query:**
```
action=phase_transition     // Tipo de acción
user_id=1                   // Usuario que ejecutó
date_from=2026-03-01       // Fecha inicio (ISO 8601)
date_to=2026-03-31         // Fecha fin (ISO 8601)
search=string              // Search en mensaje/usuario
limit=50                   // Items per page (max: 100)
page=1                     // Página
```

**Ejemplo de Request:**
```bash
curl -X GET "http://localhost:8000/api/deployment/verification/audit-logs?action=phase_transition&date_from=2026-03-01&date_to=2026-03-31&limit=50" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response 200 OK:**
```json
{
  "data": [
    {
      "id": 10001,
      "action": "phase_transition",
      "user_id": null,
      "user_name": "System",
      "phase_from": "silent",
      "phase_to": "flagging",
      "reason": "confidence exceeded 90% (current: 92%)",
      "triggered_by": "automatic",
      "metadata": {
        "confidence": 92,
        "error_rate": 30,
        "retry_rate": 15,
        "sample_size": 250,
        "previous_phase": "silent",
        "new_phase": "flagging"
      },
      "created_at": "2026-03-24T10:00:00Z"
    },
    {
      "id": 10000,
      "action": "configuration_changed",
      "user_id": 5,
      "user_name": "John Admin",
      "description": "Updated Slack webhook URL",
      "field": "slack_webhook_url",
      "old_value": "***",
      "new_value": "***",
      "triggered_by": "manual",
      "metadata": {
        "changed_by_user_id": 5,
        "ip_address": "192.168.1.100"
      },
      "created_at": "2026-03-24T09:30:00Z"
    }
  ],
  "pagination": {
    "total": 342,
    "per_page": 50,
    "current_page": 1,
    "last_page": 7
  },
  "summary": {
    "total_events": 342,
    "by_action": {
      "phase_transition": 45,
      "configuration_changed": 120,
      "manual_override": 10,
      "test_message_sent": 167
    },
    "by_user": {
      "System": 200,
      "John Admin": 120,
      "Jane Operator": 22
    }
  }
}
```

**Frontend Integration:**
```typescript
// AuditLogExplorer.vue
const searchAndFilter = async () => {
  const params = new URLSearchParams({
    action: filters.action || 'all',
    date_from: dateRange.from,
    date_to: dateRange.to,
    search: searchQuery.value,
    limit: '50',
    page: currentPage.value.toString()
  })
  
  const response = await fetch(
    `/api/deployment/verification/audit-logs?${params}`
  ).then(r => r.json())
  
  auditLogs.value = response.data
  summary.value = response.summary
}

// Exportar como CSV
const exportCSV = () => {
  const csv = convertToCSV(auditLogs.value)
  downloadFile(csv, 'audit-logs.csv')
}
```

---

### 7. POST /dry-run

**Descripción:** Simula una transición de fase sin aplicar cambios reales

**Autenticación:** ✅ Requerida (admin)

**Body (JSON, todos opcionales):**
```json
{
  "confidence_threshold": 90,      // Override para simulación
  "error_rate_threshold": 40,
  "retry_rate_threshold": 20,
  "sample_size_minimum": 100
}
```

**Ejemplo de Request:**
```bash
curl -X POST "http://localhost:8000/api/deployment/verification/dry-run" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "confidence_threshold": 85,
    "error_rate_threshold": 45
  }'
```

**Response 200 OK:**
```json
{
  "data": {
    "current_phase": "flagging",
    "would_transition": true,
    "next_phase": "reject",
    "reason": "error_rate exceeds configured threshold (45 > 40)",
    "can_transition": true,
    "summary": "System is ready to transition to reject phase with current metrics",
    
    "metrics": {
      "confidence": 92,
      "confidence_threshold": 85,
      "confidence_met": true,
      
      "error_rate": 45,
      "error_rate_threshold": 45,
      "error_rate_met": true,
      
      "retry_rate": 18,
      "retry_rate_threshold": 20,
      "retry_rate_met": true,
      
      "sample_size": 250,
      "sample_size_minimum": 100,
      "sample_size_met": true
    },
    
    "gaps": [],
    "days_to_transition": 0,
    
    "estimated_impact": {
      "tests_affected": 450,
      "services_impacted": ["service-a", "service-b", "service-c"],
      "estimated_downtime_minutes": 0,
      "rollback_available": true
    },
    
    "simulation_run_at": "2026-03-24T12:15:00Z",
    "simulated_with_overrides": {
      "confidence_threshold": 85,
      "error_rate_threshold": 45
    }
  }
}
```

**Response 200 OK (con gaps):**
```json
{
  "data": {
    "current_phase": "flagging",
    "would_transition": false,
    "next_phase": "reject",
    "reason": "error_rate does not meet threshold (45 > 40)",
    "can_transition": false,
    "summary": "System is NOT ready to transition. 1 gap needs to be addressed",
    
    "metrics": {
      "confidence": 92,
      "confidence_threshold": 90,
      "confidence_met": true,
      
      "error_rate": 45,
      "error_rate_threshold": 40,
      "error_rate_met": false,
      
      "retry_rate": 18,
      "retry_rate_threshold": 20,
      "retry_rate_met": true,
      
      "sample_size": 250,
      "sample_size_minimum": 100,
      "sample_size_met": true
    },
    
    "gaps": [
      {
        "metric": "error_rate",
        "current_value": 45,
        "required_value": 40,
        "gap_percentage": 12.5,
        "days_to_meet": 3,
        "recommendation": "Reduce error rate by 5%. Focus on service-b failures."
      }
    ],
    "days_to_transition": 3
  }
}
```

**Vue Component Usage:**
```typescript
// DryRunSimulator.vue
const runSimulation = async () => {
  isLoading.value = true
  const response = await fetch(
    '/api/deployment/verification/dry-run',
    {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        confidence_threshold: confidenceSlider.value,
        error_rate_threshold: errorRateSlider.value,
        retry_rate_threshold: retryRateSlider.value
      })
    }
  ).then(r => r.json())
  
  simulationResult.value = response.data
  isLoading.value = false
}

// Exportar resultado
const exportResult = () => {
  const pdf = generatePDF(simulationResult.value)
  downloadFile(pdf, `dry-run-${new Date().toISOString()}.pdf`)
}
```

---

### 8. GET /compliance-report

**Descripción:** Genera un reporte de cumplimiento con datos de auditoría

**Autenticación:** ✅ Requerida (admin)

**Parámetros Query:**
```
date_from=2026-03-01        // ISO 8601
date_to=2026-03-31          // ISO 8601
action=phase_transition     // Filtrar por acción (opcional)
user_id=1                   // Filtrar por usuario (opcional)
format=json                 // json, csv, pdf
```

**Ejemplo de Request:**
```bash
curl -X GET "http://localhost:8000/api/deployment/verification/compliance-report?date_from=2026-03-01&date_to=2026-03-31&format=json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Accept: application/json"
```

**Response 200 OK (JSON):**
```json
{
  "data": {
    "report_period": {
      "from": "2026-03-01T00:00:00Z",
      "to": "2026-03-31T23:59:59Z",
      "days": 31,
      "generated_at": "2026-03-24T12:20:00Z"
    },
    
    "summary": {
      "total_events": 342,
      "phase_transitions": 12,
      "configuration_changes": 89,
      "manual_overrides": 5,
      "test_messages": 236,
      "unique_users": 4,
      "unique_days_with_activity": 28
    },
    
    "by_action": {
      "phase_transition": {
        "count": 12,
        "percentage": 3.5
      },
      "configuration_changed": {
        "count": 89,
        "percentage": 26.0
      },
      "manual_override": {
        "count": 5,
        "percentage": 1.5
      },
      "test_message_sent": {
        "count": 236,
        "percentage": 69.0
      }
    },
    
    "by_trigger": {
      "automatic": {
        "count": 287,
        "percentage": 84.0
      },
      "manual": {
        "count": 55,
        "percentage": 16.0
      }
    },
    
    "by_user": {
      "System": {
        "count": 287,
        "percentage": 84.0
      },
      "John Admin": {
        "count": 45,
        "percentage": 13.2
      },
      "Jane Operator": {
        "count": 10,
        "percentage": 2.8
      }
    },
    
    "phase_transitions_detail": [
      {
        "from": "silent",
        "to": "flagging",
        "count": 3,
        "dates": ["2026-03-05", "2026-03-12", "2026-03-19"]
      },
      {
        "from": "flagging",
        "to": "reject",
        "count": 2,
        "dates": ["2026-03-10", "2026-03-20"]
      }
    ],
    
    "events": [
      {
        "timestamp": "2026-03-25T10:00:00Z",
        "action": "phase_transition",
        "user": "System",
        "description": "Transitioned to FLAGGING phase",
        "metadata": {
          "from": "silent",
          "to": "flagging",
          "reason": "confidence exceeded 90%",
          "confidence": 92
        }
      },
      {
        "timestamp": "2026-03-25T09:30:00Z",
        "action": "configuration_changed",
        "user": "John Admin",
        "description": "Updated email recipients",
        "metadata": {
          "field": "email_recipients",
          "old_value": ["admin@company.com"],
          "new_value": ["admin@company.com", "ops@company.com"]
        }
      }
    ]
  }
}
```

**Response con header para descarga (si format=pdf o format=csv):**
```
HTTP/1.1 200 OK
Content-Type: application/pdf
Content-Disposition: attachment; filename="compliance-report-2026-03-01-to-2026-03-31.pdf"
Content-Length: 45821

[PDF binary data...]
```

**Vue Component Usage:**
```typescript
// ComplianceReportGenerator.vue
const generateReport = async () => {
  const params = new URLSearchParams({
    date_from: dateRange.from,
    date_to: dateRange.to,
    format: selectedFormat.value
  })
  
  // Para PDF/CSV: descarga directa
  if (selectedFormat.value !== 'json') {
    window.location.href = 
      `/api/deployment/verification/compliance-report?${params}`
  } else {
    // Para JSON: muestra en tabla
    const response = await fetch(
      `/api/deployment/verification/compliance-report?${params}`
    ).then(r => r.json())
    
    reportData.value = response.data
  }
}
```

---

## Error Handling

### Códigos de Error Comunes

| Status | Error | Solución |
|--------|-------|----------|
| 401 | Unauthenticated | Revisa token Bearer, quizá expiró |
| 403 | Forbidden (no admin role) | Solicita rol admin a administrador |
| 404 | Not Found | Endpoint no existe, revisa URL |
| 422 | Validation Error | Datos inválidos, revisa body/params |
| 429 | Too Many Requests | Demasiadas requests, espera antes de reintentar |
| 500 | Server Error | Error en backend, revisa Laravel logs |

### Error Response Format

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "channel": ["The channel field is required."],
    "webhook_url": ["Invalid URL format"]
  }
}
```

---

## Rate Limiting

**Límite:** 60 requests por minuto por token  
**Header:** `X-RateLimit-Limit: 60`  
**Remaining:** `X-RateLimit-Remaining: 55`  
**Reset:** `X-RateLimit-Reset: 1648138800`

```bash
# Si alcanzas límite:
HTTP/1.1 429 Too Many Requests
{
  "message": "Too Many Requests",
  "retry_after": 15
}
```

---

## Pagination

Todos los endpoints que retornan colecciones soportan paginación:

```json
{
  "data": [...],
  "pagination": {
    "total": 342,           // Total de registros
    "per_page": 50,         // Items por página
    "current_page": 1,      // Página actual
    "last_page": 7,         // Última página
    "from": 1,              // Inicio del rango
    "to": 50,               // Fin del rango
    "more_pages": true      // ¿Hay más páginas?
  }
}
```

---

## Authentication

Todos los endpoints requieren `Bearer token`:

```bash
curl -X GET "http://localhost:8000/api/deployment/verification/scheduler-status" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

**Para obtener token:**
```
POST /api/login
Content-Type: application/json

{
  "email": "admin@company.com",
  "password": "password"
}

Response:
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
}
```

---

## Testing Endpoints

### Test con cURL

```bash
# Test endpoint 1: Scheduler Status
curl -X GET "http://localhost:8000/api/deployment/verification/scheduler-status" \
  -H "Authorization: Bearer YOUR_TOKEN" | jq

# Test endpoint 2: Notifications
curl -X GET "http://localhost:8000/api/deployment/verification/notifications?type=transition" \
  -H "Authorization: Bearer YOUR_TOKEN" | jq

# Test endpoint 3: Test Notification
curl -X POST "http://localhost:8000/api/deployment/verification/test-notification" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"channel": "slack"}' | jq
```

### Test con JavaScript/Fetch

```typescript
async function testAPI() {
  try {
    const response = await fetch(
      '/api/deployment/verification/scheduler-status',
      {
        headers: {
          'Authorization': `Bearer ${localStorage.getItem('token')}`,
          'Accept': 'application/json'
        }
      }
    )
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`)
    }
    
    const data = await response.json()
    console.log('Success:', data)
  } catch (error) {
    console.error('Error:', error)
  }
}

testAPI()
```

---

## Webhooks (Future)

Próximamente se añadirá soporte para webhooks personalizados:

```bash
POST /api/deployment/verification/webhooks
{
  "url": "https://your-server.com/webhook",
  "events": ["phase_transition", "configuration_changed"]
}
```

---

