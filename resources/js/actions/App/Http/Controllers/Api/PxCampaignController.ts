import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PxCampaignController::index
* @see app/Http/Controllers/Api/PxCampaignController.php:15
* @route '/api/px-campaigns'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/px-campaigns',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PxCampaignController::index
* @see app/Http/Controllers/Api/PxCampaignController.php:15
* @route '/api/px-campaigns'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PxCampaignController::index
* @see app/Http/Controllers/Api/PxCampaignController.php:15
* @route '/api/px-campaigns'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PxCampaignController::index
* @see app/Http/Controllers/Api/PxCampaignController.php:15
* @route '/api/px-campaigns'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PxCampaignController::store
* @see app/Http/Controllers/Api/PxCampaignController.php:29
* @route '/api/px-campaigns'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/px-campaigns',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PxCampaignController::store
* @see app/Http/Controllers/Api/PxCampaignController.php:29
* @route '/api/px-campaigns'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PxCampaignController::store
* @see app/Http/Controllers/Api/PxCampaignController.php:29
* @route '/api/px-campaigns'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PxCampaignController::show
* @see app/Http/Controllers/Api/PxCampaignController.php:68
* @route '/api/px-campaigns/{px_campaign}'
*/
export const show = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/px-campaigns/{px_campaign}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PxCampaignController::show
* @see app/Http/Controllers/Api/PxCampaignController.php:68
* @route '/api/px-campaigns/{px_campaign}'
*/
show.url = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { px_campaign: args }
    }

    if (Array.isArray(args)) {
        args = {
            px_campaign: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        px_campaign: args.px_campaign,
    }

    return show.definition.url
            .replace('{px_campaign}', parsedArgs.px_campaign.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PxCampaignController::show
* @see app/Http/Controllers/Api/PxCampaignController.php:68
* @route '/api/px-campaigns/{px_campaign}'
*/
show.get = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PxCampaignController::show
* @see app/Http/Controllers/Api/PxCampaignController.php:68
* @route '/api/px-campaigns/{px_campaign}'
*/
show.head = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PxCampaignController::update
* @see app/Http/Controllers/Api/PxCampaignController.php:80
* @route '/api/px-campaigns/{px_campaign}'
*/
export const update = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/api/px-campaigns/{px_campaign}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Api\PxCampaignController::update
* @see app/Http/Controllers/Api/PxCampaignController.php:80
* @route '/api/px-campaigns/{px_campaign}'
*/
update.url = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { px_campaign: args }
    }

    if (Array.isArray(args)) {
        args = {
            px_campaign: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        px_campaign: args.px_campaign,
    }

    return update.definition.url
            .replace('{px_campaign}', parsedArgs.px_campaign.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PxCampaignController::update
* @see app/Http/Controllers/Api/PxCampaignController.php:80
* @route '/api/px-campaigns/{px_campaign}'
*/
update.put = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\PxCampaignController::update
* @see app/Http/Controllers/Api/PxCampaignController.php:80
* @route '/api/px-campaigns/{px_campaign}'
*/
update.patch = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\PxCampaignController::destroy
* @see app/Http/Controllers/Api/PxCampaignController.php:110
* @route '/api/px-campaigns/{px_campaign}'
*/
export const destroy = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/px-campaigns/{px_campaign}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\PxCampaignController::destroy
* @see app/Http/Controllers/Api/PxCampaignController.php:110
* @route '/api/px-campaigns/{px_campaign}'
*/
destroy.url = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { px_campaign: args }
    }

    if (Array.isArray(args)) {
        args = {
            px_campaign: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        px_campaign: args.px_campaign,
    }

    return destroy.definition.url
            .replace('{px_campaign}', parsedArgs.px_campaign.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PxCampaignController::destroy
* @see app/Http/Controllers/Api/PxCampaignController.php:110
* @route '/api/px-campaigns/{px_campaign}'
*/
destroy.delete = (args: { px_campaign: string | number } | [px_campaign: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const PxCampaignController = { index, store, show, update, destroy }

export default PxCampaignController