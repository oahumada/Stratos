import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
const index1d0224eb7d1405afd76b0543bb1fb03b = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index1d0224eb7d1405afd76b0543bb1fb03b.url(options),
    method: 'get',
})

index1d0224eb7d1405afd76b0543bb1fb03b.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenario-templates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
index1d0224eb7d1405afd76b0543bb1fb03b.url = (options?: RouteQueryOptions) => {
    return index1d0224eb7d1405afd76b0543bb1fb03b.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
index1d0224eb7d1405afd76b0543bb1fb03b.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index1d0224eb7d1405afd76b0543bb1fb03b.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
index1d0224eb7d1405afd76b0543bb1fb03b.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index1d0224eb7d1405afd76b0543bb1fb03b.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
const index1d0224eb7d1405afd76b0543bb1fb03bForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index1d0224eb7d1405afd76b0543bb1fb03b.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
index1d0224eb7d1405afd76b0543bb1fb03bForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index1d0224eb7d1405afd76b0543bb1fb03b.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/scenario-templates'
*/
index1d0224eb7d1405afd76b0543bb1fb03bForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index1d0224eb7d1405afd76b0543bb1fb03b.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index1d0224eb7d1405afd76b0543bb1fb03b.form = index1d0224eb7d1405afd76b0543bb1fb03bForm
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
const indexe547a1dcd49989c6bf93a138a64bd86a = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexe547a1dcd49989c6bf93a138a64bd86a.url(options),
    method: 'get',
})

indexe547a1dcd49989c6bf93a138a64bd86a.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/strategic-planning/scenario-templates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
indexe547a1dcd49989c6bf93a138a64bd86a.url = (options?: RouteQueryOptions) => {
    return indexe547a1dcd49989c6bf93a138a64bd86a.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
indexe547a1dcd49989c6bf93a138a64bd86a.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: indexe547a1dcd49989c6bf93a138a64bd86a.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
indexe547a1dcd49989c6bf93a138a64bd86a.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: indexe547a1dcd49989c6bf93a138a64bd86a.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
const indexe547a1dcd49989c6bf93a138a64bd86aForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexe547a1dcd49989c6bf93a138a64bd86a.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
indexe547a1dcd49989c6bf93a138a64bd86aForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexe547a1dcd49989c6bf93a138a64bd86a.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::index
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:22
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
indexe547a1dcd49989c6bf93a138a64bd86aForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: indexe547a1dcd49989c6bf93a138a64bd86a.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

indexe547a1dcd49989c6bf93a138a64bd86a.form = indexe547a1dcd49989c6bf93a138a64bd86aForm

export const index = {
    '/api/strategic-planning/scenario-templates': index1d0224eb7d1405afd76b0543bb1fb03b,
    '/api/strategic-planning/strategic-planning/scenario-templates': indexe547a1dcd49989c6bf93a138a64bd86a,
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/scenario-templates'
*/
const store1d0224eb7d1405afd76b0543bb1fb03b = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store1d0224eb7d1405afd76b0543bb1fb03b.url(options),
    method: 'post',
})

store1d0224eb7d1405afd76b0543bb1fb03b.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenario-templates',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/scenario-templates'
*/
store1d0224eb7d1405afd76b0543bb1fb03b.url = (options?: RouteQueryOptions) => {
    return store1d0224eb7d1405afd76b0543bb1fb03b.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/scenario-templates'
*/
store1d0224eb7d1405afd76b0543bb1fb03b.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store1d0224eb7d1405afd76b0543bb1fb03b.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/scenario-templates'
*/
const store1d0224eb7d1405afd76b0543bb1fb03bForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store1d0224eb7d1405afd76b0543bb1fb03b.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/scenario-templates'
*/
store1d0224eb7d1405afd76b0543bb1fb03bForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store1d0224eb7d1405afd76b0543bb1fb03b.url(options),
    method: 'post',
})

store1d0224eb7d1405afd76b0543bb1fb03b.form = store1d0224eb7d1405afd76b0543bb1fb03bForm
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
const storee547a1dcd49989c6bf93a138a64bd86a = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storee547a1dcd49989c6bf93a138a64bd86a.url(options),
    method: 'post',
})

