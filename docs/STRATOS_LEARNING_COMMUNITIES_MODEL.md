# Stratos Learning Communities — Modelo Teórico y Propuesta de Implementación

> **Fecha**: 5 de abril de 2026  
> **Objetivo**: Fundamentar con modelos modernos de aprendizaje social la implementación de Comunidades de Aprendizaje en Stratos LMS.

---

## 📚 Marcos Teóricos de Comunidades de Aprendizaje

### 1. Communities of Practice (CoP) — Lave & Wenger (1991, 1998)

El modelo más influyente. Tres pilares:

- **Dominio**: interés/competencia compartida que da identidad al grupo
- **Comunidad**: la red social que fomenta interacción y confianza
- **Práctica**: el repertorio compartido de recursos, experiencias y herramientas

> _"Most learning does not take place with the master, it takes place among the apprentices"_ — Wenger

**Referencia clave**: Lave, J. & Wenger, E. (1991). _Situated Learning: Legitimate Peripheral Participation_. Cambridge University Press. / Wenger, E. (1998). _Communities of Practice: Learning, Meaning, and Identity_. Cambridge University Press.

### 2. Connectivism — Siemens & Downes (2004)

Teoría para la era digital:

- El conocimiento reside en **redes**, no en individuos
- Aprender = crear conexiones entre nodos especializados
- "Know-where" (saber dónde encontrar) es tan importante como "know-how"
- Las conexiones que permiten aprender más **son más importantes** que el estado actual de conocimiento

**Principios clave del Connectivism**:

1. El aprendizaje y el conocimiento descansan en la diversidad de opiniones
2. El aprendizaje es un proceso de conectar nodos o fuentes de información especializadas
3. El aprendizaje puede residir en dispositivos no humanos
4. La capacidad de aprender más es más crítica que lo que se sabe actualmente
5. Mantener y nutrir conexiones es necesario para facilitar el aprendizaje continuo
6. Percibir conexiones entre campos, ideas y conceptos es una habilidad central
7. La toma de decisiones es en sí misma un proceso de aprendizaje

**Referencia clave**: Siemens, G. (2005). _Connectivism: A Learning Theory for the Digital Age_. / Downes, S. (2005). _An Introduction to Connective Knowledge_.

### 3. Community of Inquiry (CoI) — Garrison, Anderson & Archer (1996)

Tres presencias para aprendizaje profundo online:

- **Presencia Cognitiva**: exploración, integración, resolución de problemas
- **Presencia Social**: comunicación abierta, cohesión grupal, expresión afectiva
- **Presencia Docente/Facilitadora**: diseño instruccional, facilitación del discurso, instrucción directa

El modelo CoI fue desarrollado en la Universidad de Alberta para proporcionar orden conceptual y una herramienta para el uso de comunicación mediada por computadora en apoyo a una experiencia educativa. La interacción de las tres presencias crea lo que Garrison et al. llaman la "experiencia educativa".

**Referencia clave**: Garrison, D.R., Anderson, T., & Archer, W. (2000). _Critical Inquiry in a Text-Based Environment: Computer Conferencing in Higher Education_. The Internet and Higher Education.

### 4. Legitimate Peripheral Participation (LPP) — Lave & Wenger (1991)

- Novatos → participantes plenos a través de participación gradual
- Empiezan con tareas simples pero productivas
- Progresivamente asumen roles más centrales
- El mentor controla el acceso y confiere legitimidad

> Los novatos se vuelven miembros de una comunidad inicialmente participando en tareas simples y de bajo riesgo que son, no obstante, productivas y necesarias para los objetivos de la comunidad. A través de actividades periféricas, los novatos se familiarizan con las tareas, vocabulario y principios organizativos de los practicantes de la comunidad.

**Referencia clave**: Lave, J. & Wenger, E. (1991). _Situated Learning: Legitimate Peripheral Participation_. Cambridge University Press.

### 5. Professional Learning Communities (PLC) — Senge, DuFour

- Aprendizaje colaborativo entre profesionales
- Reflexión compartida, des-privatización de la práctica
- Foco en resultados, no en intenciones

