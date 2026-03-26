# 📋 PENDIENTES - Stratos (Mar 26, 2026)

**Estado:** Messaging MVP Phase 4 ✅ COMPLETO (623 tests passing)

---

## 🎯 Próximos Pasos Inmediatos (Semana de Mar 26-30)

### 1. **Merge & Deploy Messaging MVP** 🚀

- **Estado:** Feature branch `feature/messaging-mvp` listo
- **Acción:**
    - [ ] Review final de código
    - [ ] Merge a `main` branch
    - [ ] Deploy a staging (Mar 27-28)
    - [ ] Smoke tests en staging
    - [ ] Release to production (Mar 31)
- **Tiempo:** 2-3 horas
- **Responsable:** DevOps / Tech Lead

### 2. **UI del Talent Pass (CV 2.0)** 🎨

- **Estado:** Backend + API endpoints ✅ COMPLETO
- **Pendiente:** Vue3 components para visualización
- **Componentes Necesarios:**
    - [ ] `TalentPassViewer.vue` - Display del CV 2.0
    - [ ] `TalentPassEditor.vue` - Edición de skills/competencias
    - [ ] `TalentPassExport.vue` - Exportar a PDF/JSON
    - [ ] Tailwind CSS styling con Glass design system
- **Tiempo:** 3-4 días
- **COSTO:** ✅ ZERO ($0) - No requiere blockchain
- **Dependencia:** ROADMAP Priority 1 (High Value, Without costs)

### 3. ~~**Blockchain Node Setup (POSTERGAR - NO PRIORITARIO)**~~ 🛑

- **Estado: POSTPONED** - Not Cost-Effective
- **Razón:**
    - 💰 Costo: $100-300/mes nodo + dev + mantenimiento
    - ⚙️ Complejidad: Smart contracts + key management
    - 📊 ROI: Bajo (feature nice-to-have, no core MVP)
- **Alternativa recomendada:** Talent Pass SIN blockchain
    - Credentials firmadas digitalmente (no en chain)
    - JSON export para portabilidad
    - Validación centralizada (Stratos Platform)
    - Mismo valor 80% sin costos
- **Si en futuro aplica:** Revisar cuando X empresas soliciten verificación Web3
- **Dependendencia:** Bloquea SOLO si blockchain es requisito de negocio

---

## 📊 Trabajo de Mediano Plazo (Próximas 2-4 semanas)

### 3. **Admin Panel Polish** 🛠️

- **Estado:** Admin Operations fase 5 ✅ COMPLETO
- **Pendiente:**
    - [ ] Agregar más operaciones administrativas
    - [ ] Mejorar UX dashboard con gráficos
    - [ ] Implementar alertas de SLA
    - [ ] Auditoría avanzada (filtros, exports)
- **Tiempo:** 2-3 días
- **Prioridad:** MEDIA
- **COSTO:** ✅ ZERO

### 4. **LMS Nativo Hardening** 📚

- **Estado:** Versión beta, mejoras pendientes
- **Enfoque:**
    - [ ] Mejorar UX de cursos
    - [ ] Integración SSO con Successfactors/LinkedIn Learning
    - [ ] Analytics de progreso de aprendizaje
    - [ ] Soporte para contenido multimedia (video, interactivo)
- **Tiempo:** 1-2 semanas
- **Prioridad:** ALTA
- **COSTO:** ✅ ZERO (integración vía APIs públicas)

### 5. **Mobile App Nativa** 📱 (POSTERGAR - NO PRIORITARIO)**~~ 🛑

- **Estado:** Mobile web (PWA) ✅ funcional
- **Pendiente:**
    - [ ] App nativa iOS (Swift)
    - [ ] App nativa Android (Kotlin)
    - [ ] Sincronización offline de mensajes
    - [ ] Push notifications
    - [ ] Integración con Apple Wallet / Google Pay
- **Tiempo:** 4-6 semanas
- **Prioridad:** MEDIA (MVP web funciona, app nativa es "nice-to-have")
- **COSTO:** ✅ ZERO (Desarrollo interno, no requiere terceros)

### 6. **Scenario Planning Phase 2** 👥 

- **Estado:** Fase 1 completada (basic planning)
- **Pendiente - Fase 2:**
    - [ ] Scenario planning avanzado
    - [ ] Talent risk analytics
    - [ ] Career succession planning
    - [ ] Integration con People Experience
- **Tiempo:** 2-3 semanas
- **Prioridad:** ALTA (estratégico)
- **COSTO:** ✅ ZERO

---

## 🔧 Deuda Técnica & Optimizaciones

### 7. **Performance & Observability**

