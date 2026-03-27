import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see routes/web.php:92
* @route '/career/{tenant}'
*/
export const careers = (args: { tenant: string | number } | [tenant: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: careers.url(args, options),
    method: 'get',
})

careers.definition = {
    methods: ["get","head"],
    url: '/career/{tenant}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:92
* @route '/career/{tenant}'
*/
careers.url = (args: { tenant: string | number } | [tenant: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { tenant: args }
    }

    if (Array.isArray(args)) {
        args = {
            tenant: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        tenant: args.tenant,
    }

    return careers.definition.url
            .replace('{tenant}', parsedArgs.tenant.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:92
* @route '/career/{tenant}'
*/
careers.get = (args: { tenant: string | number } | [tenant: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: careers.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:92
* @route '/career/{tenant}'
*/
careers.head = (args: { tenant: string | number } | [tenant: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: careers.url(args, options),
    method: 'head',
})

const publicMethod = {
    careers: Object.assign(careers, careers),
}

export default publicMethod