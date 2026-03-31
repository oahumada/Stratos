import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\Messaging\ParticipantController::store
 * @see app/Http/Controllers/Api/Messaging/ParticipantController.php:22
 * @route '/api/messaging/conversations/{conversation}/participants'
 */
export const store = (
    args:
        | { conversation: string | { id: string } }
        | [conversation: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
});

store.definition = {
    methods: ['post'],
    url: '/api/messaging/conversations/{conversation}/participants',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\Messaging\ParticipantController::store
 * @see app/Http/Controllers/Api/Messaging/ParticipantController.php:22
 * @route '/api/messaging/conversations/{conversation}/participants'
 */
store.url = (
    args:
        | { conversation: string | { id: string } }
        | [conversation: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { conversation: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { conversation: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            conversation: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        conversation:
            typeof args.conversation === 'object'
                ? args.conversation.id
                : args.conversation,
    };

    return (
        store.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\Messaging\ParticipantController::store
 * @see app/Http/Controllers/Api/Messaging/ParticipantController.php:22
 * @route '/api/messaging/conversations/{conversation}/participants'
 */
store.post = (
    args:
        | { conversation: string | { id: string } }
        | [conversation: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\Messaging\ParticipantController::store
 * @see app/Http/Controllers/Api/Messaging/ParticipantController.php:22
 * @route '/api/messaging/conversations/{conversation}/participants'
 */
const storeForm = (
    args:
        | { conversation: string | { id: string } }
        | [conversation: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\Messaging\ParticipantController::store
 * @see app/Http/Controllers/Api/Messaging/ParticipantController.php:22
 * @route '/api/messaging/conversations/{conversation}/participants'
 */
storeForm.post = (
    args:
        | { conversation: string | { id: string } }
        | [conversation: string | { id: string }]
        | string
        | { id: string },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
});

store.form = storeForm;

/**
 * @see \App\Http\Controllers\Api\Messaging\ParticipantController::destroy
 * @see app/Http/Controllers/Api/Messaging/ParticipantController.php:53
 * @route '/api/messaging/conversations/{conversation}/participants/{participant}'
 */
export const destroy = (
    args:
        | {
              conversation: string | { id: string };
              participant: string | { id: string };
          }
        | [
              conversation: string | { id: string },
              participant: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

destroy.definition = {
    methods: ['delete'],
    url: '/api/messaging/conversations/{conversation}/participants/{participant}',
} satisfies RouteDefinition<['delete']>;

/**
 * @see \App\Http\Controllers\Api\Messaging\ParticipantController::destroy
 * @see app/Http/Controllers/Api/Messaging/ParticipantController.php:53
 * @route '/api/messaging/conversations/{conversation}/participants/{participant}'
 */
destroy.url = (
    args:
        | {
              conversation: string | { id: string };
              participant: string | { id: string };
          }
        | [
              conversation: string | { id: string },
              participant: string | { id: string },
          ],
    options?: RouteQueryOptions,
) => {
    if (Array.isArray(args)) {
        args = {
            conversation: args[0],
            participant: args[1],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        conversation:
            typeof args.conversation === 'object'
                ? args.conversation.id
                : args.conversation,
        participant:
            typeof args.participant === 'object'
                ? args.participant.id
                : args.participant,
    };

    return (
        destroy.definition.url
            .replace('{conversation}', parsedArgs.conversation.toString())
            .replace('{participant}', parsedArgs.participant.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\Messaging\ParticipantController::destroy
 * @see app/Http/Controllers/Api/Messaging/ParticipantController.php:53
 * @route '/api/messaging/conversations/{conversation}/participants/{participant}'
 */
destroy.delete = (
    args:
        | {
              conversation: string | { id: string };
              participant: string | { id: string };
          }
        | [
              conversation: string | { id: string },
              participant: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
});

/**
 * @see \App\Http\Controllers\Api\Messaging\ParticipantController::destroy
 * @see app/Http/Controllers/Api/Messaging/ParticipantController.php:53
 * @route '/api/messaging/conversations/{conversation}/participants/{participant}'
 */
const destroyForm = (
    args:
        | {
              conversation: string | { id: string };
              participant: string | { id: string };
          }
        | [
              conversation: string | { id: string },
              participant: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\Messaging\ParticipantController::destroy
 * @see app/Http/Controllers/Api/Messaging/ParticipantController.php:53
 * @route '/api/messaging/conversations/{conversation}/participants/{participant}'
 */
destroyForm.delete = (
    args:
        | {
              conversation: string | { id: string };
              participant: string | { id: string };
          }
        | [
              conversation: string | { id: string },
              participant: string | { id: string },
          ],
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

destroy.form = destroyForm;

const ParticipantController = { store, destroy };

export default ParticipantController;
