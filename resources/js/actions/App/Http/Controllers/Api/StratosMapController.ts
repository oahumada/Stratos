import {
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\StratosMapController::getGravitationalData
 * @see app/Http/Controllers/Api/StratosMapController.php:15
 * @route '/api/stratos-maps/gravitational'
 */
export const getGravitationalData = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getGravitationalData.url(options),
    method: 'get',
});

getGravitationalData.definition = {
    methods: ['get', 'head'],
    url: '/api/stratos-maps/gravitational',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getGravitationalData
 * @see app/Http/Controllers/Api/StratosMapController.php:15
 * @route '/api/stratos-maps/gravitational'
 */
getGravitationalData.url = (options?: RouteQueryOptions) => {
    return getGravitationalData.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getGravitationalData
 * @see app/Http/Controllers/Api/StratosMapController.php:15
 * @route '/api/stratos-maps/gravitational'
 */
getGravitationalData.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getGravitationalData.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getGravitationalData
 * @see app/Http/Controllers/Api/StratosMapController.php:15
 * @route '/api/stratos-maps/gravitational'
 */
getGravitationalData.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getGravitationalData.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getGravitationalData
 * @see app/Http/Controllers/Api/StratosMapController.php:15
 * @route '/api/stratos-maps/gravitational'
 */
const getGravitationalDataForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getGravitationalData.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getGravitationalData
 * @see app/Http/Controllers/Api/StratosMapController.php:15
 * @route '/api/stratos-maps/gravitational'
 */
getGravitationalDataForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getGravitationalData.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getGravitationalData
 * @see app/Http/Controllers/Api/StratosMapController.php:15
 * @route '/api/stratos-maps/gravitational'
 */
getGravitationalDataForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getGravitationalData.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getGravitationalData.form = getGravitationalDataForm;

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getCerberosData
 * @see app/Http/Controllers/Api/StratosMapController.php:55
 * @route '/api/stratos-maps/cerberos'
 */
export const getCerberosData = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getCerberosData.url(options),
    method: 'get',
});

getCerberosData.definition = {
    methods: ['get', 'head'],
    url: '/api/stratos-maps/cerberos',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getCerberosData
 * @see app/Http/Controllers/Api/StratosMapController.php:55
 * @route '/api/stratos-maps/cerberos'
 */
getCerberosData.url = (options?: RouteQueryOptions) => {
    return getCerberosData.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getCerberosData
 * @see app/Http/Controllers/Api/StratosMapController.php:55
 * @route '/api/stratos-maps/cerberos'
 */
getCerberosData.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getCerberosData.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getCerberosData
 * @see app/Http/Controllers/Api/StratosMapController.php:55
 * @route '/api/stratos-maps/cerberos'
 */
getCerberosData.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getCerberosData.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getCerberosData
 * @see app/Http/Controllers/Api/StratosMapController.php:55
 * @route '/api/stratos-maps/cerberos'
 */
const getCerberosDataForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getCerberosData.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getCerberosData
 * @see app/Http/Controllers/Api/StratosMapController.php:55
 * @route '/api/stratos-maps/cerberos'
 */
getCerberosDataForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getCerberosData.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::getCerberosData
 * @see app/Http/Controllers/Api/StratosMapController.php:55
 * @route '/api/stratos-maps/cerberos'
 */
getCerberosDataForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getCerberosData.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getCerberosData.form = getCerberosDataForm;

/**
 * @see \App\Http\Controllers\Api\StratosMapController::searchPeople
 * @see app/Http/Controllers/Api/StratosMapController.php:138
 * @route '/api/stratos-maps/people/search'
 */
export const searchPeople = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: searchPeople.url(options),
    method: 'get',
});

searchPeople.definition = {
    methods: ['get', 'head'],
    url: '/api/stratos-maps/people/search',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\StratosMapController::searchPeople
 * @see app/Http/Controllers/Api/StratosMapController.php:138
 * @route '/api/stratos-maps/people/search'
 */
searchPeople.url = (options?: RouteQueryOptions) => {
    return searchPeople.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\StratosMapController::searchPeople
 * @see app/Http/Controllers/Api/StratosMapController.php:138
 * @route '/api/stratos-maps/people/search'
 */
searchPeople.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: searchPeople.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::searchPeople
 * @see app/Http/Controllers/Api/StratosMapController.php:138
 * @route '/api/stratos-maps/people/search'
 */
searchPeople.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: searchPeople.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::searchPeople
 * @see app/Http/Controllers/Api/StratosMapController.php:138
 * @route '/api/stratos-maps/people/search'
 */
const searchPeopleForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: searchPeople.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::searchPeople
 * @see app/Http/Controllers/Api/StratosMapController.php:138
 * @route '/api/stratos-maps/people/search'
 */
searchPeopleForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: searchPeople.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\StratosMapController::searchPeople
 * @see app/Http/Controllers/Api/StratosMapController.php:138
 * @route '/api/stratos-maps/people/search'
 */
searchPeopleForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: searchPeople.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

searchPeople.form = searchPeopleForm;

const StratosMapController = {
    getGravitationalData,
    getCerberosData,
    searchPeople,
};

export default StratosMapController;
