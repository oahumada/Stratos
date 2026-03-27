import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:17
* @route '/api/pulse-surveys'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/pulse-surveys',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:17
* @route '/api/pulse-surveys'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:17
* @route '/api/pulse-surveys'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::index
* @see app/Http/Controllers/Api/PulseController.php:17
* @route '/api/pulse-surveys'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:30
* @route '/api/pulse-surveys/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/pulse-surveys/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:30
* @route '/api/pulse-surveys/{id}'
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
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:30
* @route '/api/pulse-surveys/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::show
* @see app/Http/Controllers/Api/PulseController.php:30
* @route '/api/pulse-surveys/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PulseController::storeResponse
* @see app/Http/Controllers/Api/PulseController.php:40
* @route '/api/pulse-responses'
*/
export const storeResponse = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeResponse.url(options),
    method: 'post',
})

storeResponse.definition = {
    methods: ["post"],
    url: '/api/pulse-responses',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PulseController::storeResponse
* @see app/Http/Controllers/Api/PulseController.php:40
* @route '/api/pulse-responses'
*/
storeResponse.url = (options?: RouteQueryOptions) => {
    return storeResponse.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PulseController::storeResponse
* @see app/Http/Controllers/Api/PulseController.php:40
* @route '/api/pulse-responses'
*/
storeResponse.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeResponse.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PulseController::healthScan
* @see app/Http/Controllers/Api/PulseController.php:121
* @route '/api/pulse/health-scan'
*/
export const healthScan = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: healthScan.url(options),
    method: 'get',
})

healthScan.definition = {
    methods: ["get","head"],
    url: '/api/pulse/health-scan',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PulseController::healthScan
* @see app/Http/Controllers/Api/PulseController.php:121
* @route '/api/pulse/health-scan'
*/
healthScan.url = (options?: RouteQueryOptions) => {
    return healthScan.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PulseController::healthScan
* @see app/Http/Controllers/Api/PulseController.php:121
* @route '/api/pulse/health-scan'
*/
healthScan.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: healthScan.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::healthScan
* @see app/Http/Controllers/Api/PulseController.php:121
* @route '/api/pulse/health-scan'
*/
healthScan.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: healthScan.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PulseController::storeEmployeePulse
* @see app/Http/Controllers/Api/PulseController.php:69
* @route '/api/people-experience/employee-pulses'
*/
export const storeEmployeePulse = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeEmployeePulse.url(options),
    method: 'post',
})

storeEmployeePulse.definition = {
    methods: ["post"],
    url: '/api/people-experience/employee-pulses',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PulseController::storeEmployeePulse
* @see app/Http/Controllers/Api/PulseController.php:69
* @route '/api/people-experience/employee-pulses'
*/
storeEmployeePulse.url = (options?: RouteQueryOptions) => {
    return storeEmployeePulse.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PulseController::storeEmployeePulse
* @see app/Http/Controllers/Api/PulseController.php:69
* @route '/api/people-experience/employee-pulses'
*/
storeEmployeePulse.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeEmployeePulse.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
* @see app/Http/Controllers/Api/PulseController.php:140
* @route '/api/people-experience/employee-pulses'
*/
export const listEmployeePulses = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listEmployeePulses.url(options),
    method: 'get',
})

listEmployeePulses.definition = {
    methods: ["get","head"],
    url: '/api/people-experience/employee-pulses',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
* @see app/Http/Controllers/Api/PulseController.php:140
* @route '/api/people-experience/employee-pulses'
*/
listEmployeePulses.url = (options?: RouteQueryOptions) => {
    return listEmployeePulses.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
* @see app/Http/Controllers/Api/PulseController.php:140
* @route '/api/people-experience/employee-pulses'
*/
listEmployeePulses.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listEmployeePulses.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::listEmployeePulses
* @see app/Http/Controllers/Api/PulseController.php:140
* @route '/api/people-experience/employee-pulses'
*/
listEmployeePulses.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listEmployeePulses.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
* @see app/Http/Controllers/Api/PulseController.php:156
* @route '/api/people-experience/turnover-heatmap'
*/
export const listTurnoverHeatmap = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listTurnoverHeatmap.url(options),
    method: 'get',
})

listTurnoverHeatmap.definition = {
    methods: ["get","head"],
    url: '/api/people-experience/turnover-heatmap',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
* @see app/Http/Controllers/Api/PulseController.php:156
* @route '/api/people-experience/turnover-heatmap'
*/
listTurnoverHeatmap.url = (options?: RouteQueryOptions) => {
    return listTurnoverHeatmap.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
* @see app/Http/Controllers/Api/PulseController.php:156
* @route '/api/people-experience/turnover-heatmap'
*/
listTurnoverHeatmap.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listTurnoverHeatmap.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PulseController::listTurnoverHeatmap
* @see app/Http/Controllers/Api/PulseController.php:156
* @route '/api/people-experience/turnover-heatmap'
*/
listTurnoverHeatmap.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listTurnoverHeatmap.url(options),
    method: 'head',
})

const PulseController = { index, show, storeResponse, healthScan, storeEmployeePulse, listEmployeePulses, listTurnoverHeatmap }

export default PulseController