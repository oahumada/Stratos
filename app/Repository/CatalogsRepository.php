<?php

namespace App\Repository;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CatalogsRepository
{
    /**
     * Retorna los catálogos solicitados.
     *
     * @return array
     */
    public function getCatalogs(array $requested = [])
    {
        Log::info('Requested catalogs: '.json_encode($requested));
        // Prepare closures with lean selects to reduce unnecessary data loading
        $barsClosure = fn () => \App\Models\CompetencyLevelBars::select('*')->get();

        $catalogMap = [
            'roles' => fn () => \App\Models\Roles::select('id', 'name', 'level')->get(),
            'skills' => fn () => \App\Models\Skill::select('id', 'name', 'category', 'is_critical')->get(),
            'departments' => fn () => \App\Models\Departments::select('id', 'name')->get(),
            'skill_levels' => $barsClosure,
            // Alias to same underlying source — we will reuse cached data if available
            'bars_levels' => $barsClosure,
            'people' => fn () => \App\Models\People::select('id', 'first_name', 'last_name')
                ->get()
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'name' => trim(($p->first_name ?? '') . ' ' . ($p->last_name ?? '')),
                    ];
                }),
            'agents' => fn () => \App\Models\Agent::select('id', 'name')->get(),
            'blueprints' => fn () => \App\Models\TalentBlueprint::select('id', 'role_name as name')->get(),
        ];

        $catalogos = [];

        $ttl = 86400; // 24 horas
        $keys = empty($requested) ? array_keys($catalogMap) : $requested;
        Log::info('Catalog keys to fetch: '.json_encode($keys));

        foreach ($keys as $key) {
            if (isset($catalogMap[$key])) {
                $cacheKey = 'catalogs:'.$key;

                // Special-case: avoid double-fetch when both 'skill_levels' and 'bars_levels' requested
                if ($key === 'bars_levels' && Cache::has('catalogs:skill_levels')) {
                    $catalogos[$key] = Cache::get('catalogs:skill_levels');
                    Log::info("Catalog '$key' loaded from alias cache 'catalogs:skill_levels'. Items: ".count($catalogos[$key]));
                    continue;
                }

                $catalogos[$key] = Cache::remember($cacheKey, $ttl, function () use ($catalogMap, $key) {
                    $data = $catalogMap[$key]()->toArray();
                    Log::info("Fetching catalog '$key', count: ".count($data), ['data' => array_slice($data, 0, 2)]);

                    return $data;
                });

                Log::info("Catalog '$key' loaded successfully. Items: ".count($catalogos[$key]));
            } else {
                Log::warning("Catalog '$key' not found in catalogMap");
            }
        }

        Log::info('Final fetched catalogs: '.json_encode(array_keys($catalogos)));
        foreach ($catalogos as $catalogName => $catalogData) {
            Log::info("Catalog '$catalogName' has ".count($catalogData).' items');
        }

        return $catalogos;
    }
}
