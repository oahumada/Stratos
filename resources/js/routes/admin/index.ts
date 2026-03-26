import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:45
* @route '/admin/operations'
*/
export const operations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: operations.url(options),
    method: 'get',
})

operations.definition = {
    methods: ["get","head"],
    url: '/admin/operations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:45
* @route '/admin/operations'
*/
operations.url = (options?: RouteQueryOptions) => {
    return operations.definition.url + queryParams(options)
}

/**
* @see routes/web.php:45
* @route '/admin/operations'
*/
operations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: operations.url(options),
    method: 'get',
})

/**
* @see routes/web.php:45
* @route '/admin/operations'
*/
operations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: operations.url(options),
    method: 'head',
})

/**
* @see routes/web.php:45
* @route '/admin/operations'
*/
const operationsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: operations.url(options),
    method: 'get',
})

/**
* @see routes/web.php:45
* @route '/admin/operations'
*/
operationsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: operations.url(options),
    method: 'get',
})

/**
* @see routes/web.php:45
* @route '/admin/operations'
*/
operationsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: operations.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

operations.form = operationsForm

const admin = {
    operations: Object.assign(operations, operations),
}

export default admin