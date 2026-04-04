import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::recommendations
* @see app/Http/Controllers/Api/Lms/CatalogController.php:96
* @route '/api/lms/catalog/recommendations'
*/
export const recommendations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendations.url(options),
    method: 'get',
})

recommendations.definition = {
    methods: ["get","head"],
    url: '/api/lms/catalog/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::recommendations
* @see app/Http/Controllers/Api/Lms/CatalogController.php:96
* @route '/api/lms/catalog/recommendations'
*/
recommendations.url = (options?: RouteQueryOptions) => {
    return recommendations.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::recommendations
* @see app/Http/Controllers/Api/Lms/CatalogController.php:96
* @route '/api/lms/catalog/recommendations'
*/
recommendations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::recommendations
* @see app/Http/Controllers/Api/Lms/CatalogController.php:96
* @route '/api/lms/catalog/recommendations'
*/
recommendations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recommendations.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::recommendations
* @see app/Http/Controllers/Api/Lms/CatalogController.php:96
* @route '/api/lms/catalog/recommendations'
*/
const recommendationsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::recommendations
* @see app/Http/Controllers/Api/Lms/CatalogController.php:96
* @route '/api/lms/catalog/recommendations'
*/
recommendationsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::recommendations
* @see app/Http/Controllers/Api/Lms/CatalogController.php:96
* @route '/api/lms/catalog/recommendations'
*/
recommendationsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

recommendations.form = recommendationsForm

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::categories
* @see app/Http/Controllers/Api/Lms/CatalogController.php:114
* @route '/api/lms/catalog/categories'
*/
export const categories = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: categories.url(options),
    method: 'get',
})

categories.definition = {
    methods: ["get","head"],
    url: '/api/lms/catalog/categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::categories
* @see app/Http/Controllers/Api/Lms/CatalogController.php:114
* @route '/api/lms/catalog/categories'
*/
categories.url = (options?: RouteQueryOptions) => {
    return categories.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::categories
* @see app/Http/Controllers/Api/Lms/CatalogController.php:114
* @route '/api/lms/catalog/categories'
*/
categories.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: categories.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::categories
* @see app/Http/Controllers/Api/Lms/CatalogController.php:114
* @route '/api/lms/catalog/categories'
*/
categories.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: categories.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::categories
* @see app/Http/Controllers/Api/Lms/CatalogController.php:114
* @route '/api/lms/catalog/categories'
*/
const categoriesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: categories.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::categories
* @see app/Http/Controllers/Api/Lms/CatalogController.php:114
* @route '/api/lms/catalog/categories'
*/
categoriesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: categories.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::categories
* @see app/Http/Controllers/Api/Lms/CatalogController.php:114
* @route '/api/lms/catalog/categories'
*/
categoriesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: categories.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

categories.form = categoriesForm

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::tags
* @see app/Http/Controllers/Api/Lms/CatalogController.php:127
* @route '/api/lms/catalog/tags'
*/
export const tags = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tags.url(options),
    method: 'get',
})

