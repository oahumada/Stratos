import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::profile
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:19
* @route '/api/lms/adaptive/profile'
*/
export const profile = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

profile.definition = {
    methods: ["get","head"],
    url: '/api/lms/adaptive/profile',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::profile
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:19
* @route '/api/lms/adaptive/profile'
*/
profile.url = (options?: RouteQueryOptions) => {
    return profile.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::profile
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:19
* @route '/api/lms/adaptive/profile'
*/
profile.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::profile
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:19
* @route '/api/lms/adaptive/profile'
*/
profile.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: profile.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::profile
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:19
* @route '/api/lms/adaptive/profile'
*/
const profileForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::profile
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:19
* @route '/api/lms/adaptive/profile'
*/
profileForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::profile
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:19
* @route '/api/lms/adaptive/profile'
*/
profileForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: profile.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

profile.form = profileForm

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::calibrate
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:35
* @route '/api/lms/adaptive/calibrate'
*/
export const calibrate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calibrate.url(options),
    method: 'post',
})

calibrate.definition = {
    methods: ["post"],
    url: '/api/lms/adaptive/calibrate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::calibrate
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:35
* @route '/api/lms/adaptive/calibrate'
*/
calibrate.url = (options?: RouteQueryOptions) => {
    return calibrate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::calibrate
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:35
* @route '/api/lms/adaptive/calibrate'
*/
calibrate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: calibrate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::calibrate
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:35
* @route '/api/lms/adaptive/calibrate'
*/
const calibrateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calibrate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::calibrate
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:35
* @route '/api/lms/adaptive/calibrate'
*/
calibrateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: calibrate.url(options),
    method: 'post',
})

calibrate.form = calibrateForm

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::recommendations
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:51
* @route '/api/lms/adaptive/courses/{course}/recommendations'
*/
export const recommendations = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendations.url(args, options),
    method: 'get',
})

recommendations.definition = {
    methods: ["get","head"],
    url: '/api/lms/adaptive/courses/{course}/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::recommendations
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:51
* @route '/api/lms/adaptive/courses/{course}/recommendations'
*/
recommendations.url = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return recommendations.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::recommendations
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:51
* @route '/api/lms/adaptive/courses/{course}/recommendations'
*/
recommendations.get = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::recommendations
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:51
* @route '/api/lms/adaptive/courses/{course}/recommendations'
*/
recommendations.head = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recommendations.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::recommendations
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:51
* @route '/api/lms/adaptive/courses/{course}/recommendations'
*/
const recommendationsForm = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::recommendations
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:51
* @route '/api/lms/adaptive/courses/{course}/recommendations'
*/
recommendationsForm.get = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::recommendations
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:51
* @route '/api/lms/adaptive/courses/{course}/recommendations'
*/
recommendationsForm.head = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

recommendations.form = recommendationsForm

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::rules
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:68
* @route '/api/lms/adaptive/courses/{course}/rules'
*/
export const rules = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: rules.url(args, options),
    method: 'get',
})

rules.definition = {
    methods: ["get","head"],
    url: '/api/lms/adaptive/courses/{course}/rules',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::rules
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:68
* @route '/api/lms/adaptive/courses/{course}/rules'
*/
rules.url = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return rules.definition.url
            .replace('{course}', parsedArgs.course.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::rules
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:68
* @route '/api/lms/adaptive/courses/{course}/rules'
*/
rules.get = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: rules.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::rules
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:68
* @route '/api/lms/adaptive/courses/{course}/rules'
*/
rules.head = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: rules.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::rules
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:68
* @route '/api/lms/adaptive/courses/{course}/rules'
*/
const rulesForm = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: rules.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::rules
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:68
* @route '/api/lms/adaptive/courses/{course}/rules'
*/
rulesForm.get = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: rules.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::rules
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:68
* @route '/api/lms/adaptive/courses/{course}/rules'
*/
rulesForm.head = (args: { course: string | number } | [course: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: rules.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

rules.form = rulesForm

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::storeRule
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:84
* @route '/api/lms/adaptive/rules'
*/
export const storeRule = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeRule.url(options),
    method: 'post',
})

storeRule.definition = {
    methods: ["post"],
    url: '/api/lms/adaptive/rules',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::storeRule
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:84
* @route '/api/lms/adaptive/rules'
*/
storeRule.url = (options?: RouteQueryOptions) => {
    return storeRule.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::storeRule
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:84
* @route '/api/lms/adaptive/rules'
*/
storeRule.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeRule.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::storeRule
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:84
* @route '/api/lms/adaptive/rules'
*/
const storeRuleForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeRule.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::storeRule
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:84
* @route '/api/lms/adaptive/rules'
*/
storeRuleForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeRule.url(options),
    method: 'post',
})

storeRule.form = storeRuleForm

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::updateRule
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:112
* @route '/api/lms/adaptive/rules/{adaptiveRule}'
*/
export const updateRule = (args: { adaptiveRule: number | { id: number } } | [adaptiveRule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateRule.url(args, options),
    method: 'put',
})

updateRule.definition = {
    methods: ["put"],
    url: '/api/lms/adaptive/rules/{adaptiveRule}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::updateRule
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:112
* @route '/api/lms/adaptive/rules/{adaptiveRule}'
*/
updateRule.url = (args: { adaptiveRule: number | { id: number } } | [adaptiveRule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { adaptiveRule: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { adaptiveRule: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            adaptiveRule: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        adaptiveRule: typeof args.adaptiveRule === 'object'
        ? args.adaptiveRule.id
        : args.adaptiveRule,
    }

    return updateRule.definition.url
            .replace('{adaptiveRule}', parsedArgs.adaptiveRule.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::updateRule
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:112
* @route '/api/lms/adaptive/rules/{adaptiveRule}'
*/
updateRule.put = (args: { adaptiveRule: number | { id: number } } | [adaptiveRule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: updateRule.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::updateRule
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:112
* @route '/api/lms/adaptive/rules/{adaptiveRule}'
*/
const updateRuleForm = (args: { adaptiveRule: number | { id: number } } | [adaptiveRule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateRule.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\AdaptiveController::updateRule
* @see app/Http/Controllers/Api/Lms/AdaptiveController.php:112
* @route '/api/lms/adaptive/rules/{adaptiveRule}'
*/
updateRuleForm.put = (args: { adaptiveRule: number | { id: number } } | [adaptiveRule: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateRule.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateRule.form = updateRuleForm

const AdaptiveController = { profile, calibrate, recommendations, rules, storeRule, updateRule }

export default AdaptiveController