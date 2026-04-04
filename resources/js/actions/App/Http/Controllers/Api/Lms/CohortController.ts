import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\CohortController::index
* @see app/Http/Controllers/Api/Lms/CohortController.php:19
* @route '/api/lms/cohorts'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/cohorts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::index
* @see app/Http/Controllers/Api/Lms/CohortController.php:19
* @route '/api/lms/cohorts'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::index
* @see app/Http/Controllers/Api/Lms/CohortController.php:19
* @route '/api/lms/cohorts'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::index
* @see app/Http/Controllers/Api/Lms/CohortController.php:19
* @route '/api/lms/cohorts'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::index
* @see app/Http/Controllers/Api/Lms/CohortController.php:19
* @route '/api/lms/cohorts'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::index
* @see app/Http/Controllers/Api/Lms/CohortController.php:19
* @route '/api/lms/cohorts'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::index
* @see app/Http/Controllers/Api/Lms/CohortController.php:19
* @route '/api/lms/cohorts'
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
* @see \App\Http\Controllers\Api\Lms\CohortController::store
* @see app/Http/Controllers/Api/Lms/CohortController.php:32
* @route '/api/lms/cohorts'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/cohorts',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::store
* @see app/Http/Controllers/Api/Lms/CohortController.php:32
* @route '/api/lms/cohorts'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::store
* @see app/Http/Controllers/Api/Lms/CohortController.php:32
* @route '/api/lms/cohorts'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::store
* @see app/Http/Controllers/Api/Lms/CohortController.php:32
* @route '/api/lms/cohorts'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::store
* @see app/Http/Controllers/Api/Lms/CohortController.php:32
* @route '/api/lms/cohorts'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::show
* @see app/Http/Controllers/Api/Lms/CohortController.php:59
* @route '/api/lms/cohorts/{cohort}'
*/
export const show = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/lms/cohorts/{cohort}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::show
* @see app/Http/Controllers/Api/Lms/CohortController.php:59
* @route '/api/lms/cohorts/{cohort}'
*/
show.url = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { cohort: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { cohort: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            cohort: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        cohort: typeof args.cohort === 'object'
        ? args.cohort.id
        : args.cohort,
    }

    return show.definition.url
            .replace('{cohort}', parsedArgs.cohort.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::show
* @see app/Http/Controllers/Api/Lms/CohortController.php:59
* @route '/api/lms/cohorts/{cohort}'
*/
show.get = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::show
* @see app/Http/Controllers/Api/Lms/CohortController.php:59
* @route '/api/lms/cohorts/{cohort}'
*/
show.head = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::show
* @see app/Http/Controllers/Api/Lms/CohortController.php:59
* @route '/api/lms/cohorts/{cohort}'
*/
const showForm = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::show
* @see app/Http/Controllers/Api/Lms/CohortController.php:59
* @route '/api/lms/cohorts/{cohort}'
*/
showForm.get = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::show
* @see app/Http/Controllers/Api/Lms/CohortController.php:59
* @route '/api/lms/cohorts/{cohort}'
*/
showForm.head = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::update
* @see app/Http/Controllers/Api/Lms/CohortController.php:70
* @route '/api/lms/cohorts/{cohort}'
*/
export const update = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/lms/cohorts/{cohort}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::update
* @see app/Http/Controllers/Api/Lms/CohortController.php:70
* @route '/api/lms/cohorts/{cohort}'
*/
update.url = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { cohort: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { cohort: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            cohort: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        cohort: typeof args.cohort === 'object'
        ? args.cohort.id
        : args.cohort,
    }

    return update.definition.url
            .replace('{cohort}', parsedArgs.cohort.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::update
* @see app/Http/Controllers/Api/Lms/CohortController.php:70
* @route '/api/lms/cohorts/{cohort}'
*/
update.put = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::update
* @see app/Http/Controllers/Api/Lms/CohortController.php:70
* @route '/api/lms/cohorts/{cohort}'
*/
const updateForm = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::update
* @see app/Http/Controllers/Api/Lms/CohortController.php:70
* @route '/api/lms/cohorts/{cohort}'
*/
updateForm.put = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\Lms\CohortController::addMembers
* @see app/Http/Controllers/Api/Lms/CohortController.php:90
* @route '/api/lms/cohorts/{cohort}/members'
*/
export const addMembers = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addMembers.url(args, options),
    method: 'post',
})

addMembers.definition = {
    methods: ["post"],
    url: '/api/lms/cohorts/{cohort}/members',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::addMembers
* @see app/Http/Controllers/Api/Lms/CohortController.php:90
* @route '/api/lms/cohorts/{cohort}/members'
*/
addMembers.url = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { cohort: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { cohort: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            cohort: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        cohort: typeof args.cohort === 'object'
        ? args.cohort.id
        : args.cohort,
    }

    return addMembers.definition.url
            .replace('{cohort}', parsedArgs.cohort.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::addMembers
* @see app/Http/Controllers/Api/Lms/CohortController.php:90
* @route '/api/lms/cohorts/{cohort}/members'
*/
addMembers.post = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addMembers.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::addMembers
* @see app/Http/Controllers/Api/Lms/CohortController.php:90
* @route '/api/lms/cohorts/{cohort}/members'
*/
const addMembersForm = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: addMembers.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::addMembers
* @see app/Http/Controllers/Api/Lms/CohortController.php:90
* @route '/api/lms/cohorts/{cohort}/members'
*/
addMembersForm.post = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: addMembers.url(args, options),
    method: 'post',
})

addMembers.form = addMembersForm

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::removeMember
* @see app/Http/Controllers/Api/Lms/CohortController.php:107
* @route '/api/lms/cohorts/{cohort}/remove-member'
*/
export const removeMember = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeMember.url(args, options),
    method: 'post',
})

removeMember.definition = {
    methods: ["post"],
    url: '/api/lms/cohorts/{cohort}/remove-member',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::removeMember
* @see app/Http/Controllers/Api/Lms/CohortController.php:107
* @route '/api/lms/cohorts/{cohort}/remove-member'
*/
removeMember.url = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { cohort: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { cohort: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            cohort: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        cohort: typeof args.cohort === 'object'
        ? args.cohort.id
        : args.cohort,
    }

    return removeMember.definition.url
            .replace('{cohort}', parsedArgs.cohort.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::removeMember
* @see app/Http/Controllers/Api/Lms/CohortController.php:107
* @route '/api/lms/cohorts/{cohort}/remove-member'
*/
removeMember.post = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: removeMember.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::removeMember
* @see app/Http/Controllers/Api/Lms/CohortController.php:107
* @route '/api/lms/cohorts/{cohort}/remove-member'
*/
const removeMemberForm = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: removeMember.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::removeMember
* @see app/Http/Controllers/Api/Lms/CohortController.php:107
* @route '/api/lms/cohorts/{cohort}/remove-member'
*/
removeMemberForm.post = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: removeMember.url(args, options),
    method: 'post',
})

removeMember.form = removeMemberForm

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::progress
* @see app/Http/Controllers/Api/Lms/CohortController.php:118
* @route '/api/lms/cohorts/{cohort}/progress'
*/
export const progress = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: progress.url(args, options),
    method: 'get',
})

