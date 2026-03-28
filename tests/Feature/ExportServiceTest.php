<?php

namespace Tests\Unit;

use App\Models\Organization;
use App\Models\Scenario;
use App\Services\ScenarioPlanning\ExecutiveSummaryService;
use App\Services\ScenarioPlanning\ExportService;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ExportServiceMpdfTest extends TestCase
{
    private ExportService $exportService;
    private $organization;
    private $scenario;

    protected function setUp(): void
    {
        parent::setUp();

        // Create simple mock objects
        $this->organization = (object) [
            'name' => 'Test Organization',
        ];

        $this->scenario = (object) [
            'id' => 1,
            'name' => 'Test Scenario',
            'code' => 'TEST-001',
            'organization' => $this->organization,
        ];

        // Mock ExecutiveSummaryService
        $summaryService = $this->createMock(ExecutiveSummaryService::class);
        $summaryService->method('generateExecutiveSummary')->willReturn($this->getMockSummary());

        $this->exportService = new ExportService($summaryService);
    }

    private function getMockSummary(): array
    {
        return [
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'kpis' => [
                [
                    'title' => 'Total Headcount',
                    'value' => 450,
                    'unit' => 'employees',
                    'status' => 'good',
                ],
                [
                    'title' => 'Cost Impact',
                    'value' => 2500000,
                    'unit' => 'USD',
                    'status' => 'warning',
                ],
            ],
            'decision_recommendation' => [
                'recommendation' => 'Proceed with Phase 2 planning',
                'confidence' => 85,
                'reasoning' => 'The scenario demonstrates strong financial viability.',
            ],
            'risks' => [
                [
                    'title' => 'Market Volatility',
                    'severity' => 'medium',
                    'mitigation' => 'Implement hedging strategy',
                ],
            ],
            'next_steps' => [
                [
                    'action' => 'Stakeholder alignment meeting',
                    'owner' => 'Project Manager',
                    'due_date' => '2026-04-15',
                ],
            ],
        ];
    }

    public function test_build_pdf_content_returns_string(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $html = $method->invoke($this->exportService, $this->getMockSummary(), $this->scenario);

        $this->assertIsString($html);
        $this->assertNotEmpty($html);
    }

    public function test_build_pdf_content_contains_scenario_name(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $html = $method->invoke($this->exportService, $this->getMockSummary(), $this->scenario);

        $this->assertStringContainsString($this->scenario->name, $html);
    }

    public function test_build_pdf_content_contains_executive_summary_header(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $html = $method->invoke($this->exportService, $this->getMockSummary(), $this->scenario);

        $this->assertStringContainsString('Executive Summary Report', $html);
    }

    public function test_build_pdf_content_contains_kpi_table(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $html = $method->invoke($this->exportService, $this->getMockSummary(), $this->scenario);

        $this->assertStringContainsString('Total Headcount', $html);
        $this->assertStringContainsString('Cost Impact', $html);
    }

    public function test_build_pdf_content_contains_decision_recommendation(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $html = $method->invoke($this->exportService, $this->getMockSummary(), $this->scenario);

        $this->assertStringContainsString('Decision Recommendation', $html);
        $this->assertStringContainsString('Proceed with Phase 2 planning', $html);
    }

    public function test_build_pdf_content_contains_risk_assessment(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $html = $method->invoke($this->exportService, $this->getMockSummary(), $this->scenario);

        $this->assertStringContainsString('Risk Assessment', $html);
        $this->assertStringContainsString('Market Volatility', $html);
    }

    public function test_build_pdf_content_contains_next_steps(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $html = $method->invoke($this->exportService, $this->getMockSummary(), $this->scenario);

        $this->assertStringContainsString('Next Steps', $html);
        $this->assertStringContainsString('Stakeholder alignment meeting', $html);
    }

    public function test_build_pdf_content_contains_footer(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $html = $method->invoke($this->exportService, $this->getMockSummary(), $this->scenario);

        $this->assertStringContainsString('Confidentiality Notice', $html);
        $this->assertStringContainsString('Stratos Workforce Planning System', $html);
    }

    public function test_build_pdf_content_is_valid_html(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $html = $method->invoke($this->exportService, $this->getMockSummary(), $this->scenario);

        // Check basic HTML structure
        $this->assertStringContainsString('<!DOCTYPE html>', $html);
        $this->assertStringContainsString('<html', $html);
        $this->assertStringContainsString('</html>', $html);
        $this->assertStringContainsString('<body>', $html);
        $this->assertStringContainsString('</body>', $html);
    }

    public function test_build_pdf_content_handles_special_characters(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $summary = $this->getMockSummary();
        $summary['kpis'][0]['title'] = 'Test & Special <Characters>';
        $summary['decision_recommendation']['recommendation'] = 'Proceed "carefully" with \'quotes\'';

        $html = $method->invoke($this->exportService, $summary, $this->scenario);

        $this->assertNotEmpty($html);
        $this->assertIsString($html);
    }

    public function test_build_pdf_content_handles_empty_risks(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        $summary = $this->getMockSummary();
        $summary['risks'] = [];

        $html = $method->invoke($this->exportService, $summary, $this->scenario);

        // Should still generate valid HTML without risks section
        $this->assertIsString($html);
        $this->assertNotEmpty($html);
    }

    public function test_build_pdf_content_handles_missing_fields(): void
    {
        $reflection = new ReflectionClass($this->exportService);
        $method = $reflection->getMethod('buildPdfContent');
        $method->setAccessible(true);

        // Minimal summary with only required fields
        $summary = [
            'generated_at' => '2026-03-27 10:00:00',
            'kpis' => [],
            'decision_recommendation' => [],
        ];

        $html = $method->invoke($this->exportService, $summary, $this->scenario);

        // Should still generate valid HTML
        $this->assertIsString($html);
        $this->assertNotEmpty($html);
    }
}

