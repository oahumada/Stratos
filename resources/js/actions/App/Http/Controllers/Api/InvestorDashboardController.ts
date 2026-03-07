import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\InvestorDashboardController::index
* @see app/Http/Controllers/Api/InvestorDashboardController.php:22
* @route '/api/investor/dashboard'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/investor/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\InvestorDashboardController::index
* @see app/Http/Controllers/Api/InvestorDashboardController.php:22
* @route '/api/investor/dashboard'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\InvestorDashboardController::index
* @see app/Http/Controllers/Api/InvestorDashboardController.php:22
* @route '/api/investor/dashboard'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\InvestorDashboardController::index
* @see app/Http/Controllers/Api/InvestorDashboardController.php:22
* @route '/api/investor/dashboard'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

const InvestorDashboardController = { index }

export default InvestorDashboardController