> _"Transforming the culture of schools and the systems within which they operate is the main point. It is not an innovation to be implemented, but rather a new culture to be developed."_ — Michael Fullan

Características clave de las PLC (Ontario Ministry of Education, 2005):

- Visión y valores compartidos que llevan a compromiso colectivo
- Búsqueda activa de soluciones, apertura a nuevas ideas
- Equipos de trabajo que cooperan para lograr objetivos comunes
- Estímulo a la experimentación como oportunidad de aprendizaje
- Cuestionamiento del status quo → búsqueda continua de mejora
- Mejora continua basada en evaluación de resultados, no en intenciones

**Referencia clave**: Senge, P. (1990). _The Fifth Discipline_. / DuFour, R. & Eaker, R. (1998). _Professional Learning Communities at Work_. / Hord, S.M. (1997). _Professional Learning Communities: Communities of Continuous Inquiry and Improvement_.

### 6. Social Learning Theory — Bandura (1977)

Complemento fundamental que explica los mecanismos por los que ocurre el aprendizaje social:

- Las personas adquieren nuevos comportamientos **observando e imitando** a otros
- El aprendizaje es un **proceso cognitivo** que ocurre dentro de un contexto social
- Puede ocurrir puramente por observación, sin práctica directa ni refuerzo
- **Refuerzo vicario**: cuando un comportamiento es recompensado consistentemente, tiende a persistir; cuando es castigado, tiende a cesar

**Referencia clave**: Bandura, A. (1977). _Social Learning Theory_. Prentice Hall.

---

## 🔍 Lo que Stratos YA tiene (Building Blocks)

| Componente                                  | Estado          | Modelo/Servicio                                           |
| ------------------------------------------- | --------------- | --------------------------------------------------------- |
| Discusiones por curso (threaded, likes)     | ✅ Implementado | `LmsDiscussion`, `LmsDiscussionLike`, `DiscussionService` |
| Cohorts / grupos de aprendizaje             | ✅ Implementado | `LmsCohort`, `LmsCohortMember`, `CohortService`           |
| UGC con moderación                          | ✅ Implementado | `LmsUserContent`, `UgcService`                            |
| Peer Review con rúbricas                    | ✅ Implementado | `LmsPeerReview`, `PeerReviewService`                      |
| Skill Intelligence + Gap detection          | ✅ Existente    | `SkillIntelligenceService`                                |
| Adaptive Learning profiles                  | ✅ Implementado | `LmsLearnerProfile`, `AdaptiveLearningService`            |
| Gamificación (XP, badges, leaderboard)      | ✅ Existente    | Sistema de gamificación nativo                            |
| Workforce Planning con detección de brechas | ✅ Existente    | Módulo WFP completo                                       |

**Estos son componentes aislados.** No hay un modelo integrado de comunidad que los una bajo un marco teórico coherente.

---

## 🧠 Modelo Propuesto: Stratos Learning Communities (SLC)

Un modelo híbrido que fusiona **CoP + CoI + Connectivism + LPP**, adaptado al contexto corporativo de Stratos.

### Arquitectura del modelo

```
┌─────────────────────────────────────────────────┐
│           STRATOS LEARNING COMMUNITY            │
│                                                 │
│  ┌──────────┐  ┌──────────┐  ┌──────────────┐  │
│  │ DOMINIO  │  │COMUNIDAD │  │  PRÁCTICA    │  │
│  │(CoP)     │  │(CoI+LPP) │  │(Connectivism)│  │
│  │          │  │          │  │              │  │
│  │• Skills  │  │• Roles:  │  │• Knowledge   │  │
│  │  target  │  │  Novice  │  │  Base        │  │
│  │• Learning│  │  Member  │  │• Shared      │  │
│  │  goals   │  │  Mentor  │  │  Resources   │  │
│  │• Aligned │  │  Expert  │  │• Best        │  │
│  │  to WFP  │  │  Leader  │  │  Practices   │  │
│  │          │  │• Social  │  │• Projects    │  │
│  │          │  │  presence│  │• Connections  │  │
│  └──────────┘  └──────────┘  └──────────────┘  │
│                                                 │
│  ┌─────────────────────────────────────────┐    │
│  │        HEALTH METRICS (Analytics)       │    │
│  │  Engagement · Knowledge flow · Impact   │    │
│  └─────────────────────────────────────────┘    │
└─────────────────────────────────────────────────┘
```

