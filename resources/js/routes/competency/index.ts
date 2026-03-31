import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\RoleDesignerController::approval
 * @see app/Http/Controllers/Api/RoleDesignerController.php:88
 * @route '/approve/competency/{token}'
 */
export const approval = (
    args:
        | { token: string | number }
        | [token: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: approval.url(args, options),
    method: 'get',
});

approval.definition = {
    methods: ['get', 'head'],
    url: '/approve/competency/{token}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\RoleDesignerController::approval
 * @see app/Http/Controllers/Api/RoleDesignerController.php:88
 * @route '/approve/competency/{token}'
 */
approval.url = (
    args:
        | { token: string | number }
        | [token: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args };
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        token: args.token,
    };

    return (
        approval.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\RoleDesignerController::approval
 * @see app/Http/Controllers/Api/RoleDesignerController.php:88
 * @route '/approve/competency/{token}'
 */
approval.get = (
    args:
        | { token: string | number }
        | [token: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: approval.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RoleDesignerController::approval
 * @see app/Http/Controllers/Api/RoleDesignerController.php:88
 * @route '/approve/competency/{token}'
 */
approval.head = (
    args:
        | { token: string | number }
        | [token: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: approval.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\RoleDesignerController::approval
 * @see app/Http/Controllers/Api/RoleDesignerController.php:88
 * @route '/approve/competency/{token}'
 */
const approvalForm = (
    args:
        | { token: string | number }
        | [token: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: approval.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RoleDesignerController::approval
 * @see app/Http/Controllers/Api/RoleDesignerController.php:88
 * @route '/approve/competency/{token}'
 */
approvalForm.get = (
    args:
        | { token: string | number }
        | [token: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: approval.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RoleDesignerController::approval
 * @see app/Http/Controllers/Api/RoleDesignerController.php:88
 * @route '/approve/competency/{token}'
 */
approvalForm.head = (
    args:
        | { token: string | number }
        | [token: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: approval.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

approval.form = approvalForm;

const competency = {
    approval: Object.assign(approval, approval),
};

export default competency;
