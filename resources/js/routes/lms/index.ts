import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
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
* @see routes/web.php:163
* @route '/lms'
*/
landing.url = (options?: RouteQueryOptions) => {
    return landing.definition.url + queryParams(options)
}

/**
* @see routes/web.php:163
* @route '/lms'
*/
landing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:163
* @route '/lms'
*/
landing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: landing.url(options),
    method: 'head',
})

/**
* @see routes/web.php:163
* @route '/lms'
*/
const landingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:163
* @route '/lms'
*/
landingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: landing.url(options),
    method: 'get',
})

/**
* @see routes/web.php:163
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

const lms = {
    courseDesigner: Object.assign(courseDesigner, courseDesigner),
    landing: Object.assign(landing, landing),
    courses: Object.assign(courses, courses),
}

export default lms