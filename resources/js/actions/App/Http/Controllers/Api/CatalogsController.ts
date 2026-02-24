import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\CatalogsController::getCatalogs
* @see app/Http/Controllers/Api/CatalogsController.php:0
* @route '/api/catalogs'
*/
export const getCatalogs = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCatalogs.url(options),
    method: 'get',
})

getCatalogs.definition = {
    methods: ["get","head"],
    url: '/api/catalogs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\CatalogsController::getCatalogs
* @see app/Http/Controllers/Api/CatalogsController.php:0
* @route '/api/catalogs'
*/
getCatalogs.url = (options?: RouteQueryOptions) => {
    return getCatalogs.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CatalogsController::getCatalogs
* @see app/Http/Controllers/Api/CatalogsController.php:0
* @route '/api/catalogs'
*/
getCatalogs.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCatalogs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CatalogsController::getCatalogs
* @see app/Http/Controllers/Api/CatalogsController.php:0
* @route '/api/catalogs'
*/
getCatalogs.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCatalogs.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\CatalogsController::getCatalogs
* @see app/Http/Controllers/Api/CatalogsController.php:0
* @route '/api/catalogs'
*/
const getCatalogsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCatalogs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CatalogsController::getCatalogs
* @see app/Http/Controllers/Api/CatalogsController.php:0
* @route '/api/catalogs'
*/
getCatalogsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCatalogs.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CatalogsController::getCatalogs
* @see app/Http/Controllers/Api/CatalogsController.php:0
* @route '/api/catalogs'
*/
getCatalogsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCatalogs.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getCatalogs.form = getCatalogsForm

const CatalogsController = { getCatalogs }

export default CatalogsController