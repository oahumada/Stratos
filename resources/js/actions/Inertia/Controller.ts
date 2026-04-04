import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/lms/courses/{id}/policy'
*/
const Controllerbc0be7e4deff421836f203fa0f5870dd = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerbc0be7e4deff421836f203fa0f5870dd.url(args, options),
    method: 'get',
})

Controllerbc0be7e4deff421836f203fa0f5870dd.definition = {
    methods: ["get","head"],
    url: '/lms/courses/{id}/policy',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/lms/courses/{id}/policy'
*/
Controllerbc0be7e4deff421836f203fa0f5870dd.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return Controllerbc0be7e4deff421836f203fa0f5870dd.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/lms/courses/{id}/policy'
*/
Controllerbc0be7e4deff421836f203fa0f5870dd.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerbc0be7e4deff421836f203fa0f5870dd.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/lms/courses/{id}/policy'
*/
Controllerbc0be7e4deff421836f203fa0f5870dd.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerbc0be7e4deff421836f203fa0f5870dd.url(args, options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/lms/courses/{id}/policy'
*/
const Controllerbc0be7e4deff421836f203fa0f5870ddForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerbc0be7e4deff421836f203fa0f5870dd.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/lms/courses/{id}/policy'
*/
Controllerbc0be7e4deff421836f203fa0f5870ddForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerbc0be7e4deff421836f203fa0f5870dd.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/lms/courses/{id}/policy'
*/
Controllerbc0be7e4deff421836f203fa0f5870ddForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerbc0be7e4deff421836f203fa0f5870dd.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerbc0be7e4deff421836f203fa0f5870dd.form = Controllerbc0be7e4deff421836f203fa0f5870ddForm
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
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass'
*/
const Controllerf294916370e4bfbc152ae84f39f44ed8 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerf294916370e4bfbc152ae84f39f44ed8.url(options),
    method: 'get',
})

Controllerf294916370e4bfbc152ae84f39f44ed8.definition = {
    methods: ["get","head"],
    url: '/talent-pass',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass'
*/
Controllerf294916370e4bfbc152ae84f39f44ed8.url = (options?: RouteQueryOptions) => {
    return Controllerf294916370e4bfbc152ae84f39f44ed8.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass'
*/
Controllerf294916370e4bfbc152ae84f39f44ed8.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerf294916370e4bfbc152ae84f39f44ed8.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass'
*/
Controllerf294916370e4bfbc152ae84f39f44ed8.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerf294916370e4bfbc152ae84f39f44ed8.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass'
*/
const Controllerf294916370e4bfbc152ae84f39f44ed8Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerf294916370e4bfbc152ae84f39f44ed8.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass'
*/
Controllerf294916370e4bfbc152ae84f39f44ed8Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerf294916370e4bfbc152ae84f39f44ed8.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass'
*/
Controllerf294916370e4bfbc152ae84f39f44ed8Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerf294916370e4bfbc152ae84f39f44ed8.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerf294916370e4bfbc152ae84f39f44ed8.form = Controllerf294916370e4bfbc152ae84f39f44ed8Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/create'
*/
const Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.url(options),
    method: 'get',
})

Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.definition = {
    methods: ["get","head"],
    url: '/talent-pass/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/create'
*/
Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.url = (options?: RouteQueryOptions) => {
    return Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/create'
*/
Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/create'
*/
Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/create'
*/
const Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/create'
*/
Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/create'
*/
Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2.form = Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}'
*/
const Controller377010c88ff7b9517dc6b60819bbfdab = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller377010c88ff7b9517dc6b60819bbfdab.url(args, options),
    method: 'get',
})

Controller377010c88ff7b9517dc6b60819bbfdab.definition = {
    methods: ["get","head"],
    url: '/talent-pass/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}'
*/
Controller377010c88ff7b9517dc6b60819bbfdab.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return Controller377010c88ff7b9517dc6b60819bbfdab.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}'
*/
Controller377010c88ff7b9517dc6b60819bbfdab.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller377010c88ff7b9517dc6b60819bbfdab.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}'
*/
Controller377010c88ff7b9517dc6b60819bbfdab.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller377010c88ff7b9517dc6b60819bbfdab.url(args, options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}'
*/
const Controller377010c88ff7b9517dc6b60819bbfdabForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller377010c88ff7b9517dc6b60819bbfdab.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}'
*/
Controller377010c88ff7b9517dc6b60819bbfdabForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller377010c88ff7b9517dc6b60819bbfdab.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}'
*/
Controller377010c88ff7b9517dc6b60819bbfdabForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller377010c88ff7b9517dc6b60819bbfdab.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller377010c88ff7b9517dc6b60819bbfdab.form = Controller377010c88ff7b9517dc6b60819bbfdabForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}/edit'
*/
const Controller1d23530a54575f83aa46a4f4f7537e3e = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller1d23530a54575f83aa46a4f4f7537e3e.url(args, options),
    method: 'get',
})