progress.definition = {
    methods: ["get","head"],
    url: '/api/lms/cohorts/{cohort}/progress',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::progress
* @see app/Http/Controllers/Api/Lms/CohortController.php:118
* @route '/api/lms/cohorts/{cohort}/progress'
*/
progress.url = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { cohort: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { cohort: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            cohort: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        cohort: typeof args.cohort === 'object'
        ? args.cohort.id
        : args.cohort,
    }

    return progress.definition.url
            .replace('{cohort}', parsedArgs.cohort.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::progress
* @see app/Http/Controllers/Api/Lms/CohortController.php:118
* @route '/api/lms/cohorts/{cohort}/progress'
*/
progress.get = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: progress.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::progress
* @see app/Http/Controllers/Api/Lms/CohortController.php:118
* @route '/api/lms/cohorts/{cohort}/progress'
*/
progress.head = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: progress.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::progress
* @see app/Http/Controllers/Api/Lms/CohortController.php:118
* @route '/api/lms/cohorts/{cohort}/progress'
*/
const progressForm = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: progress.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::progress
* @see app/Http/Controllers/Api/Lms/CohortController.php:118
* @route '/api/lms/cohorts/{cohort}/progress'
*/
progressForm.get = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: progress.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CohortController::progress
* @see app/Http/Controllers/Api/Lms/CohortController.php:118
* @route '/api/lms/cohorts/{cohort}/progress'
*/
progressForm.head = (args: { cohort: number | { id: number } } | [cohort: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: progress.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

progress.form = progressForm

const CohortController = { index, store, show, update, addMembers, removeMember, progress }

export default CohortController