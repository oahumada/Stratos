import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see routes/web.php:172
* @route '/quality-hub'
*/
export const hub = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hub.url(options),
    method: 'get',
})

hub.definition = {
    methods: ["get","head"],
    url: '/quality-hub',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:172
* @route '/quality-hub'
*/
hub.url = (options?: RouteQueryOptions) => {
    return hub.definition.url + queryParams(options)
}

/**
* @see routes/web.php:172
* @route '/quality-hub'
*/
hub.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: hub.url(options),
    method: 'get',
})

/**
* @see routes/web.php:172
* @route '/quality-hub'
*/
hub.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: hub.url(options),
    method: 'head',
})

/**
* @see routes/web.php:176
* @route '/quality/ragas-metrics'
*/
export const ragasMetrics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ragasMetrics.url(options),
    method: 'get',
})

ragasMetrics.definition = {
    methods: ["get","head"],
    url: '/quality/ragas-metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:176
* @route '/quality/ragas-metrics'
*/
ragasMetrics.url = (options?: RouteQueryOptions) => {
    return ragasMetrics.definition.url + queryParams(options)
}

/**
* @see routes/web.php:176
* @route '/quality/ragas-metrics'
*/
ragasMetrics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ragasMetrics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:176
* @route '/quality/ragas-metrics'
*/
ragasMetrics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ragasMetrics.url(options),
    method: 'head',
})

const quality = {
    hub: Object.assign(hub, hub),
    ragasMetrics: Object.assign(ragasMetrics, ragasMetrics),
}

export default quality