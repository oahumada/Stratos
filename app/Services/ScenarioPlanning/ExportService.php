<?php

namespace App\Services\ScenarioPlanning;

use App\Models\Scenario;
use Illuminate\Support\Facades\Storage;
use Mpdf\Mpdf;
use PhpOffice\PhpPresentation\IOFactory as PptxFactory;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Slide\Background\Color as SlideBackground;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

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
    ) {}

    /**
     * Generate PDF export of executive summary
     *
     * @param  int  $scenarioId  Scenario identifier
     * @param  array  $options  Export options (format, style, include_appendix, etc)
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
            $theme = $this->getTemplateTheme($options['template'] ?? 'default');
            $htmlContent = $this->buildPdfContent($summary, $scenario, $theme);

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
     * @param  int  $scenarioId  Scenario identifier
     * @param  array  $options  Export options (template, style, slides, etc)
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

            // Build PPTX presentation
            $theme = $this->getTemplateTheme($options['template'] ?? 'default');
            $presentation = $this->buildPptxPresentation($summary, $scenario, $theme);

            // Ensure directory exists
            Storage::makeDirectory('exports/pptx', 0755, true);

            // Save PPTX file to storage
            $storagePath = "exports/pptx/{$filename}";
            $fullPath = storage_path("app/{$storagePath}");
            $writer = PptxFactory::createWriter($presentation, 'PowerPoint2007');
            $writer->save($fullPath);

            return [
                'success' => true,
                'file_path' => $storagePath,
                'filename' => $filename,
                'download_url' => "/api/strategic-planning/scenarios/{$scenarioId}/export/pptx/download?file={$filename}",
                'expires_in_hours' => 24,
                'file_size' => filesize($fullPath),
                'generated_at' => now()->toIso8601String(),
                'slides' => 5,
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
     * Build PhpPresentation with 5 professional slides
     *
     * Slides:
     *   1. Title + scenario metadata
     *   2. Key Performance Indicators (KPI table)
     *   3. Decision Recommendation + confidence
     *   4. Risk Assessment
     *   5. Next Steps action items
     */
    private function buildPptxPresentation(array $summary, mixed $scenario, array $theme = []): PhpPresentation
    {
        // Strip '#' prefix from hex for PhpPresentation Color class
        $colorHex = ltrim($theme['primary'] ?? '#1e3a8a', '#');
        $colorHex = strtoupper($colorHex);

        $presentation = new PhpPresentation;

        $presentation->getDocumentProperties()
            ->setCreator('Stratos Strategic Planning')
            ->setLastModifiedBy('Stratos AI')
            ->setTitle('Executive Summary — '.($scenario->name ?? 'Scenario'))
            ->setDescription('Strategic Planning Executive Summary generated by Stratos')
            ->setSubject('Scenario Analysis')
            ->setKeywords('strategy planning executive scenario');

        $this->buildTitleSlide($presentation->getActiveSlide(), $summary, $scenario, $colorHex);
        $this->buildKpiSlide($presentation->createSlide(), $summary, $colorHex);
        $this->buildRecommendationSlide($presentation->createSlide(), $summary, $colorHex);
        $this->buildRiskSlide($presentation->createSlide(), $summary, $colorHex);
        $this->buildNextStepsSlide($presentation->createSlide(), $summary, $colorHex);

        return $presentation;
    }

    /** Slide 1: Title slide */
    private function buildTitleSlide(mixed $slide, array $summary, mixed $scenario, string $colorHex = '1E3A8A'): void
    {
        $slide->setBackground($this->makeSolidBackground($colorHex));

        $title = $slide->createRichTextShape()
            ->setHeight(120)->setWidth(760)->setOffsetX(100)->setOffsetY(130);
        $title->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $title->createTextRun('EXECUTIVE SUMMARY')
            ->getFont()->setBold(true)->setSize(32)->setColor(new Color('FFFFFFFF'));

        $nameBox = $slide->createRichTextShape()
            ->setHeight(80)->setWidth(760)->setOffsetX(100)->setOffsetY(260);
        $nameBox->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $nameBox->createTextRun($scenario->name ?? 'Strategic Scenario')
            ->getFont()->setSize(22)->setColor(new Color('FFBFDBFE'));

        $code = $scenario->code ?? 'N/A';
        $org = $scenario->organization->name ?? ($scenario->organization_id ?? 'Organization');
        $date = now()->format('F j, Y');
        $meta = $slide->createRichTextShape()
            ->setHeight(50)->setWidth(760)->setOffsetX(100)->setOffsetY(370);
        $meta->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $meta->createTextRun("{$code}  |  {$date}  |  {$org}")
            ->getFont()->setSize(13)->setColor(new Color('FF93C5FD'));

        $footer = $slide->createRichTextShape()
            ->setHeight(30)->setWidth(760)->setOffsetX(100)->setOffsetY(470);
        $footer->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $footer->createTextRun('CONFIDENTIAL — Internal Use Only')
            ->getFont()->setSize(10)->setItalic(true)->setColor(new Color('FF60A5FA'));
    }

    /** Slide 2: KPI table */
    private function buildKpiSlide(mixed $slide, array $summary, string $colorHex = '1E3A8A'): void
    {
        $slide->setBackground($this->makeSolidBackground('F8FAFC'));
        $this->addSlideTitle($slide, 'Key Performance Indicators', $colorHex);

        $kpis = $summary['kpis'] ?? [];
        $yOffset = 120;
        $rowHeight = 52;
        $colWidths = [280, 180, 200];
        $xOffsets = [60, 340, 520];
        $headers = ['Indicator', 'Value', 'Status'];

        foreach ($headers as $i => $header) {
            $cell = $slide->createRichTextShape()
                ->setHeight(36)->setWidth($colWidths[$i])
                ->setOffsetX($xOffsets[$i])->setOffsetY($yOffset);
            $cell->getFill()->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('FF1E3A8A'));
            $cell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $cell->createTextRun($header)
                ->getFont()->setBold(true)->setSize(11)->setColor(new Color('FFFFFFFF'));
        }

        $statusColors = [
            'excellent' => '10B981', 'good' => '3B82F6',
            'warning' => 'F59E0B', 'critical' => 'EF4444',
        ];

        foreach (array_slice($kpis, 0, 8) as $idx => $kpi) {
            $y = $yOffset + 36 + ($idx * $rowHeight);
            $rowBg = ($idx % 2 === 0) ? 'FFFFFF' : 'EFF6FF';
            $status = $kpi['status'] ?? 'neutral';
            $statusHex = $statusColors[$status] ?? '6B7280';

            $cellData = [
                $kpi['title'] ?? '',
                ($kpi['value'] ?? '').' '.($kpi['unit'] ?? ''),
                strtoupper($status),
            ];

            foreach ($cellData as $ci => $cellText) {
                $isStatus = ($ci === 2);
                $cell = $slide->createRichTextShape()
                    ->setHeight($rowHeight - 4)->setWidth($colWidths[$ci])
                    ->setOffsetX($xOffsets[$ci])->setOffsetY($y);
                $cell->getFill()->setFillType(Fill::FILL_SOLID)
                    ->setStartColor(new Color('FF'.($isStatus ? $statusHex : $rowBg)));
                $cell->getActiveParagraph()->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal($ci === 0 ? Alignment::HORIZONTAL_LEFT : Alignment::HORIZONTAL_CENTER);
                $cell->createTextRun($cellText)
                    ->getFont()->setSize(10)->setBold($isStatus)
                    ->setColor(new Color($isStatus ? 'FFFFFFFF' : 'FF1E293B'));
            }
        }
    }

    /** Slide 3: Decision Recommendation */
    private function buildRecommendationSlide(mixed $slide, array $summary, string $colorHex = '1E3A8A'): void
    {
        $slide->setBackground($this->makeSolidBackground('F0F9FF'));
        $this->addSlideTitle($slide, 'Decision Recommendation', $colorHex);

        $rec = $summary['decision_recommendation'] ?? [];
        $recommendation = $rec['recommendation'] ?? 'Analysis in progress';
        $confidence = (int) ($rec['confidence'] ?? 0);
        $reasoning = $rec['reasoning'] ?? '';

        $badge = $slide->createRichTextShape()
            ->setHeight(60)->setWidth(620)->setOffsetX(100)->setOffsetY(130);
        $badge->getFill()->setFillType(Fill::FILL_SOLID)
            ->setStartColor(new Color('FF'.$colorHex));
        $badge->getActiveParagraph()->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
        $badge->createTextRun(strtoupper($recommendation))
            ->getFont()->setBold(true)->setSize(18)->setColor(new Color('FFFFFFFF'));

        $confLabel = $slide->createRichTextShape()
            ->setHeight(30)->setWidth(300)->setOffsetX(100)->setOffsetY(210);
        $confLabel->createTextRun("Confidence: {$confidence}%")
            ->getFont()->setBold(true)->setSize(13)->setColor(new Color('FF'.$colorHex));

        $barFilled = max(0, min(10, (int) round($confidence / 10)));
        $bar = $slide->createRichTextShape()
            ->setHeight(30)->setWidth(500)->setOffsetX(100)->setOffsetY(245);
        $bar->createTextRun(str_repeat('██', $barFilled).str_repeat('░░', 10 - $barFilled))
            ->getFont()->setSize(14)->setColor(new Color('FF3B82F6'));

        if (! empty($reasoning)) {
            $reasonBox = $slide->createRichTextShape()
                ->setHeight(160)->setWidth(620)->setOffsetX(100)->setOffsetY(290);
            $reasonBox->getFill()->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('FFDBEAFE'));
            $reasonBox->createTextRun("Reasoning:\n".$reasoning)
                ->getFont()->setSize(11)->setColor(new Color('FF'.$colorHex));
        }
    }

    /** Slide 4: Risk Assessment */
    private function buildRiskSlide(mixed $slide, array $summary, string $colorHex = '1E3A8A'): void
    {
        $slide->setBackground($this->makeSolidBackground('FFF7ED'));
        $this->addSlideTitle($slide, 'Risk Assessment', $colorHex);

        $risks = $summary['risks'] ?? [];

        if (empty($risks)) {
            $slide->createRichTextShape()
                ->setHeight(60)->setWidth(620)->setOffsetX(100)->setOffsetY(180)
                ->createTextRun('No risks identified for this scenario.')
                ->getFont()->setSize(14)->setColor(new Color('FF6B7280'));

            return;
        }

        $yOffset = 120;
        $colWidths = [220, 120, 150, 250];
        $xOffsets = [40, 260, 380, 530];
        $headers = ['Risk Category', 'Likelihood', 'Impact', 'Mitigation'];

        foreach ($headers as $i => $header) {
            $cell = $slide->createRichTextShape()
                ->setHeight(32)->setWidth($colWidths[$i])
                ->setOffsetX($xOffsets[$i])->setOffsetY($yOffset);
            $cell->getFill()->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('FFB45309'));
            $cell->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $cell->createTextRun($header)
                ->getFont()->setBold(true)->setSize(10)->setColor(new Color('FFFFFFFF'));
        }

        $severityColors = [
            'critical' => 'EF4444', 'high' => 'F97316',
            'medium' => 'F59E0B', 'low' => '22C55E',
        ];

        foreach (array_slice($risks, 0, 6) as $idx => $risk) {
            $y = $yOffset + 32 + ($idx * 48);
            $rowBg = ($idx % 2 === 0) ? 'FFFFFF' : 'FFF3E0';
            $severity = strtolower($risk['severity'] ?? 'medium');
            $severityHex = $severityColors[$severity] ?? 'F59E0B';

            $rowData = [
                $risk['category'] ?? ($risk['risk'] ?? ''),
                strtoupper($risk['likelihood'] ?? $severity),
                strtoupper($risk['impact'] ?? $severity),
                $risk['mitigation'] ?? 'Under review',
            ];

            foreach ($rowData as $ci => $cellText) {
                $isSev = ($ci === 1 || $ci === 2);
                $cell = $slide->createRichTextShape()
                    ->setHeight(44)->setWidth($colWidths[$ci])
                    ->setOffsetX($xOffsets[$ci])->setOffsetY($y);
                $cell->getFill()->setFillType(Fill::FILL_SOLID)
                    ->setStartColor(new Color('FF'.($isSev ? $severityHex : $rowBg)));
                $cell->getActiveParagraph()->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal($isSev ? Alignment::HORIZONTAL_CENTER : Alignment::HORIZONTAL_LEFT);
                $cell->createTextRun($cellText)
                    ->getFont()->setSize(9)->setBold($isSev)
                    ->setColor(new Color($isSev ? 'FFFFFFFF' : 'FF1E293B'));
            }
        }
    }

    /** Slide 5: Next Steps */
    private function buildNextStepsSlide(mixed $slide, array $summary, string $colorHex = '1E3A8A'): void
    {
        $slide->setBackground($this->makeSolidBackground('F0FDF4'));
        $this->addSlideTitle($slide, 'Next Steps', $colorHex);

        $nextSteps = $summary['next_steps'] ?? [];

        if (empty($nextSteps)) {
            $slide->createRichTextShape()
                ->setHeight(60)->setWidth(620)->setOffsetX(100)->setOffsetY(180)
                ->createTextRun('No next steps defined yet.')
                ->getFont()->setSize(14)->setColor(new Color('FF6B7280'));

            return;
        }

        foreach (array_slice($nextSteps, 0, 6) as $idx => $step) {
            $y = 130 + ($idx * 60);
            $action = is_array($step) ? ($step['action'] ?? $step['step'] ?? (string) $step) : (string) $step;
            $owner = is_array($step) ? ($step['owner'] ?? '') : '';
            $due = is_array($step) ? ($step['due_date'] ?? $step['timeline'] ?? '') : '';

            $num = $slide->createRichTextShape()
                ->setHeight(40)->setWidth(40)->setOffsetX(50)->setOffsetY($y);
            $num->getFill()->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('FF166534'));
            $num->getActiveParagraph()->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                ->setVertical(Alignment::VERTICAL_CENTER);
            $num->createTextRun((string) ($idx + 1))
                ->getFont()->setBold(true)->setSize(13)->setColor(new Color('FFFFFFFF'));

            $actionBox = $slide->createRichTextShape()
                ->setHeight(40)->setWidth(480)->setOffsetX(100)->setOffsetY($y);
            $actionBox->getFill()->setFillType(Fill::FILL_SOLID)
                ->setStartColor(new Color('FFD1FAE5'));
            $actionBox->createTextRun($action)
                ->getFont()->setSize(11)->setColor(new Color('FF065F46'));

            if ($owner || $due) {
                $meta = $slide->createRichTextShape()
                    ->setHeight(20)->setWidth(480)->setOffsetX(100)->setOffsetY($y + 40);
                $metaText = trim(($owner ? "Owner: {$owner}" : '').($due ? "  |  Due: {$due}" : ''));
                $meta->createTextRun($metaText)
                    ->getFont()->setSize(9)->setItalic(true)->setColor(new Color('FF6B7280'));
            }
        }
    }

    /** Helper: slide title text */
    private function addSlideTitle(mixed $slide, string $title, string $colorHex = '1E3A8A'): void
    {
        $titleShape = $slide->createRichTextShape()
            ->setHeight(50)->setWidth(820)->setOffsetX(40)->setOffsetY(20);
        $titleShape->createTextRun($title)
            ->getFont()->setBold(true)->setSize(20)->setColor(new Color('FF'.$colorHex));
    }

    /** Helper: solid slide background */
    private function makeSolidBackground(string $colorHex): SlideBackground
    {
        $bg = new SlideBackground;
        $bg->setColor(new Color('FF'.$colorHex));

        return $bg;
    }

    /**
     * Return a theme color/style configuration for the given template name.
     *
     * Supported templates:
     *  - default   Blue corporate (Stratos brand)
     *  - executive Dark navy + gold accent (boardroom style)
     *  - minimal   Clean gray + white (light print-friendly)
     *
     * @param  string  $template  Template identifier
     * @return array{primary: string, accent: string, header_bg: string, header_text: string, badge: string, font_family: string}
     */
    private function getTemplateTheme(string $template): array
    {
        return match ($template) {
            'executive' => [
                'primary' => '#0f172a',
                'accent' => '#d97706',
                'header_bg' => 'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)',
                'header_text' => '#f8fafc',
                'badge' => '#d97706',
                'link_color' => '#f59e0b',
                'font_family' => "'Georgia', 'Times New Roman', serif",
            ],
            'minimal' => [
                'primary' => '#374151',
                'accent' => '#6b7280',
                'header_bg' => 'linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%)',
                'header_text' => '#1f2937',
                'badge' => '#6b7280',
                'link_color' => '#374151',
                'font_family' => "'Arial', 'Helvetica', sans-serif",
            ],
            default => [
                'primary' => '#1e3a8a',
                'accent' => '#1e40af',
                'header_bg' => 'linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%)',
                'header_text' => '#ffffff',
                'badge' => '#1e40af',
                'link_color' => '#3b82f6',
                'font_family' => "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif",
            ],
        };
    }

    /**
     * Build PDF content with professional HTML template
     *
     * @param  array  $summary  Executive summary data
     * @param  mixed  $scenario  Scenario model or object with scenario data
     * @param  array  $theme  Template theme colors (from getTemplateTheme)
     * @return string HTML content for PDF rendering via mPDF
     */
    private function buildPdfContent(array $summary, mixed $scenario, array $theme = []): string
    {
        // Resolve theme values (fall back to default corporate blue)
        $themePrimary = $theme['primary'] ?? '#1e3a8a';
        $themeAccent = $theme['accent'] ?? '#1e40af';
        $themeHeaderBg = $theme['header_bg'] ?? 'linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%)';
        $themeHeaderText = $theme['header_text'] ?? '#ffffff';
        $themeLinkColor = $theme['link_color'] ?? '#3b82f6';
        $themeFontFamily = $theme['font_family'] ?? "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";

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
            $confidenceBar = str_repeat('█', (int) ($confidence / 10)).str_repeat('░', 10 - (int) ($confidence / 10));
            $recommendationText = htmlspecialchars((string) ($recommendation['recommendation'] ?? 'N/A'), ENT_QUOTES, 'UTF-8');
            $reasoningText = htmlspecialchars((string) ($recommendation['reasoning'] ?? 'Analysis in progress...'), ENT_QUOTES, 'UTF-8');

            $recommendationHtml = <<<HTML
        <div style="margin-top: 20px; border-left: 4px solid $themeAccent; padding: 15px; background-color: #f0f9ff;">
            <h3 style="margin: 0 0 10px 0; color: $themeAccent; font-size: 14px;">Decision Recommendation</h3>
            <p style="margin: 5px 0; font-weight: bold; font-size: 12px;">Recommendation:</p>
            <p style="margin: 0 0 10px 0; font-size: 12px; color: $themePrimary;">
                $recommendationText
            </p>
            <p style="margin: 5px 0; font-weight: bold; font-size: 12px;">Confidence Level: {$confidence}%</p>
            <p style="margin: 5px 0; font-family: monospace; letter-spacing: 2px; font-size: 10px; color: $themeLinkColor;">
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
            $riskHtml = '<h3 style="margin-top: 20px; page-break-inside: avoid; color: '.$themePrimary.'; font-size: 14px;">Risk Assessment</h3>';
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
            $nextStepsHtml = '<h3 style="margin-top: 20px; page-break-inside: avoid; color: '.$themePrimary.'; font-size: 14px;">Next Steps & Action Items</h3>';
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
            font-family: $themeFontFamily;
            color: #1f2937;
            line-height: 1.6;
            margin: 0;
        }
        
        .header {
            background: $themeHeaderBg;
            color: $themeHeaderText;
            padding: 30px 20px;
            margin: -15px -15px 0 -15px;
            border-bottom: 4px solid $themeAccent;
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
            color: $themePrimary;
            font-size: 16px;
            margin: 25px 0 15px 0;
            border-bottom: 2px solid $themeLinkColor;
            padding-bottom: 8px;
        }
        
        h3 {
            color: $themePrimary;
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
     * @param  array  $summary  Executive summary data
     * @param  Scenario  $scenario  Scenario model
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
     * @param  string  $filename  File identifier
     * @param  string  $format  Export format (pdf|pptx)
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
     * @param  int  $scenarioId  Scenario identifier
     * @param  string  $format  Export format (pdf|pptx)
     * @param  array  $options  Additional export options
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
