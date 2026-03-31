import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\CmsArticleController::store
* @see app/Http/Controllers/Api/Lms/CmsArticleController.php:12
* @route '/api/lms/cms/articles'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/cms/articles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CmsArticleController::store
* @see app/Http/Controllers/Api/Lms/CmsArticleController.php:12
* @route '/api/lms/cms/articles'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CmsArticleController::store
* @see app/Http/Controllers/Api/Lms/CmsArticleController.php:12
* @route '/api/lms/cms/articles'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CmsArticleController::store
* @see app/Http/Controllers/Api/Lms/CmsArticleController.php:12
* @route '/api/lms/cms/articles'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CmsArticleController::store
* @see app/Http/Controllers/Api/Lms/CmsArticleController.php:12
* @route '/api/lms/cms/articles'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

const CmsArticleController = { store }

export default CmsArticleController