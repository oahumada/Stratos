import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::generateOutline
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:16
* @route '/api/lms/course-designer/generate-outline'
*/
export const generateOutline = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateOutline.url(options),
    method: 'post',
})

generateOutline.definition = {
    methods: ["post"],
    url: '/api/lms/course-designer/generate-outline',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::generateOutline
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:16
* @route '/api/lms/course-designer/generate-outline'
*/
generateOutline.url = (options?: RouteQueryOptions) => {
    return generateOutline.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::generateOutline
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:16
* @route '/api/lms/course-designer/generate-outline'
*/
generateOutline.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateOutline.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::generateOutline
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:16
* @route '/api/lms/course-designer/generate-outline'
*/
const generateOutlineForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateOutline.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::generateOutline
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:16
* @route '/api/lms/course-designer/generate-outline'
*/
generateOutlineForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateOutline.url(options),
    method: 'post',
})

generateOutline.form = generateOutlineForm

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::generateContent
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:36
* @route '/api/lms/course-designer/generate-content'
*/
export const generateContent = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateContent.url(options),
    method: 'post',
})

generateContent.definition = {
    methods: ["post"],
    url: '/api/lms/course-designer/generate-content',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::generateContent
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:36
* @route '/api/lms/course-designer/generate-content'
*/
generateContent.url = (options?: RouteQueryOptions) => {
    return generateContent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::generateContent
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:36
* @route '/api/lms/course-designer/generate-content'
*/
generateContent.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateContent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::generateContent
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:36
* @route '/api/lms/course-designer/generate-content'
*/
const generateContentForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateContent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::generateContent
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:36
* @route '/api/lms/course-designer/generate-content'
*/
generateContentForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateContent.url(options),
    method: 'post',
})

generateContent.form = generateContentForm

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::persist
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:54
* @route '/api/lms/course-designer/persist'
*/
export const persist = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: persist.url(options),
    method: 'post',
})

persist.definition = {
    methods: ["post"],
    url: '/api/lms/course-designer/persist',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::persist
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:54
* @route '/api/lms/course-designer/persist'
*/
persist.url = (options?: RouteQueryOptions) => {
    return persist.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::persist
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:54
* @route '/api/lms/course-designer/persist'
*/
persist.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: persist.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::persist
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:54
* @route '/api/lms/course-designer/persist'
*/
const persistForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: persist.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::persist
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:54
* @route '/api/lms/course-designer/persist'
*/
persistForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: persist.url(options),
    method: 'post',
})

persist.form = persistForm

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::review
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:91
* @route '/api/lms/course-designer/{id}/review'
*/
export const review = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: review.url(args, options),
    method: 'post',
})

review.definition = {
    methods: ["post"],
    url: '/api/lms/course-designer/{id}/review',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::review
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:91
* @route '/api/lms/course-designer/{id}/review'
*/
review.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return review.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::review
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:91
* @route '/api/lms/course-designer/{id}/review'
*/
review.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: review.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::review
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:91
* @route '/api/lms/course-designer/{id}/review'
*/
const reviewForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: review.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::review
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:91
* @route '/api/lms/course-designer/{id}/review'
*/
reviewForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: review.url(args, options),
    method: 'post',
})

review.form = reviewForm

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::preview
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:100
* @route '/api/lms/course-designer/{id}/preview'
*/
export const preview = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(args, options),
    method: 'get',
})

preview.definition = {
    methods: ["get","head"],
    url: '/api/lms/course-designer/{id}/preview',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::preview
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:100
* @route '/api/lms/course-designer/{id}/preview'
*/
preview.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return preview.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::preview
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:100
* @route '/api/lms/course-designer/{id}/preview'
*/
preview.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: preview.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::preview
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:100
* @route '/api/lms/course-designer/{id}/preview'
*/
preview.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: preview.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::preview
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:100
* @route '/api/lms/course-designer/{id}/preview'
*/
const previewForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::preview
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:100
* @route '/api/lms/course-designer/{id}/preview'
*/
previewForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CourseDesignerController::preview
* @see app/Http/Controllers/Api/Lms/CourseDesignerController.php:100
* @route '/api/lms/course-designer/{id}/preview'
*/
previewForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: preview.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

preview.form = previewForm

const CourseDesignerController = { generateOutline, generateContent, persist, review, preview }

export default CourseDesignerController