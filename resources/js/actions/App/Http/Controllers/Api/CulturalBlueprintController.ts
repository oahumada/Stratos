import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\CulturalBlueprintController::show
* @see app/Http/Controllers/Api/CulturalBlueprintController.php:12
* @route '/api/organization/cultural-blueprint'
*/
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/organization/cultural-blueprint',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\CulturalBlueprintController::show
* @see app/Http/Controllers/Api/CulturalBlueprintController.php:12
* @route '/api/organization/cultural-blueprint'
*/
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CulturalBlueprintController::show
* @see app/Http/Controllers/Api/CulturalBlueprintController.php:12
* @route '/api/organization/cultural-blueprint'
*/
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\CulturalBlueprintController::show
* @see app/Http/Controllers/Api/CulturalBlueprintController.php:12
* @route '/api/organization/cultural-blueprint'
*/
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\CulturalBlueprintController::store
* @see app/Http/Controllers/Api/CulturalBlueprintController.php:32
* @route '/api/organization/cultural-blueprint'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/organization/cultural-blueprint',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CulturalBlueprintController::store
* @see app/Http/Controllers/Api/CulturalBlueprintController.php:32
* @route '/api/organization/cultural-blueprint'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CulturalBlueprintController::store
* @see app/Http/Controllers/Api/CulturalBlueprintController.php:32
* @route '/api/organization/cultural-blueprint'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CulturalBlueprintController::sign
* @see app/Http/Controllers/Api/CulturalBlueprintController.php:54
* @route '/api/organization/cultural-blueprint/sign'
*/
export const sign = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sign.url(options),
    method: 'post',
})

sign.definition = {
    methods: ["post"],
    url: '/api/organization/cultural-blueprint/sign',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CulturalBlueprintController::sign
* @see app/Http/Controllers/Api/CulturalBlueprintController.php:54
* @route '/api/organization/cultural-blueprint/sign'
*/
sign.url = (options?: RouteQueryOptions) => {
    return sign.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CulturalBlueprintController::sign
* @see app/Http/Controllers/Api/CulturalBlueprintController.php:54
* @route '/api/organization/cultural-blueprint/sign'
*/
sign.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sign.url(options),
    method: 'post',
})

const CulturalBlueprintController = { show, store, sign }

export default CulturalBlueprintController