tags.definition = {
    methods: ["get","head"],
    url: '/api/lms/catalog/tags',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::tags
* @see app/Http/Controllers/Api/Lms/CatalogController.php:127
* @route '/api/lms/catalog/tags'
*/
tags.url = (options?: RouteQueryOptions) => {
    return tags.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::tags
* @see app/Http/Controllers/Api/Lms/CatalogController.php:127
* @route '/api/lms/catalog/tags'
*/
tags.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tags.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::tags
* @see app/Http/Controllers/Api/Lms/CatalogController.php:127
* @route '/api/lms/catalog/tags'
*/
tags.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tags.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::tags
* @see app/Http/Controllers/Api/Lms/CatalogController.php:127
* @route '/api/lms/catalog/tags'
*/
const tagsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tags.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::tags
* @see app/Http/Controllers/Api/Lms/CatalogController.php:127
* @route '/api/lms/catalog/tags'
*/
tagsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tags.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::tags
* @see app/Http/Controllers/Api/Lms/CatalogController.php:127
* @route '/api/lms/catalog/tags'
*/
tagsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tags.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

tags.form = tagsForm

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::index
* @see app/Http/Controllers/Api/Lms/CatalogController.php:18
* @route '/api/lms/catalog'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/catalog',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::index
* @see app/Http/Controllers/Api/Lms/CatalogController.php:18
* @route '/api/lms/catalog'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::index
* @see app/Http/Controllers/Api/Lms/CatalogController.php:18
* @route '/api/lms/catalog'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::index
* @see app/Http/Controllers/Api/Lms/CatalogController.php:18
* @route '/api/lms/catalog'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::index
* @see app/Http/Controllers/Api/Lms/CatalogController.php:18
* @route '/api/lms/catalog'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::index
* @see app/Http/Controllers/Api/Lms/CatalogController.php:18
* @route '/api/lms/catalog'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::index
* @see app/Http/Controllers/Api/Lms/CatalogController.php:18
* @route '/api/lms/catalog'
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
* @see \App\Http\Controllers\Api\Lms\CatalogController::show
* @see app/Http/Controllers/Api/Lms/CatalogController.php:38
* @route '/api/lms/catalog/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/lms/catalog/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::show
* @see app/Http/Controllers/Api/Lms/CatalogController.php:38
* @route '/api/lms/catalog/{id}'
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
* @see \App\Http\Controllers\Api\Lms\CatalogController::show
* @see app/Http/Controllers/Api/Lms/CatalogController.php:38
* @route '/api/lms/catalog/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::show
* @see app/Http/Controllers/Api/Lms/CatalogController.php:38
* @route '/api/lms/catalog/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::show
* @see app/Http/Controllers/Api/Lms/CatalogController.php:38
* @route '/api/lms/catalog/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::show
* @see app/Http/Controllers/Api/Lms/CatalogController.php:38
* @route '/api/lms/catalog/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::show
* @see app/Http/Controllers/Api/Lms/CatalogController.php:38
* @route '/api/lms/catalog/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::rate
* @see app/Http/Controllers/Api/Lms/CatalogController.php:51
* @route '/api/lms/catalog/{id}/rate'
*/
export const rate = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: rate.url(args, options),
    method: 'post',
})

rate.definition = {
    methods: ["post"],
    url: '/api/lms/catalog/{id}/rate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::rate
* @see app/Http/Controllers/Api/Lms/CatalogController.php:51
* @route '/api/lms/catalog/{id}/rate'
*/
rate.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return rate.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::rate
* @see app/Http/Controllers/Api/Lms/CatalogController.php:51
* @route '/api/lms/catalog/{id}/rate'
*/
rate.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: rate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::rate
* @see app/Http/Controllers/Api/Lms/CatalogController.php:51
* @route '/api/lms/catalog/{id}/rate'
*/
const rateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: rate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::rate
* @see app/Http/Controllers/Api/Lms/CatalogController.php:51
* @route '/api/lms/catalog/{id}/rate'
*/
rateForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: rate.url(args, options),
    method: 'post',
})

rate.form = rateForm

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::enroll
* @see app/Http/Controllers/Api/Lms/CatalogController.php:75
* @route '/api/lms/catalog/{id}/enroll'
*/
export const enroll = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: enroll.url(args, options),
    method: 'post',
})

enroll.definition = {
    methods: ["post"],
    url: '/api/lms/catalog/{id}/enroll',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::enroll
* @see app/Http/Controllers/Api/Lms/CatalogController.php:75
* @route '/api/lms/catalog/{id}/enroll'
*/
enroll.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return enroll.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::enroll
* @see app/Http/Controllers/Api/Lms/CatalogController.php:75
* @route '/api/lms/catalog/{id}/enroll'
*/
enroll.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: enroll.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::enroll
* @see app/Http/Controllers/Api/Lms/CatalogController.php:75
* @route '/api/lms/catalog/{id}/enroll'
*/
const enrollForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: enroll.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CatalogController::enroll
* @see app/Http/Controllers/Api/Lms/CatalogController.php:75
* @route '/api/lms/catalog/{id}/enroll'
*/
enrollForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: enroll.url(args, options),
    method: 'post',
})

enroll.form = enrollForm

const CatalogController = { recommendations, categories, tags, index, show, rate, enroll }

export default CatalogController