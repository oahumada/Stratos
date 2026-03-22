# QW-4: Redaction Service Improvements

## 📊 Descripción General

QW-4 re-arquitectiza el **RedactionService** para ser configurable, con audit trail completo y métricas de cobertura PII. Sistema de redacción flexible para todos los servicios LLM + data processing.

**Duración Realizada**: 2 horas  
**Status**: ✅ Completado

---

## 🎯 Objetivos Logrados

✅ Redacción **configurable por tipos** de PII  
✅ **9 patrones PII** (email, phone, SSN, credit card, token, API key, bearer, passport, DOB)  
✅ **RedactionAuditTrail model** con tracking completo  
✅ **RedactionMetricsService** con estadísticas y cobertura  
✅ **Detección de PII** sin redactar (validation)  
✅ **23 tests Feature** con cobertura completa  
✅ Logging integrado a canal `redaction` con SHA256 hashing  
✅ Caché de métricas para performance

---

## 📁 Estructura de Archivos

```
app/Services/
├── RedactionService.php (280 líneas) — MEJORADO: configurable
├── RedactionMetricsService.php (NEW - 220 líneas)
app/Models/
├── RedactionAuditTrail.php (NEW - 30 líneas)
database/migrations/
├── 2026_03_22_015154_create_redaction_audit_trails_table.php (NEW)
tests/Feature/Services/
└── RedactionServiceTest.php (UPDATED - 205 líneas, 23 tests)
config/
└── logging.php (UPDATED - canal 'redaction')
```

---

## 🔧 Patrones de PII Soportados

| Tipo            | Patrón                 | Reemplazo                 | Ejemplo             |
| --------------- | ---------------------- | ------------------------- | ------------------- |
| **email**       | RFC 5322 básico        | `[REDACTED_EMAIL]`        | john@example.com    |
| **phone**       | Internacional + US     | `[REDACTED_PHONE]`        | +1 (555) 123-4567   |
| **ssn**         | US Social Security     | `[REDACTED_SSN]`          | 123-45-6789         |
| **credit_card** | Visa/MC/Amex/Discover  | `[REDACTED_CC]`           | 1234-5678-9012-3456 |
| **token**       | Long alphanumeric >=20 | `[REDACTED_TOKEN]`        | sk_live_1234...abcd |
| **api_key**     | Query params           | `[REDACTED]`              | ?api_key=secret...  |
| **bearer**      | JWT/OAuth tokens       | `Bearer [REDACTED_TOKEN]` | Bearer eyJ...       |
| **passport**    | Pasaporte              | `[REDACTED_PASSPORT]`     | US1234567           |
| **date_birth**  | Fecha de nacimiento    | `[REDACTED_DOB]`          | 1990-05-15          |

---

## 🚀 Como Usar

### 1. Redacción con tipos por defecto

```php
use App\Services\RedactionService;

// Redacta todos los tipos habilitados por defecto
$text = "Email: john@example.com, Phone: 555-123-4567";
$redacted = RedactionService::redactText($text);
// "Email: [REDACTED_EMAIL], Phone: [REDACTED_PHONE]"
```

### 2. Redacción selectiva

```php
// Solo redactar ciertos tipos
$redacted = RedactionService::redactText(
    $text,
    ['email', 'phone'] // Ignora SSN, tokens, etc
);
```

### 3. Redacción en arrays (JSON responses)

```php
$data = [
    'user' => 'john@example.com',
    'metadata' => [
        'ssn' => '123-45-6789',
        'phone' => '555-123-4567',
    ]
];

$redacted = RedactionService::redactArray($data);
// Redacta recursivamente todos los strings
```

### 4. Configurar tipos habilitados globalmente

```php
// Cambiar qué tipos redactar por defecto
RedactionService::setEnabledTypes(['email', 'phone']);

// Luego todos los redactText() usan estos tipos
$redacted = RedactionService::redactText($text);

// Resetear a defaults
RedactionService::resetEnabledTypes();
```

### 5. Detección sin redacción (para validación)

