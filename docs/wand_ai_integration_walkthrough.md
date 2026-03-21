# 🛠️ Walkthrough: Integración de Stratos con Wand AI (Labor Force Orchestrator)

Este documento detalla los pasos técnicos para implementar la orquestación de agentes entre Stratos y **Wand AI**, permitiendo materializar las competencias de tipo `Synthetic Autonomous`.

---

## 1. Arquitectura del Puente Híbrido

La integración se basa en una arquitectura de **"Despliegue Programático"**:

1.  **Stratos (Cerebro):** Diseña y aprueba el *Skill Blueprint*.
2.  **Bridge Service (Laravel):** Traduce el ADN de la skill al lenguaje de la API de Wand AI.
3.  **Wand AI (Ejecutor):** Provisiona el agente inteligente con acceso a datos corporativos.
4.  **Feedback Loop:** Webhooks envían métricas de desempeño de vuelta a Stratos.

---

## 2. Paso 1: Extensión del Modelo de Datos

Debemos permitir que cada habilidad (`Skill`) guarde su referencia externa.

### Migración Sugerida:
```php
Schema::table('skills', function (Blueprint $table) {
    // Para guardar ID del agente externo, proveedor y estado operativo
    $table->json('automation_config')->nullable()->after('cube_dimensions');
});
```

---

## 3. Paso 2: El Servicio de Integración (`WandAIService`)

Este servicio encapsula toda la comunicación con la plataforma externa.

**Ubicación:** `app/Services/Integrations/WandAIService.php`

```php
namespace App\Services\Integrations;

class WandAIService {
    protected $apiKey;
    protected $baseUrl;

    public function __construct() {
        $this->apiKey = config('services.wand.api_key');
        $this->baseUrl = 'https://api.wand.ai/v1';
    }

    /**
     * Provisiona un agente basado en el ADN de la Skill de Stratos.
     */
    public function provisionAgentFromSkill(Skill $skill) {
        $payload = [
            'name' => "Agente: {$skill->name}",
            'mission' => $skill->description,
            'traits' => [
                'mastery_level' => $skill->cube_dimensions['mastery'] ?? 3,
                'fluency_d3' => $skill->cube_dimensions['ai_fluency']['discernment'] ?? 3,
                'fluency_d4' => $skill->cube_dimensions['ai_fluency']['diligence'] ?? 3,
            ],
            // Otros parámetros específicos de Wand AI
        ];

        return Http::withToken($this->apiKey)->post("{$this->baseUrl}/agents/create", $payload);
    }
}
```

---

## 4. Paso 3: Disparador de Provisionamiento (Event Driven)

No queremos que el provisionamiento ocurra accidentalmente. Debe ser una acción deliberada tras la aprobación del **Cubo de Skill**.

1.  **Evento:** `SkillMaterialized` o `SkillApproved`.
2.  **Listener:** `DeployToExternalRuntime`.
3.  **Lógica:** Si `talent_mode == 'synthetic_autonomous'`, llamar al `WandAIService`.

---

## 5. Paso 4: Interfaz de Usuario (Wizard Vue)

En el componente `SkillMaterializationWizard.vue`, se debe añadir un control de estado externo:

```vue
<!-- Conceptual snippet -->
<div v-if="skill.talent_mode === 'synthetic_autonomous'" class="d-flex align-center gap-2">
    <v-switch 
        v-model="skill.provision_external" 
        label="Provisionar en Wand AI"
        color="deep-purple-accent-2"
        density="compact"
    ></v-switch>
    <v-chip v-if="skill.external_id" color="success" size="x-small">AGENTE ACTIVO</v-chip>
</div>
```

---

## 6. Paso 5: El Bucle de Desempeño (Webhooks)

Configurar un endpoint de API en Laravel para recibir los "heartbeats" de Wand AI.

*   **URL:** `POST /api/v1/webhooks/wand-ai`
*   **Lógica:** Recibir el `agent_id`, extraer métricas de éxito/falla y guardarlas en la tabla de `skills` o en una nueva tabla de `skill_executions_log` para analíticas de ROI.

---

> [!IMPORTANT]
> **Gobernanza:** 
> La integración con Wand AI SIEMPRE debe respetar el **Nivel de Diligencia (D4)** definido en Stratos. Ningún agente externo debe poder ejecutar acciones críticas (ej: pagos, despidos) sin que el Hook de Stratos solicite una firma humana en la capa de aprobación.
