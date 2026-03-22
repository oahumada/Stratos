/**
 * Tests para useQualityMetrics composable
 */

import type { QualityMetrics } from '@/types/quality';
import { useQualityMetrics } from '@/composables/useQualityMetrics';
import axios from 'axios';
import { beforeEach, describe, expect, it, vi } from 'vitest';

vi.mock('axios');
const mockedAxios = vi.mocked(axios);

describe('useQualityMetrics composable', () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it('should initialize with default metrics', () => {
        const { metrics } = useQualityMetrics();

        expect(metrics.value).toEqual({
            total_evaluations: 0,
            avg_composite_score: 0,
            max_composite_score: 0,
            min_composite_score: 0,
            quality_distribution: {
                excellent: 0,
                good: 0,
                acceptable: 0,
                poor: 0,
                critical: 0,
            },
            provider_distribution: {},
            last_evaluation_at: null,
        });
    });

    it('should fetch metrics successfully', async () => {
        const mockData: QualityMetrics = {
            total_evaluations: 100,
            avg_composite_score: 0.87,
            max_composite_score: 0.95,
            min_composite_score: 0.62,
            quality_distribution: {
                excellent: 45,
                good: 40,
                acceptable: 10,
                poor: 4,
                critical: 1,
            },
            provider_distribution: {
                deepseek: 60,
                openai: 30,
                mock: 10,
            },
            last_evaluation_at: new Date().toISOString(),
        };

        mockedAxios.get.mockResolvedValue({
            data: {
                success: true,
                data: mockData,
            },
        });

        const { metrics, fetchMetrics, isLoading } = useQualityMetrics();

        expect(isLoading.value).toBe(false);

        await fetchMetrics();

        expect(metrics.value).toEqual(mockData);
        expect(mockedAxios.get).toHaveBeenCalledWith(
            '/api/qa/llm-evaluations/metrics/summary',
        );
    });

    it('should handle fetch errors gracefully', async () => {
        const errorMessage = 'Network error';
        mockedAxios.get.mockRejectedValue(new Error(errorMessage));

        const { error, fetchMetrics } = useQualityMetrics();

        await fetchMetrics();

        expect(error.value).toBe(errorMessage);
    });

    it('should calculate hallucination percentage correctly', async () => {
        const mockData: QualityMetrics = {
            total_evaluations: 100,
            avg_composite_score: 0.87,
            max_composite_score: 0.95,
            min_composite_score: 0.62,
            quality_distribution: {
                excellent: 50,
                good: 40,
                acceptable: 5,
                poor: 4,
                critical: 1,
            },
            provider_distribution: {},
            last_evaluation_at: null,
        };

        mockedAxios.get.mockResolvedValue({
            data: { success: true, data: mockData },
        });

        const { fetchMetrics, hallucination } = useQualityMetrics();

        await fetchMetrics();

        // hallucination should calculate (poor + critical) / total
        const expected = ((4 + 1) / 100).toFixed(1);
        expect(hallucination.value).toBe(expected);
    });

    it('should support provider filtering on fetch', async () => {
        const mockData: QualityMetrics = {
            total_evaluations: 60,
            avg_composite_score: 0.88,
            max_composite_score: 0.95,
            min_composite_score: 0.70,
            quality_distribution: {
                excellent: 40,
                good: 15,
                acceptable: 5,
                poor: 0,
                critical: 0,
            },
            provider_distribution: {
                deepseek: 60,
            },
            last_evaluation_at: null,
        };

        mockedAxios.get.mockResolvedValue({
            data: { success: true, data: mockData },
        });

        const { metrics, fetchMetrics } = useQualityMetrics();

        await fetchMetrics('deepseek');

        expect(mockedAxios.get).toHaveBeenCalledWith(
            '/api/qa/llm-evaluations/metrics/summary?provider=deepseek',
        );
        expect(metrics.value.total_evaluations).toBe(60);
    });

    it('should track quality passed/failed counts', async () => {
        const mockData: QualityMetrics = {
            total_evaluations: 100,
            avg_composite_score: 0.87,
            max_composite_score: 0.95,
            min_composite_score: 0.62,
            quality_distribution: {
                excellent: 50,
                good: 30,
                acceptable: 10,
                poor: 8,
                critical: 2,
            },
            provider_distribution: {},
            last_evaluation_at: null,
        };

        mockedAxios.get.mockResolvedValue({
            data: { success: true, data: mockData },
        });

        const { fetchMetrics, qualityPassed, qualityFailed } =
            useQualityMetrics();

        await fetchMetrics();

        expect(qualityPassed.value).toBe(80); // excellent + good
        expect(qualityFailed.value).toBe(10); // poor + critical
    });

    it('should identify top provider correctly', async () => {
        const mockData: QualityMetrics = {
            total_evaluations: 100,
            avg_composite_score: 0.87,
            max_composite_score: 0.95,
            min_composite_score: 0.62,
            quality_distribution: {
                excellent: 40,
                good: 40,
                acceptable: 10,
                poor: 8,
                critical: 2,
            },
            provider_distribution: {
                deepseek: 50,
                openai: 35,
                mock: 15,
            },
            last_evaluation_at: null,
        };

        mockedAxios.get.mockResolvedValue({
            data: { success: true, data: mockData },
        });

        const { fetchMetrics, topProvider } = useQualityMetrics();

        await fetchMetrics();

        expect(topProvider.value).toBe('deepseek');
    });
});
