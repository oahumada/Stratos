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
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
const Controllerb7d8e1bf21b023d4c395f04411f4f7b1 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerb7d8e1bf21b023d4c395f04411f4f7b1.url(options),
    method: 'get',
})

Controllerb7d8e1bf21b023d4c395f04411f4f7b1.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/executive',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
Controllerb7d8e1bf21b023d4c395f04411f4f7b1.url = (options?: RouteQueryOptions) => {
    return Controllerb7d8e1bf21b023d4c395f04411f4f7b1.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
Controllerb7d8e1bf21b023d4c395f04411f4f7b1.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerb7d8e1bf21b023d4c395f04411f4f7b1.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
Controllerb7d8e1bf21b023d4c395f04411f4f7b1.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerb7d8e1bf21b023d4c395f04411f4f7b1.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
const Controllerb7d8e1bf21b023d4c395f04411f4f7b1Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb7d8e1bf21b023d4c395f04411f4f7b1.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
Controllerb7d8e1bf21b023d4c395f04411f4f7b1Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb7d8e1bf21b023d4c395f04411f4f7b1.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/executive'
*/
Controllerb7d8e1bf21b023d4c395f04411f4f7b1Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb7d8e1bf21b023d4c395f04411f4f7b1.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerb7d8e1bf21b023d4c395f04411f4f7b1.form = Controllerb7d8e1bf21b023d4c395f04411f4f7b1Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
const Controller804442f9e2e331a9dbff5c298942c82e = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller804442f9e2e331a9dbff5c298942c82e.url(options),
    method: 'get',
})

Controller804442f9e2e331a9dbff5c298942c82e.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/operational',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
Controller804442f9e2e331a9dbff5c298942c82e.url = (options?: RouteQueryOptions) => {
    return Controller804442f9e2e331a9dbff5c298942c82e.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
Controller804442f9e2e331a9dbff5c298942c82e.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller804442f9e2e331a9dbff5c298942c82e.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
Controller804442f9e2e331a9dbff5c298942c82e.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller804442f9e2e331a9dbff5c298942c82e.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
const Controller804442f9e2e331a9dbff5c298942c82eForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller804442f9e2e331a9dbff5c298942c82e.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
Controller804442f9e2e331a9dbff5c298942c82eForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller804442f9e2e331a9dbff5c298942c82e.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/operational'
*/
Controller804442f9e2e331a9dbff5c298942c82eForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller804442f9e2e331a9dbff5c298942c82e.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller804442f9e2e331a9dbff5c298942c82e.form = Controller804442f9e2e331a9dbff5c298942c82eForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
const Controllerb4319a3b7a81ec4cef594889ce9a7194 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerb4319a3b7a81ec4cef594889ce9a7194.url(options),
    method: 'get',
})

Controllerb4319a3b7a81ec4cef594889ce9a7194.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/compliance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
Controllerb4319a3b7a81ec4cef594889ce9a7194.url = (options?: RouteQueryOptions) => {
    return Controllerb4319a3b7a81ec4cef594889ce9a7194.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
Controllerb4319a3b7a81ec4cef594889ce9a7194.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerb4319a3b7a81ec4cef594889ce9a7194.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
Controllerb4319a3b7a81ec4cef594889ce9a7194.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerb4319a3b7a81ec4cef594889ce9a7194.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
const Controllerb4319a3b7a81ec4cef594889ce9a7194Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb4319a3b7a81ec4cef594889ce9a7194.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
Controllerb4319a3b7a81ec4cef594889ce9a7194Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb4319a3b7a81ec4cef594889ce9a7194.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/compliance'
*/
Controllerb4319a3b7a81ec4cef594889ce9a7194Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb4319a3b7a81ec4cef594889ce9a7194.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerb4319a3b7a81ec4cef594889ce9a7194.form = Controllerb4319a3b7a81ec4cef594889ce9a7194Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
const Controllere27a306b64fc1541f6e85c04f4679ea7 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllere27a306b64fc1541f6e85c04f4679ea7.url(options),
    method: 'get',
})

