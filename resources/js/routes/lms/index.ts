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
* @see routes/web.php:223
* @route '/lms'
*/
landing.url = (options?: RouteQueryOptions) => {
    return landing.definition.url + queryParams(options)
}

/**
* @see routes/web.php:223
* @route '/lms'
*/
landing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:223
* @route '/lms'
*/
landing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: landing.url(options),
    method: 'head',
})

/**
* @see routes/web.php:223
* @route '/lms'
*/
const landingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:223
* @route '/lms'
*/
landingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:223
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
* @see routes/web.php:535
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
* @see routes/web.php:535
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
* @see routes/web.php:535
* @route '/lms/player/{lesson}'
*/
player.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: player.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:535
* @route '/lms/player/{lesson}'
*/
player.head = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: player.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:535
* @route '/lms/player/{lesson}'
*/
const playerForm = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: player.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:535
* @route '/lms/player/{lesson}'
*/
playerForm.get = (args: { lesson: string | number } | [lesson: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: player.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:535
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
    landing: Object.assign(landing, landing),
    courses: Object.assign(courses, courses),
    player: Object.assign(player, player),
}

export default lms