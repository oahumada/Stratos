<?php

uses(RefreshDatabase::class);

const WORKFORCE_BROWSER_ACTION_TITLE = 'Capacitar célula comercial';
test('workforce browser flow is pending browser session auth support', function () {
    $this->markTestSkipped('Pest Browser no mantiene sesión autenticada usable para /workforce-planning en este entorno actual.');
});
