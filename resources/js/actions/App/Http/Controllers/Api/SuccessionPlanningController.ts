import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::indexCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:21
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
export const indexCandidates = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexCandidates.url(args, options),
    method: 'get',
})

indexCandidates.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/succession/candidates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::indexCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:21
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
indexCandidates.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { scenario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: typeof args.scenario === 'object'
        ? args.scenario.id
        : args.scenario,
    }

    return indexCandidates.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::indexCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:21
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
indexCandidates.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexCandidates.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::indexCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:21
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
indexCandidates.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexCandidates.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::indexCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:21
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
const indexCandidatesForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexCandidates.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::indexCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:21
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
indexCandidatesForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexCandidates.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::indexCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:21
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
indexCandidatesForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexCandidates.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indexCandidates.form = indexCandidatesForm

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::storeCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:43
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
export const storeCandidates = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCandidates.url(args, options),
    method: 'post',
})

storeCandidates.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenario}/succession/candidates',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::storeCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:43
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
storeCandidates.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { scenario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: typeof args.scenario === 'object'
        ? args.scenario.id
        : args.scenario,
    }

    return storeCandidates.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::storeCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:43
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
storeCandidates.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeCandidates.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::storeCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:43
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
const storeCandidatesForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeCandidates.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::storeCandidates
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:43
* @route '/api/scenarios/{scenario}/succession/candidates'
*/
storeCandidatesForm.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeCandidates.url(args, options),
    method: 'post',
})

storeCandidates.form = storeCandidatesForm

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::updateCandidate
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:70
* @route '/api/succession-candidates/{candidate}'
*/
export const updateCandidate = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateCandidate.url(args, options),
    method: 'patch',
})

updateCandidate.definition = {
    methods: ["patch"],
    url: '/api/succession-candidates/{candidate}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::updateCandidate
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:70
* @route '/api/succession-candidates/{candidate}'
*/
updateCandidate.url = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { candidate: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { candidate: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            candidate: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        candidate: typeof args.candidate === 'object'
        ? args.candidate.id
        : args.candidate,
    }

    return updateCandidate.definition.url
            .replace('{candidate}', parsedArgs.candidate.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::updateCandidate
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:70
* @route '/api/succession-candidates/{candidate}'
*/
updateCandidate.patch = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateCandidate.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::updateCandidate
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:70
* @route '/api/succession-candidates/{candidate}'
*/
const updateCandidateForm = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateCandidate.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::updateCandidate
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:70
* @route '/api/succession-candidates/{candidate}'
*/
updateCandidateForm.patch = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateCandidate.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateCandidate.form = updateCandidateForm

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::deleteCandidate
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:94
* @route '/api/succession-candidates/{candidate}'
*/
export const deleteCandidate = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCandidate.url(args, options),
    method: 'delete',
})

deleteCandidate.definition = {
    methods: ["delete"],
    url: '/api/succession-candidates/{candidate}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::deleteCandidate
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:94
* @route '/api/succession-candidates/{candidate}'
*/
deleteCandidate.url = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { candidate: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { candidate: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            candidate: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        candidate: typeof args.candidate === 'object'
        ? args.candidate.id
        : args.candidate,
    }

    return deleteCandidate.definition.url
            .replace('{candidate}', parsedArgs.candidate.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::deleteCandidate
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:94
* @route '/api/succession-candidates/{candidate}'
*/
deleteCandidate.delete = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteCandidate.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::deleteCandidate
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:94
* @route '/api/succession-candidates/{candidate}'
*/
const deleteCandidateForm = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteCandidate.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::deleteCandidate
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:94
* @route '/api/succession-candidates/{candidate}'
*/
deleteCandidateForm.delete = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: deleteCandidate.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

deleteCandidate.form = deleteCandidateForm

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::getCoverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
export const getCoverage = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCoverage.url(args, options),
    method: 'get',
})

getCoverage.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/succession/coverage',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::getCoverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
getCoverage.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { scenario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: typeof args.scenario === 'object'
        ? args.scenario.id
        : args.scenario,
    }

    return getCoverage.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::getCoverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
getCoverage.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getCoverage.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::getCoverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
getCoverage.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getCoverage.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::getCoverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
const getCoverageForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCoverage.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::getCoverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
getCoverageForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCoverage.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::getCoverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
getCoverageForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getCoverage.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getCoverage.form = getCoverageForm

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::analyze
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:107
* @route '/api/scenarios/{scenario}/succession/analyze'
*/
export const analyze = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyze.url(args, options),
    method: 'post',
})