```php
// Validar si un texto contiene PII
if (RedactionService::containsPii($userInput)) {
    throw new \Exception('Input contains PII');
}

// Obtener detalles del PII encontrado
$detected = RedactionService::detectPii($text);
// ['email' => ['john@example.com'], 'phone' => ['555-123-4567']]
```

### 6. Métricas de redacción

```php
use App\Services\RedactionMetricsService;

$metricsService = new RedactionMetricsService();

// Métricas completas últimos 30 días
$metrics = $metricsService->getOrganizationMetrics(
    organizationId: auth()->user()->organization_id
);

// Breakdown por tipo de PII
$byType = $metricsService->getRedactionsByType(
    organizationId: auth()->user()->organization_id
);

// Trend diario para gráficos
$trend = $metricsService->getDailyTrend(
    organizationId: auth()->user()->organization_id,
    days: 30
);

// Score de cobertura de redacción (0-1)
$coverage = $metricsService->getRedactionCoverageScore(
    organizationId: auth()->user()->organization_id
);

// Validar si texto tiene PII
$analysis = $metricsService->checkTextForPii($userText);
if ($analysis['contains_pii']) {
    // Advertencia: encontrado {count} instancias de {types}
}
```

---

## 📊 Audit Trail - RedactionAuditTrail Model

Cada redacción se registra automáticamente:

```php
// Estructura de tabla
redaction_audit_trails:
├── id (PK)
├── redaction_types (json) — ['email', 'phone', 'ssn']
├── count (int) — Número de items redactados
├── original_hash (sha256) — Hash del texto original
├── context (string) — API endpoint donde ocurrió
├── user_id (FK) — Usuario quien disparó
├── organization_id (FK) — Org propietaria
├── created_at, updated_at
```

### Consultar auditoría

```php
use App\Models\RedactionAuditTrail;

// Últimas 100 redacciones de la org
RedactionAuditTrail::where('organization_id', $orgId)
    ->latest()
    ->limit(100)
    ->get();

// Redacciones por usuario
RedactionAuditTrail::where('organization_id', $orgId)
    ->where('user_id', $userId)
    ->with('user')
    ->get();

// Detección de patrones (ej. muchas redacciones en un contexto)
RedactionAuditTrail::where('context', '/api/llm-generate')
    ->where('created_at', '>=', now()->subHour())
    ->count();
```

---

## 🧪 Tests (23 casos - All Passing ✅)

**RedactionService (13 tests):**

- ✅ Redacta emails
- ✅ Redacta teléfonos
- ✅ Redacta SSN
- ✅ Redacta tarjetas de crédito
- ✅ Redacta tokens
- ✅ Redacta parameters API
- ✅ Redacta Bearer tokens
- ✅ Redacción selectiva por tipo
- ✅ Redacción recursiva en arrays
- ✅ Detección de PII sin redactar
- ✅ Extraer matches específicos
- ✅ Configurar tipos globalmente
- ✅ Obtener tipos enabled

**RedactionAuditTrail (4 tests):**

- ✅ Registra redacción en audit trail
- ✅ Registra usuario y organización
- ✅ Almacena hash del original
- ✅ Registra contexto de request

**RedactionMetricsService (6 tests):**

- ✅ Obtiene métricas de organización
- ✅ Breakdown por tipo de PII
- ✅ Validación de PII en texto
- ✅ Calcula coverage score
- ✅ Trend diario
- ✅ Invalidación de caché

---

## 🔄 Flujo de Integración

```
Client Request
    ↓
[Servicio LLM / Data Processing]
    ↓
RedactionService::redactText()
    ↓
├─ Detecta PII (regex patterns)
├─ Reemplaza con placeholders
└─ Llama logRedaction() si encontró
    ↓
logRedaction()
    ├─ Escribe a canal 'redaction' (logging)
    └─ Crea RedactionAuditTrail (DB) con:
        ├── types redactados
        ├── count
        ├── original_hash
        ├── contexto
        └── user/org
    ↓
Response (con PII redactado)
```

---

## 🛡️ Seguridad & Compliance

