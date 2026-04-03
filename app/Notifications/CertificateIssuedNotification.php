<?php

namespace App\Notifications;

use App\Models\LmsCertificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateIssuedNotification extends Notification
{
    use Queueable;

    private const DATE_FORMAT = 'd/m/Y';

    public function __construct(protected LmsCertificate $certificate) {}

    public function via(mixed $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $cert = $this->certificate;
        $course = $cert->enrollment?->course?->title ?? 'Curso';
        $person = $cert->person?->full_name ?? ($notifiable->name ?? 'Participante');
        $url = $cert->certificate_url ?? url('/');
        $number = $cert->certificate_number;
        $issued = $cert->issued_at?->format(self::DATE_FORMAT) ?? now()->format(self::DATE_FORMAT);

        $mail = (new MailMessage)
            ->subject("Certificado emitido: {$course}")
            ->greeting("¡Felicidades, {$person}!")
            ->line("Has completado exitosamente el curso **{$course}** y se ha emitido tu certificado.")
            ->line("**N° de certificado:** {$number}")
            ->line("**Fecha de emisión:** {$issued}");

        if ($cert->expires_at) {
            $expires = $cert->expires_at->format(self::DATE_FORMAT);
            $mail->line("**Válido hasta:** {$expires}");
        }

        $mail->action('Ver certificado', $url)
            ->line('Este certificado acredita la finalización del curso en la plataforma Stratos.');

        return $mail;
    }

    public function toArray(mixed $notifiable): array
    {
        return $this->payload();
    }

    public function toBroadcast(mixed $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'certificate_issued',
            'data' => $this->payload(),
        ]);
    }

    private function payload(): array
    {
        return [
            'type' => 'certificate_issued',
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'certificate_url' => $this->certificate->certificate_url,
            'course_title' => $this->certificate->enrollment?->course?->title,
            'person_name' => $this->certificate->person?->full_name,
            'issued_at' => $this->certificate->issued_at?->toDateTimeString(),
            'expires_at' => $this->certificate->expires_at?->toDateTimeString(),
        ];
    }
}
