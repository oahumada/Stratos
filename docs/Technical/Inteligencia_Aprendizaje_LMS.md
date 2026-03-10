# 🎓 Inteligencia de Aprendizaje & LMS Hub (Stratos Learning)

Este módulo constituye el "Cerebro de Capacitación" de Stratos, diseñado para cerrar automáticamente las brechas de habilidades detectadas por la IA después de simulaciones de movilidad o evaluaciones de desempeño.

Su arquitectura es **Dual y Agnóstica**, permitiendo tanto la gestión autónoma de contenido propio como la integración con plataformas corporativas líderes (Moodle, LinkedIn Learning, Udemy).

---

## 🏛️ Arquitectura del Sistema: The LMS Bridge

Stratos utiliza el **Provider Pattern** para abstraer la complejidad técnica de cada plataforma de aprendizaje bajo una interfaz unificada.

### Componentes Clave:

1. **`LmsService` (El Orquestador)**: Es la puerta de entrada única. Decide dinámicamente qué proveedor llamar basándose en el origen del curso seleccionado.
2. **`LmsProviderInterface` (El Contrato)**: Define los métodos obligatorios que cualquier nuevo proveedor debe implementar:
    - `searchCourses(string query)`: Búsqueda unificada en el catálogo.
    - `enrollUser(string courseId, string userId)`: Inscripción automática y transparente.
    - `getLaunchUrl(string courseId)`: URL de acceso directo (SSO apoyado).
    - `isCompleted(string enrollmentId)`: Sincronización de progreso.

---

## 🏗️ Los Pilares de Contenido

### 1. Autonomía: LMS Nativo de Stratos

Ideal para clientes que no poseen una plataforma propia o que necesitan gestionar contenido de misión crítica (ej: procesos internos de planta automotriz).

- **Modelos**: `LmsCourse`, `LmsModule`, `LmsLesson`, `LmsEnrollment`.
- **Capacidades**: Soporta video, PDF y contenido nativo en Markdown.
- **Seguridad**: Los cursos pueden ser exclusivos para una organización (`organization_id`).

### 2. Compatibilidad Corporativa (Conectores Externos)

Stratos actúa como un "Hub" que centraliza la experiencia de aprendizaje:

- **Moodle**: Integración vía Web Services para automatizar inscripciones y seguimiento.
- **LinkedIn Learning**: Acceso a bibliotecas de liderazgo y soft skills.
- **Udemy Business**: Catálogo masivo de habilidades técnicas y certificaciones industriales.
- **Mock Provider**: Para pruebas de flujo en entornos sin conexión a APIs reales.

---

## 🧠 Integración con la Inteligencia de Movilidad

El valor diferencial de Stratos es que el aprendizaje no es una lista estática, sino una **consecuencia estratégica**.

### El Flujo de "Upskilling Inteligente":

1. **Simulación**: El `MobilityAIAdvisorService` propone un movimiento (ej: Operador a Supervisor).
2. **Detección de Brecha**: El sistema identifica las habilidades faltantes (ej: Comunicación Asertiva, Protocolos Avanzados).
3. **Sugerencia Automática**: La IA escanea el Hub de LMS y asigna cursos específicos:
    - _Cuidado de Planta_ (Interno)
    - _Liderazgo para Supervisores_ (LinkedIn)
4. **Materialización**: Al aprobar el plan, el `ChangeSet` crea automáticamente las `DevelopmentActions` vinculadas a estos cursos en el LMS origen.

---

## 📘 Referencia Técnica de la API

### Búsqueda Unificada

`GET /api/talento-360/lms/search?query=liderazgo`
Devuelve resultados combinados de todas las fuentes configuradas.

### Lanzamiento de Curso

`POST /api/talento-360/lms/actions/{id}/launch`
Devuelve la URL única para que el colaborador inicie su capacitación sin fricción.

### Sincronización de Progreso

`POST /api/talento-360/lms/actions/{id}/sync`
Consulta al proveedor el estado actual del curso y actualiza el `status` del plan de desarrollo en Stratos.

---

## 🛣️ Roadmap de Evolución

1. **SSO Unificado (SAML/OIDC)**: Autenticación única para que el usuario nunca vea una pantalla de login externa.
2. **Pruebas y Certificados**: Generación interna de diplomas con validez organizacional.
3. **Analítica de IA sobre Aprendizaje**: Correlacionar el éxito de los movimientos de movilidad con la compleción de los cursos sugeridos.

---

**Documentación Generada por:** Antigravity Agent  
**Versión:** 1.0 (Marzo 2026)  
**Módulos Relacionados:** [Simulación de Movilidad Estratégica](file:///home/omar/Stratos/docs/Technical/Simulacion_Movilidad_Estrategica.md) | [ROI y Gestión de Contingencias](file:///home/omar/Stratos/docs/Technical/ROI_Contingency_Management.md)
