# Component Modularity Map: Stratos Platform

## 📦 Overview

This document maps technical frontend components (Vue 3) to their respective modules within the Stratos ecosystem.

---

## 🏛️ STRATOS CORE (Foundation Components)

_The system's "Single Source of Truth" and structural identity._

| Component Name                   | File Path                                                                                                                 | Purpose                                                                                         |
| :------------------------------- | :------------------------------------------------------------------------------------------------------------------------ | :---------------------------------------------------------------------------------------------- |
| **People Inventory**             | [pages/People/Index.vue](file:///home/omar/Stratos/resources/js/pages/People/Index.vue)                                   | Main directory for talent lifecycle management.                                                 |
| **Role Catalog**                 | [pages/Roles/Index.vue](file:///home/omar/Stratos/resources/js/pages/Roles/Index.vue)                                     | Strategic definition of roles and job architecture.                                             |
| **Competency Dictionary**        | [pages/Competencies/Index.vue](file:///home/omar/Stratos/resources/js/pages/Competencies/Index.vue)                       | Taxonomy of behavioral and technical competencies.                                              |
| **Skills Matrix**                | [pages/Skills/Index.vue](file:///home/omar/Stratos/resources/js/pages/Skills/Index.vue)                                   | Detailed inventory of atomic technical skills.                                                  |
| **Organization Flow**            | [pages/Departments/OrganizationChart.vue](file:///home/omar/Stratos/resources/js/pages/Departments/OrganizationChart.vue) | Hierarchical D3.js visualization of departments.                                                |
| **Stratos Map (La Radiografía)** | [pages/Talento360/StratosMap.vue](file:///home/omar/Stratos/resources/js/pages/Talento360/StratosMap.vue)                 | El módulo base. Releva y visualiza el inventario actual de skills de la empresa en tiempo real. |

---

## 📡 STRATOS RADAR (La Prevención Predictiva)

_Dashboard analítico para líderes que advierte sobre riesgos de obsolescencia y fuga de talento crítico._

| Component Name              | File Path                                                                                                                                                   | Purpose                                                    |
| :-------------------------- | :---------------------------------------------------------------------------------------------------------------------------------------------------------- | :--------------------------------------------------------- |
| **Strategic Scenario List** | [pages/ScenarioPlanning/ScenarioList.vue](file:///home/omar/Stratos/resources/js/pages/ScenarioPlanning/ScenarioList.vue)                                   | Dashboard for managed scenarios and simulations.           |
| **Scenario Hub (Detail)**   | [pages/ScenarioPlanning/ScenarioDetail.vue](file:///home/omar/Stratos/resources/js/pages/ScenarioPlanning/ScenarioDetail.vue)                               | Multi-step orchestration of strategic simulations.         |
| **Neural Map View**         | [components/ScenarioPlanning/Step1/PrototypeMap.vue](file:///home/omar/Stratos/resources/js/components/ScenarioPlanning/Step1/PrototypeMap.vue)             | Interactive graph for futuristic architecture design.      |
| **AI Scenario Wizard**      | [pages/ScenarioPlanning/GenerateWizard/GenerateWizard.vue](file:///home/omar/Stratos/resources/js/pages/ScenarioPlanning/GenerateWizard/GenerateWizard.vue) | LLM-driven assistant for step-by-step state creation.      |
| **Gap Analysis Engine**     | [pages/GapAnalysis/Index.vue](file:///home/omar/Stratos/resources/js/pages/GapAnalysis/Index.vue)                                                           | Contrast engine between actual inventory and target state. |

---

## 👥 STRATOS PX (People Experience) & Growth

_Employee-centric retention and growth modules._

| Component Name                     | File Path                                                                                                                     | Purpose                                                                                           |
| :--------------------------------- | :---------------------------------------------------------------------------------------------------------------------------- | :------------------------------------------------------------------------------------------------ |
| **Mi Stratos (Portal Personal)**   | [pages/MiStratos/Index.vue](file:///home/omar/Stratos/resources/js/pages/MiStratos/Index.vue)                                 | El "Home" del colaborador. Dashboard personal con KPIs de match, ADN, gamificación y mentoría AI. |
| **Stratos Navigator / Grow**       | [pages/LearningPaths/StratosNavigator.vue](file:///home/omar/Stratos/resources/js/pages/LearningPaths/StratosNavigator.vue)   | IA para trazar rutas automáticas (cursos, mentorías) para cerrar gaps detectados.                 |
| **Stratos 360 Workspace**          | [pages/Talento360/Dashboard.vue](file:///home/omar/Stratos/resources/js/pages/Talento360/Dashboard.vue)                       | Holistic evaluation and feedback gathering tools.                                                 |
| **360 Command Center**             | [pages/Talento360/Comando.vue](file:///home/omar/Stratos/resources/js/pages/Talento360/Comando.vue)                           | Admin view for psychometric triangulation and results.                                            |
| **Social Learning Hub**            | [pages/PeopleExperience/SocialLearning.vue](file:///home/omar/Stratos/resources/js/pages/PeopleExperience/SocialLearning.vue) | Peer-to-peer knowledge transmission platform (Stratos Link).                                      |
| **Comando PX**                     | [pages/PeopleExperience/ComandoPx.vue](file:///home/omar/Stratos/resources/js/pages/PeopleExperience/ComandoPx.vue)           | Dashboard analítico para líderes sobre riesgo de burnout y clima.                                 |
| **Stratos Match (La Oportunidad)** | [pages/Marketplace/Index.vue](file:///home/omar/Stratos/resources/js/pages/Marketplace/Index.vue)                             | Módulo de movilidad interna. Cruza vacantes con perfiles existentes (Fitness Match).              |

---

## 🧲 STRATOS MAGNET (Talent Acquisition)

_Candidate experience and external matching algorithms._

| Component Name          | File Path                                                                                                         | Purpose                                           |
| :---------------------- | :---------------------------------------------------------------------------------------------------------------- | :------------------------------------------------ |
| **Stratos Magnet Home** | [pages/Careers/PublicPortal.vue](file:///home/omar/Stratos/resources/js/pages/Careers/PublicPortal.vue)           | External recruitment portal with elite UX design. |
| **Candidate Portal**    | [pages/Selection/CandidatePortal.vue](file:///home/omar/Stratos/resources/js/pages/Selection/CandidatePortal.vue) | Individual application tracking and assessment.   |

---

## ⚙️ CONTROL CENTER (Admin & Governance)

_Platform health, security, and AI oversight._

| Component Name          | File Path                                                                                                   | Purpose                                                      |
| :---------------------- | :---------------------------------------------------------------------------------------------------------- | :----------------------------------------------------------- |
| **RBAC Manager**        | [pages/settings/RBAC.vue](file:///home/omar/Stratos/resources/js/pages/settings/RBAC.vue)                   | Management of roles, permissions, and security.              |
| **AI Agent Supervisor** | `pages/TalentAgents/Index.vue`                                                                              | Setup and monitoring of specialized LLM agents.              |
| **Quality Sentinel**    | [pages/Quality/QualityHub.vue](file:///home/omar/Stratos/resources/js/pages/Quality/QualityHub.vue)         | Real-time monitoring of system reliability and RAGAS scores. |
| **RAGAS Neural Dash**   | [pages/Quality/RAGASDashboard.vue](file:///home/omar/Stratos/resources/js/pages/Quality/RAGASDashboard.vue) | Deep analytics for LLM output (Faithfulness/Relevance).      |

---

> [!IMPORTANT]
> All components listed above MUST utilize the **Glass Design System** tokens defined in `StUI` and `index.css` to maintain visual consistency across modules.
