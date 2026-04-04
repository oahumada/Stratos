import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults, validateParameters } from './../../wayfinder'
import courses from './courses'
/**
* @see routes/web.php:159
* @route '/lms/course-designer'
*/
export const courseDesigner = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: courseDesigner.url(options),
    method: 'get',
})

courseDesigner.definition = {
    methods: ["get","head"],
    url: '/lms/course-designer',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:159
* @route '/lms/course-designer'
*/
courseDesigner.url = (options?: RouteQueryOptions) => {
    return courseDesigner.definition.url + queryParams(options)
}

/**
* @see routes/web.php:159
* @route '/lms/course-designer'
*/
courseDesigner.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: courseDesigner.url(options),
    method: 'get',
})

/**
* @see routes/web.php:159
* @route '/lms/course-designer'
*/
courseDesigner.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: courseDesigner.url(options),
    method: 'head',
})

/**
* @see routes/web.php:159
* @route '/lms/course-designer'
*/
const courseDesignerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: courseDesigner.url(options),
    method: 'get',
})

/**
* @see routes/web.php:159
* @route '/lms/course-designer'
*/
courseDesignerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: courseDesigner.url(options),
    method: 'get',
})

/**
* @see routes/web.php:159
* @route '/lms/course-designer'
*/
courseDesignerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: courseDesigner.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

courseDesigner.form = courseDesignerForm

/**
* @see routes/web.php:163
* @route '/lms/quiz/{quizId}'
*/
export const quizPlayer = (args: { quizId: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: quizPlayer.url(args, options),
    method: 'get',
})

