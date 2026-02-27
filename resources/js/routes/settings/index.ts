import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:132
* @route '/settings/rbac'
*/
export const rbac = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: rbac.url(options),
    method: 'get',
})

rbac.definition = {
    methods: ["get","head"],
    url: '/settings/rbac',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:132
* @route '/settings/rbac'
*/
rbac.url = (options?: RouteQueryOptions) => {
    return rbac.definition.url + queryParams(options)
}

/**
* @see routes/web.php:132
* @route '/settings/rbac'
*/
rbac.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: rbac.url(options),
    method: 'get',
})

/**
* @see routes/web.php:132
* @route '/settings/rbac'
*/
rbac.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: rbac.url(options),
    method: 'head',
})

/**
* @see routes/web.php:132
* @route '/settings/rbac'
*/
const rbacForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: rbac.url(options),
    method: 'get',
})

/**
* @see routes/web.php:132
* @route '/settings/rbac'
*/
rbacForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: rbac.url(options),
    method: 'get',
})

/**
* @see routes/web.php:132
* @route '/settings/rbac'
*/
rbacForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: rbac.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

rbac.form = rbacForm

const settings = {
    rbac: Object.assign(rbac, rbac),
}

export default settings