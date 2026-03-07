import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\AssessmentController::showByToken
* @see app/Http/Controllers/Api/AssessmentController.php:369
* @route '/api/assessments/feedback/{token}'
*/
export const showByToken = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showByToken.url(args, options),
    method: 'get',
})

showByToken.definition = {
    methods: ["get","head"],
    url: '/api/assessments/feedback/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::showByToken
* @see app/Http/Controllers/Api/AssessmentController.php:369
* @route '/api/assessments/feedback/{token}'
*/
showByToken.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return showByToken.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::showByToken
* @see app/Http/Controllers/Api/AssessmentController.php:369
* @route '/api/assessments/feedback/{token}'
*/
showByToken.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showByToken.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::showByToken
* @see app/Http/Controllers/Api/AssessmentController.php:369
* @route '/api/assessments/feedback/{token}'
*/
showByToken.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showByToken.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::submitFeedbackGuest
* @see app/Http/Controllers/Api/AssessmentController.php:406
* @route '/api/assessments/feedback/submit-guest'
*/
export const submitFeedbackGuest = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitFeedbackGuest.url(options),
    method: 'post',
})

submitFeedbackGuest.definition = {
    methods: ["post"],
    url: '/api/assessments/feedback/submit-guest',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::submitFeedbackGuest
* @see app/Http/Controllers/Api/AssessmentController.php:406
* @route '/api/assessments/feedback/submit-guest'
*/
submitFeedbackGuest.url = (options?: RouteQueryOptions) => {
    return submitFeedbackGuest.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::submitFeedbackGuest
* @see app/Http/Controllers/Api/AssessmentController.php:406
* @route '/api/assessments/feedback/submit-guest'
*/
submitFeedbackGuest.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitFeedbackGuest.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::startSession
* @see app/Http/Controllers/Api/AssessmentController.php:27
* @route '/api/strategic-planning/assessments/sessions'
*/
export const startSession = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: startSession.url(options),
    method: 'post',
})

startSession.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/assessments/sessions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::startSession
* @see app/Http/Controllers/Api/AssessmentController.php:27
* @route '/api/strategic-planning/assessments/sessions'
*/
startSession.url = (options?: RouteQueryOptions) => {
    return startSession.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::startSession
* @see app/Http/Controllers/Api/AssessmentController.php:27
* @route '/api/strategic-planning/assessments/sessions'
*/
startSession.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: startSession.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::getSession
* @see app/Http/Controllers/Api/AssessmentController.php:53
* @route '/api/strategic-planning/assessments/sessions/{id}'
*/
export const getSession = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSession.url(args, options),
    method: 'get',
})

getSession.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/assessments/sessions/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::getSession
* @see app/Http/Controllers/Api/AssessmentController.php:53
* @route '/api/strategic-planning/assessments/sessions/{id}'
*/
getSession.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getSession.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::getSession
* @see app/Http/Controllers/Api/AssessmentController.php:53
* @route '/api/strategic-planning/assessments/sessions/{id}'
*/
getSession.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getSession.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::getSession
* @see app/Http/Controllers/Api/AssessmentController.php:53
* @route '/api/strategic-planning/assessments/sessions/{id}'
*/
getSession.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getSession.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::sendMessage
* @see app/Http/Controllers/Api/AssessmentController.php:65
* @route '/api/strategic-planning/assessments/sessions/{id}/messages'
*/
export const sendMessage = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendMessage.url(args, options),
    method: 'post',
})

sendMessage.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/assessments/sessions/{id}/messages',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::sendMessage
* @see app/Http/Controllers/Api/AssessmentController.php:65
* @route '/api/strategic-planning/assessments/sessions/{id}/messages'
*/
sendMessage.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return sendMessage.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::sendMessage
* @see app/Http/Controllers/Api/AssessmentController.php:65
* @route '/api/strategic-planning/assessments/sessions/{id}/messages'
*/
sendMessage.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sendMessage.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::analyze
* @see app/Http/Controllers/Api/AssessmentController.php:101
* @route '/api/strategic-planning/assessments/sessions/{id}/analyze'
*/
export const analyze = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyze.url(args, options),
    method: 'post',
})

analyze.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/assessments/sessions/{id}/analyze',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::analyze
* @see app/Http/Controllers/Api/AssessmentController.php:101
* @route '/api/strategic-planning/assessments/sessions/{id}/analyze'
*/
analyze.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return analyze.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::analyze
* @see app/Http/Controllers/Api/AssessmentController.php:101
* @route '/api/strategic-planning/assessments/sessions/{id}/analyze'
*/
analyze.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyze.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::triangulate360
* @see app/Http/Controllers/Api/AssessmentController.php:531
* @route '/api/strategic-planning/assessments/{peopleId}/triangulate'
*/
export const triangulate360 = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: triangulate360.url(args, options),
    method: 'post',
})

