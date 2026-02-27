<?php

namespace App\Services\Intelligence;

use App\Models\AssessmentSession;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StratosAssessmentService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.python_intel.base_url');
        $this->timeout = config('services.python_intel.timeout', 30);
    }

    /**
     * Envía el historial de chat al agente y obtiene la siguiente respuesta.
     */
    public function getNextMessage(AssessmentSession $session, string $language = 'es')
    {
        $history = $session->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'role' => $m->role,
                'content' => $m->content
            ]);

        try {
            $response = Http::timeout($this->timeout)->post("{$this->baseUrl}/interview/chat", [
                'person_name' => $session->person->full_name,
                'context' => $session->type . ' interview for ' . ($session->scenario?->name ?? 'general assessment'),
                'history' => $history,
                'language' => $language
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('StratosAssessmentService Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('StratosAssessmentService Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Analiza la sesión completa para generar el perfil psicométrico.
     */
    public function analyzeSession(AssessmentSession $session)
    {
        $history = $session->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'role' => $m->role,
                'content' => $m->content
            ]);

        try {
            $response = Http::timeout(120)->post("{$this->baseUrl}/interview/analyze", [
                'person_name' => $session->person->full_name,
                'context' => $session->type . ' interview for ' . ($session->scenario?->name ?? 'general assessment'),
                'history' => $history
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('StratosAssessmentService Analysis Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('StratosAssessmentService Analysis Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function analyzeThreeSixty(AssessmentSession $session, array $externalFeedback, array $performanceData = [])
    {
        $history = $session->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($m) => [
                'role' => $m->role,
                'content' => $m->content
            ]);

        try {
            $url = "{$this->baseUrl}/interview/analyze-360";
            Log::info("Calling Python Service: $url");

            $response = Http::timeout(180)->post($url, [
                'person_name' => $session->person->full_name,
                'interview_history' => $history,
                'external_feedback' => $externalFeedback,
                'performance_data' => $performanceData, // KPI data added
                'language' => 'es'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('StratosAssessmentService 360 Analysis Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('StratosAssessmentService 360 Analysis Exception: ' . $e->getMessage());
            return null;
        }
    }

    public function matchCandidate(array $candidateProfile, array $blueprint, string $language = 'es')
    {
        try {
            $url = "{$this->baseUrl}/match-talent";
            Log::info("Calling Python Service for Matching: $url");

            $response = Http::timeout(120)->post($url, [
                'candidate_profile' => $candidateProfile,
                'blueprint' => $blueprint,
                'language' => $language
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('StratosAssessmentService Matching Error: ' . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error('StratosAssessmentService Matching Exception: ' . $e->getMessage());
            return null;
        }
    }
}