quizPlayer.definition = {
    methods: ["get","head"],
    url: '/lms/quiz/{quizId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:163
* @route '/lms/quiz/{quizId}'
*/
quizPlayer.url = (args: { quizId: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { quizId: args }
    }

    if (Array.isArray(args)) {
        args = {
            quizId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        quizId: args.quizId,
    }

    return quizPlayer.definition.url
            .replace('{quizId}', parsedArgs.quizId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:163
* @route '/lms/quiz/{quizId}'
*/
quizPlayer.get = (args: { quizId: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: quizPlayer.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:163
* @route '/lms/quiz/{quizId}'
*/
quizPlayer.head = (args: { quizId: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: quizPlayer.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:163
* @route '/lms/quiz/{quizId}'
*/
const quizPlayerForm = (args: { quizId: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: quizPlayer.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:163
* @route '/lms/quiz/{quizId}'
*/
quizPlayerForm.get = (args: { quizId: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: quizPlayer.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:163
* @route '/lms/quiz/{quizId}'
*/
quizPlayerForm.head = (args: { quizId: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: quizPlayer.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

quizPlayer.form = quizPlayerForm

/**
* @see routes/web.php:167
* @route '/lms/quiz-builder/{quizId?}'
*/
export const quizBuilder = (args?: { quizId?: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: quizBuilder.url(args, options),
    method: 'get',
})

quizBuilder.definition = {
    methods: ["get","head"],
    url: '/lms/quiz-builder/{quizId?}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:167
* @route '/lms/quiz-builder/{quizId?}'
*/
quizBuilder.url = (args?: { quizId?: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { quizId: args }
    }

    if (Array.isArray(args)) {
        args = {
            quizId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    validateParameters(args, [
        "quizId",
    ])

    const parsedArgs = {
        quizId: args?.quizId,
    }

    return quizBuilder.definition.url
            .replace('{quizId?}', parsedArgs.quizId?.toString() ?? '')
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:167
* @route '/lms/quiz-builder/{quizId?}'
*/
quizBuilder.get = (args?: { quizId?: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: quizBuilder.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:167
* @route '/lms/quiz-builder/{quizId?}'
*/
quizBuilder.head = (args?: { quizId?: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: quizBuilder.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:167
* @route '/lms/quiz-builder/{quizId?}'
*/
const quizBuilderForm = (args?: { quizId?: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: quizBuilder.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:167
* @route '/lms/quiz-builder/{quizId?}'
*/
quizBuilderForm.get = (args?: { quizId?: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: quizBuilder.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:167
* @route '/lms/quiz-builder/{quizId?}'
*/
quizBuilderForm.head = (args?: { quizId?: string | number } | [quizId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: quizBuilder.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

quizBuilder.form = quizBuilderForm

/**
* @see routes/web.php:171
* @route '/lms/learning-paths'
*/
export const learningPaths = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: learningPaths.url(options),
    method: 'get',
})

learningPaths.definition = {
    methods: ["get","head"],
    url: '/lms/learning-paths',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:171
* @route '/lms/learning-paths'
*/
learningPaths.url = (options?: RouteQueryOptions) => {
    return learningPaths.definition.url + queryParams(options)
}

/**
* @see routes/web.php:171
* @route '/lms/learning-paths'
*/
learningPaths.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: learningPaths.url(options),
    method: 'get',
})

/**
* @see routes/web.php:171
* @route '/lms/learning-paths'
*/
learningPaths.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: learningPaths.url(options),
    method: 'head',
})

/**
* @see routes/web.php:171
* @route '/lms/learning-paths'
*/
const learningPathsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: learningPaths.url(options),
    method: 'get',
})

/**
* @see routes/web.php:171
* @route '/lms/learning-paths'
*/
learningPathsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: learningPaths.url(options),
    method: 'get',
})

/**
* @see routes/web.php:171
* @route '/lms/learning-paths'
*/
learningPathsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: learningPaths.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

learningPaths.form = learningPathsForm

/**
* @see routes/web.php:175
* @route '/lms/scorm/{packageId}'
*/
export const scormPlayer = (args: { packageId: string | number } | [packageId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scormPlayer.url(args, options),
    method: 'get',
})

scormPlayer.definition = {
    methods: ["get","head"],
    url: '/lms/scorm/{packageId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:175
* @route '/lms/scorm/{packageId}'
*/
scormPlayer.url = (args: { packageId: string | number } | [packageId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { packageId: args }
    }

    if (Array.isArray(args)) {
        args = {
            packageId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        packageId: args.packageId,
    }

    return scormPlayer.definition.url
            .replace('{packageId}', parsedArgs.packageId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:175
* @route '/lms/scorm/{packageId}'
*/
scormPlayer.get = (args: { packageId: string | number } | [packageId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: scormPlayer.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:175
* @route '/lms/scorm/{packageId}'
*/
scormPlayer.head = (args: { packageId: string | number } | [packageId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: scormPlayer.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:175
* @route '/lms/scorm/{packageId}'
*/
const scormPlayerForm = (args: { packageId: string | number } | [packageId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: scormPlayer.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:175
* @route '/lms/scorm/{packageId}'
*/
scormPlayerForm.get = (args: { packageId: string | number } | [packageId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: scormPlayer.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:175
* @route '/lms/scorm/{packageId}'
*/
scormPlayerForm.head = (args: { packageId: string | number } | [packageId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: scormPlayer.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

scormPlayer.form = scormPlayerForm

/**
* @see routes/web.php:211
* @route '/lms/compliance'
*/
export const compliance = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compliance.url(options),
    method: 'get',
})

compliance.definition = {
    methods: ["get","head"],
    url: '/lms/compliance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:211
* @route '/lms/compliance'
*/
compliance.url = (options?: RouteQueryOptions) => {
    return compliance.definition.url + queryParams(options)
}

/**
* @see routes/web.php:211
* @route '/lms/compliance'
*/
compliance.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compliance.url(options),
    method: 'get',
})

/**
* @see routes/web.php:211
* @route '/lms/compliance'
*/
compliance.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: compliance.url(options),
    method: 'head',
})

/**
* @see routes/web.php:211
* @route '/lms/compliance'
*/
const complianceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compliance.url(options),
    method: 'get',
})

/**
* @see routes/web.php:211
* @route '/lms/compliance'
*/
complianceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compliance.url(options),
    method: 'get',
})

/**
* @see routes/web.php:211
* @route '/lms/compliance'
*/
complianceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compliance.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

compliance.form = complianceForm

/**
* @see routes/web.php:214
* @route '/lms/reports'
*/
export const reports = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reports.url(options),
    method: 'get',
})

reports.definition = {
    methods: ["get","head"],
    url: '/lms/reports',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:214
* @route '/lms/reports'
*/
reports.url = (options?: RouteQueryOptions) => {
    return reports.definition.url + queryParams(options)
}

/**
* @see routes/web.php:214
* @route '/lms/reports'
*/
reports.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reports.url(options),
    method: 'get',
})

/**
* @see routes/web.php:214
* @route '/lms/reports'
*/
reports.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reports.url(options),
    method: 'head',
})

/**
* @see routes/web.php:214
* @route '/lms/reports'
*/
const reportsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: reports.url(options),
    method: 'get',
})

/**
* @see routes/web.php:214
* @route '/lms/reports'
*/
reportsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: reports.url(options),
    method: 'get',
})

/**
* @see routes/web.php:214
* @route '/lms/reports'
*/
reportsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: reports.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

reports.form = reportsForm

/**
* @see routes/web.php:220
* @route '/lms/catalog'
*/
export const catalog = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: catalog.url(options),
    method: 'get',
})

catalog.definition = {
    methods: ["get","head"],
    url: '/lms/catalog',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:220
* @route '/lms/catalog'
*/
catalog.url = (options?: RouteQueryOptions) => {
    return catalog.definition.url + queryParams(options)
}

/**
* @see routes/web.php:220
* @route '/lms/catalog'
*/
catalog.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: catalog.url(options),
    method: 'get',
})

/**
* @see routes/web.php:220
* @route '/lms/catalog'
*/
catalog.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: catalog.url(options),
    method: 'head',
})

/**
* @see routes/web.php:220
* @route '/lms/catalog'
*/
const catalogForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: catalog.url(options),
    method: 'get',
})

/**
* @see routes/web.php:220
* @route '/lms/catalog'
*/
catalogForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: catalog.url(options),
    method: 'get',
})

/**
* @see routes/web.php:220
* @route '/lms/catalog'
*/
catalogForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: catalog.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

catalog.form = catalogForm

/**
* @see routes/web.php:223
* @route '/lms/webhooks'
*/
export const webhooks = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: webhooks.url(options),
    method: 'get',
})

webhooks.definition = {
    methods: ["get","head"],
    url: '/lms/webhooks',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:223
* @route '/lms/webhooks'
*/
webhooks.url = (options?: RouteQueryOptions) => {
    return webhooks.definition.url + queryParams(options)
}

/**
* @see routes/web.php:223
* @route '/lms/webhooks'
*/
webhooks.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: webhooks.url(options),
    method: 'get',
})

/**
* @see routes/web.php:223
* @route '/lms/webhooks'
*/
webhooks.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: webhooks.url(options),
    method: 'head',
})

/**
* @see routes/web.php:223
* @route '/lms/webhooks'
*/
const webhooksForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: webhooks.url(options),
    method: 'get',
})