analyze.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenario}/succession/analyze',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::analyze
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:107
* @route '/api/scenarios/{scenario}/succession/analyze'
*/
analyze.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { scenario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: typeof args.scenario === 'object'
        ? args.scenario.id
        : args.scenario,
    }

    return analyze.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::analyze
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:107
* @route '/api/scenarios/{scenario}/succession/analyze'
*/
analyze.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyze.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::analyze
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:107
* @route '/api/scenarios/{scenario}/succession/analyze'
*/
const analyzeForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyze.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::analyze
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:107
* @route '/api/scenarios/{scenario}/succession/analyze'
*/
analyzeForm.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyze.url(args, options),
    method: 'post',
})

analyze.form = analyzeForm

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listDevelopmentPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
export const listDevelopmentPlans = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listDevelopmentPlans.url(args, options),
    method: 'get',
})

listDevelopmentPlans.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/succession/development-plans',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listDevelopmentPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
listDevelopmentPlans.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenario: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { scenario: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            scenario: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        scenario: typeof args.scenario === 'object'
        ? args.scenario.id
        : args.scenario,
    }

    return listDevelopmentPlans.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listDevelopmentPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
listDevelopmentPlans.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listDevelopmentPlans.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listDevelopmentPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
listDevelopmentPlans.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listDevelopmentPlans.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listDevelopmentPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
const listDevelopmentPlansForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listDevelopmentPlans.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listDevelopmentPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
listDevelopmentPlansForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listDevelopmentPlans.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listDevelopmentPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
listDevelopmentPlansForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listDevelopmentPlans.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

listDevelopmentPlans.form = listDevelopmentPlansForm

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::createDevelopmentPlan
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:157
* @route '/api/succession-candidates/{candidate}/development-plans'
*/
export const createDevelopmentPlan = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createDevelopmentPlan.url(args, options),
    method: 'post',
})

createDevelopmentPlan.definition = {
    methods: ["post"],
    url: '/api/succession-candidates/{candidate}/development-plans',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::createDevelopmentPlan
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:157
* @route '/api/succession-candidates/{candidate}/development-plans'
*/
createDevelopmentPlan.url = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { candidate: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { candidate: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            candidate: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        candidate: typeof args.candidate === 'object'
        ? args.candidate.id
        : args.candidate,
    }

    return createDevelopmentPlan.definition.url
            .replace('{candidate}', parsedArgs.candidate.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::createDevelopmentPlan
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:157
* @route '/api/succession-candidates/{candidate}/development-plans'
*/
createDevelopmentPlan.post = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createDevelopmentPlan.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::createDevelopmentPlan
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:157
* @route '/api/succession-candidates/{candidate}/development-plans'
*/
const createDevelopmentPlanForm = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createDevelopmentPlan.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::createDevelopmentPlan
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:157
* @route '/api/succession-candidates/{candidate}/development-plans'
*/
createDevelopmentPlanForm.post = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createDevelopmentPlan.url(args, options),
    method: 'post',
})

createDevelopmentPlan.form = createDevelopmentPlanForm

const SuccessionPlanningController = { indexCandidates, storeCandidates, updateCandidate, deleteCandidate, getCoverage, analyze, listDevelopmentPlans, createDevelopmentPlan }

export default SuccessionPlanningController