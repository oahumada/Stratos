import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\BulkPeopleImportController::analyze
* @see app/Http/Controllers/Api/BulkPeopleImportController.php:22
* @route '/api/talent/bulk-import/analyze'
*/
export const analyze = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyze.url(options),
    method: 'post',
})

analyze.definition = {
    methods: ["post"],
    url: '/api/talent/bulk-import/analyze',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\BulkPeopleImportController::analyze
* @see app/Http/Controllers/Api/BulkPeopleImportController.php:22
* @route '/api/talent/bulk-import/analyze'
*/
analyze.url = (options?: RouteQueryOptions) => {
    return analyze.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BulkPeopleImportController::analyze
* @see app/Http/Controllers/Api/BulkPeopleImportController.php:22
* @route '/api/talent/bulk-import/analyze'
*/
analyze.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyze.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BulkPeopleImportController::stage
* @see app/Http/Controllers/Api/BulkPeopleImportController.php:176
* @route '/api/talent/bulk-import/stage'
*/
export const stage = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stage.url(options),
    method: 'post',
})

stage.definition = {
    methods: ["post"],
    url: '/api/talent/bulk-import/stage',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\BulkPeopleImportController::stage
* @see app/Http/Controllers/Api/BulkPeopleImportController.php:176
* @route '/api/talent/bulk-import/stage'
*/
stage.url = (options?: RouteQueryOptions) => {
    return stage.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BulkPeopleImportController::stage
* @see app/Http/Controllers/Api/BulkPeopleImportController.php:176
* @route '/api/talent/bulk-import/stage'
*/
stage.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stage.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\BulkPeopleImportController::approveAndCommit
* @see app/Http/Controllers/Api/BulkPeopleImportController.php:207
* @route '/api/talent/bulk-import/{id}/approve'
*/
export const approveAndCommit = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approveAndCommit.url(args, options),
    method: 'post',
})

approveAndCommit.definition = {
    methods: ["post"],
    url: '/api/talent/bulk-import/{id}/approve',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\BulkPeopleImportController::approveAndCommit
* @see app/Http/Controllers/Api/BulkPeopleImportController.php:207
* @route '/api/talent/bulk-import/{id}/approve'
*/
approveAndCommit.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return approveAndCommit.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\BulkPeopleImportController::approveAndCommit
* @see app/Http/Controllers/Api/BulkPeopleImportController.php:207
* @route '/api/talent/bulk-import/{id}/approve'
*/
approveAndCommit.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: approveAndCommit.url(args, options),
    method: 'post',
})

const BulkPeopleImportController = { analyze, stage, approveAndCommit }

export default BulkPeopleImportController