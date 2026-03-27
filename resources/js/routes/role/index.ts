import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\RoleDesignerController::approval
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
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
* @see \App\Http\Controllers\Api\RoleDesignerController::approval
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
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
* @see \App\Http\Controllers\Api\RoleDesignerController::approval
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
* @route '/approve/role/{token}'
*/
approval.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: approval.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RoleDesignerController::approval
* @see app/Http/Controllers/Api/RoleDesignerController.php:88
* @route '/approve/role/{token}'
*/
approval.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: approval.url(args, options),
    method: 'head',
})

const role = {
    approval: Object.assign(approval, approval),
}

export default role