triangulate360.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/assessments/{peopleId}/triangulate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::triangulate360
* @see app/Http/Controllers/Api/AssessmentController.php:531
* @route '/api/strategic-planning/assessments/{peopleId}/triangulate'
*/
triangulate360.url = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { peopleId: args }
    }

    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        peopleId: args.peopleId,
    }

    return triangulate360.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::triangulate360
* @see app/Http/Controllers/Api/AssessmentController.php:531
* @route '/api/strategic-planning/assessments/{peopleId}/triangulate'
*/
triangulate360.post = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: triangulate360.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::requestFeedback
* @see app/Http/Controllers/Api/AssessmentController.php:223
* @route '/api/strategic-planning/assessments/feedback/request'
*/
export const requestFeedback = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestFeedback.url(options),
    method: 'post',
})

requestFeedback.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/assessments/feedback/request',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::requestFeedback
* @see app/Http/Controllers/Api/AssessmentController.php:223
* @route '/api/strategic-planning/assessments/feedback/request'
*/
requestFeedback.url = (options?: RouteQueryOptions) => {
    return requestFeedback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::requestFeedback
* @see app/Http/Controllers/Api/AssessmentController.php:223
* @route '/api/strategic-planning/assessments/feedback/request'
*/
requestFeedback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: requestFeedback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::submitFeedback
* @see app/Http/Controllers/Api/AssessmentController.php:290
* @route '/api/strategic-planning/assessments/feedback/submit'
*/
export const submitFeedback = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitFeedback.url(options),
    method: 'post',
})

submitFeedback.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/assessments/feedback/submit',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::submitFeedback
* @see app/Http/Controllers/Api/AssessmentController.php:290
* @route '/api/strategic-planning/assessments/feedback/submit'
*/
submitFeedback.url = (options?: RouteQueryOptions) => {
    return submitFeedback.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::submitFeedback
* @see app/Http/Controllers/Api/AssessmentController.php:290
* @route '/api/strategic-planning/assessments/feedback/submit'
*/
submitFeedback.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: submitFeedback.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::getPendingRequests
* @see app/Http/Controllers/Api/AssessmentController.php:351
* @route '/api/strategic-planning/assessments/feedback/pending'
*/
export const getPendingRequests = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPendingRequests.url(options),
    method: 'get',
})

getPendingRequests.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/assessments/feedback/pending',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::getPendingRequests
* @see app/Http/Controllers/Api/AssessmentController.php:351
* @route '/api/strategic-planning/assessments/feedback/pending'
*/
getPendingRequests.url = (options?: RouteQueryOptions) => {
    return getPendingRequests.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::getPendingRequests
* @see app/Http/Controllers/Api/AssessmentController.php:351
* @route '/api/strategic-planning/assessments/feedback/pending'
*/
getPendingRequests.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPendingRequests.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::getPendingRequests
* @see app/Http/Controllers/Api/AssessmentController.php:351
* @route '/api/strategic-planning/assessments/feedback/pending'
*/
getPendingRequests.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getPendingRequests.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::showExternalForm
* @see app/Http/Controllers/Api/AssessmentController.php:385
* @route '/assessments/feedback/{token}'
*/
export const showExternalForm = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showExternalForm.url(args, options),
    method: 'get',
})

showExternalForm.definition = {
    methods: ["get","head"],
    url: '/assessments/feedback/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\AssessmentController::showExternalForm
* @see app/Http/Controllers/Api/AssessmentController.php:385
* @route '/assessments/feedback/{token}'
*/
showExternalForm.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    if (Array.isArray(args)) {
        args = {
            token: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        token: args.token,
    }

    return showExternalForm.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\AssessmentController::showExternalForm
* @see app/Http/Controllers/Api/AssessmentController.php:385
* @route '/assessments/feedback/{token}'
*/
showExternalForm.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showExternalForm.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\AssessmentController::showExternalForm
* @see app/Http/Controllers/Api/AssessmentController.php:385
* @route '/assessments/feedback/{token}'
*/
showExternalForm.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showExternalForm.url(args, options),
    method: 'head',
})

const AssessmentController = { showByToken, submitFeedbackGuest, startSession, getSession, sendMessage, analyze, triangulate360, requestFeedback, submitFeedback, getPendingRequests, showExternalForm }

export default AssessmentController