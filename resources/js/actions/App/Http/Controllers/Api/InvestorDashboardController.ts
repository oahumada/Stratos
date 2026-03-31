import {
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\InvestorDashboardController::index
 * @see app/Http/Controllers/Api/InvestorDashboardController.php:22
 * @route '/api/investor/dashboard'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

index.definition = {
    methods: ['get', 'head'],
    url: '/api/investor/dashboard',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\InvestorDashboardController::index
 * @see app/Http/Controllers/Api/InvestorDashboardController.php:22
 * @route '/api/investor/dashboard'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\InvestorDashboardController::index
 * @see app/Http/Controllers/Api/InvestorDashboardController.php:22
 * @route '/api/investor/dashboard'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\InvestorDashboardController::index
 * @see app/Http/Controllers/Api/InvestorDashboardController.php:22
 * @route '/api/investor/dashboard'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\InvestorDashboardController::index
 * @see app/Http/Controllers/Api/InvestorDashboardController.php:22
 * @route '/api/investor/dashboard'
 */
const indexForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\InvestorDashboardController::index
 * @see app/Http/Controllers/Api/InvestorDashboardController.php:22
 * @route '/api/investor/dashboard'
 */
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\InvestorDashboardController::index
 * @see app/Http/Controllers/Api/InvestorDashboardController.php:22
 * @route '/api/investor/dashboard'
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

const InvestorDashboardController = { index };

export default InvestorDashboardController;
