import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\MentorController::suggest
* @see app/Http/Controllers/Api/MentorController.php:26
* @route '/api/talent/mentors/suggest'
*/
export const suggest = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: suggest.url(options),
    method: 'get',
})

suggest.definition = {
    methods: ["get","head"],
    url: '/api/talent/mentors/suggest',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MentorController::suggest
* @see app/Http/Controllers/Api/MentorController.php:26
* @route '/api/talent/mentors/suggest'
*/
suggest.url = (options?: RouteQueryOptions) => {
    return suggest.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MentorController::suggest
* @see app/Http/Controllers/Api/MentorController.php:26
* @route '/api/talent/mentors/suggest'
*/
suggest.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: suggest.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MentorController::suggest
* @see app/Http/Controllers/Api/MentorController.php:26
* @route '/api/talent/mentors/suggest'
*/
suggest.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: suggest.url(options),
    method: 'head',
})

const MentorController = { suggest }

export default MentorController