/**
* @see routes/web.php:223
* @route '/lms/webhooks'
*/
webhooksForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: webhooks.url(options),
    method: 'get',
})

/**
* @see routes/web.php:223
* @route '/lms/webhooks'
*/
webhooksForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: webhooks.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

webhooks.form = webhooksForm

/**
* @see routes/web.php:226
* @route '/lms/calendar'
*/
export const calendar = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: calendar.url(options),
    method: 'get',
})

calendar.definition = {
    methods: ["get","head"],
    url: '/lms/calendar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:226
* @route '/lms/calendar'
*/
calendar.url = (options?: RouteQueryOptions) => {
    return calendar.definition.url + queryParams(options)
}

/**
* @see routes/web.php:226
* @route '/lms/calendar'
*/
calendar.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: calendar.url(options),
    method: 'get',
})

/**
* @see routes/web.php:226
* @route '/lms/calendar'
*/
calendar.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: calendar.url(options),
    method: 'head',
})

/**
* @see routes/web.php:226
* @route '/lms/calendar'
*/
const calendarForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: calendar.url(options),
    method: 'get',
})

/**
* @see routes/web.php:226
* @route '/lms/calendar'
*/
calendarForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: calendar.url(options),
    method: 'get',
})

/**
* @see routes/web.php:226
* @route '/lms/calendar'
*/
calendarForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: calendar.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

calendar.form = calendarForm

/**
* @see routes/web.php:229
* @route '/lms/marketplace'
*/
export const marketplace = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: marketplace.url(options),
    method: 'get',
})

marketplace.definition = {
    methods: ["get","head"],
    url: '/lms/marketplace',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:229
* @route '/lms/marketplace'
*/
marketplace.url = (options?: RouteQueryOptions) => {
    return marketplace.definition.url + queryParams(options)
}

/**
* @see routes/web.php:229
* @route '/lms/marketplace'
*/
marketplace.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: marketplace.url(options),
    method: 'get',
})

/**
* @see routes/web.php:229
* @route '/lms/marketplace'
*/
marketplace.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: marketplace.url(options),
    method: 'head',
})

