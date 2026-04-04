import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::log
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:17
* @route '/api/lms/audit/log'
*/
export const log = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: log.url(options),
    method: 'post',
})

log.definition = {
    methods: ["post"],
    url: '/api/lms/audit/log',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::log
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:17
* @route '/api/lms/audit/log'
*/
log.url = (options?: RouteQueryOptions) => {
    return log.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::log
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:17
* @route '/api/lms/audit/log'
*/
log.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: log.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::log
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:17
* @route '/api/lms/audit/log'
*/
const logForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: log.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::log
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:17
* @route '/api/lms/audit/log'
*/
logForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: log.url(options),
    method: 'post',
})

log.form = logForm

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::lessonHistory
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:43
* @route '/api/lms/audit/enrollments/{enrollment}/lessons/{lesson}'
*/
export const lessonHistory = (args: { enrollment: string | number, lesson: string | number } | [enrollment: string | number, lesson: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: lessonHistory.url(args, options),
    method: 'get',
})

lessonHistory.definition = {
    methods: ["get","head"],
    url: '/api/lms/audit/enrollments/{enrollment}/lessons/{lesson}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::lessonHistory
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:43
* @route '/api/lms/audit/enrollments/{enrollment}/lessons/{lesson}'
*/
lessonHistory.url = (args: { enrollment: string | number, lesson: string | number } | [enrollment: string | number, lesson: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            enrollment: args[0],
            lesson: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        enrollment: args.enrollment,
        lesson: args.lesson,
    }

    return lessonHistory.definition.url
            .replace('{enrollment}', parsedArgs.enrollment.toString())
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::lessonHistory
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:43
* @route '/api/lms/audit/enrollments/{enrollment}/lessons/{lesson}'
*/
lessonHistory.get = (args: { enrollment: string | number, lesson: string | number } | [enrollment: string | number, lesson: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: lessonHistory.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::lessonHistory
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:43
* @route '/api/lms/audit/enrollments/{enrollment}/lessons/{lesson}'
*/
lessonHistory.head = (args: { enrollment: string | number, lesson: string | number } | [enrollment: string | number, lesson: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: lessonHistory.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::lessonHistory
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:43
* @route '/api/lms/audit/enrollments/{enrollment}/lessons/{lesson}'
*/
const lessonHistoryForm = (args: { enrollment: string | number, lesson: string | number } | [enrollment: string | number, lesson: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: lessonHistory.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::lessonHistory
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:43
* @route '/api/lms/audit/enrollments/{enrollment}/lessons/{lesson}'
*/
lessonHistoryForm.get = (args: { enrollment: string | number, lesson: string | number } | [enrollment: string | number, lesson: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: lessonHistory.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::lessonHistory
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:43
* @route '/api/lms/audit/enrollments/{enrollment}/lessons/{lesson}'
*/
lessonHistoryForm.head = (args: { enrollment: string | number, lesson: string | number } | [enrollment: string | number, lesson: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: lessonHistory.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

lessonHistory.form = lessonHistoryForm

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::userTimeline
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:50
* @route '/api/lms/audit/enrollments/{enrollment}/timeline'
*/
export const userTimeline = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: userTimeline.url(args, options),
    method: 'get',
})

userTimeline.definition = {
    methods: ["get","head"],
    url: '/api/lms/audit/enrollments/{enrollment}/timeline',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::userTimeline
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:50
* @route '/api/lms/audit/enrollments/{enrollment}/timeline'
*/
userTimeline.url = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { enrollment: args }
    }

    if (Array.isArray(args)) {
        args = {
            enrollment: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        enrollment: args.enrollment,
    }

    return userTimeline.definition.url
            .replace('{enrollment}', parsedArgs.enrollment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::userTimeline
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:50
* @route '/api/lms/audit/enrollments/{enrollment}/timeline'
*/
userTimeline.get = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: userTimeline.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::userTimeline
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:50
* @route '/api/lms/audit/enrollments/{enrollment}/timeline'
*/
userTimeline.head = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: userTimeline.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::userTimeline
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:50
* @route '/api/lms/audit/enrollments/{enrollment}/timeline'
*/
const userTimelineForm = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: userTimeline.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::userTimeline
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:50
* @route '/api/lms/audit/enrollments/{enrollment}/timeline'
*/
userTimelineForm.get = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: userTimeline.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::userTimeline
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:50
* @route '/api/lms/audit/enrollments/{enrollment}/timeline'
*/
userTimelineForm.head = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: userTimeline.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

userTimeline.form = userTimelineForm

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::courseSummary
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:57
* @route '/api/lms/audit/courses/{course}/summary'
*/
export const courseSummary = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: courseSummary.url(args, options),
    method: 'get',
})

courseSummary.definition = {
    methods: ["get","head"],
    url: '/api/lms/audit/courses/{course}/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::courseSummary
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:57
* @route '/api/lms/audit/courses/{course}/summary'
*/
courseSummary.url = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { course: args }
    }

    if (Array.isArray(args)) {
        args = {
            course: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        course: args.course,
    }

    return courseSummary.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::courseSummary
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:57
* @route '/api/lms/audit/courses/{course}/summary'
*/
courseSummary.get = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: courseSummary.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::courseSummary
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:57
* @route '/api/lms/audit/courses/{course}/summary'
*/
courseSummary.head = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: courseSummary.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::courseSummary
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:57
* @route '/api/lms/audit/courses/{course}/summary'
*/
const courseSummaryForm = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: courseSummary.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::courseSummary
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:57
* @route '/api/lms/audit/courses/{course}/summary'
*/
courseSummaryForm.get = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: courseSummary.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::courseSummary
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:57
* @route '/api/lms/audit/courses/{course}/summary'
*/
courseSummaryForm.head = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: courseSummary.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

courseSummary.form = courseSummaryForm

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::exportMethod
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:65
* @route '/api/lms/audit/enrollments/{enrollment}/export'
*/
export const exportMethod = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
})

exportMethod.definition = {
    methods: ["get","head"],
    url: '/api/lms/audit/enrollments/{enrollment}/export',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::exportMethod
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:65
* @route '/api/lms/audit/enrollments/{enrollment}/export'
*/
exportMethod.url = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { enrollment: args }
    }

    if (Array.isArray(args)) {
        args = {
            enrollment: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        enrollment: args.enrollment,
    }

    return exportMethod.definition.url
            .replace('{enrollment}', parsedArgs.enrollment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::exportMethod
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:65
* @route '/api/lms/audit/enrollments/{enrollment}/export'
*/
exportMethod.get = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::exportMethod
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:65
* @route '/api/lms/audit/enrollments/{enrollment}/export'
*/
exportMethod.head = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportMethod.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::exportMethod
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:65
* @route '/api/lms/audit/enrollments/{enrollment}/export'
*/
const exportMethodForm = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::exportMethod
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:65
* @route '/api/lms/audit/enrollments/{enrollment}/export'
*/
exportMethodForm.get = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\LessonAuditController::exportMethod
* @see app/Http/Controllers/Api/Lms/LessonAuditController.php:65
* @route '/api/lms/audit/enrollments/{enrollment}/export'
*/
exportMethodForm.head = (args: { enrollment: string | number } | [enrollment: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportMethod.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportMethod.form = exportMethodForm

const LessonAuditController = { log, lessonHistory, userTimeline, courseSummary, exportMethod, export: exportMethod }

export default LessonAuditController