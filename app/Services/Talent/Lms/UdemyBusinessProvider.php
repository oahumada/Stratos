<?php

namespace App\Services\Talent\Lms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UdemyBusinessProvider implements LmsProviderInterface
{
    protected string $accountName;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->accountName = config('services.udemy_business.account_name', '');
        $this->clientId = config('services.udemy_business.client_id', '');
        $this->clientSecret = config('services.udemy_business.client_secret', '');
    }

    public function getLaunchUrl(string $courseId, ?string $userId = null): string
    {
        return "https://" . $this->accountName . ".udemy.com/courses/" . $courseId;
    }

    public function enrollUser(string $courseId, string $userId): string
    {
        // Udemy suele registrar la inscripción vía SSO (SAML)
        return "udemy_" . $courseId . "_" . $userId;
    }

    public function getProgress(string $enrollmentId): float
    {
        // Requiere integración con la Reporting API de Udemy Business
        return 0.0;
    }

    public function isCompleted(string $enrollmentId): bool
    {
        return false;
    }

    public function searchCourses(string $query): array
    {
        if (empty($this->accountName)) {
            return [];
        }

        try {
            // Ejemplo de llamada a la API de búsqueda de Udemy
            // $response = Http::withBasicAuth($this->clientId, $this->clientSecret)->get('https://' . $this->accountName . '.udemy.com/api-2.0/courses/', [...]);

            return [
                [
                    'id' => 'ud-001',
                    'title' => 'Especialista en Mantenimiento Industrial y Robótica',
                    'description' => 'Curso integral de Udemy Business sobre automatización de planta.',
                    'provider' => 'udemy'
                ]
            ];
        } catch (\Exception $e) {
            Log::error("Error searching Udemy Business: " . $e->getMessage());
            return [];
        }
    }
}
