<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        // Ensure sqlite database file exists for RefreshDatabase migrations when using sqlite
        try {
            $default = config('database.default');
            if ($default === 'sqlite') {
                $database = config('database.connections.sqlite.database');
                if ($database && ! file_exists($database)) {
                    $dir = dirname($database);
                    if (! is_dir($dir)) {
                        mkdir($dir, 0777, true);
                    }
                    @touch($database);
                }
            }
        } catch (\Throwable $e) {
            // ignore in lightweight contexts where config/app not yet loaded
        }

        parent::setUp();
    }
}
