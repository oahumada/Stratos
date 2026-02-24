import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
import bars from './bars'
import qb from './qb'
/**
* @see routes/web.php:91
* @route '/talento360'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/talento360',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:91
* @route '/talento360'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:91
* @route '/talento360'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:91
* @route '/talento360'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:91
* @route '/talento360'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:91
* @route '/talento360'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:91
* @route '/talento360'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see routes/web.php:103
* @route '/talento360/command-center'
*/
export const commandCenter = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: commandCenter.url(options),
    method: 'get',
})

commandCenter.definition = {
    methods: ["get","head"],
    url: '/talento360/command-center',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:103
* @route '/talento360/command-center'
*/
commandCenter.url = (options?: RouteQueryOptions) => {
    return commandCenter.definition.url + queryParams(options)
}

/**
* @see routes/web.php:103
* @route '/talento360/command-center'
*/
commandCenter.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: commandCenter.url(options),
    method: 'get',
})

/**
* @see routes/web.php:103
* @route '/talento360/command-center'
*/
commandCenter.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: commandCenter.url(options),
    method: 'head',
})

/**
* @see routes/web.php:103
* @route '/talento360/command-center'
*/
const commandCenterForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: commandCenter.url(options),
    method: 'get',
})

/**
* @see routes/web.php:103
* @route '/talento360/command-center'
*/
commandCenterForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: commandCenter.url(options),
    method: 'get',
})

/**
* @see routes/web.php:103
* @route '/talento360/command-center'
*/
commandCenterForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: commandCenter.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

commandCenter.form = commandCenterForm

const talento360 = {
    index: Object.assign(index, index),
    bars: Object.assign(bars, bars),
    qb: Object.assign(qb, qb),
    commandCenter: Object.assign(commandCenter, commandCenter),
}

export default talento360