<?php

namespace App\Services\Talent\Lms;

use App\Models\DevelopmentAction;
use Illuminate\Support\Facades\Log;

class LmsService
{
    protected $providers = [];

    public function __construct(MockLmsProvider $mockProvider)
    {
        $this->providers['mock'] = $mockProvider;
        // Aquí podríamos inyectar otros proveedores como MoodleProvider o LinkedInLearningProvider
    }

    /**
     * Obtiene el proveedor adecuado.
     */
    protected function getProvider(string $name): LmsProviderInterface
    {
        return $this->providers[$name] ?? $this->providers['mock'];
    }

    /**
     * Lanza una acción de desarrollo en el LMS.
     */
    public function launchAction(DevelopmentAction $action): string
    {
        if (!$action->lms_course_id) {
            throw new \InvalidArgumentException("Esta acción no está vinculada a un curso de LMS.");
        }

        $provider = $this->getProvider($action->lms_provider ?? 'mock');
        $userId = (string) auth()->id();

        // Si no está inscrito, inscribir ahora
        if (!$action->lms_enrollment_id) {
            $enrollmentId = $provider->enrollUser($action->lms_course_id, $userId);
            $action->update(['lms_enrollment_id' => $enrollmentId]);
        }

        return $provider->getLaunchUrl($action->lms_course_id, $userId);
    }

    /**
     * Sincroniza el progreso de una acción desde el LMS.
     */
    public function syncProgress(DevelopmentAction $action): bool
    {
        if (!$action->lms_enrollment_id) {
            return false;
        }

        $provider = $this->getProvider($action->lms_provider ?? 'mock');
        
        try {
            $isCompleted = $provider->isCompleted($action->lms_enrollment_id);
            
            if ($isCompleted && $action->status !== 'completed') {
                $action->update([
                    'status' => 'completed',
                    'completed_at' => now()
                ]);
                return true;
            }
        } catch (\Exception $e) {
            Log::error("Error syncing LMS progress: " . $e->getMessage());
        }

        return false;
    }
}
