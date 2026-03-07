<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_runs_successfully()
    {
        // Execute the main database seeder
        $exitCode = Artisan::call('db:seed', ['--class' => 'DatabaseSeeder']);

        // Assert the command executed successfully
        $this->assertEquals(0, $exitCode, 'El DatabaseSeeder principal falló al ejecutarse.');

        $this->assertTrue(\App\Models\User::count() >= 1); // Al menos un usuario se debería crear
    }
}
