import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\CatalogsController::index
* @see app/Http/Controllers/Api/CatalogsController.php:11
* @route '/api/catalogs'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/catalogs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\CatalogsController::index
* @see app/Http/Controllers/Api/CatalogsController.php:11
* @route '/api/catalogs'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CatalogsController::index
* @see app/Http/Controllers/Api/CatalogsController.php:11
* @route '/api/catalogs'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CatalogsController::index
* @see app/Http/Controllers/Api/CatalogsController.php:11
* @route '/api/catalogs'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

const catalogs = {
    index: Object.assign(index, index),
}

export default catalogs