import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::index
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:16
* @route '/api/lms/lessons/{lesson}/interactive'
*/
export const index = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/lessons/{lesson}/interactive',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::index
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:16
* @route '/api/lms/lessons/{lesson}/interactive'
*/
index.url = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lesson: args }
    }

    if (Array.isArray(args)) {
        args = {
            lesson: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        lesson: args.lesson,
    }

    return index.definition.url
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::index
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:16
* @route '/api/lms/lessons/{lesson}/interactive'
*/
index.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::index
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:16
* @route '/api/lms/lessons/{lesson}/interactive'
*/
index.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::index
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:16
* @route '/api/lms/lessons/{lesson}/interactive'
*/
const indexForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::index
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:16
* @route '/api/lms/lessons/{lesson}/interactive'
*/
indexForm.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::index
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:16
* @route '/api/lms/lessons/{lesson}/interactive'
*/
indexForm.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::store
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:23
* @route '/api/lms/lessons/{lesson}/interactive'
*/
export const store = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/lessons/{lesson}/interactive',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::store
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:23
* @route '/api/lms/lessons/{lesson}/interactive'
*/
store.url = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lesson: args }
    }

    if (Array.isArray(args)) {
        args = {
            lesson: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        lesson: args.lesson,
    }

    return store.definition.url
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::store
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:23
* @route '/api/lms/lessons/{lesson}/interactive'
*/
store.post = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::store
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:23
* @route '/api/lms/lessons/{lesson}/interactive'
*/
const storeForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::store
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:23
* @route '/api/lms/lessons/{lesson}/interactive'
*/
storeForm.post = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::update
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:44
* @route '/api/lms/interactive/{interactiveContent}'
*/
export const update = (args: { interactiveContent: string | number } | [interactiveContent: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/lms/interactive/{interactiveContent}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::update
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:44
* @route '/api/lms/interactive/{interactiveContent}'
*/
update.url = (args: { interactiveContent: string | number } | [interactiveContent: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { interactiveContent: args }
    }

    if (Array.isArray(args)) {
        args = {
            interactiveContent: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        interactiveContent: args.interactiveContent,
    }

    return update.definition.url
            .replace('{interactiveContent}', parsedArgs.interactiveContent.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::update
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:44
* @route '/api/lms/interactive/{interactiveContent}'
*/
update.put = (args: { interactiveContent: string | number } | [interactiveContent: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::update
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:44
* @route '/api/lms/interactive/{interactiveContent}'
*/
const updateForm = (args: { interactiveContent: string | number } | [interactiveContent: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::update
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:44
* @route '/api/lms/interactive/{interactiveContent}'
*/
updateForm.put = (args: { interactiveContent: string | number } | [interactiveContent: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::destroy
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:60
* @route '/api/lms/interactive/{interactiveContent}'
*/
export const destroy = (args: { interactiveContent: string | number } | [interactiveContent: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/lms/interactive/{interactiveContent}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::destroy
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:60
* @route '/api/lms/interactive/{interactiveContent}'
*/
destroy.url = (args: { interactiveContent: string | number } | [interactiveContent: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { interactiveContent: args }
    }

    if (Array.isArray(args)) {
        args = {
            interactiveContent: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        interactiveContent: args.interactiveContent,
    }

    return destroy.definition.url
            .replace('{interactiveContent}', parsedArgs.interactiveContent.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::destroy
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:60
* @route '/api/lms/interactive/{interactiveContent}'
*/
destroy.delete = (args: { interactiveContent: string | number } | [interactiveContent: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::destroy
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:60
* @route '/api/lms/interactive/{interactiveContent}'
*/
const destroyForm = (args: { interactiveContent: string | number } | [interactiveContent: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::destroy
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:60
* @route '/api/lms/interactive/{interactiveContent}'
*/
destroyForm.delete = (args: { interactiveContent: string | number } | [interactiveContent: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::widgetTypes
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:68
* @route '/api/lms/interactive/widget-types'
*/
export const widgetTypes = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: widgetTypes.url(options),
    method: 'get',
})

widgetTypes.definition = {
    methods: ["get","head"],
    url: '/api/lms/interactive/widget-types',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::widgetTypes
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:68
* @route '/api/lms/interactive/widget-types'
*/
widgetTypes.url = (options?: RouteQueryOptions) => {
    return widgetTypes.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::widgetTypes
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:68
* @route '/api/lms/interactive/widget-types'
*/
widgetTypes.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: widgetTypes.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::widgetTypes
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:68
* @route '/api/lms/interactive/widget-types'
*/
widgetTypes.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: widgetTypes.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::widgetTypes
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:68
* @route '/api/lms/interactive/widget-types'
*/
const widgetTypesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: widgetTypes.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::widgetTypes
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:68
* @route '/api/lms/interactive/widget-types'
*/
widgetTypesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: widgetTypes.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\InteractiveContentController::widgetTypes
* @see app/Http/Controllers/Api/Lms/InteractiveContentController.php:68
* @route '/api/lms/interactive/widget-types'
*/
widgetTypesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: widgetTypes.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

widgetTypes.form = widgetTypesForm

const InteractiveContentController = { index, store, update, destroy, widgetTypes }

export default InteractiveContentController