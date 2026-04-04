import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::index
* @see app/Http/Controllers/Api/Lms/WebhookController.php:16
* @route '/api/lms/webhooks'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/webhooks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::index
* @see app/Http/Controllers/Api/Lms/WebhookController.php:16
* @route '/api/lms/webhooks'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::index
* @see app/Http/Controllers/Api/Lms/WebhookController.php:16
* @route '/api/lms/webhooks'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::index
* @see app/Http/Controllers/Api/Lms/WebhookController.php:16
* @route '/api/lms/webhooks'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::index
* @see app/Http/Controllers/Api/Lms/WebhookController.php:16
* @route '/api/lms/webhooks'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::index
* @see app/Http/Controllers/Api/Lms/WebhookController.php:16
* @route '/api/lms/webhooks'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::index
* @see app/Http/Controllers/Api/Lms/WebhookController.php:16
* @route '/api/lms/webhooks'
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
* @see \App\Http\Controllers\Api\Lms\WebhookController::store
* @see app/Http/Controllers/Api/Lms/WebhookController.php:24
* @route '/api/lms/webhooks'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/webhooks',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::store
* @see app/Http/Controllers/Api/Lms/WebhookController.php:24
* @route '/api/lms/webhooks'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::store
* @see app/Http/Controllers/Api/Lms/WebhookController.php:24
* @route '/api/lms/webhooks'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::store
* @see app/Http/Controllers/Api/Lms/WebhookController.php:24
* @route '/api/lms/webhooks'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::store
* @see app/Http/Controllers/Api/Lms/WebhookController.php:24
* @route '/api/lms/webhooks'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::update
* @see app/Http/Controllers/Api/Lms/WebhookController.php:39
* @route '/api/lms/webhooks/{webhook}'
*/
export const update = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/lms/webhooks/{webhook}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::update
* @see app/Http/Controllers/Api/Lms/WebhookController.php:39
* @route '/api/lms/webhooks/{webhook}'
*/
update.url = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { webhook: args }
    }

    if (Array.isArray(args)) {
        args = {
            webhook: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        webhook: args.webhook,
    }

    return update.definition.url
            .replace('{webhook}', parsedArgs.webhook.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::update
* @see app/Http/Controllers/Api/Lms/WebhookController.php:39
* @route '/api/lms/webhooks/{webhook}'
*/
update.put = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::update
* @see app/Http/Controllers/Api/Lms/WebhookController.php:39
* @route '/api/lms/webhooks/{webhook}'
*/
const updateForm = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::update
* @see app/Http/Controllers/Api/Lms/WebhookController.php:39
* @route '/api/lms/webhooks/{webhook}'
*/
updateForm.put = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\Lms\WebhookController::destroy
* @see app/Http/Controllers/Api/Lms/WebhookController.php:59
* @route '/api/lms/webhooks/{webhook}'
*/
export const destroy = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/lms/webhooks/{webhook}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::destroy
* @see app/Http/Controllers/Api/Lms/WebhookController.php:59
* @route '/api/lms/webhooks/{webhook}'
*/
destroy.url = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { webhook: args }
    }

    if (Array.isArray(args)) {
        args = {
            webhook: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        webhook: args.webhook,
    }

    return destroy.definition.url
            .replace('{webhook}', parsedArgs.webhook.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::destroy
* @see app/Http/Controllers/Api/Lms/WebhookController.php:59
* @route '/api/lms/webhooks/{webhook}'
*/
destroy.delete = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::destroy
* @see app/Http/Controllers/Api/Lms/WebhookController.php:59
* @route '/api/lms/webhooks/{webhook}'
*/
const destroyForm = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::destroy
* @see app/Http/Controllers/Api/Lms/WebhookController.php:59
* @route '/api/lms/webhooks/{webhook}'
*/
destroyForm.delete = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\Lms\WebhookController::test
* @see app/Http/Controllers/Api/Lms/WebhookController.php:71
* @route '/api/lms/webhooks/{webhook}/test'
*/
export const test = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: test.url(args, options),
    method: 'post',
})

test.definition = {
    methods: ["post"],
    url: '/api/lms/webhooks/{webhook}/test',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::test
* @see app/Http/Controllers/Api/Lms/WebhookController.php:71
* @route '/api/lms/webhooks/{webhook}/test'
*/
test.url = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { webhook: args }
    }

    if (Array.isArray(args)) {
        args = {
            webhook: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        webhook: args.webhook,
    }

    return test.definition.url
            .replace('{webhook}', parsedArgs.webhook.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::test
* @see app/Http/Controllers/Api/Lms/WebhookController.php:71
* @route '/api/lms/webhooks/{webhook}/test'
*/
test.post = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: test.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::test
* @see app/Http/Controllers/Api/Lms/WebhookController.php:71
* @route '/api/lms/webhooks/{webhook}/test'
*/
const testForm = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: test.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\WebhookController::test
* @see app/Http/Controllers/Api/Lms/WebhookController.php:71
* @route '/api/lms/webhooks/{webhook}/test'
*/
testForm.post = (args: { webhook: string | number } | [webhook: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: test.url(args, options),
    method: 'post',
})

test.form = testForm

const WebhookController = { index, store, update, destroy, test }

export default WebhookController