✅ **Sin almacenar original:** Solo SHA256 hash para comparación  
✅ **Rastreabilidad:** Usuario quien ejecutó + contexto  
✅ **GDPR compatible:** Facilita auditorías de acceso a PII  
✅ **Configurable:** Habilitar/deshabilitar tipos por org  
✅ **Performance:** Caché de métricas, índices en BD  
✅ **Multi-tenant:** Scoping automático por organization_id

---

## 📈 Características Avanzadas

### 1. Detección Multi-contexto

```php
// Detectar en múltiples tipos simultáneamente
$detected = RedactionService::detectPii($text,
    ['email', 'phone', 'ssn', 'date_birth']
);

// Retorna array con matches:
// ['email' => [...], 'phone' => [...]]
```

### 2. Análisis de Cobertura

```php
// Score indicador (0-1) de qué tan bien se redacta
$coverage = $metricsService->getRedactionCoverageScore($orgId);
// 0.75 = Buena cobertura
// 0.95 = Excelente cobertura
```

### 3. Tendencias Temporales

```php
// Ver cómo cambia el volumen de redacciones en el tiempo
$trend = $metricsService->getDailyTrend($orgId, days: 90);
// Útil para detectar anomalías o cambios en patrones
```

---

## 📚 Próximas Mejoras (Post-MVP)

- [ ] pgvector para búsqueda de PII similar
- [ ] Machine learning para patrones custom por industria
- [ ] Dashboard UI para métricas de redacción
- [ ] Webhook notifications para anomalías detectadas
- [ ] Integración con DLP (Data Loss Prevention) tools
- [ ] OCR de documentos scaneados

---

## 🔗 Integración con Servicios Existentes

### Script de Migración (reemplazar RedactionService viejo)

```bash
# Automático en QW-4, pero manual si necesita reemplazo:

# 1. Backup
cp app/Services/RedactionService.php app/Services/RedactionService.php.backup

# 2. Ejecutar migración (ya hecha)
php artisan migrate

# 3. Los servicios existentes usan el nuevo RedactionService automáticamente
# (Sin cambios en código, es compatible hacia atrás)
```

---

## 💾 Cambios en Configuración

### Nuevo canal de logging: `config/logging.php`

```php
'redaction' => [
    'driver' => 'daily',
    'path' => storage_path('logs/redaction/redaction.log'),
    'level' => env('LOG_LEVEL', 'debug'),
    'days' => 30,
    'permission' => 0644,
],
```

---

## 🧬 Commit Message

```
feat: QW-4 - Redaction Service Improvements con configurabilidad y audit

- RedactionService refactorizado: 9 patrones PII + configurable por tipos
- Soporte: email, phone, SSN, CC, token, API key, bearer, passport, DOB
- RedactionAuditTrail model: tracking completo (user, org, hash, contexto)
- RedactionMetricsService: estadísticas, breakdown, trend, coverage score
- Detección de PII sin redactar: containsPii(), detectPii()
- 23 tests Feature Coverage (todos pasando)
- Caché de métricas para performance
- Migración BD aplicada: create_redaction_audit_trails_table
- Canal logging 'redaction' con rotación 30 días
- Multi-tenant scoping automático
- Backward compatible con servicios existentes
```

---

## 📊 Commit Status

| Componente                | Líneas            | Tests     | Status         |
| ------------------------- | ----------------- | --------- | -------------- |
| RedactionService          | 280 (mejorado)    | 13 ✅     | ✅             |
| RedactionMetricsService   | 220 (nuevo)       | 6 ✅      | ✅             |
| RedactionAuditTrail Model | 30 (nuevo)        | 4 ✅      | ✅             |
| Tests                     | 205 (actualizado) | 23 ✅     | ✅             |
| **TOTAL**                 | **735 LOC**       | **23 ✅** | **Completado** |

---

## 🚀 Próximos Quick Wins

- **QW-5:** Agent Interaction Metrics (1-2 días)
- **Sprint 0:** pgvector + Knowledge Indexing (12-14 días)
