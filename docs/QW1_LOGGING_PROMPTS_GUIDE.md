# QW-1: Mejorar Logging de Prompts — Guía de Implementación

**Fecha**: 21 de marzo de 2026  
**Duración Estimada**: 1-2 días  
**Status**: ✅ Completado

---

## 📋 Resumen

Esta feature implementa **logging PII-safe** de prompts LLM y outputs usando hashing SHA-256 para auditabilidad sin exponer datos sensibles.

### Componentes Agregados:

1. **Trait `LogsPrompts`** (`app/Traits/LogsPrompts.php`)
    - `logPrompt()` — loguea prompt + output con hashing
    - `logPromptError()` — loguea errores de LLM calls
    - `correlatePromptFeedback()` — vincula feedback con prompts históricos

2. **Canal de Logging `llm_prompts`** (`config/logging.php`)
    - Archivo dedicado: `storage/logs/llm_prompts.log`
    - Rotation diaria, retención configurable (default 30 días)

3. **Integración en Servicios**:
    - `AiOrchestratorService` — loguea cada `agentThink()` call
    - `LLMClient` — loguea cada `generate()` call

4. **Tests** (`tests/Unit/Traits/LogsPromptsTest.php`)
    - Validación de hashing
    - Verificación de no-plaintext-storage
    - Correlación de feedback

---

## 🏗️ Arquitectura

### Flow: Prompt → Hash → Log (PII-safe)

```
┌─────────────────────────────┐
│    User Request             │
│  (with sensitive data)      │
└──────────────┬──────────────┘
               │
               ▼
┌─────────────────────────────┐
│  AiOrchestratorService      │
│  .agentThink()              │
└──────────────┬──────────────┘
               │
      ┌────────┴────────┐
      ▼                 ▼
  ┌────────┐      ┌──────────┐
  │ LLM    │      │ Log      │
  │ Output │      │ Prompt   │
  └────────┘      │ (hash)   │
                  └──────────┘
                       │
                       ▼
                  ┌──────────────────────┐
                  │ LogsPrompts Trait    │
                  │ .logPrompt()         │
                  │                      │
                  │ SHA256(prompt)       │
                  │ SHA256(output)       │
                  │ metadata             │
                  └──────────┬───────────┘
                             │
                             ▼
                  ┌──────────────────────┐
                  │ storage/logs/        │
                  │ llm_prompts.log      │
                  │                      │
                  │ [daily rotation]     │
                  │ [stored hashes only] │
                  └──────────────────────┘
```

### Seguridad: Sin Plaintext Sensible

| Dato                      | Almacenado | Cómo                                   |
| ------------------------- | ---------- | -------------------------------------- |
| **Prompt original**       | ❌ No      | Reemplazado con SHA-256 hash           |
| **Output original**       | ❌ No      | Reemplazado con SHA-256 hash           |
| **Email, SSN, etc.**      | ❌ No      | Nunca salen del prompt hash            |
| **Metadata (agent, org)** | ✅ Sí      | Información no-sensible para auditoría |
| **User ID**               | ✅ Sí      | Si autenticado; para audit trail       |

---

## 💻 Uso

### Ejemplo 1: Integración Automática (Recomendado)

Ya está integrado en `AiOrchestratorService` y `LLMClient`, así que funciona automáticamente:

```php
// En cualquier código que use estos servicios:
$orchestrator = new AiOrchestratorService();
$result = $orchestrator->agentThink('sentinel', 'Analyze user skills');
// ✅ Prompt + output automáticamente hasheados y hasheados y logged a storage/logs/llm_prompts.log
```

### Ejemplo 2: Uso Manual en Nuevo Servicio

```php
<?php

namespace App\Services;

use App\Traits\LogsPrompts;

class MyCustomLLMService
{
    use LogsPrompts;

    public function analyzeContent(string $content, int $orgId): array
    {
        // ... your logic ...
        $prompt = "Analyze: {$content}";
        $output = $this->callLLM($prompt);

        // Log with PII protection
        $promptHash = $this->logPrompt($prompt, $output, [
            'agent' => 'custom_analyzer',
            'organization_id' => $orgId,
            'model' => 'gpt-4',
            'provider' => 'openai',
        ]);

        return [
            'output' => $output,
            'prompt_hash' => $promptHash, // for correlation later
        ];
    }

    private function callLLM(string $prompt): array
    {
        // ... LLM API call ...
    }
}
```

### Ejemplo 3: Loguear Errores

```php
try {
    $output = $orchestrator->agentThink('guide', $userQuestion);
} catch (\Throwable $e) {
    // Automatically logged with PII protection in catch block
    // (logPromptError called inside AiOrchestratorService)
    throw $e;
}
```

### Ejemplo 4: Correlacionar Feedback (Learning Loop Intro)

```php
// Usuario da feedback en UI: "This answer is wrong"

$promptHash = 'abc123...'; // Returned from logPrompt()
$feedbackService->recordFeedback(
    promptHash: $promptHash,
    rating: -1,
    feedbackType: 'hallucination',
    feedbackText: 'Referenced non-existent policy',
);

// Internamente correlaciona:
LogsPrompts::correlatePromptFeedback($promptHash, 'hallucination', [
    'feedback_text' => 'Referenced non-existent policy',
    'organization_id' => $orgId,
    'user_id' => auth()->id(),
]);
// ✅ Logged a llm_prompts.log para análisis de calidad
```

