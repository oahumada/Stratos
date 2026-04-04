import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see routes/web.php:217
* @route '/lms/catalog/{courseId}'
*/
export const detail = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

detail.definition = {
    methods: ["get","head"],
    url: '/lms/catalog/{courseId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:217
* @route '/lms/catalog/{courseId}'
*/
detail.url = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { courseId: args }
    }

    if (Array.isArray(args)) {
        args = {
            courseId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        courseId: args.courseId,
    }

    return detail.definition.url
            .replace('{courseId}', parsedArgs.courseId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:217
* @route '/lms/catalog/{courseId}'
*/
detail.get = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: detail.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:217
* @route '/lms/catalog/{courseId}'
*/
detail.head = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: detail.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:217
* @route '/lms/catalog/{courseId}'
*/
const detailForm = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: detail.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:217
* @route '/lms/catalog/{courseId}'
*/
detailForm.get = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: detail.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:217
* @route '/lms/catalog/{courseId}'
*/
detailForm.head = (args: { courseId: string | number } | [courseId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: detail.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

detail.form = detailForm
