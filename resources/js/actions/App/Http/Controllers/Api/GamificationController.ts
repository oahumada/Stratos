import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests'
 */
const getAvailableQuests5b252a8824a6a8263003061ce8b22847 = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getAvailableQuests5b252a8824a6a8263003061ce8b22847.url(options),
    method: 'get',
});

getAvailableQuests5b252a8824a6a8263003061ce8b22847.definition = {
    methods: ['get', 'head'],
    url: '/api/gamification/quests',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests'
 */
getAvailableQuests5b252a8824a6a8263003061ce8b22847.url = (
    options?: RouteQueryOptions,
) => {
    return (
        getAvailableQuests5b252a8824a6a8263003061ce8b22847.definition.url +
        queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests'
 */
getAvailableQuests5b252a8824a6a8263003061ce8b22847.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getAvailableQuests5b252a8824a6a8263003061ce8b22847.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests'
 */
getAvailableQuests5b252a8824a6a8263003061ce8b22847.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getAvailableQuests5b252a8824a6a8263003061ce8b22847.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests'
 */
const getAvailableQuests5b252a8824a6a8263003061ce8b22847Form = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getAvailableQuests5b252a8824a6a8263003061ce8b22847.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests'
 */
getAvailableQuests5b252a8824a6a8263003061ce8b22847Form.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getAvailableQuests5b252a8824a6a8263003061ce8b22847.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests'
 */
getAvailableQuests5b252a8824a6a8263003061ce8b22847Form.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getAvailableQuests5b252a8824a6a8263003061ce8b22847.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getAvailableQuests5b252a8824a6a8263003061ce8b22847.form =
    getAvailableQuests5b252a8824a6a8263003061ce8b22847Form;
/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests/available'
 */
const getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51 = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.url(options),
    method: 'get',
});

getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.definition = {
    methods: ['get', 'head'],
    url: '/api/gamification/quests/available',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests/available'
 */
getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.url = (
    options?: RouteQueryOptions,
) => {
    return (
        getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.definition.url +
        queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests/available'
 */
getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.get = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests/available'
 */
getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.head = (
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests/available'
 */
const getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51Form = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests/available'
 */
getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51Form.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getAvailableQuests
 * @see app/Http/Controllers/Api/GamificationController.php:37
 * @route '/api/gamification/quests/available'
 */
getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51Form.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51.form =
    getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51Form;

export const getAvailableQuests = {
    '/api/gamification/quests':
        getAvailableQuests5b252a8824a6a8263003061ce8b22847,
    '/api/gamification/quests/available':
        getAvailableQuestsc8cfe87155ee76ac4a8d887f595a7a51,
};

/**
 * @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
 * @see app/Http/Controllers/Api/GamificationController.php:23
 * @route '/api/gamification/people/{peopleId}/quests'
 */
export const getPersonQuests = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getPersonQuests.url(args, options),
    method: 'get',
});

getPersonQuests.definition = {
    methods: ['get', 'head'],
    url: '/api/gamification/people/{peopleId}/quests',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
 * @see app/Http/Controllers/Api/GamificationController.php:23
 * @route '/api/gamification/people/{peopleId}/quests'
 */
getPersonQuests.url = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { peopleId: args };
    }

    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        peopleId: args.peopleId,
    };

    return (
        getPersonQuests.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
 * @see app/Http/Controllers/Api/GamificationController.php:23
 * @route '/api/gamification/people/{peopleId}/quests'
 */
getPersonQuests.get = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getPersonQuests.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
 * @see app/Http/Controllers/Api/GamificationController.php:23
 * @route '/api/gamification/people/{peopleId}/quests'
 */
getPersonQuests.head = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getPersonQuests.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
 * @see app/Http/Controllers/Api/GamificationController.php:23
 * @route '/api/gamification/people/{peopleId}/quests'
 */
const getPersonQuestsForm = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getPersonQuests.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
 * @see app/Http/Controllers/Api/GamificationController.php:23
 * @route '/api/gamification/people/{peopleId}/quests'
 */
getPersonQuestsForm.get = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getPersonQuests.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getPersonQuests
 * @see app/Http/Controllers/Api/GamificationController.php:23
 * @route '/api/gamification/people/{peopleId}/quests'
 */
getPersonQuestsForm.head = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getPersonQuests.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getPersonQuests.form = getPersonQuestsForm;

/**
 * @see \App\Http\Controllers\Api\GamificationController::startQuest
 * @see app/Http/Controllers/Api/GamificationController.php:51
 * @route '/api/gamification/people/{peopleId}/quests/{questId}/start'
 */
export const startQuest = (
    args:
        | { peopleId: string | number; questId: string | number }
        | [peopleId: string | number, questId: string | number],
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: startQuest.url(args, options),
    method: 'post',
});

startQuest.definition = {
    methods: ['post'],
    url: '/api/gamification/people/{peopleId}/quests/{questId}/start',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\GamificationController::startQuest
 * @see app/Http/Controllers/Api/GamificationController.php:51
 * @route '/api/gamification/people/{peopleId}/quests/{questId}/start'
 */
startQuest.url = (
    args:
        | { peopleId: string | number; questId: string | number }
        | [peopleId: string | number, questId: string | number],
    options?: RouteQueryOptions,
) => {
    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
            questId: args[1],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        peopleId: args.peopleId,
        questId: args.questId,
    };

    return (
        startQuest.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace('{questId}', parsedArgs.questId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\GamificationController::startQuest
 * @see app/Http/Controllers/Api/GamificationController.php:51
 * @route '/api/gamification/people/{peopleId}/quests/{questId}/start'
 */
startQuest.post = (
    args:
        | { peopleId: string | number; questId: string | number }
        | [peopleId: string | number, questId: string | number],
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: startQuest.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::startQuest
 * @see app/Http/Controllers/Api/GamificationController.php:51
 * @route '/api/gamification/people/{peopleId}/quests/{questId}/start'
 */
const startQuestForm = (
    args:
        | { peopleId: string | number; questId: string | number }
        | [peopleId: string | number, questId: string | number],
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: startQuest.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::startQuest
 * @see app/Http/Controllers/Api/GamificationController.php:51
 * @route '/api/gamification/people/{peopleId}/quests/{questId}/start'
 */
startQuestForm.post = (
    args:
        | { peopleId: string | number; questId: string | number }
        | [peopleId: string | number, questId: string | number],
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: startQuest.url(args, options),
    method: 'post',
});

startQuest.form = startQuestForm;

/**
 * @see \App\Http\Controllers\Api\GamificationController::progressQuest
 * @see app/Http/Controllers/Api/GamificationController.php:69
 * @route '/api/gamification/people/{peopleId}/quests/{questId}/progress'
 */
export const progressQuest = (
    args:
        | { peopleId: string | number; questId: string | number }
        | [peopleId: string | number, questId: string | number],
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: progressQuest.url(args, options),
    method: 'post',
});

progressQuest.definition = {
    methods: ['post'],
    url: '/api/gamification/people/{peopleId}/quests/{questId}/progress',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\GamificationController::progressQuest
 * @see app/Http/Controllers/Api/GamificationController.php:69
 * @route '/api/gamification/people/{peopleId}/quests/{questId}/progress'
 */
progressQuest.url = (
    args:
        | { peopleId: string | number; questId: string | number }
        | [peopleId: string | number, questId: string | number],
    options?: RouteQueryOptions,
) => {
    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
            questId: args[1],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        peopleId: args.peopleId,
        questId: args.questId,
    };

    return (
        progressQuest.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace('{questId}', parsedArgs.questId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\GamificationController::progressQuest
 * @see app/Http/Controllers/Api/GamificationController.php:69
 * @route '/api/gamification/people/{peopleId}/quests/{questId}/progress'
 */
progressQuest.post = (
    args:
        | { peopleId: string | number; questId: string | number }
        | [peopleId: string | number, questId: string | number],
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: progressQuest.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::progressQuest
 * @see app/Http/Controllers/Api/GamificationController.php:69
 * @route '/api/gamification/people/{peopleId}/quests/{questId}/progress'
 */
const progressQuestForm = (
    args:
        | { peopleId: string | number; questId: string | number }
        | [peopleId: string | number, questId: string | number],
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: progressQuest.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::progressQuest
 * @see app/Http/Controllers/Api/GamificationController.php:69
 * @route '/api/gamification/people/{peopleId}/quests/{questId}/progress'
 */
progressQuestForm.post = (
    args:
        | { peopleId: string | number; questId: string | number }
        | [peopleId: string | number, questId: string | number],
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: progressQuest.url(args, options),
    method: 'post',
});

progressQuest.form = progressQuestForm;

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRewards
 * @see app/Http/Controllers/Api/GamificationController.php:93
 * @route '/api/gamification/rewards'
 */
export const getRewards = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getRewards.url(options),
    method: 'get',
});

getRewards.definition = {
    methods: ['get', 'head'],
    url: '/api/gamification/rewards',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRewards
 * @see app/Http/Controllers/Api/GamificationController.php:93
 * @route '/api/gamification/rewards'
 */
getRewards.url = (options?: RouteQueryOptions) => {
    return getRewards.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRewards
 * @see app/Http/Controllers/Api/GamificationController.php:93
 * @route '/api/gamification/rewards'
 */
getRewards.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getRewards.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRewards
 * @see app/Http/Controllers/Api/GamificationController.php:93
 * @route '/api/gamification/rewards'
 */
getRewards.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getRewards.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRewards
 * @see app/Http/Controllers/Api/GamificationController.php:93
 * @route '/api/gamification/rewards'
 */
const getRewardsForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getRewards.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRewards
 * @see app/Http/Controllers/Api/GamificationController.php:93
 * @route '/api/gamification/rewards'
 */
getRewardsForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getRewards.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRewards
 * @see app/Http/Controllers/Api/GamificationController.php:93
 * @route '/api/gamification/rewards'
 */
getRewardsForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getRewards.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getRewards.form = getRewardsForm;

/**
 * @see \App\Http\Controllers\Api\GamificationController::redeem
 * @see app/Http/Controllers/Api/GamificationController.php:107
 * @route '/api/gamification/people/{peopleId}/redeem'
 */
export const redeem = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: redeem.url(args, options),
    method: 'post',
});

redeem.definition = {
    methods: ['post'],
    url: '/api/gamification/people/{peopleId}/redeem',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\GamificationController::redeem
 * @see app/Http/Controllers/Api/GamificationController.php:107
 * @route '/api/gamification/people/{peopleId}/redeem'
 */
redeem.url = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { peopleId: args };
    }

    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        peopleId: args.peopleId,
    };

    return (
        redeem.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\GamificationController::redeem
 * @see app/Http/Controllers/Api/GamificationController.php:107
 * @route '/api/gamification/people/{peopleId}/redeem'
 */
redeem.post = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: redeem.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::redeem
 * @see app/Http/Controllers/Api/GamificationController.php:107
 * @route '/api/gamification/people/{peopleId}/redeem'
 */
const redeemForm = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: redeem.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::redeem
 * @see app/Http/Controllers/Api/GamificationController.php:107
 * @route '/api/gamification/people/{peopleId}/redeem'
 */
redeemForm.post = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: redeem.url(args, options),
    method: 'post',
});

redeem.form = redeemForm;

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRedemptionHistory
 * @see app/Http/Controllers/Api/GamificationController.php:129
 * @route '/api/gamification/people/{peopleId}/history'
 */
export const getRedemptionHistory = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getRedemptionHistory.url(args, options),
    method: 'get',
});

getRedemptionHistory.definition = {
    methods: ['get', 'head'],
    url: '/api/gamification/people/{peopleId}/history',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRedemptionHistory
 * @see app/Http/Controllers/Api/GamificationController.php:129
 * @route '/api/gamification/people/{peopleId}/history'
 */
getRedemptionHistory.url = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { peopleId: args };
    }

    if (Array.isArray(args)) {
        args = {
            peopleId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        peopleId: args.peopleId,
    };

    return (
        getRedemptionHistory.definition.url
            .replace('{peopleId}', parsedArgs.peopleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRedemptionHistory
 * @see app/Http/Controllers/Api/GamificationController.php:129
 * @route '/api/gamification/people/{peopleId}/history'
 */
getRedemptionHistory.get = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: getRedemptionHistory.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRedemptionHistory
 * @see app/Http/Controllers/Api/GamificationController.php:129
 * @route '/api/gamification/people/{peopleId}/history'
 */
getRedemptionHistory.head = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: getRedemptionHistory.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRedemptionHistory
 * @see app/Http/Controllers/Api/GamificationController.php:129
 * @route '/api/gamification/people/{peopleId}/history'
 */
const getRedemptionHistoryForm = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getRedemptionHistory.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRedemptionHistory
 * @see app/Http/Controllers/Api/GamificationController.php:129
 * @route '/api/gamification/people/{peopleId}/history'
 */
getRedemptionHistoryForm.get = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getRedemptionHistory.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\GamificationController::getRedemptionHistory
 * @see app/Http/Controllers/Api/GamificationController.php:129
 * @route '/api/gamification/people/{peopleId}/history'
 */
getRedemptionHistoryForm.head = (
    args:
        | { peopleId: string | number }
        | [peopleId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: getRedemptionHistory.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

getRedemptionHistory.form = getRedemptionHistoryForm;

const GamificationController = {
    getAvailableQuests,
    getPersonQuests,
    startQuest,
    progressQuest,
    getRewards,
    redeem,
    getRedemptionHistory,
};

export default GamificationController;
