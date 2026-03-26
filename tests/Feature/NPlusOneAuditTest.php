<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class NPlusOneAuditTest extends TestCase
{
    /**
     * Collected SQL queries during the request
     *
     * @var array<int,string>
     */
    protected array $queries = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Collect queries to $this->queries
        DB::listen(function ($query) {
            $this->queries[] = trim($query->sql);
        });
    }

    public function test_collect_queries_for_representative_endpoints(): void
    {
        // Public endpoints (no auth)
        $public = [
            ['method' => 'GET', 'url' => '/api/catalogs'],
        ];

        $report = [];

        foreach ($public as $ep) {
            $this->queries = [];
            $response = $this->json($ep['method'], $ep['url']);

            $report[$ep['url']] = [
                'status' => $response->status(),
                'queries_count' => count($this->queries),
                'sample_queries' => array_slice($this->queries, 0, 12),
            ];
        }

        // Authenticated endpoints (use a single user with admin role to avoid permission aborts)
        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $authEndpoints = [
            ['method' => 'GET', 'url' => '/api/people'],
            ['method' => 'GET', 'url' => '/api/messaging/conversations'],
        ];

        foreach ($authEndpoints as $ep) {
            $this->queries = [];
            $response = $this->json($ep['method'], $ep['url']);

            $report[$ep['url']] = [
                'status' => $response->status(),
                'queries_count' => count($this->queries),
                'sample_queries' => array_slice($this->queries, 0, 12),
            ];
        }

        // Save a JSON report for manual inspection
        @mkdir(storage_path('logs'), 0755, true);
        file_put_contents(storage_path('logs/nplusone_report.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Always pass — this test is for data collection, not assertion
        $this->assertTrue(true);
    }
}
