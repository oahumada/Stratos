import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::get
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:22
* @route '/api/scenarios/{scenario}/transformation/roadmap'
*/
export const get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: get.url(args, options),
    method: 'get',
})

get.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/transformation/roadmap',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::get
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:22
* @route '/api/scenarios/{scenario}/transformation/roadmap'
*/
get.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return get.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::get
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:22
* @route '/api/scenarios/{scenario}/transformation/roadmap'
*/
get.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: get.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::get
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:22
* @route '/api/scenarios/{scenario}/transformation/roadmap'
*/
get.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: get.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::get
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:22
* @route '/api/scenarios/{scenario}/transformation/roadmap'
*/
const getForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: get.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::get
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:22
* @route '/api/scenarios/{scenario}/transformation/roadmap'
*/
getForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: get.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::get
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:22
* @route '/api/scenarios/{scenario}/transformation/roadmap'
*/
getForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: get.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

get.form = getForm

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::generate
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:34
* @route '/api/scenarios/{scenario}/transformation/generate'
*/
export const generate = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(args, options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenario}/transformation/generate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::generate
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:34
* @route '/api/scenarios/{scenario}/transformation/generate'
*/
generate.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return generate.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::generate
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:34
* @route '/api/scenarios/{scenario}/transformation/generate'
*/
generate.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::generate
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:34
* @route '/api/scenarios/{scenario}/transformation/generate'
*/
const generateForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::generate
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:34
* @route '/api/scenarios/{scenario}/transformation/generate'
*/
generateForm.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generate.url(args, options),
    method: 'post',
})

generate.form = generateForm

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listPhases
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:49
* @route '/api/scenarios/{scenario}/transformation/phases'
*/
export const listPhases = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listPhases.url(args, options),
    method: 'get',
})

listPhases.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/transformation/phases',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listPhases
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:49
* @route '/api/scenarios/{scenario}/transformation/phases'
*/
listPhases.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return listPhases.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listPhases
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:49
* @route '/api/scenarios/{scenario}/transformation/phases'
*/
listPhases.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listPhases.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listPhases
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:49
* @route '/api/scenarios/{scenario}/transformation/phases'
*/
listPhases.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listPhases.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listPhases
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:49
* @route '/api/scenarios/{scenario}/transformation/phases'
*/
const listPhasesForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listPhases.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listPhases
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:49
* @route '/api/scenarios/{scenario}/transformation/phases'
*/
listPhasesForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listPhases.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listPhases
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:49
* @route '/api/scenarios/{scenario}/transformation/phases'
*/
listPhasesForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listPhases.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

listPhases.form = listPhasesForm

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updatePhase
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:71
* @route '/api/phases/{phase}'
*/
export const updatePhase = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatePhase.url(args, options),
    method: 'patch',
})

