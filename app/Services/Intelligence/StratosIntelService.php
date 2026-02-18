<?php

namespace App\Services\Intelligence;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StratosIntelService
{
    protected string $baseUrl;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.python_intel.base_url');
        $this->timeout = config('services.python_intel.timeout', 30);
    }

    /**
     * Send gap data to the Python Intelligence Service for strategy recommendation.
     *
     * @param array $gapData The structured data as per DataContract_GapAnalysis.md
     * @return array|null The strategy recommendation or null on failure
     */
    public function analyzeGap(array $gapData): ?array
    {
        try {
            Log::info('Sending gap analysis request to Python service', ['role_id' => $gapData['role_context']['role_id'] ?? 'unknown']);

            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/analyze-gap", [
                    'gap_data' => $gapData
                ]);

            if ($response->successful()) {
                Log::info('Received strategy recommendation from Python service');
                return $response->json();
            }

            Log::error('Failed to get strategy from Python service', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception calling Python service (analyzeGap)', ['message' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate a scenario using the Python Intelligence Service (DeepSeek/GPT).
     */
    public function generateScenario(string $companyName, string $instruction, string $language = 'es', ?array $marketContext = null): ?array
    {
        try {
            Log::info('Sending scenario generation request to Python service', ['company' => $companyName]);

            $response = Http::timeout(120) // Scenarios take longer
                ->post("{$this->baseUrl}/generate-scenario", [
                    'company_name' => $companyName,
                    'instruction' => $instruction,
                    'language' => $language,
                    'market_context' => $marketContext
                ]);

            if ($response->successful()) {
                Log::info('Received scenario generation from Python service');
                return $response->json();
            }

            Log::error('Failed to generate scenario from Python service', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception calling Python service (generateScenario)', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
