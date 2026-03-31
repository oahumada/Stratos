import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:15
* @route '/core'
*/
export const landing = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: landing.url(options),
    method: 'get',
})

landing.definition = {
    methods: ["get","head"],
    url: '/core',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:15
* @route '/core'
*/
landing.url = (options?: RouteQueryOptions) => {
    return landing.definition.url + queryParams(options)
}

/**
* @see routes/web.php:15
* @route '/core'
*/
landing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:15
* @route '/core'
*/
landing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: landing.url(options),
    method: 'head',
})

/**
* @see routes/web.php:15
* @route '/core'
*/
const landingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:15
* @route '/core'
*/
landingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:15
* @route '/core'
*/
landingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

landing.form = landingForm

const core = {
    landing: Object.assign(landing, landing),
}

export default core