---

## 📊 Archivo de Logs

### Ubicación

```
storage/logs/llm_prompts.log          -- hoy
storage/logs/llm_prompts-2026-03-20.log
storage/logs/llm_prompts-2026-03-19.log
```

### Contenido (Ejemplo)

```json
[2026-03-21 14:32:15] local.INFO: LLM Call {
  "prompt_hash": "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855",
  "output_hash": "a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3",
  "organization_id": 42,
  "agent": "sentinel",
  "model": "gpt-4",
  "provider": "openai",
  "timestamp": "2026-03-21T14:32:15+00:00",
  "user_id": 5
}

[2026-03-21 14:35:22] local.ERROR: LLM Call Failed {
  "prompt_hash": "5d41402abc4b2a76b9719d911017c592",
  "error_class": "RuntimeException",
  "error_message": "API timeout after 30 seconds",
  "organization_id": 42,
  "agent": "guide",
  "timestamp": "2026-03-21T14:35:22+00:00",
  "user_id": 8
}

[2026-03-21 14:40:15] local.INFO: LLM Feedback Correlated {
  "prompt_hash": "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855",
  "feedback_type": "hallucination",
  "feedback_data": {"feedback_text": "Wrong date mentioned"},
  "organization_id": 42,
  "timestamp": "2026-03-21T14:40:15+00:00",
  "user_id": 5
}
```

---

## ✅ Verificación

### Test de Funcionamiento

```bash
# 1. Ejecutar tests
php artisan test tests/Unit/Traits/LogsPromptsTest.php

# ✅ Debería pasar todos los tests

# 2. Verificar archivo de logs
tail -f storage/logs/llm_prompts.log

# 3. Ejecutar cualquier operación LLM (ej: via Tinker)
php artisan tinker
>>> $svc = app(App\Services\AiOrchestratorService::class);
>>> $result = $svc->agentThink('guide', 'What is a skill?');
>>> exit

# tail debe mostrar entries nuevas en llm_prompts.log sin plaintext prompts
```

### Checklist

- [ ] Trait `LogsPrompts` creado en `app/Traits/LogsPrompts.php`
- [ ] Canal `llm_prompts` agregado a `config/logging.php`
- [ ] `AiOrchestratorService` usa `LogsPrompts` trait
- [ ] `LLMClient` usa `LogsPrompts` trait
- [ ] Tests en `tests/Unit/Traits/LogsPromptsTest.php` pasan
- [ ] Logs aparecen en `storage/logs/llm_prompts.log`
- [ ] No hay plaintext sensible en logs
- [ ] Documentación leída y entendida ✅

---

## 🔗 Próximos Pasos (QW-2, QW-3, etc.)

Ahora que está implementado QW-1, los próximos Quick Wins pueden usar estos logs:

- **QW-2: Dashboard de Salud RAGAS** → leer métricas de `llm_evaluations` + `llm_prompts.log`
- **QW-3: Endpoint `/api/rag/ask`** → loguear llamadas con `logPrompt()`
- **QW-4: Redaction Service** → sólo agrega más layer (QW-1 ya es safe)
- **QW-5: Agent Metrics** → correlacionar con `prompt_hash` para tracking

---

## 📝 Configuraciones Disponibles

En `.env`:

```bash
# Retención de logs llm_prompts (días)
LOG_LLM_PROMPTS_RETENTION=30

# Nivel de logging (por defecto 'info', puede ser 'debug')
LOG_LEVEL=info
```

---

## 🚨 Consideraciones de Seguridad

1. **Hashes No Reversibles**: SHA-256 no puede convertirse de vuelta a plaintext. Es una vía de 1 sentido. ✅
2. **Archivo de Logs Protegido**: Asegurar permisos en `storage/logs/`:
    ```bash
    chmod 750 storage/logs/
    chmod 640 storage/logs/llm_prompts.log*
    ```
3. **Acceso a Logs**: Só admin/ops pueden leer directamente.
4. **Cumplimiento GDPR**: Logs se rotan cada 30 días (configurable). Old logs pueden ser archivados/borrados.
5. **Multi-tenant**: `organization_id` siempre se loguea; queries pueden filtrar por org.

---

## 📚 Referencia Rápida

| Método                      | Parámetros                     | Retorno       | Cuándo Usar                 |
| --------------------------- | ------------------------------ | ------------- | --------------------------- |
| `logPrompt()`               | (prompt, output, metadata?)    | string (hash) | Después de LLM call exitoso |
| `logPromptError()`          | (prompt, exception, metadata?) | string (hash) | En catch block de LLM error |
| `correlatePromptFeedback()` | (hash, type, feedbackData?)    | void          | Cuando usuario da ratings   |

---

**Fin de Documentación QW-1.** Distribuir al equipo y marcar completado en próximas iteraciones.
