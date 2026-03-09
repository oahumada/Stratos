# 🌈 People Experience (PX) & Engagement - Stratos

## 📌 Overview

The **People Experience (PX)** module is Stratos' proactive sensor for organizational health. It moves beyond static climate surveys by triggering high-context pulses automatically in response to strategic events or simulated crises.

---

## 🏗️ The "Proactive Pulse" Architecture

Unlike a traditional ERP, Stratos doesn't wait for an annual survey. It uses the `PxService` to detect system events and "listen" to the organization when it matters most.

### 🔄 Event-Driven Triggers

Working together with the **Simulation Engine**, the PX module can trigger campaigns for:

- **`scenario.finalized`**: When a new strategic direction is set, the system asks employees about their alignment and clarity.
- **`crisis.attrition`**: After a mass attrition simulation, it measures feelings of stability and support.
- **`merger.restructuring`**: During a restructuring simulation, it focuses on cultural integration and communication transparency.

---

## 🛠️ Components

### 1. `PxService`

The brain of the module. It contains the templates and the logic for creating `PxCampaign` entities and their associated `PulseSurvey`.

### 2. `PxCampaign`

Represents a specific "listening window". It tracks:

- **Organization Context:** Where is this happening?
- **Topics:** What are we measuring? (e.g., Alignment, Security, Integration).
- **Mode:** `Automatic` (Event-driven) or `Manual`.

### 3. `PulseSurvey` (Event-Driven)

The actual instrument delivered to employees. When triggered by PX, it uses specialized questions optimized for the specific context of the trigger.

---

## 📊 Measuring the Pulse

The data collected flows into the **Stratos Sentinel**, which:

1.  Calculates a **Health Score**.
2.  Detects **Anomalies** (Low sentiment, declining trends).
3.  Generates **AI Diagnostics** with priority actions for the leadership.

---

## 🚀 How to Trigger via API

To manually trigger an event-driven campaign (for testing or ad-hoc needs):

**Endpoint:** `POST /api/px/campaigns/trigger`
**Payload:**

```json
{
    "event_type": "scenario.finalized"
}
```

## 📈 Status

- [x] **Database Schema**: `px_campaigns`, `pulse_surveys`, `pulse_responses` active.
- [x] **Core Logic**: `PxService` implementation.
- [x] **Trigger Engine**: Prototype event-driven logic active.
- [x] **API Layer**: `PxController` and routes registered.
