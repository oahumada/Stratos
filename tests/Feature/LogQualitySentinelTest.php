<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class LogQualitySentinelTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->org = Organization::create([
            'name' => 'QA Org',
            'subdomain' => 'qa',
        ]);

        $this->admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@qa.stratos',
            'password' => bcrypt('password'),
            'organization_id' => $this->org->id,
            'role' => 'admin',
        ]);
    }

    public function test_creates_ticket_on_critical_log_error()
    {
        // Limpiamos la caché para evitar que el throttling nos afecte
        \Illuminate\Support\Facades\Cache::flush();

        // Disparamos un Log de error
        Log::error('Se ha producido un error inesperado al calcular la nómina');

        // Verificamos que se haya creado un ticket de tipo code_quality
        $this->assertDatabaseHas('support_tickets', [
            'type' => 'code_quality',
            'reporter_id' => $this->admin->id,
            'priority' => 'high',
        ]);

        $ticket = SupportTicket::first();
        $this->assertStringContainsString('Se ha producido un error inesperado', $ticket->description);
    }

    public function test_does_not_create_ticket_for_info_logs()
    {
        Log::info('El usuario ha iniciado sesión');

        $this->assertDatabaseCount('support_tickets', 0);
    }
}
