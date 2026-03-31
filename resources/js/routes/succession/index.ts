import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
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
* @see \App\Http\Controllers\Api\SuccessionPlanningController::coverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
export const coverage = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: coverage.url(args, options),
    method: 'get',
})

coverage.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/succession/coverage',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::coverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
coverage.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return coverage.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::coverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
coverage.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: coverage.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::coverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
coverage.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: coverage.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::coverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
const coverageForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: coverage.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::coverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
coverageForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: coverage.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::coverage
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:82
* @route '/api/scenarios/{scenario}/succession/coverage'
*/
coverageForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: coverage.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

coverage.form = coverageForm

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
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
export const listPlans = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listPlans.url(args, options),
    method: 'get',
})

listPlans.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/succession/development-plans',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
listPlans.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return listPlans.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
listPlans.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listPlans.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
listPlans.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listPlans.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
const listPlansForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listPlans.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
listPlansForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listPlans.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::listPlans
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:134
* @route '/api/scenarios/{scenario}/succession/development-plans'
*/
listPlansForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listPlans.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

listPlans.form = listPlansForm

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::createPlan
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:157
* @route '/api/succession-candidates/{candidate}/development-plans'
*/
export const createPlan = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createPlan.url(args, options),
    method: 'post',
})

createPlan.definition = {
    methods: ["post"],
    url: '/api/succession-candidates/{candidate}/development-plans',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::createPlan
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:157
* @route '/api/succession-candidates/{candidate}/development-plans'
*/
createPlan.url = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return createPlan.definition.url
            .replace('{candidate}', parsedArgs.candidate.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::createPlan
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:157
* @route '/api/succession-candidates/{candidate}/development-plans'
*/
createPlan.post = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createPlan.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::createPlan
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:157
* @route '/api/succession-candidates/{candidate}/development-plans'
*/
const createPlanForm = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createPlan.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\SuccessionPlanningController::createPlan
* @see app/Http/Controllers/Api/SuccessionPlanningController.php:157
* @route '/api/succession-candidates/{candidate}/development-plans'
*/
createPlanForm.post = (args: { candidate: number | { id: number } } | [candidate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createPlan.url(args, options),
    method: 'post',
})

createPlan.form = createPlanForm

/**
* @see routes/web.php:151
* @route '/succession'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/succession',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:151
* @route '/succession'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:151
* @route '/succession'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:151
* @route '/succession'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:151
* @route '/succession'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:151
* @route '/succession'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:151
* @route '/succession'
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

const succession = {
    indexCandidates: Object.assign(indexCandidates, indexCandidates),
    storeCandidates: Object.assign(storeCandidates, storeCandidates),
    updateCandidate: Object.assign(updateCandidate, updateCandidate),
    deleteCandidate: Object.assign(deleteCandidate, deleteCandidate),
    coverage: Object.assign(coverage, coverage),
    analyze: Object.assign(analyze, analyze),
    listPlans: Object.assign(listPlans, listPlans),
    createPlan: Object.assign(createPlan, createPlan),
    index: Object.assign(index, index),
}

export default succession