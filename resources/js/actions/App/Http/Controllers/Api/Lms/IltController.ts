import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\IltController::index
* @see app/Http/Controllers/Api/Lms/IltController.php:18
* @route '/api/lms/sessions'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/sessions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\IltController::index
* @see app/Http/Controllers/Api/Lms/IltController.php:18
* @route '/api/lms/sessions'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\IltController::index
* @see app/Http/Controllers/Api/Lms/IltController.php:18
* @route '/api/lms/sessions'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::index
* @see app/Http/Controllers/Api/Lms/IltController.php:18
* @route '/api/lms/sessions'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::index
* @see app/Http/Controllers/Api/Lms/IltController.php:18
* @route '/api/lms/sessions'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::index
* @see app/Http/Controllers/Api/Lms/IltController.php:18
* @route '/api/lms/sessions'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::index
* @see app/Http/Controllers/Api/Lms/IltController.php:18
* @route '/api/lms/sessions'
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
* @see \App\Http\Controllers\Api\Lms\IltController::store
* @see app/Http/Controllers/Api/Lms/IltController.php:32
* @route '/api/lms/sessions'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/lms/sessions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\IltController::store
* @see app/Http/Controllers/Api/Lms/IltController.php:32
* @route '/api/lms/sessions'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\IltController::store
* @see app/Http/Controllers/Api/Lms/IltController.php:32
* @route '/api/lms/sessions'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::store
* @see app/Http/Controllers/Api/Lms/IltController.php:32
* @route '/api/lms/sessions'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::store
* @see app/Http/Controllers/Api/Lms/IltController.php:32
* @route '/api/lms/sessions'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\Lms\IltController::show
* @see app/Http/Controllers/Api/Lms/IltController.php:61
* @route '/api/lms/sessions/{session}'
*/
export const show = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/lms/sessions/{session}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\IltController::show
* @see app/Http/Controllers/Api/Lms/IltController.php:61
* @route '/api/lms/sessions/{session}'
*/
show.url = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { session: args }
    }

    if (Array.isArray(args)) {
        args = {
            session: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        session: args.session,
    }

    return show.definition.url
            .replace('{session}', parsedArgs.session.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\IltController::show
* @see app/Http/Controllers/Api/Lms/IltController.php:61
* @route '/api/lms/sessions/{session}'
*/
show.get = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::show
* @see app/Http/Controllers/Api/Lms/IltController.php:61
* @route '/api/lms/sessions/{session}'
*/
show.head = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::show
* @see app/Http/Controllers/Api/Lms/IltController.php:61
* @route '/api/lms/sessions/{session}'
*/
const showForm = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::show
* @see app/Http/Controllers/Api/Lms/IltController.php:61
* @route '/api/lms/sessions/{session}'
*/
showForm.get = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::show
* @see app/Http/Controllers/Api/Lms/IltController.php:61
* @route '/api/lms/sessions/{session}'
*/
showForm.head = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\Api\Lms\IltController::update
* @see app/Http/Controllers/Api/Lms/IltController.php:69
* @route '/api/lms/sessions/{session}'
*/
export const update = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/lms/sessions/{session}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Lms\IltController::update
* @see app/Http/Controllers/Api/Lms/IltController.php:69
* @route '/api/lms/sessions/{session}'
*/
update.url = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { session: args }
    }

    if (Array.isArray(args)) {
        args = {
            session: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        session: args.session,
    }

    return update.definition.url
            .replace('{session}', parsedArgs.session.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\IltController::update
* @see app/Http/Controllers/Api/Lms/IltController.php:69
* @route '/api/lms/sessions/{session}'
*/
update.put = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::update
* @see app/Http/Controllers/Api/Lms/IltController.php:69
* @route '/api/lms/sessions/{session}'
*/
const updateForm = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::update
* @see app/Http/Controllers/Api/Lms/IltController.php:69
* @route '/api/lms/sessions/{session}'
*/
updateForm.put = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\Api\Lms\IltController::destroy
* @see app/Http/Controllers/Api/Lms/IltController.php:90
* @route '/api/lms/sessions/{session}'
*/
export const destroy = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/lms/sessions/{session}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\Lms\IltController::destroy
* @see app/Http/Controllers/Api/Lms/IltController.php:90
* @route '/api/lms/sessions/{session}'
*/
destroy.url = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { session: args }
    }

    if (Array.isArray(args)) {
        args = {
            session: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        session: args.session,
    }

    return destroy.definition.url
            .replace('{session}', parsedArgs.session.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\IltController::destroy
* @see app/Http/Controllers/Api/Lms/IltController.php:90
* @route '/api/lms/sessions/{session}'
*/
destroy.delete = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::destroy
* @see app/Http/Controllers/Api/Lms/IltController.php:90
* @route '/api/lms/sessions/{session}'
*/
const destroyForm = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::destroy
* @see app/Http/Controllers/Api/Lms/IltController.php:90
* @route '/api/lms/sessions/{session}'
*/
destroyForm.delete = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\Api\Lms\IltController::register
* @see app/Http/Controllers/Api/Lms/IltController.php:98
* @route '/api/lms/sessions/{session}/register'
*/
export const register = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: register.url(args, options),
    method: 'post',
})

register.definition = {
    methods: ["post"],
    url: '/api/lms/sessions/{session}/register',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\IltController::register
* @see app/Http/Controllers/Api/Lms/IltController.php:98
* @route '/api/lms/sessions/{session}/register'
*/
register.url = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { session: args }
    }

    if (Array.isArray(args)) {
        args = {
            session: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        session: args.session,
    }

    return register.definition.url
            .replace('{session}', parsedArgs.session.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\IltController::register
* @see app/Http/Controllers/Api/Lms/IltController.php:98
* @route '/api/lms/sessions/{session}/register'
*/
register.post = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: register.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::register
* @see app/Http/Controllers/Api/Lms/IltController.php:98
* @route '/api/lms/sessions/{session}/register'
*/
const registerForm = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: register.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::register
* @see app/Http/Controllers/Api/Lms/IltController.php:98
* @route '/api/lms/sessions/{session}/register'
*/
registerForm.post = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: register.url(args, options),
    method: 'post',
})

register.form = registerForm

/**
* @see \App\Http\Controllers\Api\Lms\IltController::cancelRegistration
* @see app/Http/Controllers/Api/Lms/IltController.php:111
* @route '/api/lms/sessions/{session}/cancel-registration'
*/
export const cancelRegistration = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancelRegistration.url(args, options),
    method: 'post',
})