Controller1d23530a54575f83aa46a4f4f7537e3e.definition = {
    methods: ["get","head"],
    url: '/talent-pass/{id}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}/edit'
*/
Controller1d23530a54575f83aa46a4f4f7537e3e.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return Controller1d23530a54575f83aa46a4f4f7537e3e.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}/edit'
*/
Controller1d23530a54575f83aa46a4f4f7537e3e.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller1d23530a54575f83aa46a4f4f7537e3e.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}/edit'
*/
Controller1d23530a54575f83aa46a4f4f7537e3e.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller1d23530a54575f83aa46a4f4f7537e3e.url(args, options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}/edit'
*/
const Controller1d23530a54575f83aa46a4f4f7537e3eForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller1d23530a54575f83aa46a4f4f7537e3e.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}/edit'
*/
Controller1d23530a54575f83aa46a4f4f7537e3eForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller1d23530a54575f83aa46a4f4f7537e3e.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/talent-pass/{id}/edit'
*/
Controller1d23530a54575f83aa46a4f4f7537e3eForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller1d23530a54575f83aa46a4f4f7537e3e.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller1d23530a54575f83aa46a4f4f7537e3e.form = Controller1d23530a54575f83aa46a4f4f7537e3eForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/public/talent-pass/{ulid}'
*/
const Controller98ea1aa8bf95e23aa708198b0e2ab85e = (args: { ulid: string | number } | [ulid: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller98ea1aa8bf95e23aa708198b0e2ab85e.url(args, options),
    method: 'get',
})

