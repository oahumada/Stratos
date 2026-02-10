import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\MarketplaceController::opportunities
* @see app/Http/Controllers/Api/MarketplaceController.php:24
* @route '/api/people/{people_id}/marketplace'
*/
export const opportunities = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: opportunities.url(args, options),
    method: 'get',
})

opportunities.definition = {
    methods: ["get","head"],
    url: '/api/people/{people_id}/marketplace',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MarketplaceController::opportunities
* @see app/Http/Controllers/Api/MarketplaceController.php:24
* @route '/api/people/{people_id}/marketplace'
*/
opportunities.url = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { people_id: args }
    }

    if (Array.isArray(args)) {
        args = {
            people_id: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        people_id: args.people_id,
    }

    return opportunities.definition.url
            .replace('{people_id}', parsedArgs.people_id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MarketplaceController::opportunities
* @see app/Http/Controllers/Api/MarketplaceController.php:24
* @route '/api/people/{people_id}/marketplace'
*/
opportunities.get = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: opportunities.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MarketplaceController::opportunities
* @see app/Http/Controllers/Api/MarketplaceController.php:24
* @route '/api/people/{people_id}/marketplace'
*/
opportunities.head = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: opportunities.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\MarketplaceController::opportunities
* @see app/Http/Controllers/Api/MarketplaceController.php:24
* @route '/api/people/{people_id}/marketplace'
*/
const opportunitiesForm = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: opportunities.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MarketplaceController::opportunities
* @see app/Http/Controllers/Api/MarketplaceController.php:24
* @route '/api/people/{people_id}/marketplace'
*/
opportunitiesForm.get = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: opportunities.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MarketplaceController::opportunities
* @see app/Http/Controllers/Api/MarketplaceController.php:24
* @route '/api/people/{people_id}/marketplace'
*/
opportunitiesForm.head = (args: { people_id: string | number } | [people_id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: opportunities.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

opportunities.form = opportunitiesForm

/**
* @see \App\Http\Controllers\Api\MarketplaceController::recruiterView
* @see app/Http/Controllers/Api/MarketplaceController.php:90
* @route '/api/marketplace/recruiter'
*/
export const recruiterView = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recruiterView.url(options),
    method: 'get',
})

recruiterView.definition = {
    methods: ["get","head"],
    url: '/api/marketplace/recruiter',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\MarketplaceController::recruiterView
* @see app/Http/Controllers/Api/MarketplaceController.php:90
* @route '/api/marketplace/recruiter'
*/
recruiterView.url = (options?: RouteQueryOptions) => {
    return recruiterView.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\MarketplaceController::recruiterView
* @see app/Http/Controllers/Api/MarketplaceController.php:90
* @route '/api/marketplace/recruiter'
*/
recruiterView.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recruiterView.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MarketplaceController::recruiterView
* @see app/Http/Controllers/Api/MarketplaceController.php:90
* @route '/api/marketplace/recruiter'
*/
recruiterView.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recruiterView.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\MarketplaceController::recruiterView
* @see app/Http/Controllers/Api/MarketplaceController.php:90
* @route '/api/marketplace/recruiter'
*/
const recruiterViewForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recruiterView.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MarketplaceController::recruiterView
* @see app/Http/Controllers/Api/MarketplaceController.php:90
* @route '/api/marketplace/recruiter'
*/
recruiterViewForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recruiterView.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\MarketplaceController::recruiterView
* @see app/Http/Controllers/Api/MarketplaceController.php:90
* @route '/api/marketplace/recruiter'
*/
recruiterViewForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recruiterView.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

recruiterView.form = recruiterViewForm

const MarketplaceController = { opportunities, recruiterView }

export default MarketplaceController