storee547a1dcd49989c6bf93a138a64bd86a.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/strategic-planning/scenario-templates',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
storee547a1dcd49989c6bf93a138a64bd86a.url = (options?: RouteQueryOptions) => {
    return storee547a1dcd49989c6bf93a138a64bd86a.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
storee547a1dcd49989c6bf93a138a64bd86a.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storee547a1dcd49989c6bf93a138a64bd86a.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
const storee547a1dcd49989c6bf93a138a64bd86aForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storee547a1dcd49989c6bf93a138a64bd86a.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::store
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:63
* @route '/api/strategic-planning/strategic-planning/scenario-templates'
*/
storee547a1dcd49989c6bf93a138a64bd86aForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storee547a1dcd49989c6bf93a138a64bd86a.url(options),
    method: 'post',
})

storee547a1dcd49989c6bf93a138a64bd86a.form = storee547a1dcd49989c6bf93a138a64bd86aForm

export const store = {
    '/api/strategic-planning/scenario-templates': store1d0224eb7d1405afd76b0543bb1fb03b,
    '/api/strategic-planning/strategic-planning/scenario-templates': storee547a1dcd49989c6bf93a138a64bd86a,
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
const recommendationsd25328ec602eb1d0b46a63b438a5dfb2 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendationsd25328ec602eb1d0b46a63b438a5dfb2.url(options),
    method: 'get',
})

recommendationsd25328ec602eb1d0b46a63b438a5dfb2.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenario-templates/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
recommendationsd25328ec602eb1d0b46a63b438a5dfb2.url = (options?: RouteQueryOptions) => {
    return recommendationsd25328ec602eb1d0b46a63b438a5dfb2.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
recommendationsd25328ec602eb1d0b46a63b438a5dfb2.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendationsd25328ec602eb1d0b46a63b438a5dfb2.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
recommendationsd25328ec602eb1d0b46a63b438a5dfb2.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recommendationsd25328ec602eb1d0b46a63b438a5dfb2.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
const recommendationsd25328ec602eb1d0b46a63b438a5dfb2Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendationsd25328ec602eb1d0b46a63b438a5dfb2.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
recommendationsd25328ec602eb1d0b46a63b438a5dfb2Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendationsd25328ec602eb1d0b46a63b438a5dfb2.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/scenario-templates/recommendations'
*/
recommendationsd25328ec602eb1d0b46a63b438a5dfb2Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendationsd25328ec602eb1d0b46a63b438a5dfb2.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

recommendationsd25328ec602eb1d0b46a63b438a5dfb2.form = recommendationsd25328ec602eb1d0b46a63b438a5dfb2Form
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/strategic-planning/scenario-templates/recommendations'
*/
const recommendations99f7984f96be9302d3c5552a0285d1fa = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendations99f7984f96be9302d3c5552a0285d1fa.url(options),
    method: 'get',
})

recommendations99f7984f96be9302d3c5552a0285d1fa.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/strategic-planning/scenario-templates/recommendations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/strategic-planning/scenario-templates/recommendations'
*/
recommendations99f7984f96be9302d3c5552a0285d1fa.url = (options?: RouteQueryOptions) => {
    return recommendations99f7984f96be9302d3c5552a0285d1fa.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/strategic-planning/scenario-templates/recommendations'
*/
recommendations99f7984f96be9302d3c5552a0285d1fa.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: recommendations99f7984f96be9302d3c5552a0285d1fa.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/strategic-planning/scenario-templates/recommendations'
*/
recommendations99f7984f96be9302d3c5552a0285d1fa.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: recommendations99f7984f96be9302d3c5552a0285d1fa.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/strategic-planning/scenario-templates/recommendations'
*/
const recommendations99f7984f96be9302d3c5552a0285d1faForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations99f7984f96be9302d3c5552a0285d1fa.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/strategic-planning/scenario-templates/recommendations'
*/
recommendations99f7984f96be9302d3c5552a0285d1faForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations99f7984f96be9302d3c5552a0285d1fa.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::recommendations
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:198
* @route '/api/strategic-planning/strategic-planning/scenario-templates/recommendations'
*/
recommendations99f7984f96be9302d3c5552a0285d1faForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: recommendations99f7984f96be9302d3c5552a0285d1fa.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

recommendations99f7984f96be9302d3c5552a0285d1fa.form = recommendations99f7984f96be9302d3c5552a0285d1faForm

export const recommendations = {
    '/api/strategic-planning/scenario-templates/recommendations': recommendationsd25328ec602eb1d0b46a63b438a5dfb2,
    '/api/strategic-planning/strategic-planning/scenario-templates/recommendations': recommendations99f7984f96be9302d3c5552a0285d1fa,
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
const statistics08d2927809189e3d894b175f75987a14 = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: statistics08d2927809189e3d894b175f75987a14.url(options),
    method: 'get',
})