/**
* @see routes/web.php:229
* @route '/lms/marketplace'
*/
const marketplaceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: marketplace.url(options),
    method: 'get',
})

/**
* @see routes/web.php:229
* @route '/lms/marketplace'
*/
marketplaceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: marketplace.url(options),
    method: 'get',
})

/**
* @see routes/web.php:229
* @route '/lms/marketplace'
*/
marketplaceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: marketplace.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

marketplace.form = marketplaceForm

/**
* @see routes/web.php:232
* @route '/lms/sessions'
*/
export const sessions = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sessions.url(options),
    method: 'get',
})

sessions.definition = {
    methods: ["get","head"],
    url: '/lms/sessions',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:232
* @route '/lms/sessions'
*/
sessions.url = (options?: RouteQueryOptions) => {
    return sessions.definition.url + queryParams(options)
}

/**
* @see routes/web.php:232
* @route '/lms/sessions'
*/
sessions.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: sessions.url(options),
    method: 'get',
})

/**
* @see routes/web.php:232
* @route '/lms/sessions'
*/
sessions.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: sessions.url(options),
    method: 'head',
})

/**
* @see routes/web.php:232
* @route '/lms/sessions'
*/
const sessionsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sessions.url(options),
    method: 'get',
})

/**
* @see routes/web.php:232
* @route '/lms/sessions'
*/
sessionsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sessions.url(options),
    method: 'get',
})

/**
* @see routes/web.php:232
* @route '/lms/sessions'
*/
sessionsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: sessions.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

sessions.form = sessionsForm

/**
* @see routes/web.php:235
* @route '/lms/surveys'
*/
export const surveys = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: surveys.url(options),
    method: 'get',
})

surveys.definition = {
    methods: ["get","head"],
    url: '/lms/surveys',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:235
* @route '/lms/surveys'
*/
surveys.url = (options?: RouteQueryOptions) => {
    return surveys.definition.url + queryParams(options)
}

/**
* @see routes/web.php:235
* @route '/lms/surveys'
*/
surveys.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: surveys.url(options),
    method: 'get',
})

/**
* @see routes/web.php:235
* @route '/lms/surveys'
*/
surveys.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: surveys.url(options),
    method: 'head',
})

/**
* @see routes/web.php:235
* @route '/lms/surveys'
*/
const surveysForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: surveys.url(options),
    method: 'get',
})

/**
* @see routes/web.php:235
* @route '/lms/surveys'
*/
surveysForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: surveys.url(options),
    method: 'get',
})

/**
* @see routes/web.php:235
* @route '/lms/surveys'
*/
surveysForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: surveys.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

surveys.form = surveysForm

/**
* @see routes/web.php:238
* @route '/lms/peer-reviews'
*/
export const peerReviews = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: peerReviews.url(options),
    method: 'get',
})

peerReviews.definition = {
    methods: ["get","head"],
    url: '/lms/peer-reviews',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:238
* @route '/lms/peer-reviews'
*/
peerReviews.url = (options?: RouteQueryOptions) => {
    return peerReviews.definition.url + queryParams(options)
}

/**
* @see routes/web.php:238
* @route '/lms/peer-reviews'
*/
peerReviews.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: peerReviews.url(options),
    method: 'get',
})

/**
* @see routes/web.php:238
* @route '/lms/peer-reviews'
*/
peerReviews.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: peerReviews.url(options),
    method: 'head',
})

/**
* @see routes/web.php:238
* @route '/lms/peer-reviews'
*/
const peerReviewsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: peerReviews.url(options),
    method: 'get',
})

/**
* @see routes/web.php:238
* @route '/lms/peer-reviews'
*/
peerReviewsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: peerReviews.url(options),
    method: 'get',
})

/**
* @see routes/web.php:238
* @route '/lms/peer-reviews'
*/
peerReviewsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: peerReviews.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

peerReviews.form = peerReviewsForm

/**
* @see routes/web.php:241
* @route '/lms/community'
*/
export const community = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: community.url(options),
    method: 'get',
})

community.definition = {
    methods: ["get","head"],
    url: '/lms/community',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:241
* @route '/lms/community'
*/
community.url = (options?: RouteQueryOptions) => {
    return community.definition.url + queryParams(options)
}

/**
* @see routes/web.php:241
* @route '/lms/community'
*/
community.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: community.url(options),
    method: 'get',
})

