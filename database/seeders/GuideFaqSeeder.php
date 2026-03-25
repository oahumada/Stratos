<?php

namespace Database\Seeders;

use App\Models\GuideFaq;
use Illuminate\Database\Seeder;

/**
 * Semilla de preguntas frecuentes para Stratos Guide.
 *
 * Proporciona una base de conocimiento inicial para el sistema RAG.
 * Las entradas son globales (organization_id = null) y están disponibles
 * para todas las organizaciones. Para contenido específico por organización,
 * crea registros con organization_id asignado.
 */
class GuideFaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [

            // ─── Primeros Pasos ────────────────────────────────────────────
            [
                'slug' => 'que-es-stratos',
                'category' => 'getting_started',
                'title' => '¿Qué es Stratos?',
                'question' => '¿Qué es Stratos y para qué sirve?',
                'answer' => 'Stratos es una plataforma SaaS de planificación estratégica del talento. Permite a las organizaciones modelar escenarios de fuerza laboral, mapear competencias, identificar brechas de habilidades, gestionar perfiles de roles y obtener recomendaciones de desarrollo mediante inteligencia artificial. Su enfoque es ayudar a los equipos de RRHH y líderes a tomar decisiones basadas en datos sobre el capital humano.',
                'tags' => ['introducción', 'plataforma', 'talento'],
            ],
            [
                'slug' => 'como-comenzar-stratos',
                'category' => 'getting_started',
                'title' => '¿Cómo comenzar a usar Stratos?',
                'question' => '¿Cuáles son los primeros pasos para configurar Stratos en mi organización?',
                'answer' => 'Para comenzar: 1) Crea tu organización e invita a los usuarios administradores. 2) Importa o crea los roles de tu organización (Paso 1 del asistente). 3) Define el catálogo de competencias y habilidades (Paso 2). 4) Asigna competencias a los roles. 5) Carga los perfiles de tus colaboradores (People). 6) Evalúa el talento actual con las herramientas de 360° o cargo directo. Con estos datos puedes comenzar a generar escenarios y obtener análisis de brechas.',
                'tags' => ['onboarding', 'configuración', 'primeros pasos'],
            ],
            [
                'slug' => 'multi-tenant-organizaciones',
                'category' => 'getting_started',
                'title' => 'Multi-tenancy: organizaciones aisladas',
                'question' => '¿Los datos de mi organización son privados? ¿Pueden otras organizaciones ver mi información?',
                'answer' => 'Sí, Stratos es completamente multi-tenant. Cada organización tiene sus datos totalmente aislados. Todos los registros (roles, personas, competencias, escenarios, evaluaciones) están vinculados por organization_id y ninguna consulta puede acceder a datos de otras organizaciones. El middleware de autenticación valida este aislamiento en cada petición.',
                'tags' => ['seguridad', 'privacidad', 'multi-tenant'],
            ],

            // ─── Planificación de Escenarios ───────────────────────────────
            [
                'slug' => 'que-es-un-escenario',
                'category' => 'scenario_planning',
                'title' => '¿Qué es un escenario en Stratos?',
                'question' => '¿Qué es un escenario y cómo se diferencia de mi estructura organizacional actual?',
                'answer' => 'Un escenario en Stratos es una proyección hipotética de cómo podría evolucionar tu organización. Mientras la estructura actual refleja el presente, un escenario modela un futuro posible: nuevos roles, cambios en competencias requeridas, transformaciones organizacionales. Puedes crear múltiples escenarios, compararlos y seleccionar el que mejor se alinea con tu estrategia. Los escenarios tienen estados (draft, in_review, approved, active, completed, archived) que gobiernan su ciclo de vida.',
                'tags' => ['escenarios', 'planificación', 'futuro'],
            ],
            [
                'slug' => 'como-generar-escenario-ia',
                'category' => 'scenario_planning',
                'title' => 'Generación de escenarios con IA',
                'question' => '¿Cómo funciona la generación de escenarios con inteligencia artificial?',
                'answer' => 'El asistente de IA de Stratos analiza el contexto estratégico que proporcionas (tendencias del mercado, objetivos de negocio, cambios tecnológicos) y utiliza modelos de lenguaje (LLM) para proponer: nuevos roles, competencias clave a desarrollar, roles en riesgo de obsolescencia y un roadmap de transformación. El resultado se guarda como generación (ScenarioGeneration) en estado borrador para que puedas revisarlo, editarlo y eventualmente aceptarlo como escenario formal. Todo el proceso es auditable.',
                'tags' => ['IA', 'generación', 'LLM', 'escenarios'],
            ],
            [
                'slug' => 'estados-del-escenario',
                'category' => 'scenario_planning',
                'title' => 'Estados del ciclo de vida de un escenario',
                'question' => '¿Cuáles son los estados de un escenario y qué significa cada uno?',
                'answer' => 'Los escenarios siguen este ciclo: draft (borrador inicial, editable) → in_review (enviado para revisión, requiere aprobación) → approved (aprobado, listo para activar) → active (escenario vigente, en ejecución) → completed (finalizado exitosamente) → archived (archivado, histórico). También existe cancelled. Un escenario draft puede editarse libremente. Una vez en in_review o superior, los cambios requieren revertirlo a draft primero. Solo puede haber un escenario active por organización.',
                'tags' => ['estados', 'ciclo de vida', 'workflow'],
            ],
            [
                'slug' => 'comparar-escenarios',
                'category' => 'scenario_planning',
                'title' => 'Comparación de escenarios',
                'question' => '¿Cómo puedo comparar dos o más escenarios entre sí?',
                'answer' => 'Desde la vista de Escenarios puedes seleccionar dos escenarios y usar la función "Comparar". Stratos muestra lado a lado: roles presentes en cada escenario, competencias requeridas, número de FTEs proyectados, costo impacto estimado y brecha de habilidades. Esto te ayuda a evaluar trade-offs antes de comprometerte con un camino de transformación.',
                'tags' => ['comparación', 'análisis', 'escenarios'],
            ],
            [
                'slug' => 'roles-en-escenario',
                'category' => 'scenario_planning',
                'title' => 'Roles dentro de un escenario',
                'question' => '¿Cómo se gestionan los roles dentro de un escenario?',
                'answer' => 'Cada escenario tiene su propia colección de roles (ScenarioRole) independiente del catálogo de roles actual. Puedes agregar roles existentes de tu catálogo, crear roles nuevos específicos del escenario, definir FTEs requeridos, asignar peso estratégico y establecer prioridades. Los roles del escenario tienen su propio mapeo de competencias y pueden generar embeddings para búsqueda semántica de roles similares.',
                'tags' => ['roles', 'escenarios', 'FTE', 'transformación'],
            ],

            // ─── Mapa de Competencias ──────────────────────────────────────
            [
                'slug' => 'que-es-competencia',
                'category' => 'competency_map',
                'title' => '¿Qué es una competencia en Stratos?',
                'question' => '¿Cómo se define una competencia y en qué se diferencia de una habilidad (skill)?',
                'answer' => 'En Stratos, una competencia es un conjunto estructurado de conocimientos, habilidades y comportamientos que permiten desempeñarse efectivamente en un contexto. Las habilidades (skills) son más granulares y técnicas: pueden ser "Python", "Negociación" o "Análisis de datos". Una competencia agrupa múltiples skills bajo un marco conceptual: por ejemplo, "Liderazgo Digital" puede incluir skills de gestión ágil, comunicación virtual y toma de decisiones basada en datos.',
                'tags' => ['competencias', 'habilidades', 'skills', 'definición'],
            ],
            [
                'slug' => 'niveles-de-habilidad',
                'category' => 'competency_map',
                'title' => 'Niveles de habilidad BARS',
                'question' => '¿Qué son los niveles de habilidad y cómo se usan las definiciones BARS?',
                'answer' => 'Los niveles de habilidad en Stratos van del 1 (básico) al 5 (experto). Para cada skill puedes definir descriptores conductuales (BARS - Behaviorally Anchored Rating Scales) que especifican qué comportamientos concretos se esperan en cada nivel. Por ejemplo, para "Python" nivel 3: "Es capaz de escribir scripts de automatización y trabajar con APIs REST, sin supervisión". Los BARS hacen la evaluación más objetiva y calibrada entre evaluadores.',
                'tags' => ['BARS', 'niveles', 'habilidades', 'evaluación'],
            ],
            [
                'slug' => 'mapear-competencias-roles',
                'category' => 'competency_map',
                'title' => 'Mapeo de competencias a roles',
                'question' => '¿Cómo asocio competencias a un rol y qué significa el nivel requerido?',
                'answer' => 'En la vista de un rol, puedes agregar competencias desde el catálogo y definir el nivel mínimo requerido (1-5). Este nivel es el umbral que necesita alcanzar una persona en ese rol. Cuando evalúas a alguien con ese rol, Stratos compara su nivel actual vs el requerido y calcula la brecha. Las competencias con brecha grande se priorizan en los planes de desarrollo individual.',
                'tags' => ['roles', 'competencias', 'brecha', 'nivel requerido'],
            ],
            [
                'slug' => 'deteccion-duplicados-semanticos',
                'category' => 'competency_map',
                'title' => 'Detección de duplicados semánticos en competencias',
                'question' => '¿Stratos detecta automáticamente competencias o roles similares para evitar duplicados?',
                'answer' => 'Sí, cuando está activa la función de embeddings (FEATURE_GENERATE_EMBEDDINGS=true), Stratos vectoriza las competencias y roles en el momento de crearlos y los compara con el catálogo existente usando similitud coseno (pgvector). Si la similitud supera el 90%, el sistema te avisa que ya existe una competencia semánticamente equivalente y te sugiere reutilizarla en lugar de crear un duplicado. Esto mantiene el catálogo limpio y coherente.',
                'tags' => ['IA', 'duplicados', 'embeddings', 'semantic search'],
            ],

            // ─── Talento 360° ──────────────────────────────────────────────
            [
                'slug' => 'evaluacion-360-stratos',
                'category' => 'talent_360',
                'title' => 'Evaluación 360° en Stratos',
                'question' => '¿Qué es la evaluación 360° y cómo se configura en Stratos?',
                'answer' => 'La evaluación 360° en Stratos permite que una persona sea evaluada desde múltiples perspectivas: autoevaluación, jefe directo, pares, colaboradores y clientes internos. Se configura por ciclo de evaluación: defines los evaluadores, las competencias/skills a evaluar y el período. Los resultados se agregan ponderadamente y generan un perfil de talento multidimensional. Stratos calcula el delta entre la autopercepción y la evaluación externa, detectando blind spots.',
                'tags' => ['360', 'evaluación', 'feedback', 'talento'],
            ],
            [
                'slug' => 'brecha-de-habilidades',
                'category' => 'talent_360',
                'title' => 'Análisis de brecha de habilidades',
                'question' => '¿Cómo calcula Stratos la brecha de habilidades de mi equipo?',
                'answer' => 'Stratos compara nivel actual (medido por evaluaciones) vs nivel requerido (definido en el rol). La brecha = nivel_requerido - nivel_actual. Si alguien tiene nivel 2 en "Análisis de datos" y su rol requiere nivel 4, la brecha es 2. El sistema agrega estas brechas por equipo, departamento y organización. Puedes ver un heatmap de brechas que muestra dónde concentrar los esfuerzos de desarrollo.',
                'tags' => ['brecha', 'habilidades', 'análisis', 'desarrollo'],
            ],
            [
                'slug' => 'riesgos-de-talento',
                'category' => 'talent_360',
                'title' => 'Identificación de riesgos de talento',
                'question' => '¿Cómo identifica Stratos los riesgos de talento en mi organización?',
                'answer' => 'Stratos analiza varios indicadores de riesgo: (1) Concentración de conocimiento crítico en pocas personas, (2) Roles sin sucesor identificado, (3) Alta brecha en competencias estratégicas, (4) Personas con bajo engagement según encuestas, (5) Rotación histórica alta en ciertos perfiles. El módulo de Talent Risk genera un score de riesgo por persona y por área, permitiendo tomar acciones preventivas antes de perder talento clave.',
                'tags' => ['riesgo', 'talento', 'sucesión', 'retención'],
            ],

            // ─── Planificación de Fuerza Laboral ──────────────────────────
            [
                'slug' => 'workforce-planning-concepto',
                'category' => 'workforce_planning',
                'title' => '¿Qué es Workforce Planning?',
                'question' => '¿Qué es la planificación de fuerza laboral y qué módulo de Stratos la cubre?',
                'answer' => 'El Workforce Planning (WFP) en Stratos es la Fase 1 del módulo de planificación estratégica. Permite crear Planes de Fuerza Laboral formales con: unidades de alcance (scope units), roles necesarios por unidad, proyectos de transformación asociados, riesgos de talento identificados, stakeholders involucrados y documentos adjuntos. Los planes siguen un ciclo de vida (draft → in_review → approved → active) y soportan múltiples versiones para análisis histórico.',
                'tags' => ['workforce planning', 'fuerza laboral', 'planificación estratégica'],
            ],
            [
                'slug' => 'planes-wfp-estados',
                'category' => 'workforce_planning',
                'title' => 'Estados de un plan de fuerza laboral',
                'question' => '¿Cuáles son las fases de un plan de workforce planning y quién puede aprobarlos?',
                'answer' => 'Los planes de WFP pasan por: draft (editable, solo creador/admin), in_review (para aprobación, se notifica a stakeholders), approved (listo para ejecución), active (en curso), completed (finalizado) y archived. Para aprobar un plan se requiere rol de manager o admin. Los planes en draft pueden editarse libremente; una vez en in_review requieren ser rechazados primero para modificarse. La política `WorkforcePlanPolicy` controla qué acciones puede hacer cada rol.',
                'tags' => ['WFP', 'estados', 'aprobación', 'workflow'],
            ],
            [
                'slug' => 'scope-units-wfp',
                'category' => 'workforce_planning',
                'title' => 'Unidades de alcance en WFP',
                'question' => '¿Qué son las scope units y cómo se usan en un plan de fuerza laboral?',
                'answer' => 'Las Scope Units son las unidades organizacionales que cubre un plan de WFP (departamentos, áreas, equipos, BUs). Para cada scope unit defines: roles necesarios con FTEs requeridos, fechas de disponibilidad, nivel de criticidad y si el rol es nuevo o de reemplazo. Esta granularidad permite calcular el headcount total del plan, identificar cuellos de botella de contratación y asignar presupuesto por área.',
                'tags' => ['scope units', 'WFP', 'headcount', 'FTE'],
            ],

            // ─── IA y RAG ──────────────────────────────────────────────────
            [
                'slug' => 'que-es-rag-stratos',
                'category' => 'rag_intelligence',
                'title' => '¿Qué es RAG en Stratos?',
                'question' => '¿Qué significa RAG y cómo lo usa Stratos para responder preguntas?',
                'answer' => 'RAG (Retrieval Augmented Generation) es la tecnología que usa Stratos para responder preguntas sobre tu organización con información real. Funciona en tres pasos: (1) Recupera documentos relevantes de tu base de conocimiento (evaluaciones, FAQs, datos de competencias) usando búsqueda híbrida keywords + embeddings semánticos, (2) Construye un contexto enriquecido con esos documentos, (3) Envía el contexto + la pregunta al LLM para generar una respuesta fundamentada. Así el LLM no "inventa": responde con tus datos reales.',
                'tags' => ['RAG', 'IA', 'preguntas', 'búsqueda semántica'],
            ],
            [
                'slug' => 'como-usar-rag-ask',
                'category' => 'rag_intelligence',
                'title' => 'Cómo usar el endpoint /api/rag/ask',
                'question' => '¿Cómo puedo hacer preguntas sobre mi organización usando la IA de Stratos?',
                'answer' => 'Usa el endpoint POST /api/rag/ask con los parámetros: "question" (tu pregunta en lenguaje natural), "context_type" (evaluations, guide_faq, all) y "max_sources" (cuántos documentos recuperar, máximo 10). Ejemplo: {"question": "¿Cuál es la calidad promedio de mis evaluaciones LLM?", "context_type": "evaluations", "max_sources": 5}. La respuesta incluye la respuesta generada, las fuentes utilizadas y un score de confianza.',
                'tags' => ['RAG', 'API', 'preguntas', 'endpoint'],
            ],
            [
                'slug' => 'embeddings-busqueda-semantica',
                'category' => 'rag_intelligence',
                'title' => 'Búsqueda semántica con embeddings',
                'question' => '¿Qué es la búsqueda semántica y cómo mejora los resultados en Stratos?',
                'answer' => 'La búsqueda semántica usa vectores matemáticos (embeddings) para encontrar contenido relacionado por significado, no solo por palabras exactas. Por ejemplo, si buscas "persona que gestiona equipos ágiles" encontrará roles con "Scrum Master" o "Agile Coach" aunque no usen esas palabras exactas. Stratos usa el modelo text-embedding-3-small de OpenAI y almacena los vectores en PostgreSQL con la extensión pgvector, que permite cálculos de similitud coseno eficientes.',
                'tags' => ['embeddings', 'búsqueda semántica', 'pgvector', 'similitud'],
            ],
            [
                'slug' => 'calidad-evaluaciones-llm',
                'category' => 'rag_intelligence',
                'title' => 'Calidad de las evaluaciones LLM',
                'question' => '¿Cómo mide Stratos la calidad de las respuestas generadas por IA?',
                'answer' => 'Stratos usa métricas RAGAS para evaluar la calidad de sus generaciones LLM: (1) Faithfulness: qué tanto la respuesta se basa en el contexto recuperado (evita alucinaciones), (2) Answer Relevancy: relevancia de la respuesta respecto a la pregunta, (3) Context Precision: precisión de los documentos recuperados, (4) Context Recall: cobertura del contexto necesario, (5) Answer Correctness: corrección factual. Un score compuesto pondera estas 5 métricas. Puedes ver estas métricas en el Quality Dashboard.',
                'tags' => ['RAGAS', 'calidad', 'LLM', 'alucinaciones', 'métricas'],
            ],
            [
                'slug' => 'anomaly-detection-analytics',
                'category' => 'analytics',
                'title' => 'Detección de anomalías en Analytics',
                'question' => '¿Cómo detecta Stratos anomalías en el desempeño de la IA?',
                'answer' => 'El módulo de Analytics de Stratos usa algoritmos estadísticos para detectar anomalías: (1) Z-score: detecta picos de latencia o caídas de calidad que superen 2.5 desviaciones estándar, (2) Desviación de tendencia: alerta cuando el valor se desvía más de 15% de la tendencia histórica, (3) Degradación de salud: identifica períodos sostenidos de bajo rendimiento. Las anomalías detectadas disparan workflows de remediación automáticos.',
                'tags' => ['anomalías', 'analytics', 'z-score', 'monitoreo'],
            ],
            [
                'slug' => 'metricas-agentes-ia',
                'category' => 'analytics',
                'title' => 'Métricas de agentes de IA',
                'question' => '¿Qué métricas registra Stratos sobre las interacciones con los agentes de IA?',
                'answer' => 'Para cada interacción con agentes LLM, Stratos registra: nombre del agente, proveedor LLM (OpenAI, Abacus, etc.), tipo de acción, latencia en ms, tokens de entrada/salida, costo estimado, estado (success/failed/timeout) y hash único del prompt (para deduplicación y trazabilidad sin exponer contenido). Estos datos alimentan el Agent Metrics Dashboard con KPIs de disponibilidad, latencia P50/P95/P99 y top agentes fallidos.',
                'tags' => ['agentes', 'métricas', 'LLM', 'observabilidad'],
            ],

            // ─── Mobile y Notificaciones ───────────────────────────────────
            [
                'slug' => 'aprobaciones-mobile',
                'category' => 'mobile',
                'title' => 'Flujo de aprobaciones móvil',
                'question' => '¿Cómo funciona el sistema de aprobaciones desde dispositivos móviles?',
                'answer' => 'Stratos tiene soporte mobile-first para flujos de aprobación. Los managers reciben notificaciones push (FCM para Android, APNs para iOS) cuando una solicitud requiere su aprobación. Desde la app o PWA pueden: ver el contexto completo de la solicitud, aprobar o rechazar con un toque, ver el historial de aprobaciones y recibir confirmación inmediata. Las aprobaciones tienen un timeout de 24 horas; si no se responden, escalan automáticamente al manager superior.',
                'tags' => ['mobile', 'aprobaciones', 'notificaciones push', 'workflow'],
            ],
            [
                'slug' => 'sincronizacion-offline',
                'category' => 'mobile',
                'title' => 'Sincronización offline',
                'question' => '¿Qué pasa si el usuario pierde conexión mientras trabaja en Stratos mobile?',
                'answer' => 'Stratos mobile usa una cola offline (OfflineQueue) que almacena localmente las acciones realizadas sin conexión. Cuando se recupera la conectividad, la app sincroniza automáticamente en lotes de hasta 50 ítems. El sistema maneja 3 reintentos con backoff exponencial en caso de errores. Cada ítem tiene una clave de deduplicación para evitar duplicados si la conexión se interrumpe durante la sincronización.',
                'tags' => ['offline', 'sync', 'móvil', 'conexión'],
            ],

            // ─── Seguridad y Privacidad ────────────────────────────────────
            [
                'slug' => 'pii-logging-seguridad',
                'category' => 'security',
                'title' => 'Seguridad en logging de prompts LLM',
                'question' => '¿Cómo maneja Stratos la privacidad de los datos enviados a los LLMs?',
                'answer' => 'Stratos no registra el contenido literal de los prompts enviados a LLMs. En su lugar, usa hashing SHA-256 de los prompts para auditabilidad (puedes verificar si un prompt específico fue enviado) sin exponer datos sensibles (PII). Los logs almacenan: hash del prompt, proveedor LLM, timestamp, duración, tokens usados y estado. Esta arquitectura PII-safe permite cumplir con normativas de protección de datos sin perder trazabilidad.',
                'tags' => ['privacidad', 'PII', 'seguridad', 'LLM', 'logging'],
            ],
            [
                'slug' => 'autenticacion-sanctum',
                'category' => 'security',
                'title' => 'Autenticación con Sanctum',
                'question' => '¿Cómo funciona la autenticación en la API de Stratos?',
                'answer' => 'La API de Stratos usa Laravel Sanctum para autenticación. Para acceder a los endpoints de API necesitas un token Bearer en el header Authorization. Los tokens se generan desde el panel de Stratos en Configuración > API Tokens. Todos los endpoints de API requieren auth:sanctum. Además, el middleware de tenant valida que el usuario pertenezca a la organización que intenta acceder, previniendo acceso cruzado entre tenants.',
                'tags' => ['Sanctum', 'autenticación', 'API', 'tokens'],
            ],
            [
                'slug' => 'roles-y-permisos',
                'category' => 'security',
                'title' => 'Roles y permisos en Stratos',
                'question' => '¿Qué roles de usuario existen en Stratos y qué puede hacer cada uno?',
                'answer' => 'Stratos tiene tres roles principales: (1) Admin: acceso total, puede aprobar escenarios y planes WFP, gestionar usuarios y configurar la organización. (2) Manager: puede crear y editar escenarios y planes WFP, ver reportes completos, aprobar solicitudes de su equipo. (3) User: puede ver su perfil de talento, completar evaluaciones asignadas y ver escenarios aprobados. Los permisos se gestionan mediante Policies de Laravel registradas en AuthServiceProvider.',
                'tags' => ['roles', 'permisos', 'admin', 'manager', 'usuario'],
            ],

            // ─── Integraciones y Configuración ─────────────────────────────
            [
                'slug' => 'proveedores-llm-soportados',
                'category' => 'integrations',
                'title' => 'Proveedores LLM soportados',
                'question' => '¿Qué proveedores de IA soporta Stratos para la generación de escenarios?',
                'answer' => 'Stratos soporta múltiples proveedores LLM configurables por variable de entorno: OpenAI (GPT-4, GPT-3.5), Abacus.AI (para empresas con requerimientos de privacidad estrictos) y un proveedor Mock para desarrollo y testing sin costo. El proveedor de embeddings es configurable independientemente: OpenAI text-embedding-3-small (producción), Mock determinístico (testing). Para cambiar el proveedor actualiza LLM_PROVIDER y EMBEDDINGS_PROVIDER en tu .env.',
                'tags' => ['OpenAI', 'Abacus', 'LLM', 'proveedores', 'configuración'],
            ],
            [
                'slug' => 'webhooks-automatizacion',
                'category' => 'integrations',
                'title' => 'Webhooks y automatización',
                'question' => '¿Puedo integrar Stratos con otras herramientas mediante webhooks?',
                'answer' => 'Sí. Stratos tiene un sistema de webhooks con firma HMAC-SHA256 para garantizar autenticidad. Puedes registrar webhooks para recibir notificaciones en tiempo real cuando ocurren eventos: anomalías detectadas, escenarios aprobados, métricas fuera de SLA, etc. También integra con n8n para workflows de automatización: un evento en Stratos puede disparar acciones en herramientas externas (Slack, JIRA, Salesforce). Los webhooks tienen retry con backoff exponencial (60s → 120s → 240s).',
                'tags' => ['webhooks', 'automatización', 'n8n', 'integración'],
            ],
        ];

        foreach ($faqs as $faq) {
            GuideFaq::updateOrCreate(
                ['slug' => $faq['slug']],
                array_merge($faq, [
                    'organization_id' => null,
                    'is_active' => true,
                ])
            );
        }

        $this->command->info('✅ GuideFaqSeeder: '.count($faqs).' FAQs sembradas correctamente.');
    }
}
