import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:116
* @route '/people-experience'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/people-experience',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:116
* @route '/people-experience'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:116
* @route '/people-experience'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:116
* @route '/people-experience'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:116
* @route '/people-experience'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:116
* @route '/people-experience'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:116
* @route '/people-experience'
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
* @see routes/web.php:120
* @route '/people-experience/comando'
*/
export const comando = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: comando.url(options),
    method: 'get',
})

comando.definition = {
    methods: ["get","head"],
    url: '/people-experience/comando',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:120
* @route '/people-experience/comando'
*/
comando.url = (options?: RouteQueryOptions) => {
    return comando.definition.url + queryParams(options)
}

/**
* @see routes/web.php:120
* @route '/people-experience/comando'
*/
comando.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: comando.url(options),
    method: 'get',
})

/**
* @see routes/web.php:120
* @route '/people-experience/comando'
*/
comando.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: comando.url(options),
    method: 'head',
})

/**
* @see routes/web.php:120
* @route '/people-experience/comando'
*/
const comandoForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: comando.url(options),
    method: 'get',
})

/**
* @see routes/web.php:120
* @route '/people-experience/comando'
*/
comandoForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: comando.url(options),
    method: 'get',
})

/**
* @see routes/web.php:120
* @route '/people-experience/comando'
*/
comandoForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: comando.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

comando.form = comandoForm

const peopleExperience = {
    index: Object.assign(index, index),
    comando: Object.assign(comando, comando),
}

export default peopleExperience