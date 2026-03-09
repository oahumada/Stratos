# 🔗 Automatización e Interconexión: n8n & Stratos Intelligence

## 📌 Overview

Stratos no es una isla. A través de la capa de **Automation**, la plataforma puede "escuchar" y "actuar" en el ecosistema digital de la empresa (Slack, Jira, LinkedIn, ATS, etc.). La pieza central de esta interconexión es **n8n**.

---

## 🛠️ Webhook de Inteligencia

Hemos habilitado un endpoint unificado para recibir señales externas que alimenten al **Sentinel** o disparen **Nudges**.

- **Endpoint:** `/api/automation/webhooks/n8n`
- **Controller:** `App\Http\Controllers\Api\Automation\N8nController`

### 📤 Outbound: Stratos -> n8n

Cuando la IA de Stratos toma una decisión (ej: "Sugerir cambio de rol", "Notificar riesgo de fuga"), puede enviar un webhook a n8n con el siguiente payload:

```json
{
    "event": "talent.nudge.generated",
    "priority": "critical",
    "data": {
        "people_id": 123,
        "name": "John Doe",
        "recommendation": "Mentorship required in React Architecture",
        "roi_impact": "$12,000 savings"
    }
}
```

### 📥 Inbound: n8n -> Stratos

n8n puede inyectar datos externos (ej: completion de un curso en Coursera, feedback en un canal de Slack) para actualizar el `Iceberg Dinámico` del colaborador.

---

## 🎨 Escenarios de Automatización (Recetas)

1.  **Hiring Autopilot:** n8n detecta un nuevo aplicante en el ATS -> Crea sesión de evaluación en Stratos -> IA realiza entrevista BEI -> n8n devuelve el score al ATS.
2.  **Crisis Watcher:** Sentinel detecta anomalía cultural -> n8n crea un ticket en Jira para HRBP -> n8n agenda reunión de feedback en Google Calendar.
3.  **Gamification Alert:** Colaborador completa un "Quest" -> n8n publica un mensaje de celebración en Slack con el badge obtenido.

---

## 📡 Configuración Técnica

Para conectar una instancia de n8n:

1.  Configurar la variable de entorno `N8N_WEBHOOK_URL` en el `.env` de Stratos.
2.  Utilizar el controller de automatización para validar tokens de seguridad (Bearer Auth).

---

## 📊 Valor de Negocio

Esta integración reduce la fricción operativa, asegurando que los insights de Stratos se conviertan en acciones reales sin intervención manual constante.