statistics08d2927809189e3d894b175f75987a14.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenario-templates/statistics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
statistics08d2927809189e3d894b175f75987a14.url = (options?: RouteQueryOptions) => {
    return statistics08d2927809189e3d894b175f75987a14.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
statistics08d2927809189e3d894b175f75987a14.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: statistics08d2927809189e3d894b175f75987a14.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
statistics08d2927809189e3d894b175f75987a14.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: statistics08d2927809189e3d894b175f75987a14.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
const statistics08d2927809189e3d894b175f75987a14Form = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics08d2927809189e3d894b175f75987a14.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
statistics08d2927809189e3d894b175f75987a14Form.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics08d2927809189e3d894b175f75987a14.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/scenario-templates/statistics'
*/
statistics08d2927809189e3d894b175f75987a14Form.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics08d2927809189e3d894b175f75987a14.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

statistics08d2927809189e3d894b175f75987a14.form = statistics08d2927809189e3d894b175f75987a14Form
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/strategic-planning/scenario-templates/statistics'
*/
const statistics408df13a43642c8059dbe9818f87b58e = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: statistics408df13a43642c8059dbe9818f87b58e.url(options),
    method: 'get',
})

statistics408df13a43642c8059dbe9818f87b58e.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/strategic-planning/scenario-templates/statistics',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/strategic-planning/scenario-templates/statistics'
*/
statistics408df13a43642c8059dbe9818f87b58e.url = (options?: RouteQueryOptions) => {
    return statistics408df13a43642c8059dbe9818f87b58e.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/strategic-planning/scenario-templates/statistics'
*/
statistics408df13a43642c8059dbe9818f87b58e.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: statistics408df13a43642c8059dbe9818f87b58e.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/strategic-planning/scenario-templates/statistics'
*/
statistics408df13a43642c8059dbe9818f87b58e.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: statistics408df13a43642c8059dbe9818f87b58e.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/strategic-planning/scenario-templates/statistics'
*/
const statistics408df13a43642c8059dbe9818f87b58eForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics408df13a43642c8059dbe9818f87b58e.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/strategic-planning/scenario-templates/statistics'
*/
statistics408df13a43642c8059dbe9818f87b58eForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics408df13a43642c8059dbe9818f87b58e.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::statistics
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:218
* @route '/api/strategic-planning/strategic-planning/scenario-templates/statistics'
*/
statistics408df13a43642c8059dbe9818f87b58eForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: statistics408df13a43642c8059dbe9818f87b58e.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

statistics408df13a43642c8059dbe9818f87b58e.form = statistics408df13a43642c8059dbe9818f87b58eForm

export const statistics = {
    '/api/strategic-planning/scenario-templates/statistics': statistics08d2927809189e3d894b175f75987a14,
    '/api/strategic-planning/strategic-planning/scenario-templates/statistics': statistics408df13a43642c8059dbe9818f87b58e,
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
const showe5d2ac85c532ce9fd56dbee867fb333b = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showe5d2ac85c532ce9fd56dbee867fb333b.url(args, options),
    method: 'get',
})