- [ ] Implementar caching distribuido (Redis) para queries pesadas
- [ ] APM (Application Performance Monitoring) - Datadog o New Relic +++(No por ahora - ver mas adelante)+++
- [ ] Database query optimization (N+1 analysis)
- [ ] CDN para assets estáticos
- **Tiempo:** 1-2 semanas
- **COSTO:** ✅ ZERO (salvo herramientas APM si son Cloud)

### 8. **Security Hardening** 🔐

- [ ] Rate limiting en APIs (implemented frameworks, needs tuning)
- [ ] WAF (Web Application Firewall) setup
- [ ] Penetration testing
- [ ] GDPR compliance audit
- [ ] Secrets rotation policy
- **Tiempo:** 2-3 weeks
- **COSTO:** ✅ ZERO (salvo pentest professional si lo requiere)

### 9. **Testing Coverage Expansion**

- [ ] E2E tests browser (Pest 4 browser testing)
- [ ] Load testing (k6 o JMeter)
- [ ] Chaos testing (fault injection)
- [ ] Security testing (OWASP top 10)
- **Tiempo:** 1-2 weeks
- **COSTO:** ✅ ZERO

---

## 📈 Roadmap Q2 (Abril-Junio 2026)

### A. **Escala Inteligente**

- [ ] Scale messaging para teams (group chats)
- [ ] Integración con Teams/Slack
- [ ] Archiving y compliance retention
- **Est. Complejidad:** MEDIA

### B. **Analytics & Business Intelligence**

- [ ] Dashboard executivo (talent insights)
- [ ] Predictive Analytics para retention
- [ ] Skills gap analysis at enterprise level
- **Est. Complejidad:** ALTA

### C. **Ecosystem Integrations** (POSTERGAR - NO PRIORITARIO)**~~ 🛑

- [ ] SAP SuccessFactors API
- [ ] Workday connector
- [ ] Azure AD / Okta SSO refinement
- [ ] Calendar sync (Google Calendar, Outlook)
- **Est. Complejidad:** MEDIA

### D. **Community Features**

- [ ] Internal social network
- [ ] Skill communities / guilds
- [ ] Peer mentoring system
- [ ] Knowledge sharing (wikis, Q&A)
- **Est. Complejidad:** MEDIA

---

## 📌 Criterios de Aceptación (Definition of Done)

- ✅ All tests passing (unit + feature + E2E)
- ✅ Code review approved by 1+ senior dev
- ✅ Performance: p95 latency < 500ms, CPU < 70%
- ✅ Security: No HIGH/CRITICAL vulnerabilities
- ✅ Documentation: README + API docs updated
- ✅ Git: Semantic commits, clean merge history

---

## 🗓️ Timeline Recomendado

| Fase       | Items                         | Duración | ETA    | COSTO   |
| :--------- | :---------------------------- | :------- | :----- | :------ |
| **ASAP**   | 1-2 (Deploy + Talent Pass UI) | 1 semana | Mar 31 | $0      |
| **Week 2** | 3-6 (Admin, LMS, Mobile, WFP) | 2-3 sem  | Abr 14 | $0      |
| **Q2**     | 7-9 + Roadmap                 | 8+ sem   | Jun 30 | \*$100+ |

\*Q2 costos opcionales: APM ($100-300/mes), Professional Pentest (~$0.5-1k), etc.

---

## 📊 Recursos Recomendados

- **Frontend:** 1 dev (UI components, Tailwind CSS, Vue3)
- **Backend:** 1-2 devs (APIs, blockchain, integrations)
- **DevOps:** 1 dev (deployment, monitoring, K8s)
- **QA:** 1 dev (testing, automation, performance)

---

## 🚀 Velocity & Estimaciones

**Basado en Messaging MVP:**

- Average velocity: 12-15 story points/semana
- Sprint length: 1 semana (ágil)
- Próxima sprint: Mar 26-31 (Messaging deploy + Talent Pass UI)

---

**Última actualización:** Mar 26, 2026  
**Estado General:** 🟢 **ON TRACK** - Messaging MVP ✅, Plan sin costos operacionales

---

## 💰 RESUMEN FINANCIERO

### Costos Anticipados (Próximos 3 meses)

- **CERO** por desarrollo interno ✅
- **CERO** por blockchain (POSTPONED) ✅
- **Opcional Q2:** APM (~$100-300/mes), Pentest (~$500-1k one-time)

### ROI Focus

- ✅ Máximo valor con ZERO inversión
- ✅ Diferir blockchain hasta que sea business requirement
- ✅ Enfoque en features que generan daily user value
