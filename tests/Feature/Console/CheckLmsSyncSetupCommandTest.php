<?php

it('validates lms sync scheduler setup', function () {
    $this->artisan('lms:check-sync-setup')
        ->assertExitCode(0);
});
