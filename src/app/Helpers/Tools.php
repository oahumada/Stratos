<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use \Illuminate\Database\QueryException;
use \Illuminate\Support\Collection;

class Tools
{
    /**
     * Convención para filtros en searchQuery:
     * - Usar null para indicar que NO se debe filtrar por ese campo.
     * - Usar '' (string vacío) solo si explícitamente se desea buscar por valores vacíos.
     * Esto permite que el backend ignore filtros nulos y solo aplique los relevantes, evitando consultas innecesarias como WHERE campo IS NULL.
     *
     * Filters data based on provided criteria.
     *
     * Esta función aplica filtros a una consulta de base de datos con soporte especial para campos booleanos.
     * Maneja diferentes formatos de valores booleanos para garantizar compatibilidad entre bases de datos.
     *
     * Ejemplos de uso de filtros booleanos:
     * - ['activo' => true]     // Booleano PHP
     * - ['activo' => 'true']   // Cadena 'true'
     * - ['activo' => 1]        // Entero 1
     * - ['activo' => '1']      // Cadena '1'
     *
     * @param array $filters Un array asociativo de filtros donde las claves son nombres de campos y los valores son criterios de filtrado.
     * @param object $query El objeto de consulta al que se aplicarán los filtros.
     *
     * @return mixed Devuelve los resultados de la consulta filtrada como una colección.
     * @throws QueryException Si ocurre un error durante el filtrado.
     */
    public static function filterData(array $filters, object $query)
    {
        // Relaciones solicitadas (compat con "catalogs")
        $with = $filters['with'] ?? ($filters['catalogs'] ?? []);
        unset($filters['with'], $filters['catalogs']);

        // Paginacion y ordenamiento opcional
        $perPage = $filters['per_page'] ?? null;
        $page = $filters['page'] ?? 1;
        $orderBy = $filters['order_by'] ?? null;
        $orderDir = strtolower($filters['order_dir'] ?? 'asc') === 'desc' ? 'desc' : 'asc';
        unset($filters['per_page'], $filters['page'], $filters['order_by'], $filters['order_dir']);

        // Limpia filtros vacios y normaliza booleanos
        $filters = self::cleanFilters($filters);

        // Cargar relaciones validas declaradas
        if (!empty($with) && method_exists($query, 'getModel')) {
            $model = $query->getModel();
            $validRelations = array_filter((array) $with, fn($rel) => method_exists($model, $rel));
            if (!empty($validRelations)) {
                $query->with(array_unique($validRelations));
            }
        }

        try {
            foreach ($filters as $field => $criteria) {
                if (strpos($field, 'fecha') === 0) {
                    self::applyDateFilter($query, $field, $criteria);
                    continue;
                }

                if ($field === 'programa_seguimiento') {
                    // 0: sin GES, 1: con GES
                    $query->where(function ($q) use ($criteria) {
                        if ((int) $criteria === 0) {
                            $q->whereNull('ges_id')->orWhere('ges_id', 0);
                        } elseif ((int) $criteria === 1) {
                            $q->where('ges_id', '>', 0);
                        }
                    });
                    continue;
                }

                if (is_bool($criteria)) {
                    $query->where($field, $criteria);
                    continue;
                }

                // Normaliza booleanos representados como string/int
                $bool = self::normalizeBoolean($criteria);
                if ($bool !== null) {
                    $query->where($field, $bool);
                    continue;
                }

                $query->where($field, $criteria);
            }

            if ($orderBy) {
                $query->orderBy($orderBy, $orderDir);
            }

            if ($perPage !== null) {
                $perPage = min(max((int) $perPage, 1), 100);
                return $query->paginate($perPage, ['*'], 'page', (int) $page ?: 1);
            }

            return $query->get();
        } catch (QueryException $exception) {
            return 'Error filtering data: ' . $exception->getMessage();
        }
    }

    /**
     * Limpia filtros vacios y normaliza booleanos en arrays.
     */
    private static function cleanFilters(array $filters): array
    {
        return array_filter($filters, function ($v) {
            if (is_array($v)) {
                $nonEmpty = array_filter($v, function ($item) {
                    return $item !== null && $item !== '';
                });
                return !empty($nonEmpty);
            }
            return $v !== null && $v !== '';
        });
    }

