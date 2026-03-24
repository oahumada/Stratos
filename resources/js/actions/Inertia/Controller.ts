import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-metrics'
*/
const Controller1536443aaca1d0055cf9728225f4c9b5 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller1536443aaca1d0055cf9728225f4c9b5.url(options),
    method: 'get',
})

Controller1536443aaca1d0055cf9728225f4c9b5.definition = {
    methods: ["get","head"],
    url: '/deployment/verification-metrics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-metrics'
*/
Controller1536443aaca1d0055cf9728225f4c9b5.url = (options?: RouteQueryOptions) => {
    return Controller1536443aaca1d0055cf9728225f4c9b5.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-metrics'
*/
Controller1536443aaca1d0055cf9728225f4c9b5.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller1536443aaca1d0055cf9728225f4c9b5.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-metrics'
*/
Controller1536443aaca1d0055cf9728225f4c9b5.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller1536443aaca1d0055cf9728225f4c9b5.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-metrics'
*/
const Controller1536443aaca1d0055cf9728225f4c9b5Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller1536443aaca1d0055cf9728225f4c9b5.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-metrics'
*/
Controller1536443aaca1d0055cf9728225f4c9b5Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller1536443aaca1d0055cf9728225f4c9b5.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-metrics'
*/
Controller1536443aaca1d0055cf9728225f4c9b5Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller1536443aaca1d0055cf9728225f4c9b5.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller1536443aaca1d0055cf9728225f4c9b5.form = Controller1536443aaca1d0055cf9728225f4c9b5Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-hub'
*/
const Controllera7dbed35a12d9097e6c74f450b68d592 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllera7dbed35a12d9097e6c74f450b68d592.url(options),
    method: 'get',
})

Controllera7dbed35a12d9097e6c74f450b68d592.definition = {
    methods: ["get","head"],
    url: '/deployment/verification-hub',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-hub'
*/
Controllera7dbed35a12d9097e6c74f450b68d592.url = (options?: RouteQueryOptions) => {
    return Controllera7dbed35a12d9097e6c74f450b68d592.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-hub'
*/
Controllera7dbed35a12d9097e6c74f450b68d592.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllera7dbed35a12d9097e6c74f450b68d592.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-hub'
*/
Controllera7dbed35a12d9097e6c74f450b68d592.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllera7dbed35a12d9097e6c74f450b68d592.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-hub'
*/
const Controllera7dbed35a12d9097e6c74f450b68d592Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllera7dbed35a12d9097e6c74f450b68d592.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-hub'
*/
Controllera7dbed35a12d9097e6c74f450b68d592Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllera7dbed35a12d9097e6c74f450b68d592.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification-hub'
*/
Controllera7dbed35a12d9097e6c74f450b68d592Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllera7dbed35a12d9097e6c74f450b68d592.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllera7dbed35a12d9097e6c74f450b68d592.form = Controllera7dbed35a12d9097e6c74f450b68d592Form

const Controller = {
    '/deployment/verification-metrics': Controller1536443aaca1d0055cf9728225f4c9b5,
    '/deployment/verification-hub': Controllera7dbed35a12d9097e6c74f450b68d592,
}

export default Controller