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

class AssessmentController extends Controller
{
    public function __construct(
        private StratosAssessmentService $service
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

        return response()->json($session->load('person', 'agent'));
    }

    /**
     * Obtener el historial de la sesión.
     */
    public function getSession($id)
    {
        $session = AssessmentSession::with(['messages' => function($q) {
            $q->orderBy('created_at', 'asc');
        }, 'person', 'psychometricProfiles'])->findOrFail($id);

        return response()->json($session);
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
                return response()->json($aiMessage);
            }

            return response()->json(['message' => 'Error al obtener respuesta de la IA'], 500);
        });
    }

    /**
     * Finalizar y analizar la sesión.
     */
    public function analyze($id)
    {
        $session = AssessmentSession::findOrFail($id);

        if ($session->messages()->count() < 3) {
            return response()->json(['message' => 'Insuficientes mensajes para analizar'], 400);
        }

        // 1. Obtener feedback externo si existe
        $externalFeedback = AssessmentFeedback::join('assessment_requests', 'assessment_feedback.assessment_request_id', '=', 'assessment_requests.id')
            ->where('assessment_requests.subject_id', $session->people_id)
            ->where('assessment_requests.status', 'completed')
            ->select('assessment_requests.relationship', 'assessment_feedback.answer as content')
            ->get()
            ->toArray();

        // 2. Ejecutar análisis (Normal o 360)
        if (!empty($externalFeedback)) {
            $analysis = $this->service->analyzeThreeSixty($session, $externalFeedback);
        } else {
            $analysis = $this->service->analyzeSession($session);
        }

        if ($analysis) {
            return DB::transaction(function () use ($session, $analysis) {
                // Guardar resultados psicométricos
                foreach ($analysis['traits'] as $trait) {
                    PsychometricProfile::create([
                        'people_id' => $session->people_id,
                        'assessment_session_id' => $session->id,
                        'trait_name' => $trait['name'],
                        'score' => $trait['score'],
                        'rationale' => $trait['rationale']
                    ]);
                }

                $session->update([
                    'status' => 'analyzed',
                    'completed_at' => now(),
                    'metadata' => array_merge($session->metadata ?? [], [
                        'overall_potential' => $analysis['overall_potential'],
                        'summary_report' => $analysis['summary_report'],
                        'blind_spots' => $analysis['blind_spots'] ?? []
                    ])
                ]);

                return response()->json([
                    'success' => true,
                    'session' => $session->load('psychometricProfiles')
                ]);
            });
        }

        return response()->json(['message' => 'Error en el análisis de la sesión'], 500);
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

        $requestFeedback = AssessmentRequest::create([
            'organization_id' => auth()->user()->organization_id,
            'subject_id' => $validated['subject_id'],
            'evaluator_id' => $validated['evaluator_id'],
            'relationship' => $validated['relationship'],
            'status' => 'pending',
            'token' => Str::random(40)
        ]);

        return response()->json($requestFeedback);
    }

    /**
     * Enviar feedback de terceros.
     */
    public function submitFeedback(Request $request)
    {
        $validated = $request->validate([
            'request_id' => 'required|exists:assessment_requests,id',
            'answers' => 'required|array',
            'answers.*.question' => 'required|string',
            'answers.*.answer' => 'required|string',
        ]);

        $assessmentRequest = AssessmentRequest::findOrFail($validated['request_id']);

        if ($assessmentRequest->status === 'completed') {
            return response()->json(['message' => 'Feedback ya enviado'], 400);
        }

        return DB::transaction(function() use ($assessmentRequest, $validated) {
            foreach ($validated['answers'] as $item) {
                $assessmentRequest->feedback()->create([
                    'question' => $item['question'],
                    'answer' => $item['answer']
                ]);
            }

            $assessmentRequest->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

            return response()->json(['success' => true]);
        });
    }

    public function getPendingRequests()
    {
        $person = auth()->user()->person;
        if (!$person) {
            return response()->json([]);
        }

        $requests = AssessmentRequest::with('subject')
            ->where('evaluator_id', $person->id)
            ->where('status', 'pending')
            ->get();

        return response()->json($requests);
    }
}