showe5d2ac85c532ce9fd56dbee867fb333b.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/scenario-templates/{template}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
showe5d2ac85c532ce9fd56dbee867fb333b.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return showe5d2ac85c532ce9fd56dbee867fb333b.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
showe5d2ac85c532ce9fd56dbee867fb333b.get = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: showe5d2ac85c532ce9fd56dbee867fb333b.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
showe5d2ac85c532ce9fd56dbee867fb333b.head = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: showe5d2ac85c532ce9fd56dbee867fb333b.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
const showe5d2ac85c532ce9fd56dbee867fb333bForm = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showe5d2ac85c532ce9fd56dbee867fb333b.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
showe5d2ac85c532ce9fd56dbee867fb333bForm.get = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showe5d2ac85c532ce9fd56dbee867fb333b.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
showe5d2ac85c532ce9fd56dbee867fb333bForm.head = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: showe5d2ac85c532ce9fd56dbee867fb333b.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

showe5d2ac85c532ce9fd56dbee867fb333b.form = showe5d2ac85c532ce9fd56dbee867fb333bForm
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
const show26e6ca352a56646474e7b07182c40456 = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show26e6ca352a56646474e7b07182c40456.url(args, options),
    method: 'get',
})

show26e6ca352a56646474e7b07182c40456.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/strategic-planning/scenario-templates/{template}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
show26e6ca352a56646474e7b07182c40456.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show26e6ca352a56646474e7b07182c40456.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
show26e6ca352a56646474e7b07182c40456.get = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show26e6ca352a56646474e7b07182c40456.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
show26e6ca352a56646474e7b07182c40456.head = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show26e6ca352a56646474e7b07182c40456.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
const show26e6ca352a56646474e7b07182c40456Form = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show26e6ca352a56646474e7b07182c40456.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
show26e6ca352a56646474e7b07182c40456Form.get = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show26e6ca352a56646474e7b07182c40456.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::show
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:48
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
show26e6ca352a56646474e7b07182c40456Form.head = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show26e6ca352a56646474e7b07182c40456.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show26e6ca352a56646474e7b07182c40456.form = show26e6ca352a56646474e7b07182c40456Form

export const show = {
    '/api/strategic-planning/scenario-templates/{template}': showe5d2ac85c532ce9fd56dbee867fb333b,
    '/api/strategic-planning/strategic-planning/scenario-templates/{template}': show26e6ca352a56646474e7b07182c40456,
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
const updatee5d2ac85c532ce9fd56dbee867fb333b = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatee5d2ac85c532ce9fd56dbee867fb333b.url(args, options),
    method: 'patch',
})

