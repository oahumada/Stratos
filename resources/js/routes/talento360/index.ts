import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
import bars from './bars'
import qb from './qb'
/**
* @see routes/web.php:112
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
* @see routes/web.php:112
* @route '/talento360'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:112
* @route '/talento360'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:112
* @route '/talento360'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:116
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
* @see routes/web.php:116
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
* @see routes/web.php:116
* @route '/talento360/results/{id}'
*/
results.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: results.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:116
* @route '/talento360/results/{id}'
*/
results.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: results.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:120
* @route '/talento360/map'
*/
export const map = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: map.url(options),
    method: 'get',
})

map.definition = {
    methods: ["get","head"],
    url: '/talento360/map',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:120
* @route '/talento360/map'
*/
map.url = (options?: RouteQueryOptions) => {
    return map.definition.url + queryParams(options)
}

/**
* @see routes/web.php:120
* @route '/talento360/map'
*/
map.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: map.url(options),
    method: 'get',
})

/**
* @see routes/web.php:120
* @route '/talento360/map'
*/
map.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: map.url(options),
    method: 'head',
})

/**
* @see routes/web.php:124
* @route '/talento360/triangulation/{id}'
*/
export const triangulation = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: triangulation.url(args, options),
    method: 'get',
})

triangulation.definition = {
    methods: ["get","head"],
    url: '/talento360/triangulation/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:124
* @route '/talento360/triangulation/{id}'
*/
triangulation.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return triangulation.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:124
* @route '/talento360/triangulation/{id}'
*/
triangulation.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: triangulation.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:124
* @route '/talento360/triangulation/{id}'
*/
triangulation.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: triangulation.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:128
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
* @see routes/web.php:128
* @route '/talento360/relationships'
*/
relationships.url = (options?: RouteQueryOptions) => {
    return relationships.definition.url + queryParams(options)
}

/**
* @see routes/web.php:128
* @route '/talento360/relationships'
*/
relationships.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: relationships.url(options),
    method: 'get',
})

/**
* @see routes/web.php:128
* @route '/talento360/relationships'
*/
relationships.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: relationships.url(options),
    method: 'head',
})

/**
* @see routes/web.php:140
* @route '/talento360/comando'
*/
export const comando = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: comando.url(options),
    method: 'get',
})

comando.definition = {
    methods: ["get","head"],
    url: '/talento360/comando',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:140
* @route '/talento360/comando'
*/
comando.url = (options?: RouteQueryOptions) => {
    return comando.definition.url + queryParams(options)
}

/**
* @see routes/web.php:140
* @route '/talento360/comando'
*/
comando.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: comando.url(options),
    method: 'get',
})

/**
* @see routes/web.php:140
* @route '/talento360/comando'
*/
comando.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: comando.url(options),
    method: 'head',
})

const talento360 = {
    index: Object.assign(index, index),
    results: Object.assign(results, results),
    map: Object.assign(map, map),
    triangulation: Object.assign(triangulation, triangulation),
    relationships: Object.assign(relationships, relationships),
    bars: Object.assign(bars, bars),
    qb: Object.assign(qb, qb),
    comando: Object.assign(comando, comando),
}

export default talento360