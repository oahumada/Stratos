import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::store
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:26
 * @route '/api/qa/llm-evaluations'
 */
export const store = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

store.definition = {
    methods: ['post'],
    url: '/api/qa/llm-evaluations',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::store
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:26
 * @route '/api/qa/llm-evaluations'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::store
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:26
 * @route '/api/qa/llm-evaluations'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::store
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:26
 * @route '/api/qa/llm-evaluations'
 */
const storeForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::store
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:26
 * @route '/api/qa/llm-evaluations'
 */
storeForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
});

store.form = storeForm;

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::show
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
 * @route '/api/qa/llm-evaluations/{id}'
 */
export const show = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

show.definition = {
    methods: ['get', 'head'],
    url: '/api/qa/llm-evaluations/{id}',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::show
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
 * @route '/api/qa/llm-evaluations/{id}'
 */
show.url = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args };
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        id: args.id,
    };

    return (
        show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::show
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
 * @route '/api/qa/llm-evaluations/{id}'
 */
show.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::show
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
 * @route '/api/qa/llm-evaluations/{id}'
 */
show.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::show
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
 * @route '/api/qa/llm-evaluations/{id}'
 */
const showForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::show
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
 * @route '/api/qa/llm-evaluations/{id}'
 */
showForm.get = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::show
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:63
 * @route '/api/qa/llm-evaluations/{id}'
 */
showForm.head = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

show.form = showForm;

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::index
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
 * @route '/api/qa/llm-evaluations'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/api/qa/llm-evaluations',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::index
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
 * @route '/api/qa/llm-evaluations'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::index
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
 * @route '/api/qa/llm-evaluations'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::index
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
 * @route '/api/qa/llm-evaluations'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::index
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
 * @route '/api/qa/llm-evaluations'
 */
const indexForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::index
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
 * @route '/api/qa/llm-evaluations'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::index
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:80
 * @route '/api/qa/llm-evaluations'
 */
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

index.form = indexForm;

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
 * @route '/api/qa/llm-evaluations/metrics/summary'
 */
export const summary = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
});

summary.definition = {
    methods: ['get', 'head'],
    url: '/api/qa/llm-evaluations/metrics/summary',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
 * @route '/api/qa/llm-evaluations/metrics/summary'
 */
summary.url = (options?: RouteQueryOptions) => {
    return summary.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
 * @route '/api/qa/llm-evaluations/metrics/summary'
 */
summary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
 * @route '/api/qa/llm-evaluations/metrics/summary'
 */
summary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summary.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
 * @route '/api/qa/llm-evaluations/metrics/summary'
 */
const summaryForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
 * @route '/api/qa/llm-evaluations/metrics/summary'
 */
summaryForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\RAGASEvaluationController::summary
 * @see app/Http/Controllers/Api/RAGASEvaluationController.php:124
 * @route '/api/qa/llm-evaluations/metrics/summary'
 */
summaryForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: summary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

summary.form = summaryForm;

const RAGASEvaluationController = { store, show, index, summary };

export default RAGASEvaluationController;
