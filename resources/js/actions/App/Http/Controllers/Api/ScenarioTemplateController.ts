import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenario-templates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
indexForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/scenario-templates'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenario-templates',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/scenario-templates'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/scenario-templates'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/scenario-templates'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/scenario-templates'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
export const recommendations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendations.url(options),
    method: 'get',
})

recommendations.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenario-templates/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
recommendations.url = (options?: RouteQueryOptions) => {
    return recommendations.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
recommendations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
recommendations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recommendations.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
const recommendationsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
recommendationsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
recommendationsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

recommendations.form = recommendationsForm

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
export const statistics = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: statistics.url(options),
    method: 'get',
})

statistics.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenario-templates/statistics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
statistics.url = (options?: RouteQueryOptions) => {
    return statistics.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
statistics.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: statistics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
statistics.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: statistics.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
const statisticsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
statisticsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
statisticsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

statistics.form = statisticsForm

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
export const show = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenario-templates/{template}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
show.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { template: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            template: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        template: typeof args.template === 'object'
        ? args.template.id
        : args.template,
    }

    return show.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
show.get = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
show.head = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
const showForm = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
showForm.get = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
showForm.head = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
export const update = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/api/strategic-planning/scenario-templates/{template}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
update.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { template: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            template: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        template: typeof args.template === 'object'
        ? args.template.id
        : args.template,
    }

    return update.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
update.patch = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
const updateForm = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
updateForm.patch = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
export const destroy = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/strategic-planning/scenario-templates/{template}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
destroy.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { template: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            template: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        template: typeof args.template === 'object'
        ? args.template.id
        : args.template,
    }

    return destroy.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
destroy.delete = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
const destroyForm = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
destroyForm.delete = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/scenario-templates/save-as-template'
*/
export const saveAsTemplate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveAsTemplate.url(options),
    method: 'post',
})

saveAsTemplate.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenario-templates/save-as-template',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/scenario-templates/save-as-template'
*/
saveAsTemplate.url = (options?: RouteQueryOptions) => {
    return saveAsTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/scenario-templates/save-as-template'
*/
saveAsTemplate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveAsTemplate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/scenario-templates/save-as-template'
*/
const saveAsTemplateForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveAsTemplate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/scenario-templates/save-as-template'
*/
saveAsTemplateForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveAsTemplate.url(options),
    method: 'post',
})

saveAsTemplate.form = saveAsTemplateForm

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/scenario-templates/{template}/instantiate'
*/
export const instantiate = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: instantiate.url(args, options),
    method: 'post',
})

instantiate.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenario-templates/{template}/instantiate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/scenario-templates/{template}/instantiate'
*/
instantiate.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { template: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            template: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        template: typeof args.template === 'object'
        ? args.template.id
        : args.template,
    }

    return instantiate.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/scenario-templates/{template}/instantiate'
*/
instantiate.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: instantiate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/scenario-templates/{template}/instantiate'
*/
const instantiateForm = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: instantiate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/scenario-templates/{template}/instantiate'
*/
instantiateForm.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: instantiate.url(args, options),
    method: 'post',
})

instantiate.form = instantiateForm

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/scenario-templates/{template}/clone'
*/
export const clone = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clone.url(args, options),
    method: 'post',
})

clone.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenario-templates/{template}/clone',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/scenario-templates/{template}/clone'
*/
clone.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { template: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { template: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            template: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        template: typeof args.template === 'object'
        ? args.template.id
        : args.template,
    }

    return clone.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/scenario-templates/{template}/clone'
*/
clone.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clone.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/scenario-templates/{template}/clone'
*/
const cloneForm = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: clone.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/scenario-templates/{template}/clone'
*/
cloneForm.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: clone.url(args, options),
    method: 'post',
})

clone.form = cloneForm

const ScenarioTemplateController = { index, store, recommendations, statistics, show, update, destroy, saveAsTemplate, instantiate, clone }

export default ScenarioTemplateController