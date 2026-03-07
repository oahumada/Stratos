import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
* @see app/Http/Controllers/Api/GamificationController.php:37
* @route '/api/gamification/quests'
*/
export const getAvailableQuests = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAvailableQuests.url(options),
    method: 'get',
})

getAvailableQuests.definition = {
    methods: ["get","head"],
    url: '/api/gamification/quests',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
* @see app/Http/Controllers/Api/GamificationController.php:37
* @route '/api/gamification/quests'
*/
getAvailableQuests.url = (options?: RouteQueryOptions) => {
    return getAvailableQuests.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
* @see app/Http/Controllers/Api/GamificationController.php:37
* @route '/api/gamification/quests'
*/
getAvailableQuests.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getAvailableQuests.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
* @see app/Http/Controllers/Api/GamificationController.php:37
* @route '/api/gamification/quests'
*/
getAvailableQuests.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getAvailableQuests.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
* @see app/Http/Controllers/Api/GamificationController.php:23
* @route '/api/gamification/people/{peopleId}/quests'
*/
export const getPersonQuests = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPersonQuests.url(args, options),
    method: 'get',
})

getPersonQuests.definition = {
    methods: ["get","head"],
    url: '/api/gamification/people/{peopleId}/quests',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
* @see app/Http/Controllers/Api/GamificationController.php:23
* @route '/api/gamification/people/{peopleId}/quests'
*/
getPersonQuests.url = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return getPersonQuests.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
* @see app/Http/Controllers/Api/GamificationController.php:23
* @route '/api/gamification/people/{peopleId}/quests'
*/
getPersonQuests.get = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getPersonQuests.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
* @see app/Http/Controllers/Api/GamificationController.php:23
* @route '/api/gamification/people/{peopleId}/quests'
*/
getPersonQuests.head = (args: { peopleId: string | number } | [peopleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getPersonQuests.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\GamificationController::startQuest
* @see app/Http/Controllers/Api/GamificationController.php:51
* @route '/api/gamification/people/{peopleId}/quests/{questId}/start'
*/
export const startQuest = (args: { peopleId: string | number, questId: string | number } | [peopleId: string | number, questId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: startQuest.url(args, options),
    method: 'post',
})

startQuest.definition = {
    methods: ["post"],
    url: '/api/gamification/people/{peopleId}/quests/{questId}/start',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\GamificationController::startQuest
* @see app/Http/Controllers/Api/GamificationController.php:51
* @route '/api/gamification/people/{peopleId}/quests/{questId}/start'
*/
startQuest.url = (args: { peopleId: string | number, questId: string | number } | [peopleId: string | number, questId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
            questId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        peopleId: args.peopleId,
        questId: args.questId,
    }

    return startQuest.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace('{questId}', parsedArgs.questId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\GamificationController::startQuest
* @see app/Http/Controllers/Api/GamificationController.php:51
* @route '/api/gamification/people/{peopleId}/quests/{questId}/start'
*/
startQuest.post = (args: { peopleId: string | number, questId: string | number } | [peopleId: string | number, questId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: startQuest.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\GamificationController::progressQuest
* @see app/Http/Controllers/Api/GamificationController.php:69
* @route '/api/gamification/people/{peopleId}/quests/{questId}/progress'
*/
export const progressQuest = (args: { peopleId: string | number, questId: string | number } | [peopleId: string | number, questId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: progressQuest.url(args, options),
    method: 'post',
})

progressQuest.definition = {
    methods: ["post"],
    url: '/api/gamification/people/{peopleId}/quests/{questId}/progress',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\GamificationController::progressQuest
* @see app/Http/Controllers/Api/GamificationController.php:69
* @route '/api/gamification/people/{peopleId}/quests/{questId}/progress'
*/
progressQuest.url = (args: { peopleId: string | number, questId: string | number } | [peopleId: string | number, questId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
            questId: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        peopleId: args.peopleId,
        questId: args.questId,
    }

    return progressQuest.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace('{questId}', parsedArgs.questId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\GamificationController::progressQuest
* @see app/Http/Controllers/Api/GamificationController.php:69
* @route '/api/gamification/people/{peopleId}/quests/{questId}/progress'
*/
progressQuest.post = (args: { peopleId: string | number, questId: string | number } | [peopleId: string | number, questId: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: progressQuest.url(args, options),
    method: 'post',
})

const GamificationController = { getAvailableQuests, getPersonQuests, startQuest, progressQuest }

export default GamificationController