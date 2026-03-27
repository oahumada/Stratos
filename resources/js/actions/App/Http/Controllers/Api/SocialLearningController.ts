import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\SocialLearningController::dashboard
* @see app/Http/Controllers/Api/SocialLearningController.php:19
* @route '/api/social-learning/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/api/social-learning/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SocialLearningController::dashboard
* @see app/Http/Controllers/Api/SocialLearningController.php:19
* @route '/api/social-learning/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SocialLearningController::dashboard
* @see app/Http/Controllers/Api/SocialLearningController.php:19
* @route '/api/social-learning/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SocialLearningController::dashboard
* @see app/Http/Controllers/Api/SocialLearningController.php:19
* @route '/api/social-learning/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SocialLearningController::matches
* @see app/Http/Controllers/Api/SocialLearningController.php:48
* @route '/api/social-learning/matches/{skillId}'
*/
export const matches = (args: { skillId: string | number } | [skillId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: matches.url(args, options),
    method: 'get',
})

matches.definition = {
    methods: ["get","head"],
    url: '/api/social-learning/matches/{skillId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SocialLearningController::matches
* @see app/Http/Controllers/Api/SocialLearningController.php:48
* @route '/api/social-learning/matches/{skillId}'
*/
matches.url = (args: { skillId: string | number } | [skillId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { skillId: args }
    }

    if (Array.isArray(args)) {
        args = {
            skillId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        skillId: args.skillId,
    }

    return matches.definition.url
            .replace('{skillId}', parsedArgs.skillId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SocialLearningController::matches
* @see app/Http/Controllers/Api/SocialLearningController.php:48
* @route '/api/social-learning/matches/{skillId}'
*/
matches.get = (args: { skillId: string | number } | [skillId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: matches.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SocialLearningController::matches
* @see app/Http/Controllers/Api/SocialLearningController.php:48
* @route '/api/social-learning/matches/{skillId}'
*/
matches.head = (args: { skillId: string | number } | [skillId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: matches.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SocialLearningController::generateBlueprint
* @see app/Http/Controllers/Api/SocialLearningController.php:58
* @route '/api/social-learning/generate-blueprint'
*/
export const generateBlueprint = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateBlueprint.url(options),
    method: 'post',
})

generateBlueprint.definition = {
    methods: ["post"],
    url: '/api/social-learning/generate-blueprint',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\SocialLearningController::generateBlueprint
* @see app/Http/Controllers/Api/SocialLearningController.php:58
* @route '/api/social-learning/generate-blueprint'
*/
generateBlueprint.url = (options?: RouteQueryOptions) => {
    return generateBlueprint.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SocialLearningController::generateBlueprint
* @see app/Http/Controllers/Api/SocialLearningController.php:58
* @route '/api/social-learning/generate-blueprint'
*/
generateBlueprint.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateBlueprint.url(options),
    method: 'post',
})

const SocialLearningController = { dashboard, matches, generateBlueprint }

export default SocialLearningController