### Mapeo Teórico → Implementación

| Concepto Teórico                  | Marco                     | Implementación en Stratos                                                              |
| --------------------------------- | ------------------------- | -------------------------------------------------------------------------------------- |
| Dominio compartido                | CoP (Wenger)              | Community vinculada a skills target + learning goals alineados con WFP                 |
| Participación Periférica Legítima | LPP (Lave & Wenger)       | Sistema de roles progresivos: Novice → Member → Contributor → Mentor → Expert → Leader |
| Presencia Cognitiva               | CoI (Garrison)            | Knowledge Base wiki + discusiones reflexivas + proyectos compartidos                   |
| Presencia Social                  | CoI (Garrison)            | Activity Feed + reconocimiento + badges de expertise                                   |
| Presencia Docente                 | CoI (Garrison)            | Rol de Facilitador/Leader que guía el discurso y diseña actividades                    |
| Redes de conocimiento             | Connectivism (Siemens)    | Cross-community connections + landscape of practice + recomendaciones IA               |
| Aprendizaje observacional         | Social Learning (Bandura) | Best practices compartidas + UGC + peer review visible                                 |
| Reflexión compartida              | PLC (DuFour)              | Retrospectivas de comunidad + métricas de impacto en skills                            |

### Componentes nuevos que agregaría al sistema

1. **Community entity** — Entidad central que agrupa todo (dominio, miembros, recursos, actividad)
2. **Roles con progresión (LPP)** — Novice → Member → Contributor → Mentor → Expert → Leader
3. **Knowledge Base** — Wiki/artículos compartidos por la comunidad (evolución del UGC actual)
4. **Activity Feed** — Timeline social estilo LinkedIn/Yammer dentro de la comunidad
5. **Mentorship System** — Matching mentor-mentee basado en skills + proficiency level
6. **Reputation/Expertise** — Puntos de contribución, badges de expertise, reconocimiento
7. **Community Health Dashboard** — Métricas de salud: engagement, knowledge flow, impacto en skills
8. **Cross-community connections (Landscape of Practice)** — Miembros en múltiples comunidades, conocimiento fluye entre ellas

### Flujo de la experiencia del usuario

```
1. DESCUBRIMIENTO
   └── IA detecta brecha de skills en WFP
       └── Sugiere comunidad existente o propone crear una nueva

2. ONBOARDING (LPP - Participación Periférica)
   └── Rol: Novice
       ├── Observar discusiones y best practices
       ├── Completar "learning path de entrada" sugerido
       └── Presentarse en el Activity Feed

3. PARTICIPACIÓN ACTIVA (CoP - Práctica compartida)
   └── Rol: Member → Contributor
       ├── Participar en discusiones
       ├── Compartir recursos y experiencias (UGC)
       ├── Colaborar en peer reviews
       └── Completar cursos recomendados por la comunidad

4. LIDERAZGO (CoI - Presencia Docente)
   └── Rol: Mentor → Expert → Leader
       ├── Mentorar a nuevos miembros
       ├── Crear contenido para la Knowledge Base
       ├── Facilitar discusiones y proyectos
       └── Diseñar actividades de aprendizaje

5. IMPACTO (PLC - Resultados medibles)
   └── Dashboard de salud de la comunidad
       ├── ¿Se cerraron las brechas de skills?
       ├── ¿Cuánto conocimiento tácito se convirtió en explícito?
       ├── ¿Cuál es el NPS de la comunidad?
       └── ¿Cuál es el ROI medible?
```

---

## ✅ ¿Vale la pena? **SÍ, rotundamente.**

### Razones estratégicas

1. **Diferenciador competitivo #1**: 360Learning basa TODO su valor en social learning. Es la tendencia más fuerte en L&D moderno. Pero ningún LMS integra comunidades con **Talent Intelligence**.