cancelRegistration.definition = {
    methods: ["post"],
    url: '/api/lms/sessions/{session}/cancel-registration',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\IltController::cancelRegistration
* @see app/Http/Controllers/Api/Lms/IltController.php:111
* @route '/api/lms/sessions/{session}/cancel-registration'
*/
cancelRegistration.url = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { session: args }
    }

    if (Array.isArray(args)) {
        args = {
            session: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        session: args.session,
    }

    return cancelRegistration.definition.url
            .replace('{session}', parsedArgs.session.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\IltController::cancelRegistration
* @see app/Http/Controllers/Api/Lms/IltController.php:111
* @route '/api/lms/sessions/{session}/cancel-registration'
*/
cancelRegistration.post = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancelRegistration.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::cancelRegistration
* @see app/Http/Controllers/Api/Lms/IltController.php:111
* @route '/api/lms/sessions/{session}/cancel-registration'
*/
const cancelRegistrationForm = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancelRegistration.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::cancelRegistration
* @see app/Http/Controllers/Api/Lms/IltController.php:111
* @route '/api/lms/sessions/{session}/cancel-registration'
*/
cancelRegistrationForm.post = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancelRegistration.url(args, options),
    method: 'post',
})

cancelRegistration.form = cancelRegistrationForm

/**
* @see \App\Http\Controllers\Api\Lms\IltController::markAttendance
* @see app/Http/Controllers/Api/Lms/IltController.php:119
* @route '/api/lms/sessions/{session}/attendance'
*/
export const markAttendance = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAttendance.url(args, options),
    method: 'post',
})

markAttendance.definition = {
    methods: ["post"],
    url: '/api/lms/sessions/{session}/attendance',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\IltController::markAttendance
* @see app/Http/Controllers/Api/Lms/IltController.php:119
* @route '/api/lms/sessions/{session}/attendance'
*/
markAttendance.url = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { session: args }
    }

    if (Array.isArray(args)) {
        args = {
            session: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        session: args.session,
    }

    return markAttendance.definition.url
            .replace('{session}', parsedArgs.session.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\IltController::markAttendance
* @see app/Http/Controllers/Api/Lms/IltController.php:119
* @route '/api/lms/sessions/{session}/attendance'
*/
markAttendance.post = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: markAttendance.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::markAttendance
* @see app/Http/Controllers/Api/Lms/IltController.php:119
* @route '/api/lms/sessions/{session}/attendance'
*/
const markAttendanceForm = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markAttendance.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::markAttendance
* @see app/Http/Controllers/Api/Lms/IltController.php:119
* @route '/api/lms/sessions/{session}/attendance'
*/
markAttendanceForm.post = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: markAttendance.url(args, options),
    method: 'post',
})

markAttendance.form = markAttendanceForm

/**
* @see \App\Http\Controllers\Api\Lms\IltController::feedback
* @see app/Http/Controllers/Api/Lms/IltController.php:132
* @route '/api/lms/sessions/{session}/feedback'
*/
export const feedback = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: feedback.url(args, options),
    method: 'post',
})

feedback.definition = {
    methods: ["post"],
    url: '/api/lms/sessions/{session}/feedback',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\IltController::feedback
* @see app/Http/Controllers/Api/Lms/IltController.php:132
* @route '/api/lms/sessions/{session}/feedback'
*/
feedback.url = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { session: args }
    }

    if (Array.isArray(args)) {
        args = {
            session: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        session: args.session,
    }

    return feedback.definition.url
            .replace('{session}', parsedArgs.session.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\IltController::feedback
* @see app/Http/Controllers/Api/Lms/IltController.php:132
* @route '/api/lms/sessions/{session}/feedback'
*/
feedback.post = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: feedback.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::feedback
* @see app/Http/Controllers/Api/Lms/IltController.php:132
* @route '/api/lms/sessions/{session}/feedback'
*/
const feedbackForm = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: feedback.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\IltController::feedback
* @see app/Http/Controllers/Api/Lms/IltController.php:132
* @route '/api/lms/sessions/{session}/feedback'
*/
feedbackForm.post = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: feedback.url(args, options),
    method: 'post',
})

feedback.form = feedbackForm

const IltController = { index, store, show, update, destroy, register, cancelRegistration, markAttendance, feedback }

export default IltController