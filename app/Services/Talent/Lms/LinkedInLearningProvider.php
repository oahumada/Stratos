<?php

namespace App\Services\Talent\Lms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LinkedInLearningProvider implements LmsProviderInterface
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $accessToken;

    public function __construct()
    {
        $this->clientId = config('services.linkedin_learning.client_id', '');
        $this->clientSecret = config('services.linkedin_learning.client_secret', '');
    }

    public function getLaunchUrl(string $courseId, ?string $userId = null): string
    {
        // LinkedIn Learning suele usar URNs para los cursos
        return "https://www.linkedin.com/learning/courses/" . $courseId;
    }

    public function enrollUser(string $courseId, string $userId): string
    {
        // LinkedIn suele manejar la inscripción automática vía SSO (SAML/OIDC)
        // Pero aquí podríamos registrar el intento en una tabla de auditoría
        return "li_learning_" . $courseId . "_" . $userId;
    }

    public function getProgress(string $enrollmentId): float
    {
        // Requiere integración con la Reporting API de LinkedIn Learning
        return 0.0;
    }

    public function isCompleted(string $enrollmentId): bool
    {
        return false;
    }

    public function searchCourses(string $query): array
    {
        if (empty($this->clientId)) {
            return [];
        }

        try {
            // Ejemplo de llamada a la API de búsqueda de contenido de LinkedIn
            // $response = Http::withToken($this->accessToken)->get('https://api.linkedin.com/v2/learningAssets', [...]);
            
            return [
                [
                    'id' => 'li-001',
                    'title' => 'Liderazgo en la Industria 4.0: Sector Automotriz',
                    'description' => 'Curso especializado de LinkedIn Learning sobre transformación digital.',
                    'provider' => 'linkedin'
                ]
            ];
        } catch (\Exception $e) {
            Log::error("Error searching LinkedIn Learning: " . $e->getMessage());
            return [];
        }
    }
}