updatee5d2ac85c532ce9fd56dbee867fb333b.definition = {
    methods: ["patch"],
    url: '/api/strategic-planning/scenario-templates/{template}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
updatee5d2ac85c532ce9fd56dbee867fb333b.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updatee5d2ac85c532ce9fd56dbee867fb333b.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
updatee5d2ac85c532ce9fd56dbee867fb333b.patch = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updatee5d2ac85c532ce9fd56dbee867fb333b.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
const updatee5d2ac85c532ce9fd56dbee867fb333bForm = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatee5d2ac85c532ce9fd56dbee867fb333b.url(args, {
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
updatee5d2ac85c532ce9fd56dbee867fb333bForm.patch = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updatee5d2ac85c532ce9fd56dbee867fb333b.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updatee5d2ac85c532ce9fd56dbee867fb333b.form = updatee5d2ac85c532ce9fd56dbee867fb333bForm
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
const update26e6ca352a56646474e7b07182c40456 = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update26e6ca352a56646474e7b07182c40456.url(args, options),
    method: 'patch',
})

update26e6ca352a56646474e7b07182c40456.definition = {
    methods: ["patch"],
    url: '/api/strategic-planning/strategic-planning/scenario-templates/{template}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
update26e6ca352a56646474e7b07182c40456.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update26e6ca352a56646474e7b07182c40456.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
update26e6ca352a56646474e7b07182c40456.patch = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update26e6ca352a56646474e7b07182c40456.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::update
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:79
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
const update26e6ca352a56646474e7b07182c40456Form = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update26e6ca352a56646474e7b07182c40456.url(args, {
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
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
update26e6ca352a56646474e7b07182c40456Form.patch = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update26e6ca352a56646474e7b07182c40456.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update26e6ca352a56646474e7b07182c40456.form = update26e6ca352a56646474e7b07182c40456Form

export const update = {
    '/api/strategic-planning/scenario-templates/{template}': updatee5d2ac85c532ce9fd56dbee867fb333b,
    '/api/strategic-planning/strategic-planning/scenario-templates/{template}': update26e6ca352a56646474e7b07182c40456,
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
const destroye5d2ac85c532ce9fd56dbee867fb333b = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroye5d2ac85c532ce9fd56dbee867fb333b.url(args, options),
    method: 'delete',
})

destroye5d2ac85c532ce9fd56dbee867fb333b.definition = {
    methods: ["delete"],
    url: '/api/strategic-planning/scenario-templates/{template}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
destroye5d2ac85c532ce9fd56dbee867fb333b.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroye5d2ac85c532ce9fd56dbee867fb333b.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
destroye5d2ac85c532ce9fd56dbee867fb333b.delete = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroye5d2ac85c532ce9fd56dbee867fb333b.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/scenario-templates/{template}'
*/
const destroye5d2ac85c532ce9fd56dbee867fb333bForm = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroye5d2ac85c532ce9fd56dbee867fb333b.url(args, {
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
destroye5d2ac85c532ce9fd56dbee867fb333bForm.delete = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroye5d2ac85c532ce9fd56dbee867fb333b.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroye5d2ac85c532ce9fd56dbee867fb333b.form = destroye5d2ac85c532ce9fd56dbee867fb333bForm
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
const destroy26e6ca352a56646474e7b07182c40456 = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy26e6ca352a56646474e7b07182c40456.url(args, options),
    method: 'delete',
})

destroy26e6ca352a56646474e7b07182c40456.definition = {
    methods: ["delete"],
    url: '/api/strategic-planning/strategic-planning/scenario-templates/{template}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
destroy26e6ca352a56646474e7b07182c40456.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy26e6ca352a56646474e7b07182c40456.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
destroy26e6ca352a56646474e7b07182c40456.delete = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy26e6ca352a56646474e7b07182c40456.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::destroy
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:95
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
const destroy26e6ca352a56646474e7b07182c40456Form = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy26e6ca352a56646474e7b07182c40456.url(args, {
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
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}'
*/
destroy26e6ca352a56646474e7b07182c40456Form.delete = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy26e6ca352a56646474e7b07182c40456.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy26e6ca352a56646474e7b07182c40456.form = destroy26e6ca352a56646474e7b07182c40456Form

export const destroy = {
    '/api/strategic-planning/scenario-templates/{template}': destroye5d2ac85c532ce9fd56dbee867fb333b,
    '/api/strategic-planning/strategic-planning/scenario-templates/{template}': destroy26e6ca352a56646474e7b07182c40456,
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/scenario-templates/save-as-template'
*/
const saveAsTemplateaa799161c58818b2201d6f8306de2169 = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveAsTemplateaa799161c58818b2201d6f8306de2169.url(options),
    method: 'post',
})

saveAsTemplateaa799161c58818b2201d6f8306de2169.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenario-templates/save-as-template',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/scenario-templates/save-as-template'
*/
saveAsTemplateaa799161c58818b2201d6f8306de2169.url = (options?: RouteQueryOptions) => {
    return saveAsTemplateaa799161c58818b2201d6f8306de2169.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/scenario-templates/save-as-template'
*/
saveAsTemplateaa799161c58818b2201d6f8306de2169.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveAsTemplateaa799161c58818b2201d6f8306de2169.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/scenario-templates/save-as-template'
*/
const saveAsTemplateaa799161c58818b2201d6f8306de2169Form = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveAsTemplateaa799161c58818b2201d6f8306de2169.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/scenario-templates/save-as-template'
*/
saveAsTemplateaa799161c58818b2201d6f8306de2169Form.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveAsTemplateaa799161c58818b2201d6f8306de2169.url(options),
    method: 'post',
})

saveAsTemplateaa799161c58818b2201d6f8306de2169.form = saveAsTemplateaa799161c58818b2201d6f8306de2169Form
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/strategic-planning/scenario-templates/save-as-template'
*/
const saveAsTemplate4c9889707e2459d8d8c5e84724154795 = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveAsTemplate4c9889707e2459d8d8c5e84724154795.url(options),
    method: 'post',
})

saveAsTemplate4c9889707e2459d8d8c5e84724154795.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/strategic-planning/scenario-templates/save-as-template',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/strategic-planning/scenario-templates/save-as-template'
*/
saveAsTemplate4c9889707e2459d8d8c5e84724154795.url = (options?: RouteQueryOptions) => {
    return saveAsTemplate4c9889707e2459d8d8c5e84724154795.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/strategic-planning/scenario-templates/save-as-template'
*/
saveAsTemplate4c9889707e2459d8d8c5e84724154795.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveAsTemplate4c9889707e2459d8d8c5e84724154795.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/strategic-planning/scenario-templates/save-as-template'
*/
const saveAsTemplate4c9889707e2459d8d8c5e84724154795Form = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveAsTemplate4c9889707e2459d8d8c5e84724154795.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::saveAsTemplate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:112
* @route '/api/strategic-planning/strategic-planning/scenario-templates/save-as-template'
*/
saveAsTemplate4c9889707e2459d8d8c5e84724154795Form.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: saveAsTemplate4c9889707e2459d8d8c5e84724154795.url(options),
    method: 'post',
})

saveAsTemplate4c9889707e2459d8d8c5e84724154795.form = saveAsTemplate4c9889707e2459d8d8c5e84724154795Form

export const saveAsTemplate = {
    '/api/strategic-planning/scenario-templates/save-as-template': saveAsTemplateaa799161c58818b2201d6f8306de2169,
    '/api/strategic-planning/strategic-planning/scenario-templates/save-as-template': saveAsTemplate4c9889707e2459d8d8c5e84724154795,
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/scenario-templates/{template}/instantiate'
*/
const instantiatefdc07b18ba67e3dd539612961deeaad5 = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: instantiatefdc07b18ba67e3dd539612961deeaad5.url(args, options),
    method: 'post',
})