2. **Modelo 70-20-10**: El consenso en L&D es que el 70% del aprendizaje es experiencial, 20% social, y solo 10% formal (cursos). Stratos LMS ya cubre el 10% formal. Las comunidades cubren el 20% social.

3. **Ya tienes los building blocks**: Discusiones, Cohorts, UGC, Peer Review, Adaptive Profiles — solo falta el "pegamento" que los integre como comunidad.

4. **Ventaja ÚNICA de Stratos**: Ningún LMS puede hacer esto →

    > _"Crear una comunidad de práctica automáticamente cuando el análisis de Workforce Planning detecta una brecha de skills crítica en el departamento de Ingeniería, asignando mentores basándose en proficiency levels del Talent Pass"_

    Eso es **AI-driven community formation** — algo que no existe en ningún competidor.

5. **ROI medible**: Puedes medir si la participación en una comunidad **cierra brechas de skills** (conectando con el Skill Intelligence existente), algo que ningún competidor ofrece.

### Análisis de inversión

La inversión es moderada:

- ~5-6 modelos nuevos, ~2 servicios, ~1 controlador
- Reutiliza mucho del sistema existente (discusiones, UGC, cohorts, peer review)
- Es un **modelo integrador**, no una reescritura

### Comparación con competidores

| Feature                             | 360Learning  | Docebo    | Cornerstone | Degreed | **Stratos SLC** |
| ----------------------------------- | ------------ | --------- | ----------- | ------- | --------------- |
| Discusiones por curso               | ✅           | ✅        | ✅          | ❌      | ✅              |
| Comunidades formales                | ✅           | ⚠️ Básico | ❌          | ✅      | ✅ (propuesto)  |
| Roles progresivos (LPP)             | ❌           | ❌        | ❌          | ❌      | ✅ (propuesto)  |
| Knowledge Base por comunidad        | ❌           | ❌        | ❌          | ⚠️      | ✅ (propuesto)  |
| Mentorship matching por skills      | ❌           | ❌        | ❌          | ❌      | ✅ (propuesto)  |
| Community Health Analytics          | ❌           | ❌        | ❌          | ❌      | ✅ (propuesto)  |
| AI-driven community formation       | ❌           | ❌        | ❌          | ❌      | ✅ (propuesto)  |
| Integración con Talent Intelligence | ❌           | ❌        | ❌          | ⚠️      | ✅ Nativo       |
| UGC con moderación                  | ✅ (su core) | ⚠️        | ❌          | ✅      | ✅              |
| Peer Review                         | ✅           | ❌        | ❌          | ❌      | ✅              |

---

## 📖 Bibliografía

1. Lave, J. & Wenger, E. (1991). _Situated Learning: Legitimate Peripheral Participation_. Cambridge University Press.
2. Wenger, E. (1998). _Communities of Practice: Learning, Meaning, and Identity_. Cambridge University Press.
3. Wenger, E., McDermott, R. & Snyder, W.M. (2002). _Cultivating Communities of Practice_. Harvard Business Press.
4. Wenger, E. (2009). _Digital Habitats: Stewarding Technology for Communities_. CPsquare.
5. Siemens, G. (2005). _Connectivism: A Learning Theory for the Digital Age_.
6. Downes, S. (2005). _An Introduction to Connective Knowledge_.
7. Garrison, D.R., Anderson, T. & Archer, W. (2000). _Critical Inquiry in a Text-Based Environment_. The Internet and Higher Education.
8. Bandura, A. (1977). _Social Learning Theory_. Prentice Hall.
9. Senge, P. (1990). _The Fifth Discipline_. Doubleday.
10. DuFour, R. & Eaker, R. (1998). _Professional Learning Communities at Work_. Solution Tree.
11. Hord, S.M. (1997). _Professional Learning Communities: Communities of Continuous Inquiry and Improvement_. SEDL.
12. Brown, J.S., Collins, A. & Duguid, P. (1989). _Situated Cognition and the Culture of Learning_. Educational Researcher.
13. Lesser, E. & Storck, J. (2001). _Communities of Practice and Organizational Performance_. IBM Systems Journal.

---

_Documento generado como base teórica para la implementación del módulo de Comunidades de Aprendizaje en Stratos LMS._
