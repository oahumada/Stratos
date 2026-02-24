import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
import bars from './bars'
import qb from './qb'
/**
* @see routes/web.php:91
* @route '/talento360'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/talento360',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:91
* @route '/talento360'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:91
* @route '/talento360'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:91
* @route '/talento360'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:91
* @route '/talento360'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:91
* @route '/talento360'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:91
* @route '/talento360'
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

/**
* @see routes/web.php:95
* @route '/talento360/results/{id}'
*/
export const results = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

results.definition = {
    methods: ["get","head"],
    url: '/talento360/results/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:95
* @route '/talento360/results/{id}'
*/
results.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return results.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:95
* @route '/talento360/results/{id}'
*/
results.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:95
* @route '/talento360/results/{id}'
*/
results.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:95
* @route '/talento360/results/{id}'
*/
const resultsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: results.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:95
* @route '/talento360/results/{id}'
*/
resultsForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: results.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:95
* @route '/talento360/results/{id}'
*/
resultsForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: results.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

results.form = resultsForm

/**
* @see routes/web.php:99
* @route '/talento360/relationships'
*/
export const relationships = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: relationships.url(options),
    method: 'get',
})

relationships.definition = {
    methods: ["get","head"],
    url: '/talento360/relationships',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:99
* @route '/talento360/relationships'
*/
relationships.url = (options?: RouteQueryOptions) => {
    return relationships.definition.url + queryParams(options)
}

/**
* @see routes/web.php:99
* @route '/talento360/relationships'
*/
relationships.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: relationships.url(options),
    method: 'get',
})

/**
* @see routes/web.php:99
* @route '/talento360/relationships'
*/
relationships.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: relationships.url(options),
    method: 'head',
})

/**
* @see routes/web.php:99
* @route '/talento360/relationships'
*/
const relationshipsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: relationships.url(options),
    method: 'get',
})

/**
* @see routes/web.php:99
* @route '/talento360/relationships'
*/
relationshipsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: relationships.url(options),
    method: 'get',
})

/**
* @see routes/web.php:99
* @route '/talento360/relationships'
*/
relationshipsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: relationships.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

relationships.form = relationshipsForm

/**
* @see routes/web.php:111
* @route '/talento360/command-center'
*/
export const commandCenter = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: commandCenter.url(options),
    method: 'get',
})

commandCenter.definition = {
    methods: ["get","head"],
    url: '/talento360/command-center',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:111
* @route '/talento360/command-center'
*/
commandCenter.url = (options?: RouteQueryOptions) => {
    return commandCenter.definition.url + queryParams(options)
}

/**
* @see routes/web.php:111
* @route '/talento360/command-center'
*/
commandCenter.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: commandCenter.url(options),
    method: 'get',
})

/**
* @see routes/web.php:111
* @route '/talento360/command-center'
*/
commandCenter.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: commandCenter.url(options),
    method: 'head',
})

/**
* @see routes/web.php:111
* @route '/talento360/command-center'
*/
const commandCenterForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: commandCenter.url(options),
    method: 'get',
})

/**
* @see routes/web.php:111
* @route '/talento360/command-center'
*/
commandCenterForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: commandCenter.url(options),
    method: 'get',
})

/**
* @see routes/web.php:111
* @route '/talento360/command-center'
*/
commandCenterForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: commandCenter.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

commandCenter.form = commandCenterForm

const talento360 = {
    index: Object.assign(index, index),
    results: Object.assign(results, results),
    relationships: Object.assign(relationships, relationships),
    bars: Object.assign(bars, bars),
    qb: Object.assign(qb, qb),
    commandCenter: Object.assign(commandCenter, commandCenter),
}

export default talento360