<?php

namespace App\Services\Talent\Lms;

use App\Models\DevelopmentAction;
use Illuminate\Support\Facades\Log;

class LmsService
{
    protected $providers = [];

    public function __construct(
        MockLmsProvider $mockProvider,
        StratosInternalProvider $internalProvider,
        MoodleProvider $moodleProvider,
        LinkedInLearningProvider $linkedinProvider,
        UdemyBusinessProvider $udemyProvider
    ) {
        $this->providers['mock'] = $mockProvider;
        $this->providers['internal'] = $internalProvider;
        $this->providers['moodle'] = $moodleProvider;
        $this->providers['linkedin'] = $linkedinProvider;
        $this->providers['udemy'] = $udemyProvider;
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
        if (! $action->lms_course_id) {
            throw new \InvalidArgumentException('Esta acción no está vinculada a un curso de LMS.');
        }

        $provider = $this->getProvider($action->lms_provider ?? 'mock');
        $userId = (string) auth()->id();

        // Si no está inscrito, inscribir ahora
        if (! $action->lms_enrollment_id) {
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
        if (! $action->lms_enrollment_id) {
            return false;
        }

        $provider = $this->getProvider($action->lms_provider ?? 'mock');

        try {
            $isCompleted = $provider->isCompleted($action->lms_enrollment_id);

            if ($isCompleted && $action->status !== 'completed') {
                $action->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                ]);

                // 🏅 GAMIFICATION: Award points to the user
                $course = \App\Models\LmsCourse::find($action->lms_course_id);
                if ($course) {
                    $xp = $course->xp_points > 0 ? $course->xp_points : 50; // default 50
                    $gamification = \App\Models\UserGamification::firstOrCreate(
                        ['user_id' => $action->person_id], // En Stratos, DevelopmentAction usa person_id (vínculo People-User)
                        ['total_xp' => 0, 'level' => 1, 'current_points' => 0]
                    );
                    $gamification->addExperience($xp);
                    
                    Log::info("User {$action->person_id} awarded {$xp} XP for completing course {$course->title}");
                }

                return true;
            }
        } catch (\Exception $e) {
            Log::error('Error syncing LMS progress: '.$e->getMessage());
        }

        return false;
    }

    /**
     * Busca cursos en el proveedor por defecto.
     */
    public function searchCourses(string $query): array
    {
        $allResults = [];
        
        foreach ($this->providers as $provider) {
            $allResults = array_merge($allResults, $provider->searchCourses($query));
        }

        return $allResults;
    }
}
