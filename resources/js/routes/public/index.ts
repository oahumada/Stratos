import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see routes/web.php:95
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
* @see routes/web.php:95
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
* @see routes/web.php:95
* @route '/career/{tenant}'
*/
careers.get = (args: { tenant: string | number } | [tenant: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: careers.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:95
* @route '/career/{tenant}'
*/
careers.head = (args: { tenant: string | number } | [tenant: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: careers.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:95
* @route '/career/{tenant}'
*/
const careersForm = (args: { tenant: string | number } | [tenant: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: careers.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:95
* @route '/career/{tenant}'
*/
careersForm.get = (args: { tenant: string | number } | [tenant: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: careers.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:95
* @route '/career/{tenant}'
*/
careersForm.head = (args: { tenant: string | number } | [tenant: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: careers.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

careers.form = careersForm

const publicMethod = {
    careers: Object.assign(careers, careers),
}

export default publicMethod