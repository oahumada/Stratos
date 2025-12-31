<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('gap:analyze {people_id} {role_name}', function () {
    $peopleId = (string) $this->argument('people_id');
    $roleName = (string) $this->argument('role_name');

    $people = \App\Models\People::find($peopleId);
    $role = \App\Models\Roles::where('name', $roleName)->first();

    if (! $people) {
        $this->error('Peoplea no encontrada: ' . $peopleId);
        return 1;
    }

    if (! $role) {
        $this->error('Rol no encontrado por nombre: ' . $roleName);
        return 1;
    }

    $service = new \App\Services\GapAnalysisService();
    $analysis = $service->calculate($people, $role);

    // Mostrar salida formateada
    $this->info('Match: ' . $analysis['match_percentage'] . '% (' . $analysis['summary']['category'] . ')');
    $this->line('Skills OK: ' . $analysis['summary']['skills_ok'] . ' / ' . $analysis['summary']['total_skills']);

    foreach ($analysis['gaps'] as $gap) {
        $this->line(sprintf(
            '- %s: actual=%d, requerido=%d, gap=%d, status=%s%s',
            $gap['skill_name'],
            $gap['current_level'],
            $gap['required_level'],
            $gap['gap'],
            $gap['status'],
            $gap['is_critical'] ? ' (critical)' : ''
        ));
    }

    return 0;
})->purpose('Analiza brechas de una peoplea contra un rol por nombre');

Artisan::command('devpath:generate {people_id} {role_name}', function () {
    $peopleId = (string) $this->argument('people_id');
    $roleName = (string) $this->argument('role_name');

    $people = \App\Models\People::find($peopleId);
    $role = \App\Models\Roles::where('name', $roleName)->first();

    if (! $people) {
        $this->error('Peoplea no encontrada: ' . $peopleId);
        return 1;
    }

    if (! $role) {
        $this->error('Rol no encontrado por nombre: ' . $roleName);
        return 1;
    }

    $service = new \App\Services\DevelopmentPathService();
    $path = $service->generate($people, $role);

    $this->info('DevelopmentPath generado: #' . $path->id . ' (status=' . $path->status . ')');
    $this->line('Duración estimada (meses): ' . $path->estimated_duration_months);
    foreach ($path->steps as $i => $step) {
        $this->line(sprintf(
            '%d) [%s] %s - %dh (skill: %s)',
            $i + 1,
            $step['action_type'] ?? '?',
            $step['title'] ?? 'Paso',
            $step['duration_hours'] ?? 0,
            $step['skill_name'] ?? 'N/A'
        ));
    }

    return 0;
})->purpose('Genera una ruta de desarrollo para una peoplea hacia un rol objetivo');

Artisan::command('candidates:rank {job_opening_id}', function () {
    $openingId = (string) $this->argument('job_opening_id');
    $jobOpening = \App\Models\JobOpening::find($openingId);

    if (! $jobOpening) {
        $this->error('Vacante no encontrada: ' . $openingId);
        return 1;
    }

    $service = new \App\Services\MatchingService();
    $candidates = $service->rankCandidatesForOpening($jobOpening);

    $this->info('Candidatos para: ' . $jobOpening->title . ' (rol: ' . $jobOpening->role->name . ')');
    foreach ($candidates as $i => $c) {
        $this->line(sprintf(
            '%d) %s  — match=%0.2f%%, time=%0.1f meses, risk=%d, missing=[%s]',
            $i + 1,
            $c['name'],
            $c['match_percentage'],
            $c['time_to_productivity_months'],
            $c['risk_factor'],
            implode(', ', $c['missing_skills'])
        ));
    }

    return 0;
})->purpose('Lista candidatos internos rankeados para una vacante');
