1. DISEÑO (nuevo)
   ├── Definir objetivos de aprendizaje (form)
   ├── IA sugiere outline basado en skill gaps del equipo (LearningArchitectValidator + SkillIntelligenceService)
   └── Ajustar manualmente → guardar como draft

2. ESTRUCTURA (nuevo frontend, backend ya existe)
   ├── Drag & drop de módulos (LmsModule ya existe)
   ├── Drag & drop de lecciones dentro de módulos (LmsLesson ya existe)
   └── Asignar duración por lección (duration_minutes ya existe)

3. CONTENIDO (parcialmente existe)
   ├── Editor WYSIWYG por lección (content_body ya existe, falta editor rich)
   ├── Generar contenido con IA por lección (ContentAgentService ya existe)
   ├── Subir video / embeber URL (content_url + content_type ya existen)
   └── Banco de preguntas para evaluación (NUEVO)

4. CONFIGURACIÓN (ya existe)
   ├── Política de certificación (cert_min_*, ya existe)
   ├── Template de certificado (LmsCertificateTemplate, ya existe)
   ├── XP y gamificación (xp_points, ya existe)
   └── Prerrequisitos + mandatory/due_date (NUEVO)

5. PUBLICAR (parcialmente existe)
   ├── Preview antes de publicar (NUEVO)
   ├── Publicar → is_active=true (ya existe)
   └── Asignar a personas/departamentos (enrollment, ya existe)

   Stratos LMS vs. LMS Líderes del Mercado — Gap Analysis
Lo que Stratos YA tiene bien (ventajas competitivas)
Ventaja	Detalle
### ✅ Integración nativa con talento
	El LMS no vive en silo — está conectado con planes de desarrollo, skills, workforce planning, Talent Pass
### ✅ Multi-tenant desde diseño	
Aislamiento por organization_id en todo, no es un bolt-on
### ✅ Agente IA operador	

LmsOperatorAgent automatiza enrollment, certificación, seguimiento — no existe en Cornerstone/Docebo

### ✅ Certificados verificables	
URL pública de verificación + firma digital + revocación

### ✅ Gamificación nativa	XP, niveles, leaderboard integrado
✅ Detección de learners en riesgo	Analytics con alertas automatizadas, no solo reportes pasivos
✅ SSO extensible	OAuth 2.0 PKCE con LinkedIn Learning, interfaz para cualquier proveedor

🔴 Lo que falta (gaps críticos vs. Docebo, Cornerstone, 360Learning, Degreed)

