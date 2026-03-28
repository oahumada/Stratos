<?php

namespace App\Services\ScenarioPlanning;

use App\Models\Scenario;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;

/**
 * ExportService — Generate executive summary exports in PDF/PPTX formats
 *
 * Handles document generation for executive presentations:
 * - PDF reports with charts and tables (via mPDF)
 * - PPTX presentations for stakeholder meetings
 * - Storage and URL generation for downloads
 */
class ExportService
{
    public function __construct(
        private ExecutiveSummaryService $summaryService
    ) {
    }

    /**
     * Generate PDF export of executive summary
     *
     * @param int $scenarioId Scenario identifier
     * @param array $options Export options (format, style, include_appendix, etc)
     * @return array ['success' => bool, 'file_path' => string, 'download_url' => string]
     */
    public function exportToPdf(int $scenarioId, array $options = []): array
    {
        try {
            $scenario = Scenario::findOrFail($scenarioId);
            $summary = $this->summaryService->generateExecutiveSummary($scenarioId);

            // Generate filename with timestamp
            $filename = sprintf(
                'executive-summary-%s-%d.pdf',
                $scenario->code,
                now()->timestamp
            );

            // Build HTML content for PDF
            $htmlContent = $this->buildPdfContent($summary, $scenario);

            // Initialize mPDF with configuration
            $mpdf = new Mpdf([
                'tempDir' => storage_path('app/temp'),
                'format' => 'A4',
                'orientation' => 'P',
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_left' => 15,
                'margin_right' => 15,
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
            ]);

            // Write HTML to PDF
            $mpdf->WriteHTML($htmlContent);

            // Generate full path for storage
            $storagePath = "exports/pdf/{$filename}";
            $fullPath = storage_path("app/{$storagePath}");

            // Ensure directory exists
            Storage::makeDirectory('exports/pdf', 0755, true);

            // Save PDF to storage
            $mpdf->Output($fullPath, Mpdf\Output\Destination::FILE);

            // Return success response with download URL
            return [
                'success' => true,
                'file_path' => $storagePath,
                'filename' => $filename,
                'download_url' => "/api/strategic-planning/scenarios/{$scenarioId}/export/pdf/download?file={$filename}",
                'expires_in_hours' => 24,
                'file_size' => filesize($fullPath),
                'generated_at' => now()->toIso8601String(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
            ];
        }
    }

    /**
     * Generate PPTX export of executive summary
     *
     * @param int $scenarioId Scenario identifier
     * @param array $options Export options (template, style, slides, etc)
     * @return array ['success' => bool, 'file_path' => string, 'download_url' => string]
     */
    public function exportToPptx(int $scenarioId, array $options = []): array
    {
        try {
            $scenario = Scenario::findOrFail($scenarioId);
            $summary = $this->summaryService->generateExecutiveSummary($scenarioId);

            // Generate filename with timestamp
            $filename = sprintf(
                'executive-summary-%s-%d.pptx',
                $scenario->code,
                now()->timestamp
            );

            // TODO: Implement actual PPTX generation with PHPOffice/PhpPresentation
            // For now, return stub response
            return [
                'success' => true,
                'file_path' => "exports/pptx/{$filename}",
                'download_url' => "/api/strategic-planning/scenarios/{$scenarioId}/executive-summary/download?format=pptx&file={$filename}",
                'expires_in_hours' => 24,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Build PDF content with professional HTML template
     *
     * @param array $summary Executive summary data
     * @param mixed $scenario Scenario model or object with scenario data
     * @return string HTML content for PDF rendering via mPDF
     */
    private function buildPdfContent(array $summary, mixed $scenario): string
    {
        $generatedAt = $summary['generated_at'] ?? now()->format('Y-m-d H:i:s');
        $kpiRows = '';

        // Build KPI table rows with proper status styling
        if (! empty($summary['kpis'])) {
            foreach ($summary['kpis'] as $kpi) {
                $status = $kpi['status'] ?? 'neutral';
                $statusColor = match ($status) {
                    'excellent' => '#10b981',
                    'good' => '#3b82f6',
                    'warning' => '#f59e0b',
                    'critical' => '#ef4444',
                    default => '#6b7280',
                };

                $kpiTitle = htmlspecialchars($kpi['title'] ?? '', ENT_QUOTES, 'UTF-8');
                $kpiValue = htmlspecialchars((string) ($kpi['value'] ?? ''), ENT_QUOTES, 'UTF-8');
                $kpiUnit = htmlspecialchars($kpi['unit'] ?? '', ENT_QUOTES, 'UTF-8');

                $kpiRows .= <<<HTML
                <tr>
                    <td style="width: 35%;">$kpiTitle</td>
                    <td style="width: 30%; text-align: right; font-weight: bold;">$kpiValue $kpiUnit</td>
                    <td style="width: 35%; text-align: center;">
                        <span style="background-color: $statusColor; color: white; padding: 4px 8px; border-radius: 3px; font-size: 11px;">
                            $status
                        </span>
                    </td>
                </tr>
HTML;
            }
        }

        // Build decision recommendation section
        $recommendation = $summary['decision_recommendation'] ?? [];
        $recommendationHtml = '';

        if (! empty($recommendation)) {
            $confidence = (int) ($recommendation['confidence'] ?? 0);
            $confidenceBar = str_repeat('█', (int) ($confidence / 10)) . str_repeat('░', 10 - (int) ($confidence / 10));
            $recommendationText = htmlspecialchars((string) ($recommendation['recommendation'] ?? 'N/A'), ENT_QUOTES, 'UTF-8');
            $reasoningText = htmlspecialchars((string) ($recommendation['reasoning'] ?? 'Analysis in progress...'), ENT_QUOTES, 'UTF-8');

            $recommendationHtml = <<<HTML
        <div style="margin-top: 20px; border-left: 4px solid #1e40af; padding: 15px; background-color: #f0f9ff;">
            <h3 style="margin: 0 0 10px 0; color: #1e40af; font-size: 14px;">Decision Recommendation</h3>
            <p style="margin: 5px 0; font-weight: bold; font-size: 12px;">Recommendation:</p>
            <p style="margin: 0 0 10px 0; font-size: 12px; color: #1e3a8a;">
                $recommendationText
            </p>
            <p style="margin: 5px 0; font-weight: bold; font-size: 12px;">Confidence Level: {$confidence}%</p>
            <p style="margin: 5px 0; font-family: monospace; letter-spacing: 2px; font-size: 10px; color: #3b82f6;">
                $confidenceBar
            </p>
            <p style="margin: 10px 0 0 0; font-size: 11px; color: #475569; font-style: italic;">
                $reasoningText
            </p>
        </div>
HTML;
        }

        // Build risk assessment section if available
        $riskHtml = '';
        if (! empty($summary['risks'])) {
            $riskHtml = '<h3 style="margin-top: 20px; page-break-inside: avoid; color: #1e3a8a; font-size: 14px;">Risk Assessment</h3>';
            $riskHtml .= '<table style="width: 100%; border-collapse: collapse; font-size: 11px; margin-top: 10px;">';
            $riskHtml .= '<tr style="background-color: #f3f4f6;"><th style="border: 1px solid #d1d5db; padding: 8px; text-align: left;">Risk Category</th><th style="border: 1px solid #d1d5db; padding: 8px;">Severity</th><th style="border: 1px solid #d1d5db; padding: 8px;">Mitigation</th></tr>';

            foreach ($summary['risks'] as $risk) {
                $severity = $risk['severity'] ?? 'medium';
                $severityColor = match ($severity) {
                    'critical' => '#dc2626',
                    'high' => '#ea580c',
                    'medium' => '#f59e0b',
                    'low' => '#84cc16',
                    default => '#6b7280',
                };

                $riskTitle = htmlspecialchars((string) ($risk['title'] ?? ''), ENT_QUOTES, 'UTF-8');
                $riskMitigation = htmlspecialchars((string) ($risk['mitigation'] ?? 'TBD'), ENT_QUOTES, 'UTF-8');

                $riskHtml .= <<<HTML
                <tr>
                    <td style="border: 1px solid #d1d5db; padding: 8px; font-weight: 500;">$riskTitle</td>
                    <td style="border: 1px solid #d1d5db; padding: 8px; text-align: center;">
                        <span style="background-color: $severityColor; color: white; padding: 3px 6px; border-radius: 2px; font-weight: bold; font-size: 10px;">
                            $severity
                        </span>
                    </td>
                    <td style="border: 1px solid #d1d5db; padding: 8px; font-size: 10px;">$riskMitigation</td>
                </tr>
HTML;
            }

            $riskHtml .= '</table>';
        }

        // Build next steps section if available
        $nextStepsHtml = '';
        if (! empty($summary['next_steps'])) {
            $nextStepsHtml = '<h3 style="margin-top: 20px; page-break-inside: avoid; color: #1e3a8a; font-size: 14px;">Next Steps & Action Items</h3>';
            $nextStepsHtml .= '<ol style="margin: 10px 0; padding-left: 20px; font-size: 11px;">';

            foreach ($summary['next_steps'] as $step) {
                $action = htmlspecialchars((string) ($step['action'] ?? ''), ENT_QUOTES, 'UTF-8');
                $owner = htmlspecialchars((string) ($step['owner'] ?? 'Unassigned'), ENT_QUOTES, 'UTF-8');
                $dueDate = htmlspecialchars((string) ($step['due_date'] ?? 'TBD'), ENT_QUOTES, 'UTF-8');
                $nextStepsHtml .= "<li style=\"margin: 5px 0;\">$action (Owner: $owner - Due: $dueDate)</li>";
            }

            $nextStepsHtml .= '</ol>';
        }

        // Assemble complete HTML document
        $scenarioName = htmlspecialchars($scenario->name, ENT_QUOTES, 'UTF-8');
        $scenarioCode = htmlspecialchars($scenario->code, ENT_QUOTES, 'UTF-8');
        $organizationName = htmlspecialchars($scenario->organization->name ?? 'N/A', ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Executive Summary - $scenarioName</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2937;
            line-height: 1.6;
            margin: 0;
        }
        
        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: white;
            padding: 30px 20px;
            margin: -15px -15px 0 -15px;
            border-bottom: 4px solid #0284c7;
        }
        
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: bold;
        }
        
        .header p {
            margin: 5px 0;
            font-size: 12px;
            opacity: 0.9;
        }
        
        .metadata {
            display: flex;
            justify-content: space-between;
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
            font-size: 11px;
            color: #6b7280;
        }
        
        h2 {
            color: #1e3a8a;
            font-size: 16px;
            margin: 25px 0 15px 0;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 8px;
        }
        
        h3 {
            color: #1e3a8a;
            font-size: 13px;
            margin: 15px 0 10px 0;
        }
        
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
        }
        
        .kpi-table th {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            color: #1f2937;
        }
        
        .kpi-table td {
            border: 1px solid #d1d5db;
            padding: 10px;
        }
        
        .kpi-table tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        .footer {
            margin-top: 40px;
            padding: 15px;
            border-top: 1px solid #d1d5db;
            background-color: #f9fafb;
            font-size: 10px;
            color: #6b7280;
            text-align: center;
        }
        
        .footer p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>$scenarioName</h1>
        <p>Executive Summary Report</p>
        <p>Strategic Initiative Assessment & Analysis</p>
    </div>
    
    <div class="metadata">
        <div><strong>Scenario Code:</strong> $scenarioCode</div>
        <div><strong>Generated:</strong> $generatedAt</div>
        <div><strong>Organization:</strong> $organizationName</div>
    </div>
    
    <h2>Key Performance Indicators</h2>
    <table class="kpi-table">
        <thead>
            <tr style="background-color: #f3f4f6;">
                <th>Metric</th>
                <th style="text-align: right;">Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            $kpiRows
        </tbody>
    </table>
    
    $recommendationHtml
    $riskHtml
    $nextStepsHtml
    
    <div class="footer">
        <p><strong>Confidentiality Notice:</strong> This document contains sensitive business information.</p>
        <p>For authorized use only. Unauthorized distribution is prohibited.</p>
        <p>Generated by Stratos Workforce Planning System</p>
    </div>
</body>
</html>
HTML;

        return $html;
    }

    /**
     * Build PPTX slide structure (placeholder for actual PHPPresentation implementation)
     *
     * @param array $summary Executive summary data
     * @param Scenario $scenario Scenario model
     * @return array Slide definitions for presentation
     */
    private function buildPptxSlides(array $summary, Scenario $scenario): array
    {
        // TODO: Implement slide structure for PPTX generation
        // Should include:
        // - Title slide with scenario name and date
        // - KPI dashboard slide with 8 cards
        // - Decision recommendation slide
        // - Risk assessment slide with heatmap
        // - Readiness evaluation slide
        // - Next steps action items slide
        // - Q&A slide

        return [
            [
                'title' => 'Executive Summary',
                'type' => 'title',
                'content' => [
                    'title' => $scenario->name,
                    'subtitle' => 'Strategic Initiative Assessment',
                    'date' => now()->format('Y-m-d'),
                ],
            ],
            [
                'title' => 'Key Performance Indicators',
                'type' => 'kpi_dashboard',
                'content' => $summary['kpis'],
            ],
            [
                'title' => 'Decision Recommendation',
                'type' => 'recommendation',
                'content' => $summary['decision_recommendation'],
            ],
        ];
    }

    /**
     * Get download URL for exported file
     *
     * @param string $filename File identifier
     * @param string $format Export format (pdf|pptx)
     * @return array File metadata and content
     */
    public function getExportFile(string $filename, string $format): array
    {
        $filepath = "exports/{$format}/{$filename}";

        // TODO: Implement file retrieval with expiration checking
        // Should:
        // - Verify file exists
        // - Check expiration time (24 hours)
        // - Return file stream or download URL
        // - Clean up expired files

        if (! Storage::exists($filepath)) {
            return [
                'success' => false,
                'error' => 'File not found or has expired.',
            ];
        }

        return [
            'success' => true,
            'file_path' => $filepath,
            'mime_type' => $format === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];
    }

    /**
     * Queue an async export job
     *
     * @param int $scenarioId Scenario identifier
     * @param string $format Export format (pdf|pptx)
     * @param array $options Additional export options
     * @return array Job metadata
     */
    public function queueExport(int $scenarioId, string $format, array $options = []): array
    {
        // TODO: Implement job queuing for large exports
        // Should:
        // - Create export job record
        // - Queue background job (use Laravel jobs)
        // - Return job_id and status_url
        // - Provide polling mechanism

        return [
            'success' => true,
            'job_id' => uniqid('export_', true),
            'status_url' => "/api/strategic-planning/exports/{$format}/status",
            'estimated_time_seconds' => 30,
        ];
    }
}
