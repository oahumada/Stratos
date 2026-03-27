import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see routes/web.php:108
* @route '/dashboard/analytics'
*/
export const analytics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: analytics.url(options),
    method: 'get',
})

analytics.definition = {
    methods: ["get","head"],
    url: '/dashboard/analytics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:108
* @route '/dashboard/analytics'
*/
analytics.url = (options?: RouteQueryOptions) => {
    return analytics.definition.url + queryParams(options)
}

/**
* @see routes/web.php:108
* @route '/dashboard/analytics'
*/
analytics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: analytics.url(options),
    method: 'get',
})

/**
* @see routes/web.php:108
* @route '/dashboard/analytics'
*/
analytics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: analytics.url(options),
    method: 'head',
})

/**
* @see routes/web.php:112
* @route '/dashboard/investor'
*/
export const investor = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: investor.url(options),
    method: 'get',
})

investor.definition = {
    methods: ["get","head"],
    url: '/dashboard/investor',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:112
* @route '/dashboard/investor'
*/
investor.url = (options?: RouteQueryOptions) => {
    return investor.definition.url + queryParams(options)
}

/**
* @see routes/web.php:112
* @route '/dashboard/investor'
*/
investor.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: investor.url(options),
    method: 'get',
})

/**
* @see routes/web.php:112
* @route '/dashboard/investor'
*/
investor.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: investor.url(options),
    method: 'head',
})

const dashboard = {
    analytics: Object.assign(analytics, analytics),
    investor: Object.assign(investor, investor),
}

export default dashboard