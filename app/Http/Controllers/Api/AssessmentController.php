<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssessmentFeedback;
use App\Models\AssessmentMessage;
use App\Models\AssessmentRequest;
use App\Models\AssessmentSession;
use App\Models\PsychometricProfile;
use App\Services\Intelligence\StratosAssessmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AssessmentController extends Controller
{
    public function __construct(
        private StratosAssessmentService $service,
        private \App\Services\Assessment\CompetencyAssessmentService $competencyService,
        private \App\Services\Performance\PerformanceDataService $kpiService
    ) {}

    /**
     * Iniciar una nueva sesión de evaluación.
     */
    public function startSession(Request $request)
    {
        $validated = $request->validate([
            'people_id' => 'required|exists:people,id',
            'scenario_id' => 'nullable|exists:scenarios,id',
            'type' => 'nullable|string|in:psychometric,technical,behavioral'
        ]);

        $session = AssessmentSession::create([
            'organization_id' => auth()->user()->organization_id,
            'people_id' => $validated['people_id'],
            'scenario_id' => $validated['scenario_id'] ?? null,
            'type' => $validated['type'] ?? 'psychometric',
            'status' => 'started',
            'started_at' => now(),
        ]);

        // Opcional: Podríamos disparar el primer mensaje de bienvenida aquí
        // llamando al servicio inmediatamente si queremos que el AI empiece.

        return $this->successResponse($session->load('person', 'agent'));
    }

    /**
     * Obtener el historial de la sesión.
     */
    public function getSession($id)
    {
        $session = AssessmentSession::with(['messages' => function($q) {
            $q->orderBy('created_at', 'asc');
        }, 'person', 'psychometricProfiles'])->findOrFail($id);

        return $this->successResponse($session);
    }

    /**
     * Enviar mensaje y obtener respuesta de la IA.
     */
    public function sendMessage($id, Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $session = AssessmentSession::findOrFail($id);

        return DB::transaction(function () use ($session, $validated) {
            // 1. Guardar mensaje del usuario
            $session->messages()->create([
                'role' => 'user',
                'content' => $validated['content']
            ]);

            $session->update(['status' => 'in_progress']);

            // 2. Obtener respuesta de la IA
            $aiResponse = $this->service->getNextMessage($session);

            if ($aiResponse) {
                $aiMessage = $session->messages()->create([
                    'role' => $aiResponse['role'],
                    'content' => $aiResponse['content']
                ]);
                return $this->successResponse($aiMessage);
            }

            return $this->errorResponse('Error al obtener respuesta de la IA', 500);
        });
    }

    /**
     * Finalizar y analizar la sesión.
     */
    public function analyze($id)
    {
        $session = AssessmentSession::findOrFail($id);

        if ($session->messages()->count() < 3) {
            return $this->errorResponse('Insuficientes mensajes para analizar');
        }

        $externalFeedback = $this->getExternalFeedbackEnriched($session->people_id);
        $performanceData = $this->kpiService->getPerformanceData($session->people_id);

        $analysis = $this->performAnalysis($session, $externalFeedback, $performanceData);

        if (!$analysis) {
            return $this->errorResponse('Error en el análisis de la sesión', 500);
        }

        return $this->saveAnalysisResults($session, $analysis, !empty($externalFeedback));
    }

    private function getExternalFeedbackEnriched($peopleId)
    {
        return AssessmentFeedback::join('assessment_requests', 'assessment_feedback.assessment_request_id', '=', 'assessment_requests.id')
            ->leftJoin('skills', 'assessment_feedback.skill_id', '=', 'skills.id')
            ->where('assessment_requests.subject_id', $peopleId)
            ->where('assessment_requests.status', 'completed')
            ->select(
                'assessment_requests.relationship',
                'assessment_feedback.question',
                'assessment_feedback.answer as content',
                'skills.name as skill_name',
                'assessment_feedback.score',
                'assessment_feedback.confidence_level'
            )
            ->get()
            ->map(fn($item) => [
                'relationship' => $item->relationship,
                'question' => $item->question,
                'content' => $item->content,
                'skill_context' => $item->skill_name ? [
                    'skill' => $item->skill_name,
                    'score' => $item->score,
                    'confidence' => $item->confidence_level
                ] : null
            ])
            ->toArray();
    }

    private function performAnalysis($session, $externalFeedback, $performanceData)
    {
        return !empty($externalFeedback)
            ? $this->service->analyzeThreeSixty($session, $externalFeedback, $performanceData)
            : $this->service->analyzeSession($session);
    }

    private function saveAnalysisResults($session, $analysis, $hasExternalFeedback)
    {
        return DB::transaction(function () use ($session, $analysis, $hasExternalFeedback) {
            $this->savePsychometricProfiles($session, $analysis);
            $this->updateSessionStatus($session, $analysis);

            // Automatizar Mitigaciones (Paso 1 del Roadmap)
            $mitigationService = new \App\Services\MitigationService;
            $suggestions = $mitigationService->generateSuggestions($analysis);
            $mitigationService->persistMitigations($session->person, $suggestions);

            // Registrar en Audit Trail (Paso 4 del Roadmap)
            $audit = new \App\Services\AuditTrailService;
            $audit->logDecision(
                'Assessment360',
                "session_{$session->id}",
                "Potential Assessment: {$analysis['overall_potential']}",
                $analysis['ai_reasoning_flow'] ?? [],
                'Cerbero (360 Analyst)'
            );

            if ($hasExternalFeedback) {
                $this->competencyService->updateAllSkillsForPerson($session->people_id);
            }

            return $this->successResponse($session->load('psychometricProfiles'), 'Análisis completado');
        });
    }

    private function savePsychometricProfiles($session, $analysis)
    {
        if (!isset($analysis['traits']) || !is_array($analysis['traits'])) {
            return;
        }

        foreach ($analysis['traits'] as $trait) {
            PsychometricProfile::create([
                'people_id' => $session->people_id,
                'assessment_session_id' => $session->id,
                'trait_name' => $trait['name'] ?? 'Unknown',
                'score' => $trait['score'] ?? 0,
                'rationale' => $trait['rationale'] ?? 'No rationale provided'
            ]);
        }
    }

    private function updateSessionStatus($session, $analysis)
    {
        $session->update([
            'status' => 'analyzed',
            'completed_at' => now(),
            'metadata' => array_merge($session->metadata ?? [], [
                'overall_potential' => $analysis['overall_potential'] ?? 'N/A',
                'cultural_fit' => $analysis['cultural_fit'] ?? 0,
                'success_probability' => $analysis['success_probability'] ?? 0,
                'summary_report' => $analysis['summary_report'] ?? 'Error generando reporte',
                'cultural_analysis' => $analysis['cultural_analysis'] ?? 'Análisis cultural no disponible',
                'team_synergy_preview' => $analysis['team_synergy_preview'] ?? 'Análisis de sinergia no disponible',
                'blind_spots' => $analysis['blind_spots'] ?? [],
                'ai_reasoning_flow' => $analysis['ai_reasoning_flow'] ?? []
            ])
        ]);
    }

    /**
     * Solicitar feedback a terceros.
     */
    public function requestFeedback(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:people,id',
            'evaluator_id' => 'required|exists:people,id',
            'relationship' => 'required|string'
        ]);

        $requestFeedback = DB::transaction(function () use ($validated) {
            $req = AssessmentRequest::create([
                'organization_id' => auth()->user()->organization_id,
                'subject_id' => $validated['subject_id'],
                'evaluator_id' => $validated['evaluator_id'],
                'relationship' => $validated['relationship'],
                'status' => 'pending',
                'token' => Str::random(40)
            ]);

            // Intelligent Question Selection (BARS)
            $subject = \App\Models\People::with('activeSkills')->find($validated['subject_id']);
            
            if ($subject && $subject->activeSkills->isNotEmpty()) {
                foreach ($subject->activeSkills as $roleSkill) {
                    // Try to find specific questions for this skill and relationship
                    $questions = \App\Models\SkillQuestionBank::where('skill_id', $roleSkill->skill_id)
                        ->where(function($q) use ($validated) {
                            $q->where('target_relationship', $validated['relationship'])
                              ->orWhere('is_global', true);
                        })
                        ->inRandomOrder()
                        ->take(1) // Select 1 question per skill
                        ->get();

                if ($questions->isNotEmpty()) {
                    foreach ($questions as $q) {
                        $req->feedback()->create([
                            'skill_id' => $roleSkill->skill_id,
                            'question' => $q->question,
                            'answer' => null
                        ]);
                    }
                } else {
                    // Fallback: Create a generic BARS placeholder for this skill
                    $req->feedback()->create([
                        'skill_id' => $roleSkill->skill_id,
                        'question' => 'Evalúe el nivel de competencia observado.',
                        'answer' => null
                    ]);
                }
                }
            }
            
            // Add one personalized open-ended question
            $req->feedback()->create([
                'question' => '¿Qué recomendación de mejora le daría a esta persona?',
                'answer' => null
            ]);

            return $req;
        });

        return $this->successResponse($requestFeedback, 'Solicitud de feedback enviada');
    }

    /**
     * Enviar feedback de terceros.
     */
    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:assessment_requests,id',
            'answers' => 'required|array',
            'answers.*.id' => 'nullable|exists:assessment_feedback,id',
            'answers.*.question' => 'nullable|string',
            'answers.*.answer' => 'nullable|string',
            'answers.*.skill_id' => 'nullable|exists:skills,id',
            'answers.*.score' => 'nullable|integer|between:1,5',
            'answers.*.evidence_url' => 'nullable|url',
            'answers.*.confidence_level' => 'nullable|integer|between:0,100',
        ]);

        $assessmentRequest = AssessmentRequest::findOrFail($validated['request_id']);

        if ($assessmentRequest->status === 'completed') {
            return $this->errorResponse('Feedback ya enviado');
        }

        return DB::transaction(function() use ($assessmentRequest, $validated) {
            foreach ($validated['answers'] as $item) {
                // Determine if it's BARS or standard Q&A
                $isBARS = isset($item['skill_id']) && isset($item['score']);
                $skillId = $item['skill_id'] ?? null;
                $score = $item['score'] ?? null;
                $evidenceUrl = $item['evidence_url'] ?? null;
                $confidenceLevel = $item['confidence_level'] ?? null;
                
                $question = $item['question'] ?? ($isBARS ? 'Evaluación de competencia' : 'Pregunta general');
                $answer = $item['answer'] ?? ($isBARS ? ('Nivel asignado: ' . $score) : 'Sin respuesta');

                $data = [
                    'question' => $question,
                    'answer' => $answer,
                    'skill_id' => $skillId,
                    'score' => $score,
                    'evidence_url' => $evidenceUrl,
                    'confidence_level' => $confidenceLevel,
                ];

                if (isset($item['id'])) {
                    // Update existing pre-filled feedback
                     $assessmentRequest->feedback()
                        ->where('id', $item['id'])
                        ->update($data);
                } else {
                    // Create new feedback
                    $assessmentRequest->feedback()->create($data);
                }
            }

            $assessmentRequest->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            return $this->successResponse(null, 'Feedback enviado correctamente');
        });
    }

    public function getPendingRequests()
    {
        $person = auth()->user()->person;
        if (!$person) {
            return response()->json([]);
        }

        $requests = AssessmentRequest::with(['subject', 'feedback'])
            ->where('evaluator_id', $person->id)
            ->where('status', 'pending')
            ->get();

        return $this->successResponse($requests);
    }

    /**
     * Obtener una solicitud de feedback por su token único (acceso externo).
     */
    public function showByToken($token)
    {
        $request = AssessmentRequest::with(['subject', 'feedback.skill'])
            ->where('token', $token)
            ->firstOrFail();

        if ($request->status === 'completed') {
            return $this->errorResponse('Esta evaluación ya ha sido completada.');
        }

        return $this->successResponse($request);
    }

    /**
     * Renderizar el formulario de feedback premium (vía Inertia).
     */
    public function showExternalForm($token)
    {
        $request = AssessmentRequest::with(['subject', 'feedback.skill'])
            ->where('token', $token)
            ->firstOrFail();

        if ($request->status === 'completed') {
            return Inertia::render('Welcome', [
                'message' => 'Esta evaluación ya ha sido completada. ¡Gracias!'
            ]);
        }

        return Inertia::render('Assessments/ExternalFeedback', [
            'token' => $token,
            'request' => $request
        ]);
    }

    /**
     * Enviar feedback desde el acceso público usando el token.
     */
    public function submitFeedbackGuest(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string|exists:assessment_requests,token',
            'answers' => 'required|array',
            'answers.*.id' => 'nullable|exists:assessment_feedback,id',
            'answers.*.question' => 'nullable|string',
            'answers.*.answer' => 'nullable|string',
            'answers.*.skill_id' => 'nullable|exists:skills,id',
            'answers.*.score' => 'nullable|integer|between:1,5',
        ]);

        $assessmentRequest = AssessmentRequest::where('token', $validated['token'])->firstOrFail();

        if ($assessmentRequest->status === 'completed') {
            return $this->errorResponse('Feedback ya enviado');
        }

        return DB::transaction(function() use ($assessmentRequest, $validated) {
            foreach ($validated['answers'] as $item) {
                $isBARS = isset($item['skill_id']) && isset($item['score']);
                $data = [
                    'question' => $item['question'] ?? ($isBARS ? 'Evaluación de competencia' : 'Pregunta general'),
                    'answer' => $item['answer'] ?? ($isBARS ? ('Nivel: ' . $item['score']) : 'Sin respuesta'),
                    'skill_id' => $item['skill_id'] ?? null,
                    'score' => $item['score'] ?? null,
                ];

                if (isset($item['id'])) {
                    $assessmentRequest->feedback()->where('id', $item['id'])->update($data);
                } else {
                    $assessmentRequest->feedback()->create($data);
                }
            }

            $assessmentRequest->update(['status' => 'completed', 'completed_at' => now()]);
            return $this->successResponse(null, 'Feedback enviado con éxito');
        });
    }

    /**
     * Disparar el ciclo completo 360 basado en el mapa de relaciones 'Cerbero'.
     */
    public function triggerThreeSixty(Request $request)
    {
        $validated = $request->validate([
            'people_id' => 'required|exists:people,id',
        ]);

        $subject = \App\Models\People::with(['managers', 'peers', 'subordinates', 'activeSkills'])->findOrFail($validated['people_id']);

        return DB::transaction(function () use ($subject) {
            // 1. Crear sesión de autoevaluación (Chat AI)
            $session = AssessmentSession::create([
                'organization_id' => $subject->organization_id,
                'people_id' => $subject->id,
                'type' => 'psychometric',
                'status' => 'started',
                'started_at' => now(),
            ]);

            // 2. Disparar solicitudes a Jefes
            foreach ($subject->managers as $manager) {
                $this->dispatchFeedbackRequest($subject, $manager, 'manager');
            }

            // 3. Disparar solicitudes a Pares
            foreach ($subject->peers as $peer) {
                $this->dispatchFeedbackRequest($subject, $peer, 'peer');
            }

            // 4. Disparar solicitudes a Subordinados
            foreach ($subject->subordinates as $sub) {
                $this->dispatchFeedbackRequest($subject, $sub, 'subordinate');
            }

            return $this->successResponse([
                'subject' => $subject->full_name,
                'session_id' => $session->id,
                'counts' => [
                    'managers' => $subject->managers->count(),
                    'peers' => $subject->peers->count(),
                    'subordinates' => $subject->subordinates->count(),
                ]
            ], 'Ciclo 360 disparado con éxito.');
        });
    }

    /**
     * Helper para despachar solicitudes individuales.
     */
    private function dispatchFeedbackRequest($subject, $evaluator, $relationship)
    {
        $req = AssessmentRequest::create([
            'organization_id' => $subject->organization_id,
            'subject_id' => $subject->id,
            'evaluator_id' => $evaluator->id,
            'relationship' => $relationship,
            'status' => 'pending',
            'token' => Str::random(40)
        ]);

        foreach ($subject->activeSkills as $roleSkill) {
            $q = \App\Models\SkillQuestionBank::where('skill_id', $roleSkill->skill_id)
                ->where(function($query) use ($relationship) {
                    $query->where('target_relationship', $relationship)
                          ->orWhere('is_global', true);
                })
                ->inRandomOrder()
                ->first();

            $req->feedback()->create([
                'skill_id' => $roleSkill->skill_id,
                'question' => $q ? $q->question : 'Evalúe el desempeño en ' . $roleSkill->skill->name,
                'answer' => ''
            ]);
        }

        return $req;
    }
}
