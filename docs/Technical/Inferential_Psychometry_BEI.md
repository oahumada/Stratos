# 🧠 Intelligence Motors: BEI & Inferential Psychometry

## 📌 Overview

Stratos has evolved its assessment capabilities by implementing two powerful cognitive motors:

1.  **BEI Chatbot (Behavioral Event Interview):** A specialized interviewer that uses the STAR method.
2.  **Inferential Psychometry:** An analysis engine that provides explicit evidence for every diagnosed trait.

---

## 🤖 Chatbot BEI (Behavioral Event Interview)

Unlike standard chatbots, the Stratos BEI Chatbot is designed to uncover specific, high-fidelity behavioral evidence.

### 🔑 Key Features

- **Methodology (STAR):** The agent probes for **S**ituation, **T**ask, **A**ction, and **R**esult.
- **Critical Incident Focus:** It ignores generic "I am a team player" responses and asks for a specific time when that trait was put to the test.
- **Dynamic Probing:** If a user provides a vague answer, the AI detects the missing STAR component and asks a targeted follow-up.

### 🧩 The STAR Method Deep Dive

The STAR method is the backbone of our BEI Chatbot's interrogation logic. It forces the subject to provide a structured narrative, which Stratos then deconstructs into data points:

1.  **S - Situation:** The AI identifies the context. _What was the specific challenge?_
2.  **T - Task:** The AI confirms the role. _What exactly was your responsibility in that situation?_
3.  **A - Action:** The most critical phase. _What SPECIFIC steps did you take?_ (The AI filters for "We" vs "I" to ensure individual accountability).
4.  **R - Result:** The quant/qual outcome. _What happened after your intervention?_

By ensuring every "STAR" is complete, the AI can perform **High-Fidelity Inference**, assigning scores that are grounded in real-world professional history rather than aspirations.

### 🔌 Backend Implementation

- **Agent Role:** `Expert Behavioral Event Interviewer`
- **Model:** Optimized for situational reasoning (DeepSeek-Chat / GPT-4o).

---

## 🔍 Inferential Psychometry

The goal of Inferential Psychometry is to move from "The AI says so" to "The AI proves so".

### 🔎 Dynamic Evidence Tracking

Every psychometric trait saved in the database now includes an **Evidence** field. This field contains a direct or paraphrased quote from the interview that justifies the specific score assigned.

**Example Insight:**

- **Trait:** _Resilience under Pressure_
- **Score:** 0.92
- **Evidence:** "In the crisis of Oct 2025, I personally took over the server migration when the lead was out, working 24 hours to ensure 99% uptime."

### 🏗️ Multi-Agent Analysis (360° Vision)

The analysis engine uses three specialized agents:

1.  **Senior Inferential Psychometrist:** Extracts behavioral traits and evidence.
2.  **Guardian of Organizational Culture:** Checks alignment with the Culture Manifesto.
3.  **Strategic Success Predictor:** Projects ROI and team synergy.

---

## 📊 Data Schema Update

The `psychometric_profiles` table has been extended to support this:

```sql
ALTER TABLE psychometric_profiles ADD COLUMN evidence TEXT;
```

---

## 🚀 Impact on Explainability

These motors directly feed the **Explainability UI**, allowing managers to click on a trait and see the exact segment of the conversation that led to that conclusion. This increases trust and allows for human-in-the-loop verification of AI decisions.
