<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route as RouteFacade;
use App\Models\User;

class NPlusOneFullScanTest extends TestCase
{
    protected array $queries = [];

    protected function setUp(): void
    {
        parent::setUp();

        DB::listen(function ($query) {
            $this->queries[] = trim($query->sql);
        });
    }

    public function test_scan_all_get_api_routes_and_report(): void
    {
        $routes = app('router')->getRoutes()->getRoutes();

        // Ensure an admin user for authenticated endpoints
        $admin = User::factory()->create(['role' => 'admin']);

        $report = [];

        foreach ($routes as $route) {
            $methods = $route->methods();
            $uri = $route->uri();

            // Only API GET routes
            if (! str_starts_with($uri, 'api/')) {
                continue;
            }

            if (! in_array('GET', $methods, true) && ! in_array('HEAD', $methods, true)) {
                continue;
            }

            // Replace route params with sample values
            $path = preg_replace_callback('/\{([^}]+)\}/', function ($m) {
                $param = $m[1];
                if (str_contains($param, 'slug') || str_contains($param, 'tenant')) {
                    return 'demo';
                }
                if (str_contains($param, 'uuid')) {
                    return '00000000-0000-0000-0000-000000000000';
                }
                return '1';
            }, $uri);

            $url = '/'.$path;

            $this->queries = [];

            try {
                // Use admin user to avoid permission aborts
                $this->actingAs($admin);
                $response = $this->json('GET', $url);
                $status = $response->status();
                $body = $response->getContent();
            } catch (\Throwable $e) {
                $status = 'exception';
                $body = $e->getMessage();
            }

            $report[$url] = [
                'status' => $status,
                'queries_count' => count($this->queries),
                'sample_queries' => array_slice($this->queries, 0, 8),
                'body_preview' => substr($body ?? '', 0, 200),
            ];
        }

        @mkdir(storage_path('logs'), 0755, true);
        file_put_contents(storage_path('logs/nplusone_full_report.json'), json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // CSV prioritized by queries_count desc
        $rows = [];
        foreach ($report as $endpoint => $data) {
            $rows[] = [$endpoint, $data['status'], $data['queries_count']];
        }

        usort($rows, function ($a, $b) {
            return $b[2] <=> $a[2];
        });

        $csv = "endpoint,status,queries_count\n";
        foreach ($rows as $r) {
            $csv .= sprintf("%s,%s,%d\n", str_replace(',', '\\,', $r[0]), $r[1], $r[2]);
        }

        file_put_contents(storage_path('logs/nplusone_full_report.csv'), $csv);

        // No assertions — this is a data collection test
        $this->assertTrue(true);
    }
}
