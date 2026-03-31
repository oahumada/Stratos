import {
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\RagController::ask
 * @see app/Http/Controllers/Api/RagController.php:25
 * @route '/api/rag/ask'
 */
export const ask = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ask.url(options),
    method: 'post',
});

ask.definition = {
    methods: ['post'],
    url: '/api/rag/ask',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\RagController::ask
 * @see app/Http/Controllers/Api/RagController.php:25
 * @route '/api/rag/ask'
 */
ask.url = (options?: RouteQueryOptions) => {
    return ask.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\RagController::ask
 * @see app/Http/Controllers/Api/RagController.php:25
 * @route '/api/rag/ask'
 */
ask.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: ask.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\RagController::ask
 * @see app/Http/Controllers/Api/RagController.php:25
 * @route '/api/rag/ask'
 */
const askForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ask.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\RagController::ask
 * @see app/Http/Controllers/Api/RagController.php:25
 * @route '/api/rag/ask'
 */
askForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: ask.url(options),
    method: 'post',
});

ask.form = askForm;

const rag = {
    ask: Object.assign(ask, ask),
};

export default rag;