instantiatefdc07b18ba67e3dd539612961deeaad5.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenario-templates/{template}/instantiate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/scenario-templates/{template}/instantiate'
*/
instantiatefdc07b18ba67e3dd539612961deeaad5.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return instantiatefdc07b18ba67e3dd539612961deeaad5.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/scenario-templates/{template}/instantiate'
*/
instantiatefdc07b18ba67e3dd539612961deeaad5.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: instantiatefdc07b18ba67e3dd539612961deeaad5.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/scenario-templates/{template}/instantiate'
*/
const instantiatefdc07b18ba67e3dd539612961deeaad5Form = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: instantiatefdc07b18ba67e3dd539612961deeaad5.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/scenario-templates/{template}/instantiate'
*/
instantiatefdc07b18ba67e3dd539612961deeaad5Form.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: instantiatefdc07b18ba67e3dd539612961deeaad5.url(args, options),
    method: 'post',
})

instantiatefdc07b18ba67e3dd539612961deeaad5.form = instantiatefdc07b18ba67e3dd539612961deeaad5Form
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}/instantiate'
*/
const instantiate3841fc07643e3cbc538ed7d572ccd333 = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: instantiate3841fc07643e3cbc538ed7d572ccd333.url(args, options),
    method: 'post',
})

instantiate3841fc07643e3cbc538ed7d572ccd333.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/strategic-planning/scenario-templates/{template}/instantiate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}/instantiate'
*/
instantiate3841fc07643e3cbc538ed7d572ccd333.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return instantiate3841fc07643e3cbc538ed7d572ccd333.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}/instantiate'
*/
instantiate3841fc07643e3cbc538ed7d572ccd333.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: instantiate3841fc07643e3cbc538ed7d572ccd333.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}/instantiate'
*/
const instantiate3841fc07643e3cbc538ed7d572ccd333Form = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: instantiate3841fc07643e3cbc538ed7d572ccd333.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::instantiate
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:141
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}/instantiate'
*/
instantiate3841fc07643e3cbc538ed7d572ccd333Form.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: instantiate3841fc07643e3cbc538ed7d572ccd333.url(args, options),
    method: 'post',
})

