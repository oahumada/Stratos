<?php

namespace App\Repository;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CatalogsRepository
{
    /**
     * Retorna los catálogos solicitados.
     * @param array $requested
     * @return array
     */
    public function getCatalogs(array $requested = [])
    {
        $catalogMap = [
            'roles' => fn() => \App\Models\Roles::select('id', 'name', 'department', 'level')->get(),
            'skills' => fn() => \App\Models\Skills::select('id', 'name', 'category')->get(),
            'departments' => fn() => \App\Models\Departments::select('id', 'name')->get(),
        ];

        $catalogos = [];

        $ttl = 86400; // 24 horas
        $keys = empty($requested) ? array_keys($catalogMap) : $requested;
        foreach ($keys as $key) {
            if (isset($catalogMap[$key])) {
                $cacheKey = 'catalogs:' . $key;
                $catalogos[$key] = Cache::remember($cacheKey, $ttl, function () use ($catalogMap, $key) {
                    return $catalogMap[$key]()->toArray();
                });
            }
        }
        return $catalogos;
    }
}