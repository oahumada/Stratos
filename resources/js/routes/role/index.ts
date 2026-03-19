import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see routes/web.php:71
* @route '/approve/role/{token}'
*/
export const approval = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: approval.url(args, options),
    method: 'get',
})

approval.definition = {
    methods: ["get","head"],
    url: '/approve/role/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:71
* @route '/approve/role/{token}'
*/
approval.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return approval.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:71
* @route '/approve/role/{token}'
*/
approval.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: approval.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:71
* @route '/approve/role/{token}'
*/
approval.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: approval.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:71
* @route '/approve/role/{token}'
*/
const approvalForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: approval.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:71
* @route '/approve/role/{token}'
*/
approvalForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: approval.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:71
* @route '/approve/role/{token}'
*/
approvalForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: approval.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

approval.form = approvalForm

const role = {
    approval: Object.assign(approval, approval),
}

export default role