G1. Estándares de contenido e-learning
Gap	Impacto	Referencia mercado
No hay soporte SCORM (1.2 / 2004)	No puedes importar el 90% del contenido e-learning existente del mercado	Cornerstone, Docebo, Moodle — todos lo soportan
No hay soporte xAPI (Tin Can)	No puedes trackear experiencias de aprendizaje fuera de la plataforma (simuladores, apps, VR)	Degreed, EdCast — xAPI es el estándar moderno
No hay soporte cmi5	El sucesor de SCORM — necesario para contenido moderno empaquetado	Docebo, Absorb
G2. Autoría de contenido nativo
Gap	Impacto	Referencia mercado
No hay editor de contenido rico	Las lecciones solo tienen content_type + content_url / content_body — no hay editor WYSIWYG inline, slides, video embebido	Docebo Shape, 360Learning, TalentLMS
No hay banco de preguntas / quizzes	assessment_score existe pero no hay modelo de preguntas, respuestas, ni tipos de evaluación (opción múltiple, drag & drop, etc.)	Cornerstone, Moodle, TalentLMS
No hay encuestas / feedback post-curso	No mides satisfacción del learner (NPS del curso)	360Learning, Docebo
G3. Learning Paths estructurados
Gap	Impacto	Referencia mercado
No hay modelo LearningPath	Hay DevelopmentPath pero no un learning path secuencial con prerrequisitos y progreso acumulado	Cornerstone, Degreed, LinkedIn Learning
No hay prerrequisitos entre cursos	No puedes forzar "completa Curso A antes de desbloquear Curso B"	Docebo, Absorb
No hay cursos obligatorios/compliance	No hay campo is_mandatory, due_date, compliance_category	Cornerstone (compliance training es su fuerte)
G4. Aprendizaje social y colaborativo
Gap	Impacto	Referencia mercado
No hay foros / discusiones por curso	El aprendizaje colaborativo es la tendencia #1 en L&D	360Learning (su core), Docebo Social Learning
No hay peer review	Los learners no pueden evaluar el trabajo de otros	360Learning
No hay contenido generado por usuarios (UGC)	Solo admins crean contenido — en los LMS modernos los expertos internos contribuyen	360Learning, EdCast
No hay cohorts / cohortes de aprendizaje	No puedes agrupar learners para seguimiento colectivo	Docebo, Cornerstone
G5. Experiencia de contenido
Gap	Impacto	Referencia mercado
No hay reproductor de video integrado	No hay streaming, marcadores, transcripciones, subtítulos	Docebo, Panopto (integrado)
No hay microlearning	No hay formato de contenido breve (< 5 min) con spaced repetition	Axonify, EdApp
No hay adaptive learning	El camino es lineal — no se adapta al nivel del learner	Area9 Rhapsode, Docebo
No hay mobile app / PWA	Hay MobileBottomNav.vue pero no offline mode, no push notifications nativos	TalentLMS, Docebo
G6. Reporting y compliance
Gap	Impacto	Referencia mercado
No hay reportes exportables	Analytics son solo API JSON — no hay exportación a CSV/PDF/Excel	Cornerstone (200+ reportes prediseñados)
No hay compliance tracking	No hay categorías regulatorias (SOX, HIPAA, OSHA), fechas de vencimiento, recertificación automática	Cornerstone, Absorb
No hay audit trail del learner	Se trackea al nivel de enrollment, pero no a nivel de lección individual visitada	Cornerstone, Moodle
No hay scheduled reports	No puedes programar un reporte semanal automático al CHRO	Docebo
G7. Catálogo y descubrimiento
Gap	Impacto	Referencia mercado
No hay marketplace de contenido externo	No puedes navegar/comprar cursos de proveedores (Udemy Business, Coursera, Pluralsight) desde Stratos	Degreed (su core), Cornerstone Content Anytime
No hay recomendaciones IA de cursos	Hay Learning Blueprints (IA), pero no un motor de recomendación estilo "también tomaron..."	Degreed, EdCast
No hay tags/categorías ni filtros avanzados	Solo hay category y level — falta taxonomía rica, skills asociados, duración, rating	Docebo, Udemy Business
G8. Integraciones
Gap	Impacto	Referencia mercado
Solo un proveedor SSO implementado (LinkedIn Learning)	Falta SAP SuccessFactors, Workday, Cornerstone — son los más usados en enterprise	Degreed (40+ integraciones)
No hay webhook outbound	Eventos LMS no se publican como webhooks para que otros sistemas consuman	Docebo, Cornerstone
No hay integración con calendario (Google Calendar, Outlook)	Para instructor-led training (ILT) necesitas invitaciones de calendario	Cornerstone
No hay soporte para ILT (Instructor-Led Training)	Solo hay e-learning async — falta blended learning (presencial + virtual + online)	Cornerstone, Docebo
🟡 Priorización sugerida (por impacto en mercado)
Prioridad	Feature	Razón
P0	SCORM 1.2 player	Sin esto no puedes importar contenido existente — bloqueante para enterprise
P0	Learning Paths con prerrequisitos	Fundamental para cualquier programa de formación serio
P0	Quizzes / banco de preguntas	Sin evaluación nativa no certificas conocimiento
P1	Compliance training (mandatory + due_date)	Requisito regulatorio en enterprise (banca, salud, energía)
P1	Reportes exportables (CSV/PDF)	El CHRO necesita llevar un PDF a la junta, no un JSON
P1	Catálogo con filtros + búsqueda avanzada	La experiencia del learner empieza por encontrar el curso
P2	xAPI tracking	Para experiencias fuera de la plataforma
P2	Video player con transcripción	Contenido video es 60%+ del e-learning moderno
P2	Social learning (discusiones por curso)	Diferenciador vs LMS legacy
P3	Microlearning + spaced repetition	Tendencia fuerte pero no bloqueante
P3	Marketplace de contenido externo	Requiere acuerdos comerciales con Udemy/Coursera
P3	PWA / offline mode	Para equipos de campo sin conectividad constante
La ventaja única de Stratos
Lo que ningún LMS del mercado tiene es tu integración nativa con Workforce Planning + Skill Intelligence + Performance AI. La mayoría de los LMS son silos que miden "¿completó el curso?" — Stratos puede responder "¿cerró la brecha de skill que motivó el curso?", y eso es el santo grial de L&D.

