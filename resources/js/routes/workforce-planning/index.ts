import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:337
* @route '/workforce-planning'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/workforce-planning',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:337
* @route '/workforce-planning'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:337
* @route '/workforce-planning'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:337
* @route '/workforce-planning'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:337
* @route '/workforce-planning'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:337
* @route '/workforce-planning'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:337
* @route '/workforce-planning'
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
* @see routes/web.php:341
* @route '/workforce-planning/recomendaciones'
*/
export const recomendaciones = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recomendaciones.url(options),
    method: 'get',
})

recomendaciones.definition = {
    methods: ["get","head"],
    url: '/workforce-planning/recomendaciones',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:341
* @route '/workforce-planning/recomendaciones'
*/
recomendaciones.url = (options?: RouteQueryOptions) => {
    return recomendaciones.definition.url + queryParams(options)
}

/**
* @see routes/web.php:341
* @route '/workforce-planning/recomendaciones'
*/
recomendaciones.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recomendaciones.url(options),
    method: 'get',
})

/**
* @see routes/web.php:341
* @route '/workforce-planning/recomendaciones'
*/
recomendaciones.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recomendaciones.url(options),
    method: 'head',
})

/**
* @see routes/web.php:341
* @route '/workforce-planning/recomendaciones'
*/
const recomendacionesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recomendaciones.url(options),
    method: 'get',
})

/**
* @see routes/web.php:341
* @route '/workforce-planning/recomendaciones'
*/
recomendacionesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recomendaciones.url(options),
    method: 'get',
})

/**
* @see routes/web.php:341
* @route '/workforce-planning/recomendaciones'
*/
recomendacionesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recomendaciones.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

recomendaciones.form = recomendacionesForm

/**
* @see routes/web.php:345
* @route '/workforce-planning/gobernanza'
*/
export const gobernanza = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: gobernanza.url(options),
    method: 'get',
})

gobernanza.definition = {
    methods: ["get","head"],
    url: '/workforce-planning/gobernanza',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:345
* @route '/workforce-planning/gobernanza'
*/
gobernanza.url = (options?: RouteQueryOptions) => {
    return gobernanza.definition.url + queryParams(options)
}

/**
* @see routes/web.php:345
* @route '/workforce-planning/gobernanza'
*/
gobernanza.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: gobernanza.url(options),
    method: 'get',
})

/**
* @see routes/web.php:345
* @route '/workforce-planning/gobernanza'
*/
gobernanza.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: gobernanza.url(options),
    method: 'head',
})

/**
* @see routes/web.php:345
* @route '/workforce-planning/gobernanza'
*/
const gobernanzaForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: gobernanza.url(options),
    method: 'get',
})

/**
* @see routes/web.php:345
* @route '/workforce-planning/gobernanza'
*/
gobernanzaForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: gobernanza.url(options),
    method: 'get',
})

/**
* @see routes/web.php:345
* @route '/workforce-planning/gobernanza'
*/
gobernanzaForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: gobernanza.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

gobernanza.form = gobernanzaForm

/**
* @see routes/web.php:349
* @route '/workforce-planning/comparador'
*/
export const comparador = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: comparador.url(options),
    method: 'get',
})

comparador.definition = {
    methods: ["get","head"],
    url: '/workforce-planning/comparador',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:349
* @route '/workforce-planning/comparador'
*/
comparador.url = (options?: RouteQueryOptions) => {
    return comparador.definition.url + queryParams(options)
}

/**
* @see routes/web.php:349
* @route '/workforce-planning/comparador'
*/
comparador.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: comparador.url(options),
    method: 'get',
})

/**
* @see routes/web.php:349
* @route '/workforce-planning/comparador'
*/
comparador.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: comparador.url(options),
    method: 'head',
})

/**
* @see routes/web.php:349
* @route '/workforce-planning/comparador'
*/
const comparadorForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: comparador.url(options),
    method: 'get',
})

/**
* @see routes/web.php:349
* @route '/workforce-planning/comparador'
*/
comparadorForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: comparador.url(options),
    method: 'get',
})

/**
* @see routes/web.php:349
* @route '/workforce-planning/comparador'
*/
comparadorForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: comparador.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

comparador.form = comparadorForm

const workforcePlanning = {
    index: Object.assign(index, index),
    recomendaciones: Object.assign(recomendaciones, recomendaciones),
    gobernanza: Object.assign(gobernanza, gobernanza),
    comparador: Object.assign(comparador, comparador),
}

export default workforcePlanning