updatePhase.definition = {
    methods: ["patch"],
    url: '/api/phases/{phase}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updatePhase
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:71
* @route '/api/phases/{phase}'
*/
updatePhase.url = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { phase: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { phase: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            phase: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        phase: typeof args.phase === 'object'
        ? args.phase.id
        : args.phase,
    }

    return updatePhase.definition.url
            .replace('{phase}', parsedArgs.phase.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updatePhase
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:71
* @route '/api/phases/{phase}'
*/
updatePhase.patch = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatePhase.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updatePhase
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:71
* @route '/api/phases/{phase}'
*/
const updatePhaseForm = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatePhase.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updatePhase
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:71
* @route '/api/phases/{phase}'
*/
updatePhaseForm.patch = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatePhase.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updatePhase.form = updatePhaseForm

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listTasks
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:85
* @route '/api/phases/{phase}/tasks'
*/
export const listTasks = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listTasks.url(args, options),
    method: 'get',
})

listTasks.definition = {
    methods: ["get","head"],
    url: '/api/phases/{phase}/tasks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listTasks
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:85
* @route '/api/phases/{phase}/tasks'
*/
listTasks.url = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { phase: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { phase: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            phase: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        phase: typeof args.phase === 'object'
        ? args.phase.id
        : args.phase,
    }

    return listTasks.definition.url
            .replace('{phase}', parsedArgs.phase.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listTasks
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:85
* @route '/api/phases/{phase}/tasks'
*/
listTasks.get = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: listTasks.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listTasks
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:85
* @route '/api/phases/{phase}/tasks'
*/
listTasks.head = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: listTasks.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listTasks
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:85
* @route '/api/phases/{phase}/tasks'
*/
const listTasksForm = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listTasks.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listTasks
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:85
* @route '/api/phases/{phase}/tasks'
*/
listTasksForm.get = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listTasks.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::listTasks
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:85
* @route '/api/phases/{phase}/tasks'
*/
listTasksForm.head = (args: { phase: number | { id: number } } | [phase: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: listTasks.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

listTasks.form = listTasksForm

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::createTask
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:107
* @route '/api/scenarios/{scenario}/transformation/tasks'
*/
export const createTask = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createTask.url(args, options),
    method: 'post',
})

createTask.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenario}/transformation/tasks',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::createTask
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:107
* @route '/api/scenarios/{scenario}/transformation/tasks'
*/
createTask.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return createTask.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::createTask
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:107
* @route '/api/scenarios/{scenario}/transformation/tasks'
*/
createTask.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createTask.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::createTask
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:107
* @route '/api/scenarios/{scenario}/transformation/tasks'
*/
const createTaskForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createTask.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::createTask
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:107
* @route '/api/scenarios/{scenario}/transformation/tasks'
*/
createTaskForm.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createTask.url(args, options),
    method: 'post',
})

createTask.form = createTaskForm

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updateTask
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:128
* @route '/api/tasks/{task}'
*/
export const updateTask = (args: { task: number | { id: number } } | [task: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateTask.url(args, options),
    method: 'patch',
})

updateTask.definition = {
    methods: ["patch"],
    url: '/api/tasks/{task}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updateTask
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:128
* @route '/api/tasks/{task}'
*/
updateTask.url = (args: { task: number | { id: number } } | [task: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { task: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { task: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            task: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        task: typeof args.task === 'object'
        ? args.task.id
        : args.task,
    }

    return updateTask.definition.url
            .replace('{task}', parsedArgs.task.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updateTask
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:128
* @route '/api/tasks/{task}'
*/
updateTask.patch = (args: { task: number | { id: number } } | [task: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateTask.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updateTask
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:128
* @route '/api/tasks/{task}'
*/
const updateTaskForm = (args: { task: number | { id: number } } | [task: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateTask.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updateTask
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:128
* @route '/api/tasks/{task}'
*/
updateTaskForm.patch = (args: { task: number | { id: number } } | [task: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateTask.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateTask.form = updateTaskForm

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updateStatus
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:142
* @route '/api/tasks/{task}/status'
*/
export const updateStatus = (args: { task: number | { id: number } } | [task: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

updateStatus.definition = {
    methods: ["patch"],
    url: '/api/tasks/{task}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updateStatus
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:142
* @route '/api/tasks/{task}/status'
*/
updateStatus.url = (args: { task: number | { id: number } } | [task: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { task: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { task: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            task: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        task: typeof args.task === 'object'
        ? args.task.id
        : args.task,
    }

    return updateStatus.definition.url
            .replace('{task}', parsedArgs.task.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updateStatus
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:142
* @route '/api/tasks/{task}/status'
*/
updateStatus.patch = (args: { task: number | { id: number } } | [task: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updateStatus
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:142
* @route '/api/tasks/{task}/status'
*/
const updateStatusForm = (args: { task: number | { id: number } } | [task: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::updateStatus
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:142
* @route '/api/tasks/{task}/status'
*/
updateStatusForm.patch = (args: { task: number | { id: number } } | [task: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateStatus.form = updateStatusForm

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::blockers
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:162
* @route '/api/scenarios/{scenario}/transformation/blockers'
*/
export const blockers = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: blockers.url(args, options),
    method: 'get',
})

blockers.definition = {
    methods: ["get","head"],
    url: '/api/scenarios/{scenario}/transformation/blockers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::blockers
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:162
* @route '/api/scenarios/{scenario}/transformation/blockers'
*/
blockers.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return blockers.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::blockers
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:162
* @route '/api/scenarios/{scenario}/transformation/blockers'
*/
blockers.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: blockers.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::blockers
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:162
* @route '/api/scenarios/{scenario}/transformation/blockers'
*/
blockers.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: blockers.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::blockers
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:162
* @route '/api/scenarios/{scenario}/transformation/blockers'
*/
const blockersForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: blockers.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::blockers
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:162
* @route '/api/scenarios/{scenario}/transformation/blockers'
*/
blockersForm.get = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: blockers.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::blockers
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:162
* @route '/api/scenarios/{scenario}/transformation/blockers'
*/
blockersForm.head = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: blockers.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

blockers.form = blockersForm

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::exportMethod
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:181
* @route '/api/scenarios/{scenario}/transformation/export'
*/
export const exportMethod = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: exportMethod.url(args, options),
    method: 'post',
})

exportMethod.definition = {
    methods: ["post"],
    url: '/api/scenarios/{scenario}/transformation/export',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::exportMethod
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:181
* @route '/api/scenarios/{scenario}/transformation/export'
*/
exportMethod.url = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return exportMethod.definition.url
            .replace('{scenario}', parsedArgs.scenario.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::exportMethod
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:181
* @route '/api/scenarios/{scenario}/transformation/export'
*/
exportMethod.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: exportMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::exportMethod
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:181
* @route '/api/scenarios/{scenario}/transformation/export'
*/
const exportMethodForm = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: exportMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TransformationRoadmapController::exportMethod
* @see app/Http/Controllers/Api/TransformationRoadmapController.php:181
* @route '/api/scenarios/{scenario}/transformation/export'
*/
exportMethodForm.post = (args: { scenario: number | { id: number } } | [scenario: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: exportMethod.url(args, options),
    method: 'post',
})

exportMethod.form = exportMethodForm

const roadmap = {
    get: Object.assign(get, get),
    generate: Object.assign(generate, generate),
    listPhases: Object.assign(listPhases, listPhases),
    updatePhase: Object.assign(updatePhase, updatePhase),
    listTasks: Object.assign(listTasks, listTasks),
    createTask: Object.assign(createTask, createTask),
    updateTask: Object.assign(updateTask, updateTask),
    updateStatus: Object.assign(updateStatus, updateStatus),
    blockers: Object.assign(blockers, blockers),
    export: Object.assign(exportMethod, exportMethod),
}

export default roadmap