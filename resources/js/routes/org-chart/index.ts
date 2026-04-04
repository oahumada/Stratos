import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::tree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:24
* @route '/api/org-chart/people'
*/
export const tree = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tree.url(options),
    method: 'get',
})

tree.definition = {
    methods: ["get","head"],
    url: '/api/org-chart/people',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::tree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:24
* @route '/api/org-chart/people'
*/
tree.url = (options?: RouteQueryOptions) => {
    return tree.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::tree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:24
* @route '/api/org-chart/people'
*/
tree.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tree.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::tree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:24
* @route '/api/org-chart/people'
*/
tree.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tree.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::tree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:24
* @route '/api/org-chart/people'
*/
const treeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tree.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::tree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:24
* @route '/api/org-chart/people'
*/
treeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tree.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::tree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:24
* @route '/api/org-chart/people'
*/
treeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tree.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

tree.form = treeForm

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::stats
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:54
* @route '/api/org-chart/people/stats'
*/
export const stats = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})

stats.definition = {
    methods: ["get","head"],
    url: '/api/org-chart/people/stats',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::stats
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:54
* @route '/api/org-chart/people/stats'
*/
stats.url = (options?: RouteQueryOptions) => {
    return stats.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::stats
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:54
* @route '/api/org-chart/people/stats'
*/
stats.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::stats
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:54
* @route '/api/org-chart/people/stats'
*/
stats.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: stats.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::stats
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:54
* @route '/api/org-chart/people/stats'
*/
const statsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::stats
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:54
* @route '/api/org-chart/people/stats'
*/
statsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::stats
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:54
* @route '/api/org-chart/people/stats'
*/
statsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: stats.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

stats.form = statsForm

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::subtree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:39
* @route '/api/org-chart/people/{id}/subtree'
*/
export const subtree = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: subtree.url(args, options),
    method: 'get',
})

subtree.definition = {
    methods: ["get","head"],
    url: '/api/org-chart/people/{id}/subtree',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::subtree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:39
* @route '/api/org-chart/people/{id}/subtree'
*/
subtree.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return subtree.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::subtree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:39
* @route '/api/org-chart/people/{id}/subtree'
*/
subtree.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: subtree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::subtree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:39
* @route '/api/org-chart/people/{id}/subtree'
*/
subtree.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: subtree.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::subtree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:39
* @route '/api/org-chart/people/{id}/subtree'
*/
const subtreeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: subtree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::subtree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:39
* @route '/api/org-chart/people/{id}/subtree'
*/
subtreeForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: subtree.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\OrgPeopleChartController::subtree
* @see app/Http/Controllers/Api/OrgPeopleChartController.php:39
* @route '/api/org-chart/people/{id}/subtree'
*/
subtreeForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: subtree.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

subtree.form = subtreeForm

/**
* @see routes/web.php:361
* @route '/org-chart'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/org-chart',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:361
* @route '/org-chart'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:361
* @route '/org-chart'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:361
* @route '/org-chart'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:361
* @route '/org-chart'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:361
* @route '/org-chart'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:361
* @route '/org-chart'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

const orgChart = {
    tree: Object.assign(tree, tree),
    stats: Object.assign(stats, stats),
    subtree: Object.assign(subtree, subtree),
    index: Object.assign(index, index),
}

export default orgChart