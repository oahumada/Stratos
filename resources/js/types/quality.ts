/**
 * Quality Metrics Types for RAGAS LLM Evaluation Dashboard
 * Aligned with app/Models/LLMEvaluation.php structure
 */

export interface QualityScore {
    faithfulness: number;
    relevance: number;
    context_alignment: number;
    coherence: number;
    hallucination_rate: number;
}

export interface QualityDistribution {
    excellent: number;
    good: number;
    acceptable: number;
    poor: number;
    critical: number;
}

export interface ProviderMetrics {
    [key: string]: number;
}

export interface QualityMetrics {
    total_evaluations: number;
    avg_composite_score: number;
    max_composite_score: number;
    min_composite_score: number;
    quality_distribution: QualityDistribution;
    provider_distribution: ProviderMetrics;
    last_evaluation_at: string | null;
}

export interface QualityResponse {
    success: boolean;
    data: QualityMetrics;
    message?: string;
}

export interface KpiCard {
    title: string;
    value: string | number;
    icon: string;
    color: 'indigo' | 'emerald' | 'amber' | 'rose' | 'cyan';
    unit?: string;
    trend?: number;
    thresholdWarning?: number;
}
