<?php

namespace App\Notifications;

use App\Models\DevelopmentAction;
use App\Models\LmsCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LmsCourseCompletedNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected DevelopmentAction $action,
        protected ?LmsCourse $course,
    ) {}

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $courseTitle = $this->course?->title ?? 'Curso';
        $person = $notifiable->name ?? 'Participante';
        $xp = $this->course?->xp_points > 0 ? $this->course->xp_points : 50;

        return (new MailMessage)
            ->subject("¡Curso completado: {$courseTitle}!")
            ->greeting("¡Felicidades, {$person}!")
            ->line("Has completado el curso **{$courseTitle}** exitosamente.")
            ->line("Has ganado **{$xp} XP** por este logro.")
            ->action('Ver mis logros', url('/lms'))
            ->line('Sigue aprendiendo para continuar tu desarrollo en Stratos.');
    }

    public function toArray(mixed $notifiable): array
    {
        return $this->payload();
    }

    public function toBroadcast(mixed $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'course_completed',
            'data' => $this->payload(),
        ]);
    }

    private function payload(): array
    {
        return [
            'type' => 'course_completed',
            'action_id' => $this->action->id,
            'course_id' => $this->course?->id,
            'course_title' => $this->course?->title,
            'xp_points' => $this->course?->xp_points ?? 50,
            'completed_at' => $this->action->completed_at?->toDateTimeString(),
        ];
    }
}
