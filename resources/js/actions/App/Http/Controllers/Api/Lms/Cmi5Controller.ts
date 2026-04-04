import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::launch
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:16
* @route '/api/lms/cmi5/{package}/launch'
*/
export const launch = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: launch.url(args, options),
    method: 'post',
})

launch.definition = {
    methods: ["post"],
    url: '/api/lms/cmi5/{package}/launch',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::launch
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:16
* @route '/api/lms/cmi5/{package}/launch'
*/
launch.url = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { package: args }
    }

    if (Array.isArray(args)) {
        args = {
            package: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        package: args.package,
    }

    return launch.definition.url
            .replace('{package}', parsedArgs.package.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::launch
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:16
* @route '/api/lms/cmi5/{package}/launch'
*/
launch.post = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: launch.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::launch
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:16
* @route '/api/lms/cmi5/{package}/launch'
*/
const launchForm = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: launch.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::launch
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:16
* @route '/api/lms/cmi5/{package}/launch'
*/
launchForm.post = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: launch.url(args, options),
    method: 'post',
})

launch.form = launchForm

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::fetchUrl
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:43
* @route '/api/lms/cmi5/sessions/{session}/fetch'
*/
export const fetchUrl = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fetchUrl.url(args, options),
    method: 'get',
})

fetchUrl.definition = {
    methods: ["get","head"],
    url: '/api/lms/cmi5/sessions/{session}/fetch',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::fetchUrl
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:43
* @route '/api/lms/cmi5/sessions/{session}/fetch'
*/
fetchUrl.url = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return fetchUrl.definition.url
            .replace('{session}', parsedArgs.session.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::fetchUrl
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:43
* @route '/api/lms/cmi5/sessions/{session}/fetch'
*/
fetchUrl.get = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fetchUrl.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::fetchUrl
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:43
* @route '/api/lms/cmi5/sessions/{session}/fetch'
*/
fetchUrl.head = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: fetchUrl.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::fetchUrl
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:43
* @route '/api/lms/cmi5/sessions/{session}/fetch'
*/
const fetchUrlForm = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: fetchUrl.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::fetchUrl
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:43
* @route '/api/lms/cmi5/sessions/{session}/fetch'
*/
fetchUrlForm.get = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: fetchUrl.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::fetchUrl
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:43
* @route '/api/lms/cmi5/sessions/{session}/fetch'
*/
fetchUrlForm.head = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: fetchUrl.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

fetchUrl.form = fetchUrlForm

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::statement
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:62
* @route '/api/lms/cmi5/sessions/{session}/statement'
*/
export const statement = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: statement.url(args, options),
    method: 'post',
})

statement.definition = {
    methods: ["post"],
    url: '/api/lms/cmi5/sessions/{session}/statement',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::statement
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:62
* @route '/api/lms/cmi5/sessions/{session}/statement'
*/
statement.url = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return statement.definition.url
            .replace('{session}', parsedArgs.session.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::statement
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:62
* @route '/api/lms/cmi5/sessions/{session}/statement'
*/
statement.post = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: statement.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::statement
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:62
* @route '/api/lms/cmi5/sessions/{session}/statement'
*/
const statementForm = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: statement.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::statement
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:62
* @route '/api/lms/cmi5/sessions/{session}/statement'
*/
statementForm.post = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: statement.url(args, options),
    method: 'post',
})

statement.form = statementForm

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::authToken
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:80
* @route '/api/lms/cmi5/sessions/{session}/auth-token'
*/
export const authToken = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: authToken.url(args, options),
    method: 'get',
})

authToken.definition = {
    methods: ["get","head"],
    url: '/api/lms/cmi5/sessions/{session}/auth-token',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::authToken
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:80
* @route '/api/lms/cmi5/sessions/{session}/auth-token'
*/
authToken.url = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return authToken.definition.url
            .replace('{session}', parsedArgs.session.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::authToken
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:80
* @route '/api/lms/cmi5/sessions/{session}/auth-token'
*/
authToken.get = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: authToken.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::authToken
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:80
* @route '/api/lms/cmi5/sessions/{session}/auth-token'
*/
authToken.head = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: authToken.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::authToken
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:80
* @route '/api/lms/cmi5/sessions/{session}/auth-token'
*/
const authTokenForm = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authToken.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::authToken
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:80
* @route '/api/lms/cmi5/sessions/{session}/auth-token'
*/
authTokenForm.get = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authToken.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::authToken
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:80
* @route '/api/lms/cmi5/sessions/{session}/auth-token'
*/
authTokenForm.head = (args: { session: string | number } | [session: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: authToken.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

authToken.form = authTokenForm

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::sessions
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:89
* @route '/api/lms/cmi5/{package}/sessions'
*/
export const sessions = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sessions.url(args, options),
    method: 'get',
})

sessions.definition = {
    methods: ["get","head"],
    url: '/api/lms/cmi5/{package}/sessions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::sessions
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:89
* @route '/api/lms/cmi5/{package}/sessions'
*/
sessions.url = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { package: args }
    }

    if (Array.isArray(args)) {
        args = {
            package: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        package: args.package,
    }

    return sessions.definition.url
            .replace('{package}', parsedArgs.package.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::sessions
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:89
* @route '/api/lms/cmi5/{package}/sessions'
*/
sessions.get = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sessions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::sessions
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:89
* @route '/api/lms/cmi5/{package}/sessions'
*/
sessions.head = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sessions.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::sessions
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:89
* @route '/api/lms/cmi5/{package}/sessions'
*/
const sessionsForm = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sessions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::sessions
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:89
* @route '/api/lms/cmi5/{package}/sessions'
*/
sessionsForm.get = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sessions.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\Cmi5Controller::sessions
* @see app/Http/Controllers/Api/Lms/Cmi5Controller.php:89
* @route '/api/lms/cmi5/{package}/sessions'
*/
sessionsForm.head = (args: { package: string | number } | [packageParam: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sessions.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

sessions.form = sessionsForm

const Cmi5Controller = { launch, fetchUrl, statement, authToken, sessions }

export default Cmi5Controller