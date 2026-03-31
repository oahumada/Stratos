import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see routes/api.php:53
* @route '/api/approvals/{token}/reject'
*/
export const reject = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

reject.definition = {
    methods: ["post"],
    url: '/api/approvals/{token}/reject',
} satisfies RouteDefinition<["post"]>

/**
* @see routes/api.php:53
* @route '/api/approvals/{token}/reject'
*/
reject.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return reject.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/api.php:53
* @route '/api/approvals/{token}/reject'
*/
reject.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reject.url(args, options),
    method: 'post',
})

/**
* @see routes/api.php:53
* @route '/api/approvals/{token}/reject'
*/
const rejectForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

/**
* @see routes/api.php:53
* @route '/api/approvals/{token}/reject'
*/
rejectForm.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reject.url(args, options),
    method: 'post',
})

reject.form = rejectForm

const approvals = {
    reject: Object.assign(reject, reject),
}

export default approvals