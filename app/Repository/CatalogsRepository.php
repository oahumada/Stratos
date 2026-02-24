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
        $catalogMap = [
            'roles' => fn () => \App\Models\Roles::select('id', 'name', 'level')->get(),
            'skills' => fn () => \App\Models\Skill::select('id', 'name', 'category', 'is_critical')->get(),
            'departments' => fn () => \App\Models\Departments::select('id', 'name')->get(),
            'skill_levels' => fn () => \App\Models\CompetencyLevelBars::all(), // Updated to BARS
            'bars_levels' => fn () => \App\Models\CompetencyLevelBars::all(),
            'people' => fn () => \App\Models\People::all()->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->full_name,
                ];
            }),
        ];

        $catalogos = [];

        $ttl = 86400; // 24 horas
        $keys = empty($requested) ? array_keys($catalogMap) : $requested;
        Log::info('Catalog keys to fetch: '.json_encode($keys));

        foreach ($keys as $key) {
            if (isset($catalogMap[$key])) {
                $cacheKey = 'catalogs:'.$key;
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