    /**
     * Convierte representaciones comunes de boolean a bool; null si no aplica.
     */
    private static function normalizeBoolean($value): ?bool
    {
        $mapTrue = ['true', '1', 1, true, 'on', 'yes'];
        $mapFalse = ['false', '0', 0, false, 'off', 'no'];

        if (in_array($value, $mapTrue, true)) {
            return true;
        }
        if (in_array($value, $mapFalse, true)) {
            return false;
        }

        return null;
    }
    /**
     * Applies a date filter to the given query.
     *
     * This function filters the query based on a date range specified in the $dates array.
     * It expects 'desde' (from) and 'hasta' (to) keys in the $dates array to define the range.
     *
     * @param object $query The query object to apply the filter to.
     * @param string $field The name of the date field in the database to filter on.
     * @param array $dates An associative array containing 'desde' and 'hasta' keys with corresponding date values.
     *
     * @return mixed Returns the modified query object if successful, a string error message if dates are missing,
     *               or an error message if there's an issue with the date format.
     */
    private static function applyDateFilter(object $query, $field, $dates)
    {
        try {
            if (isset($dates['desde']) && isset($dates['hasta'])) {
                return $query->whereBetween($field, [$dates['desde'], $dates['hasta']]);
            } else {
                return "Falta una fecha";
            }
        } catch (QueryException $e) {
            return 'Formato de fecha incorrecto ';
        }
    }
    /**
     * Applies a full-text search filter for the 'exposicion' field.
     *
     * This function performs a full-text search on the specified field using the provided criteria.
     * It applies both exact phrase matching and individual term matching for each search term.
     *
     * @param object $query The query object to apply the filter to.
     * @param string $field The name of the field in the database to apply the full-text search on.
     * @param array $criteria An array of search terms to be used in the full-text search.
     *
     * @return mixed Returns the modified query object if successful, or a string error message if an exception occurs.
     */
    private static function applyExposicionFilter(object $query, $field, $criteria)
    {
        try {
            foreach ($criteria as $term) {
                $query->whereFullText($field, '"' . $term . '\"');
                return $query->orWhereFullText($field, $term);
            }
        } catch (QueryException $e) {
            return 'Error en la búsqueda full-text: ' . $e->getMessage();
        }
    }

    /**
     * Auto-detecta relaciones para eager loading basándose en campos _id
     * 
     * @param array $filters Filtros aplicados
     * @param object $query Query object para obtener el modelo
     * @return array Array de relaciones para cargar con with()
     */
    // private static function autoDetectRelations(array $filters, object $query): array
    // {
    //     $relations = [];

    //     try {
    //         // Obtener el modelo desde el query
    //         $model = $query->getModel();

    //         // Detectar campos _id en los filtros y mapear a relaciones
    //         foreach ($filters as $field => $value) {
    //             if (str_ends_with($field, '_id')) {
    //                 // Convertir campo_id a relación (ej: empresa_id -> empresa)
    //                 $relationName = str_replace('_id', '', $field);

    //                 // Verificar si la relación existe en el modelo
    //                 if (method_exists($model, $relationName)) {
    //                     $relations[] = $relationName;
    //                 }
    //             }
    //         }

    //         // También detectar campos _id comunes en el modelo para eager loading automático
    //         $commonIdFields = ['empresa_id', 'area_id', 'ceco_id', 'estado_examen_id', 'tipo_atencion_id', 'derivacion_id'];

    //         foreach ($commonIdFields as $idField) {
    //             $relationName = str_replace('_id', '', $idField);
    //             if (method_exists($model, $relationName)) {
    //                 $relations[] = $relationName;
    //             }
    //         }

    //     } catch (\Exception $e) {
    //         // Si hay error, no cargar relaciones automáticas
    //         Log::warning('Error auto-detecting relations: ' . $e->getMessage());
    //     }

    //     return array_unique($relations);
    // }
}
