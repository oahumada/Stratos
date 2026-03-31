import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Neo4jSyncController::sync
* @see app/Http/Controllers/Neo4jSyncController.php:11
* @route '/api/neo4j/sync'
*/
export const sync = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sync.url(options),
    method: 'post',
})

sync.definition = {
    methods: ["post"],
    url: '/api/neo4j/sync',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Neo4jSyncController::sync
* @see app/Http/Controllers/Neo4jSyncController.php:11
* @route '/api/neo4j/sync'
*/
sync.url = (options?: RouteQueryOptions) => {
    return sync.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Neo4jSyncController::sync
* @see app/Http/Controllers/Neo4jSyncController.php:11
* @route '/api/neo4j/sync'
*/
sync.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sync.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Neo4jSyncController::sync
* @see app/Http/Controllers/Neo4jSyncController.php:11
* @route '/api/neo4j/sync'
*/
const syncForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sync.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Neo4jSyncController::sync
* @see app/Http/Controllers/Neo4jSyncController.php:11
* @route '/api/neo4j/sync'
*/
syncForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sync.url(options),
    method: 'post',
})

sync.form = syncForm

const Neo4jSyncController = { sync }

export default Neo4jSyncController