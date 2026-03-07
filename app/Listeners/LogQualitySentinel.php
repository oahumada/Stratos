<?php

namespace App\Listeners;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Cache;

class LogQualitySentinel
{
    /**
     * Handle the event.
     */
    public function handle(MessageLogged $event): void
    {
        // Only react to 'error', 'critical', 'alert', 'emergency'
        $severeLevels = ['error', 'critical', 'alert', 'emergency'];

        if (! in_array($event->level, $severeLevels)) {
            return;
        }

        // Avoid infinite loops if creating a ticket throws an error
        if (str_contains($event->message, 'SupportTicket')) {
            return;
        }

        // Throttling: only create one ticket per error signature per hour
        $signature = md5($event->level.$event->message);

        if (Cache::has("qa_ticket_{$signature}")) {
            return;
        }

        try {
            // Retrieve a system user or first admin to assign as reporter
            $systemUser = User::where('role', 'admin')->first();

            if (! $systemUser) {
                return;
            }

            // Extract context (e.g. file, line exception)
            $exception = $event->context['exception'] ?? null;
            $filePath = null;
            $contextData = [];

            if ($exception instanceof \Throwable) {
                $filePath = $exception->getFile().':'.$exception->getLine();
                $contextData['trace'] = array_slice($exception->getTrace(), 0, 5);
                $contextData['class'] = get_class($exception);
            }

            // Create an 'improvement' / 'code_quality' ticket inside the Quality Hub
            SupportTicket::create([
                'organization_id' => $systemUser->organization_id,
                'reporter_id' => $systemUser->id,
                'title' => 'Oportunidad de Mejora ('.strtoupper($event->level).'): '.substr($event->message, 0, 100),
                'description' => "Se ha detectado un error recurrente o crítico en producción que requiere atención de calidad.\n\n".
                                 '**Mensaje:** '.$event->message."\n".
                                 ($filePath ? '**Ubicación:** '.$filePath : ''),
                'type' => 'code_quality',
                'priority' => $event->level === 'emergency' || $event->level === 'critical' ? 'critical' : 'high',
                'context' => $contextData,
                'file_path' => $filePath,
                'status' => 'open',
            ]);

            Cache::put("qa_ticket_{$signature}", true, now()->addHour());

        } catch (\Throwable $th) {
            // Silently fail to not interrupt the main request
        }
    }
}
