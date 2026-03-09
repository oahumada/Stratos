# Impact Visualization Architecture - Stratos

## Overview

The **Impact Visualization** feature in Stratos provides stakeholders with a multi-layered view of how strategic decisions affect the organization's talent ecosystem. It ranges from detailed skill-level gaps to high-level financial ROI and agentic "What-If" simulations.

## 🏗️ Architectural Layers

### 1. Data Aggregation & Analytics (Backend)

The core logic resides in several interconnected Laravel services:

- **`ScenarioAnalyticsService`**:
    - Calculates **Scenario IQ** (Neural Readiness Index).
    - Measures skill gap closure, productivity index, and time-to-readiness.
    - Benchmarks "As-Is" vs. "To-Be" states.
- **`ImpactReportService`**:
    - Generates executive reports (Scenario Impact, Organizational ROI, Consolidated).
    - Aggregates data from multiple modules into a single JSON response.
- **`TalentRoiService`**:
    - Calculates financial metrics (Hiring cost savings, Training ROI).
    - Computes the **CEO-level KPIs**.
- **`CrisisSimulatorService`**:
    - Performs **War-Gaming** simulations: Mass Attrition, Tech Obsolescence, and Restructuring.
    - Evaluates resilience and exposed critical roles.
- **`AgenticScenarioService`**:
    - Orchestrates **Multi-Agent Simulations** using CrewAI/AI Agents to predict complex organizational shifts.

### 2. API Strategy

- `GET /api/strategic-planning/scenarios/{id}/impact`: Detailed scenario-specific impact metrics.
- `GET /api/strategic-planning/scenarios/{id}/summary`: High-level summary for the final dashboard.
- `POST /api/reports/scenario/{id}/impact`: Generates formal impact reports.
- `GET /api/reports/roi`: Organizational-level talent investment return analysis.

### 3. Visualization Components (Frontend)

Stratos uses **Vue 3**, **Vuetify**, and **ApexCharts/Chart.js** to present data with a premium "Neural/Glassmorphic" aesthetic.

#### Key UI Modules:

- **`ImpactAnalytics.vue` (Step 5)**:
    - **Radar Chart**: Visualizes skill gap closure across dimensions (Leadership, Tech Vision, etc.).
    - **TFC Timeline**: Displays "Velocity to Peak Performance" (Time to Full Capacity).
    - **Execution Guards**: Lists AI-suggested mitigations for identified vulnerabilities.
- **`FinalDashboard.vue` (Step 7)**:
    - **Investment Orbit**: Shows the 4B distribution (Build, Buy, Borrow, Bot).
    - **Neural Synthesis Balance**: Measures the ratio between Human vs. Synthetic (AI) workforce components.
    - **Scenario IQ**: The definitive "Readiness Score" for the proposed plan.
- **`OverviewDashboard.vue`**:
    - A consolidated hub that provides horizontal navigation across all impact dimensions (Simulator, Forecasts, Matches, Gaps, ROI, Crisis, Agentic).
- **`CrisisSimulator.vue`**:
    - Interactive module for running "Worst Case" scenarios and viewing their cascading impact on skills and succession.
- **`AgenticScenarioPlanner.vue` (Stratos Radar)**:
    - Advanced agentic simulation for "What-If" analysis, allowing users to ask hypothetical questions and receive real-time risk/viability assessments.

## 🧠 Design Philosophy

1.  **Neural Aesthetic**: Use of vibrant gradients, glowing glass elements, and orbital animations to convey a "living" organizational brain.
2.  **Explainability**: Every impact metric is backed by "Neural Insights" or "Strategic Narrative," ensuring stakeholders understand the _why_ behind the numbers.
3.  **Proactivity**: Instead of just showing data, the system suggests **Neural Guards** and **Mitigation Plans** automatically.

## 🚀 Key Performance Indicators (KPIs)

- **Neural Readiness (Scenario IQ)**: Percentage of alignment with required future capability.
- **Tactical Capex (4B)**: Total investment required to execute the scenario.
- **Synthetization Index**: Degree of AI/Automation adoption within the plan.
- **Success Probability**: Likelihood of meeting horizon goals given the current constraints.