/**
* @see routes/web.php:241
* @route '/lms/community'
*/
community.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: community.url(options),
    method: 'head',
})

/**
* @see routes/web.php:241
* @route '/lms/community'
*/
const communityForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: community.url(options),
    method: 'get',
})

/**
* @see routes/web.php:241
* @route '/lms/community'
*/
communityForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: community.url(options),
    method: 'get',
})

/**
* @see routes/web.php:241
* @route '/lms/community'
*/
communityForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: community.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

community.form = communityForm

/**
* @see routes/web.php:244
* @route '/lms/cohorts'
*/
export const cohorts = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cohorts.url(options),
    method: 'get',
})

cohorts.definition = {
    methods: ["get","head"],
    url: '/lms/cohorts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:244
* @route '/lms/cohorts'
*/
cohorts.url = (options?: RouteQueryOptions) => {
    return cohorts.definition.url + queryParams(options)
}

/**
* @see routes/web.php:244
* @route '/lms/cohorts'
*/
cohorts.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cohorts.url(options),
    method: 'get',
})

/**
* @see routes/web.php:244
* @route '/lms/cohorts'
*/
cohorts.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: cohorts.url(options),
    method: 'head',
})

/**
* @see routes/web.php:244
* @route '/lms/cohorts'
*/
const cohortsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: cohorts.url(options),
    method: 'get',
})

/**
* @see routes/web.php:244
* @route '/lms/cohorts'
*/
cohortsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: cohorts.url(options),
    method: 'get',
})

/**
* @see routes/web.php:244
* @route '/lms/cohorts'
*/
cohortsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: cohorts.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

cohorts.form = cohortsForm

/**
* @see routes/web.php:247
* @route '/lms'
*/
export const landing = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: landing.url(options),
    method: 'get',
})

landing.definition = {
    methods: ["get","head"],
    url: '/lms',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:247
* @route '/lms'
*/
landing.url = (options?: RouteQueryOptions) => {
    return landing.definition.url + queryParams(options)
}

/**
* @see routes/web.php:247
* @route '/lms'
*/
landing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:247
* @route '/lms'
*/
landing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: landing.url(options),
    method: 'head',
})

/**
* @see routes/web.php:247
* @route '/lms'
*/
const landingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:247
* @route '/lms'
*/
landingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:247
* @route '/lms'
*/
landingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

landing.form = landingForm

/**
* @see routes/web.php:559
* @route '/lms/player/{lesson}'
*/
export const player = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: player.url(args, options),
    method: 'get',
})

player.definition = {
    methods: ["get","head"],
    url: '/lms/player/{lesson}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:559
* @route '/lms/player/{lesson}'
*/
player.url = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { lesson: args }
    }

    if (Array.isArray(args)) {
        args = {
            lesson: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        lesson: args.lesson,
    }

    return player.definition.url
            .replace('{lesson}', parsedArgs.lesson.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:559
* @route '/lms/player/{lesson}'
*/
player.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: player.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:559
* @route '/lms/player/{lesson}'
*/
player.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: player.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:559
* @route '/lms/player/{lesson}'
*/
const playerForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: player.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:559
* @route '/lms/player/{lesson}'
*/
playerForm.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: player.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:559
* @route '/lms/player/{lesson}'
*/
playerForm.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: player.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

player.form = playerForm

const lms = {
    courseDesigner: Object.assign(courseDesigner, courseDesigner),
    quizPlayer: Object.assign(quizPlayer, quizPlayer),
    quizBuilder: Object.assign(quizBuilder, quizBuilder),
    learningPaths: Object.assign(learningPaths, learningPaths),
    scormPlayer: Object.assign(scormPlayer, scormPlayer),
    compliance: Object.assign(compliance, compliance),
    reports: Object.assign(reports, reports),
    catalog: Object.assign(catalog, catalog),
    webhooks: Object.assign(webhooks, webhooks),
    calendar: Object.assign(calendar, calendar),
    marketplace: Object.assign(marketplace, marketplace),
    sessions: Object.assign(sessions, sessions),
    surveys: Object.assign(surveys, surveys),
    peerReviews: Object.assign(peerReviews, peerReviews),
    community: Object.assign(community, community),
    cohorts: Object.assign(cohorts, cohorts),
    landing: Object.assign(landing, landing),
    courses: Object.assign(courses, courses),
    player: Object.assign(player, player),
}

export default lms