Controllere27a306b64fc1541f6e85c04f4679ea7.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/performance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
Controllere27a306b64fc1541f6e85c04f4679ea7.url = (options?: RouteQueryOptions) => {
    return Controllere27a306b64fc1541f6e85c04f4679ea7.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
Controllere27a306b64fc1541f6e85c04f4679ea7.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllere27a306b64fc1541f6e85c04f4679ea7.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
Controllere27a306b64fc1541f6e85c04f4679ea7.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllere27a306b64fc1541f6e85c04f4679ea7.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
const Controllere27a306b64fc1541f6e85c04f4679ea7Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllere27a306b64fc1541f6e85c04f4679ea7.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
Controllere27a306b64fc1541f6e85c04f4679ea7Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllere27a306b64fc1541f6e85c04f4679ea7.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/performance'
*/
Controllere27a306b64fc1541f6e85c04f4679ea7Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllere27a306b64fc1541f6e85c04f4679ea7.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllere27a306b64fc1541f6e85c04f4679ea7.form = Controllere27a306b64fc1541f6e85c04f4679ea7Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
const Controller7a93b4b13ca60da089834989d92dad53 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller7a93b4b13ca60da089834989d92dad53.url(options),
    method: 'get',
})

Controller7a93b4b13ca60da089834989d92dad53.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/insights',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
Controller7a93b4b13ca60da089834989d92dad53.url = (options?: RouteQueryOptions) => {
    return Controller7a93b4b13ca60da089834989d92dad53.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
Controller7a93b4b13ca60da089834989d92dad53.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller7a93b4b13ca60da089834989d92dad53.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
Controller7a93b4b13ca60da089834989d92dad53.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller7a93b4b13ca60da089834989d92dad53.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
const Controller7a93b4b13ca60da089834989d92dad53Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller7a93b4b13ca60da089834989d92dad53.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
Controller7a93b4b13ca60da089834989d92dad53Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller7a93b4b13ca60da089834989d92dad53.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/insights'
*/
Controller7a93b4b13ca60da089834989d92dad53Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller7a93b4b13ca60da089834989d92dad53.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller7a93b4b13ca60da089834989d92dad53.form = Controller7a93b4b13ca60da089834989d92dad53Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
const Controllerfc6f24c041bacb6b74c576eb16f12888 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerfc6f24c041bacb6b74c576eb16f12888.url(options),
    method: 'get',
})

Controllerfc6f24c041bacb6b74c576eb16f12888.definition = {
    methods: ["get","head"],
    url: '/deployment/verification/dashboard/realtime',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
Controllerfc6f24c041bacb6b74c576eb16f12888.url = (options?: RouteQueryOptions) => {
    return Controllerfc6f24c041bacb6b74c576eb16f12888.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
Controllerfc6f24c041bacb6b74c576eb16f12888.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerfc6f24c041bacb6b74c576eb16f12888.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
Controllerfc6f24c041bacb6b74c576eb16f12888.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerfc6f24c041bacb6b74c576eb16f12888.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
const Controllerfc6f24c041bacb6b74c576eb16f12888Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerfc6f24c041bacb6b74c576eb16f12888.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
Controllerfc6f24c041bacb6b74c576eb16f12888Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerfc6f24c041bacb6b74c576eb16f12888.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/deployment/verification/dashboard/realtime'
*/
Controllerfc6f24c041bacb6b74c576eb16f12888Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerfc6f24c041bacb6b74c576eb16f12888.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerfc6f24c041bacb6b74c576eb16f12888.form = Controllerfc6f24c041bacb6b74c576eb16f12888Form

const Controller = {
    '/deployment/verification-metrics': Controller1536443aaca1d0055cf9728225f4c9b5,
    '/deployment/verification-hub': Controllera7dbed35a12d9097e6c74f450b68d592,
    '/deployment/verification/dashboard/executive': Controllerb7d8e1bf21b023d4c395f04411f4f7b1,
    '/deployment/verification/dashboard/operational': Controller804442f9e2e331a9dbff5c298942c82e,
    '/deployment/verification/dashboard/compliance': Controllerb4319a3b7a81ec4cef594889ce9a7194,
    '/deployment/verification/dashboard/performance': Controllere27a306b64fc1541f6e85c04f4679ea7,
    '/deployment/verification/dashboard/insights': Controller7a93b4b13ca60da089834989d92dad53,
    '/deployment/verification/dashboard/realtime': Controllerfc6f24c041bacb6b74c576eb16f12888,
}

export default Controller