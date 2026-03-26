<?php

namespace App\Services;

use App\Models\TalentPass;
use Barryvdh\DomPDF\Facade\Pdf;

class CVExportService
{
    /**
     * Export talent pass to PDF
     */
    public function exportPdf(TalentPass $talentPass): string
    {
        $data = [
            'talent_pass' => $talentPass->load(['skills', 'credentials', 'experiences', 'person']),
            'generated_at' => now(),
        ];

        $pdf = Pdf::loadView('talent-pass.cv-pdf', $data);

        return $pdf->download($talentPass->title . '_' . now()->format('Y-m-d') . '.pdf')->getContent();
    }

    /**
     * Generate public CV link for sharing
     */
    public function generateShareableLink(TalentPass $talentPass): string
    {
        $publicId = $talentPass->ulid;

        return route('talent-pass.public', ['publicId' => $publicId]);
    }

    /**
     * Export talent pass as JSON
     */
    public function exportJson(TalentPass $talentPass): string
    {
        $data = [
            'id' => $talentPass->id,
            'title' => $talentPass->title,
            'summary' => $talentPass->summary,
            'person' => [
                'id' => $talentPass->person?->id,
                'name' => $talentPass->person?->name,
                'email' => $talentPass->person?->email,
            ],
            'skills' => $talentPass->skills->map(fn($skill) => [
                'name' => $skill->skill_name,
                'level' => $skill->proficiency_level,
                'years' => $skill->years_of_experience,
                'endorsements' => $skill->endorsement_count,
            ])->toArray(),
            'credentials' => $talentPass->credentials->map(fn($cred) => [
                'name' => $cred->credential_name,
                'issuer' => $cred->issuer,
                'issued_date' => $cred->issued_date->format('Y-m-d'),
                'expiry_date' => $cred->expiry_date?->format('Y-m-d'),
                'url' => $cred->credential_url,
            ])->toArray(),
            'experiences' => $talentPass->experiences->map(fn($exp) => [
                'job_title' => $exp->job_title,
                'company' => $exp->company,
                'location' => $exp->location,
                'type' => $exp->employment_type,
                'description' => $exp->description,
                'start_date' => $exp->start_date->format('Y-m-d'),
                'end_date' => $exp->end_date?->format('Y-m-d'),
                'duration' => $exp->getDurationFormatted(),
                'is_current' => $exp->is_current,
            ])->toArray(),
            'generated_at' => now()->toIso8601String(),
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Generate LinkedIn-compatible format
     */
    public function exportLinkedIn(TalentPass $talentPass): string
    {
        $data = [
            'headline' => $talentPass->title,
            'summary' => $talentPass->summary,
            'current_position' => $talentPass->experiences()
                ->where('is_current', true)
                ->first()?->job_title,
            'experience' => $talentPass->experiences->map(fn($exp) => [
                'title' => $exp->job_title,
                'company' => $exp->company,
                'location' => $exp->location,
                'start_date' => $exp->start_date->format('Y-m-d'),
                'end_date' => $exp->end_date?->format('Y-m-d'),
                'is_current' => $exp->is_current,
            ])->toArray(),
            'skills' => $talentPass->skills->pluck('skill_name')->toArray(),
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Calculate completeness score
     */
    public function getCompletenessScore(TalentPass $talentPass): int
    {
        $score = 0;

        // Title (10%)
        if (!empty($talentPass->title)) $score += 10;

        // Summary (10%)
        if (!empty($talentPass->summary)) $score += 10;

        // Skills (20%)
        if ($talentPass->skills->count() > 0) $score += 20;

        // Credentials (20%)
        if ($talentPass->credentials->count() > 0) $score += 20;

        // Experiences (40%)
        if ($talentPass->experiences->count() > 0) $score += 40;

        return min(100, $score);
    }
}
