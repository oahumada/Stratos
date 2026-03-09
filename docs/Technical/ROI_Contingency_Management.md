# 💰 Gestión de Contingencias & ROI: Automating Financial Impact

## 📌 Overview

Stratos moves beyond qualitative suggestions to provide hard financial data. The **ROI & Contingency** module quantifies the return on human capital investments, allowing the CEO to see the dollar value of AI-driven talent strategies.

---

## 📈 ROI Calculation Engine

The `RoiCalculatorService` deconstructs every strategy into its financial components.

### 🛡️ Savings: Attrition Mitigation

The largest source of ROI is preventing the departure of key talent.

- **Formula:** `(Total Replacement Cost) * (Avg Flight Risk) * (AI Efficiency Gain)`
- **Replacement Cost:** Calculated dynamically per role (typically 50-150% of annual salary).
- **Efficiency Gain:** The expected reduction in risk if the recommended "Nudges" and "Development Paths" are implemented.

### 💸 Costs: Strategy Implementation

- **Training Budget:** Costs of courses, mentorship hours, and lost productivity during learning.
- **Hiring Costs:** Market-benchmarked cost of talent acquisition for gaps that cannot be filled internally.

---

## 📊 Strategic KPIs

For every business scenario, the system generates:

1.  **Net Benefit:** Total savings minus implementation costs.
2.  **ROI %:** The percentage return on the training/HR budget.
3.  **Break-even Point:** How many months until the strategy pays for itself.
4.  **Confidence Index:** The AI's certainty level based on data density.

---

## 🚀 Impact on Decision Making

By adding a "Price Tag" to both action and inaction:

- **Prioritize Spending:** Focus the budget on strategies with the highest ROI.
- **Justify HR Budget:** Proof of how a $10k training program saves $100k in hiring costs.
- **Contingency Readiness:** Seeing the financial "cost of doing nothing" during an attrition crisis triggers faster executive response.

---

## 🛠️ Usage via API

The ROI analysis is typically returned as part of a scenario's recommendations:

**Example Metadata:**

```json
"roi_analysis": {
    "total_potential_savings": 125000.00,
    "estimated_implementation_cost": 25000.00,
    "net_benefit": 100000.00,
    "roi_percentage": 400.0,
    "break_even_months": 2.4
}
```
