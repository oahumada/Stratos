<?php

namespace App\Listeners;

use App\Events\RoleRequirementsUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RecalculateTalentGaps implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RoleRequirementsUpdated $event): void
    {
        // Esto se procesará en las colas (Redis/Database) de Laravel.
        // Aquí conectamos con Stratos Grow o Stratos Map en Background.
        \Illuminate\Support\Facades\Log::info('Calculando gaps en background', [
            'modulo' => 'Core + Grow',
            'role_id' => $event->roleId,
            'organization_id' => $event->organizationId,
        ]);
    }
}
