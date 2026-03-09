# 🎮 Gamificación: Stratos Quest & Talent Currency

## 📌 Overview

Stratos turns professional development and organizational alignment into a rewarding experience. The **Gamification** module uses "Stratos Points" as a proxy for talent value, rewarding employees for behaviors that drive the organization forward.

---

## 💎 Stratos Points (Talent Currency)

Points are the universal currency of Stratos. They are awarded automatically by the `GamificationService` based on specific triggers:

- **Learning Mastery:** Completing courses or closing skill gaps.
- **Quest Completion:** Achieving specific tactical goals (e.g., "Complete 3 peer feedbacks").
- **Culture Wins:** High sentiment alignment or positive cultural contributions.

### 💰 Benefit Exchange (The Talent Marketplace)

Stratos points can be "spent" in the Reward Catalog.

- **Redemption Flow:**
    1. Employee selects a reward (e.g., "1 Day of Free Time", "Premium Course", "Company Merch").
    2. System verifies points balance.
    3. Points are deducted, and a `Redemption` record is created for HR approval.

---

## 🏆 Stratos Quests

Quests are structured missions that guide the employee's journey.

- **Types:**
    - `learning`: Focused on upskilling.
    - `performance`: Focused on KPIs/Milestones.
    - `culture`: Focused on organizational values.
- **Rewards:** Usually a combination of Points and **Badges**.

---

## 🏅 Badges (Digital Credentials)

Badges represent proven expertise or exceptional contributions. They are visible in the employee's `Talent Passport` and contribute to their internal branding.

---

## 🛠️ Integrated Services

- **GamificationService:** Manages the awarding of points, badges, and quest progression.
- **Redemption Logic:** Ensures the transactional integrity of the points exchange system.

---

## 📊 Impact on Engagement

By visualizing progress and provide tangible rewards, the module:

1.  **Increases Participation:** Particularly in feedback and learning cycles.
2.  **Aligns Incentives:** Employees are rewarded for what the organization actually needs (Skill gaps).
3.  **Boosts Recognition:** Public badges foster a culture of expertise.
