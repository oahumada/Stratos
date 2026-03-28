import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:24
* @route '/growth'
*/
export const landing = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: landing.url(options),
    method: 'get',
})

landing.definition = {
    methods: ["get","head"],
    url: '/growth',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:24
* @route '/growth'
*/
landing.url = (options?: RouteQueryOptions) => {
    return landing.definition.url + queryParams(options)
}

/**
* @see routes/web.php:24
* @route '/growth'
*/
landing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:24
* @route '/growth'
*/
landing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: landing.url(options),
    method: 'head',
})

/**
* @see routes/web.php:24
* @route '/growth'
*/
const landingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:24
* @route '/growth'
*/
landingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:24
* @route '/growth'
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

const growth = {
    landing: Object.assign(landing, landing),
}

export default growth