instantiate3841fc07643e3cbc538ed7d572ccd333.form = instantiate3841fc07643e3cbc538ed7d572ccd333Form

export const instantiate = {
    '/api/strategic-planning/scenario-templates/{template}/instantiate': instantiatefdc07b18ba67e3dd539612961deeaad5,
    '/api/strategic-planning/strategic-planning/scenario-templates/{template}/instantiate': instantiate3841fc07643e3cbc538ed7d572ccd333,
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/scenario-templates/{template}/clone'
*/
const cloneb174864c9422c09a2f7c463b5b2b6386 = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cloneb174864c9422c09a2f7c463b5b2b6386.url(args, options),
    method: 'post',
})

cloneb174864c9422c09a2f7c463b5b2b6386.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/scenario-templates/{template}/clone',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/scenario-templates/{template}/clone'
*/
cloneb174864c9422c09a2f7c463b5b2b6386.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return cloneb174864c9422c09a2f7c463b5b2b6386.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/scenario-templates/{template}/clone'
*/
cloneb174864c9422c09a2f7c463b5b2b6386.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cloneb174864c9422c09a2f7c463b5b2b6386.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/scenario-templates/{template}/clone'
*/
const cloneb174864c9422c09a2f7c463b5b2b6386Form = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cloneb174864c9422c09a2f7c463b5b2b6386.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/scenario-templates/{template}/clone'
*/
cloneb174864c9422c09a2f7c463b5b2b6386Form.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cloneb174864c9422c09a2f7c463b5b2b6386.url(args, options),
    method: 'post',
})

cloneb174864c9422c09a2f7c463b5b2b6386.form = cloneb174864c9422c09a2f7c463b5b2b6386Form
/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}/clone'
*/
const clonecbd678f81308ffea02a716c51d1b7c89 = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clonecbd678f81308ffea02a716c51d1b7c89.url(args, options),
    method: 'post',
})

clonecbd678f81308ffea02a716c51d1b7c89.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/strategic-planning/scenario-templates/{template}/clone',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}/clone'
*/
clonecbd678f81308ffea02a716c51d1b7c89.url = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return clonecbd678f81308ffea02a716c51d1b7c89.definition.url
            .replace('{template}', parsedArgs.template.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}/clone'
*/
clonecbd678f81308ffea02a716c51d1b7c89.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clonecbd678f81308ffea02a716c51d1b7c89.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}/clone'
*/
const clonecbd678f81308ffea02a716c51d1b7c89Form = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: clonecbd678f81308ffea02a716c51d1b7c89.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ScenarioTemplateController::clone
* @see app/Http/Controllers/Api/ScenarioTemplateController.php:173
* @route '/api/strategic-planning/strategic-planning/scenario-templates/{template}/clone'
*/
clonecbd678f81308ffea02a716c51d1b7c89Form.post = (args: { template: number | { id: number } } | [template: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: clonecbd678f81308ffea02a716c51d1b7c89.url(args, options),
    method: 'post',
})

clonecbd678f81308ffea02a716c51d1b7c89.form = clonecbd678f81308ffea02a716c51d1b7c89Form

export const clone = {
    '/api/strategic-planning/scenario-templates/{template}/clone': cloneb174864c9422c09a2f7c463b5b2b6386,
    '/api/strategic-planning/strategic-planning/scenario-templates/{template}/clone': clonecbd678f81308ffea02a716c51d1b7c89,
}

const ScenarioTemplateController = { index, store, recommendations, statistics, show, update, destroy, saveAsTemplate, instantiate, clone }

export default ScenarioTemplateController