Controller98ea1aa8bf95e23aa708198b0e2ab85e.definition = {
    methods: ["get","head"],
    url: '/public/talent-pass/{ulid}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/public/talent-pass/{ulid}'
*/
Controller98ea1aa8bf95e23aa708198b0e2ab85e.url = (args: { ulid: string | number } | [ulid: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { ulid: args }
    }

    if (Array.isArray(args)) {
        args = {
            ulid: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        ulid: args.ulid,
    }

    return Controller98ea1aa8bf95e23aa708198b0e2ab85e.definition.url
            .replace('{ulid}', parsedArgs.ulid.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/public/talent-pass/{ulid}'
*/
Controller98ea1aa8bf95e23aa708198b0e2ab85e.get = (args: { ulid: string | number } | [ulid: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller98ea1aa8bf95e23aa708198b0e2ab85e.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/public/talent-pass/{ulid}'
*/
Controller98ea1aa8bf95e23aa708198b0e2ab85e.head = (args: { ulid: string | number } | [ulid: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller98ea1aa8bf95e23aa708198b0e2ab85e.url(args, options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/public/talent-pass/{ulid}'
*/
const Controller98ea1aa8bf95e23aa708198b0e2ab85eForm = (args: { ulid: string | number } | [ulid: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller98ea1aa8bf95e23aa708198b0e2ab85e.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/public/talent-pass/{ulid}'
*/
Controller98ea1aa8bf95e23aa708198b0e2ab85eForm.get = (args: { ulid: string | number } | [ulid: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller98ea1aa8bf95e23aa708198b0e2ab85e.url(args, options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/public/talent-pass/{ulid}'
*/
Controller98ea1aa8bf95e23aa708198b0e2ab85eForm.head = (args: { ulid: string | number } | [ulid: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller98ea1aa8bf95e23aa708198b0e2ab85e.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller98ea1aa8bf95e23aa708198b0e2ab85e.form = Controller98ea1aa8bf95e23aa708198b0e2ab85eForm
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging'
*/
const Controller6c8a3d26da0429490535634bb4527732 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller6c8a3d26da0429490535634bb4527732.url(options),
    method: 'get',
})

Controller6c8a3d26da0429490535634bb4527732.definition = {
    methods: ["get","head"],
    url: '/messaging',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging'
*/
Controller6c8a3d26da0429490535634bb4527732.url = (options?: RouteQueryOptions) => {
    return Controller6c8a3d26da0429490535634bb4527732.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging'
*/
Controller6c8a3d26da0429490535634bb4527732.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controller6c8a3d26da0429490535634bb4527732.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging'
*/
Controller6c8a3d26da0429490535634bb4527732.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controller6c8a3d26da0429490535634bb4527732.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging'
*/
const Controller6c8a3d26da0429490535634bb4527732Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller6c8a3d26da0429490535634bb4527732.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging'
*/
Controller6c8a3d26da0429490535634bb4527732Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller6c8a3d26da0429490535634bb4527732.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging'
*/
Controller6c8a3d26da0429490535634bb4527732Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controller6c8a3d26da0429490535634bb4527732.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controller6c8a3d26da0429490535634bb4527732.form = Controller6c8a3d26da0429490535634bb4527732Form
/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging/settings'
*/
const Controllerb755d752252f37986e1a7e3a2f8308db = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerb755d752252f37986e1a7e3a2f8308db.url(options),
    method: 'get',
})

Controllerb755d752252f37986e1a7e3a2f8308db.definition = {
    methods: ["get","head"],
    url: '/messaging/settings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging/settings'
*/
Controllerb755d752252f37986e1a7e3a2f8308db.url = (options?: RouteQueryOptions) => {
    return Controllerb755d752252f37986e1a7e3a2f8308db.definition.url + queryParams(options)
}

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging/settings'
*/
Controllerb755d752252f37986e1a7e3a2f8308db.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: Controllerb755d752252f37986e1a7e3a2f8308db.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging/settings'
*/
Controllerb755d752252f37986e1a7e3a2f8308db.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: Controllerb755d752252f37986e1a7e3a2f8308db.url(options),
    method: 'head',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging/settings'
*/
const Controllerb755d752252f37986e1a7e3a2f8308dbForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb755d752252f37986e1a7e3a2f8308db.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging/settings'
*/
Controllerb755d752252f37986e1a7e3a2f8308dbForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb755d752252f37986e1a7e3a2f8308db.url(options),
    method: 'get',
})

/**
* @see \Inertia\Controller::__invoke
* @see vendor/inertiajs/inertia-laravel/src/Controller.php:13
* @route '/messaging/settings'
*/
Controllerb755d752252f37986e1a7e3a2f8308dbForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: Controllerb755d752252f37986e1a7e3a2f8308db.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

Controllerb755d752252f37986e1a7e3a2f8308db.form = Controllerb755d752252f37986e1a7e3a2f8308dbForm

const Controller = {
    '/lms/courses/{id}/policy': Controllerbc0be7e4deff421836f203fa0f5870dd,
    '/deployment/verification-metrics': Controller1536443aaca1d0055cf9728225f4c9b5,
    '/deployment/verification-hub': Controllera7dbed35a12d9097e6c74f450b68d592,
    '/deployment/verification/dashboard/executive': Controllerb7d8e1bf21b023d4c395f04411f4f7b1,
    '/deployment/verification/dashboard/operational': Controller804442f9e2e331a9dbff5c298942c82e,
    '/deployment/verification/dashboard/compliance': Controllerb4319a3b7a81ec4cef594889ce9a7194,
    '/deployment/verification/dashboard/performance': Controllere27a306b64fc1541f6e85c04f4679ea7,
    '/deployment/verification/dashboard/insights': Controller7a93b4b13ca60da089834989d92dad53,
    '/deployment/verification/dashboard/realtime': Controllerfc6f24c041bacb6b74c576eb16f12888,
    '/talent-pass': Controllerf294916370e4bfbc152ae84f39f44ed8,
    '/talent-pass/create': Controllerd8eb82699ec5375ae3c8b9cd4cd9d6c2,
    '/talent-pass/{id}': Controller377010c88ff7b9517dc6b60819bbfdab,
    '/talent-pass/{id}/edit': Controller1d23530a54575f83aa46a4f4f7537e3e,
    '/public/talent-pass/{ulid}': Controller98ea1aa8bf95e23aa708198b0e2ab85e,
    '/messaging': Controller6c8a3d26da0429490535634bb4527732,
    '/messaging/settings': Controllerb755d752252f37986e1a7e3a2f8308db,
}

export default Controller