import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::store
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:26
* @route '/api/qa/llm-evaluations'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/qa/llm-evaluations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::store
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:26
* @route '/api/qa/llm-evaluations'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::store
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:26
* @route '/api/qa/llm-evaluations'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::show
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
* @route '/api/qa/llm-evaluations/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/qa/llm-evaluations/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::show
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
* @route '/api/qa/llm-evaluations/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args }
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        id: args.id,
    }

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::show
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
* @route '/api/qa/llm-evaluations/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::show
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
* @route '/api/qa/llm-evaluations/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::index
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
* @route '/api/qa/llm-evaluations'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/qa/llm-evaluations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::index
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
* @route '/api/qa/llm-evaluations'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::index
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
* @route '/api/qa/llm-evaluations'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::index
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
* @route '/api/qa/llm-evaluations'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
* @route '/api/qa/llm-evaluations/metrics/summary'
*/
export const summary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

summary.definition = {
    methods: ["get","head"],
    url: '/api/qa/llm-evaluations/metrics/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
* @route '/api/qa/llm-evaluations/metrics/summary'
*/
summary.url = (options?: RouteQueryOptions) => {
    return summary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
* @route '/api/qa/llm-evaluations/metrics/summary'
*/
summary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
* @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
* @route '/api/qa/llm-evaluations/metrics/summary'
*/
summary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summary.url(options),
    method: 'head',
})

const RAGASEvaluationController = { store, show, index, summary }

export default RAGASEvaluationController