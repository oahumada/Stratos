import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::heatmap
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:20
* @route '/api/skill-intelligence/heatmap'
*/
export const heatmap = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heatmap.url(options),
    method: 'get',
})

heatmap.definition = {
    methods: ["get","head"],
    url: '/api/skill-intelligence/heatmap',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::heatmap
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:20
* @route '/api/skill-intelligence/heatmap'
*/
heatmap.url = (options?: RouteQueryOptions) => {
    return heatmap.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::heatmap
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:20
* @route '/api/skill-intelligence/heatmap'
*/
heatmap.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heatmap.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::heatmap
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:20
* @route '/api/skill-intelligence/heatmap'
*/
heatmap.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: heatmap.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::heatmap
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:20
* @route '/api/skill-intelligence/heatmap'
*/
const heatmapForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: heatmap.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::heatmap
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:20
* @route '/api/skill-intelligence/heatmap'
*/
heatmapForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: heatmap.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::heatmap
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:20
* @route '/api/skill-intelligence/heatmap'
*/
heatmapForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: heatmap.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

heatmap.form = heatmapForm

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::topGaps
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:34
* @route '/api/skill-intelligence/top-gaps'
*/
export const topGaps = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: topGaps.url(options),
    method: 'get',
})

topGaps.definition = {
    methods: ["get","head"],
    url: '/api/skill-intelligence/top-gaps',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::topGaps
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:34
* @route '/api/skill-intelligence/top-gaps'
*/
topGaps.url = (options?: RouteQueryOptions) => {
    return topGaps.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::topGaps
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:34
* @route '/api/skill-intelligence/top-gaps'
*/
topGaps.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: topGaps.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::topGaps
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:34
* @route '/api/skill-intelligence/top-gaps'
*/
topGaps.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: topGaps.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::topGaps
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:34
* @route '/api/skill-intelligence/top-gaps'
*/
const topGapsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: topGaps.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::topGaps
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:34
* @route '/api/skill-intelligence/top-gaps'
*/
topGapsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: topGaps.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::topGaps
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:34
* @route '/api/skill-intelligence/top-gaps'
*/
topGapsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: topGaps.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

topGaps.form = topGapsForm

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::upskilling
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:49
* @route '/api/skill-intelligence/upskilling'
*/
export const upskilling = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upskilling.url(options),
    method: 'get',
})

upskilling.definition = {
    methods: ["get","head"],
    url: '/api/skill-intelligence/upskilling',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::upskilling
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:49
* @route '/api/skill-intelligence/upskilling'
*/
upskilling.url = (options?: RouteQueryOptions) => {
    return upskilling.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::upskilling
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:49
* @route '/api/skill-intelligence/upskilling'
*/
upskilling.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: upskilling.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::upskilling
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:49
* @route '/api/skill-intelligence/upskilling'
*/
upskilling.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: upskilling.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::upskilling
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:49
* @route '/api/skill-intelligence/upskilling'
*/
const upskillingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: upskilling.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::upskilling
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:49
* @route '/api/skill-intelligence/upskilling'
*/
upskillingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: upskilling.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::upskilling
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:49
* @route '/api/skill-intelligence/upskilling'
*/
upskillingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: upskilling.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

upskilling.form = upskillingForm

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::coverage
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:64
* @route '/api/skill-intelligence/coverage'
*/
export const coverage = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: coverage.url(options),
    method: 'get',
})

coverage.definition = {
    methods: ["get","head"],
    url: '/api/skill-intelligence/coverage',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::coverage
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:64
* @route '/api/skill-intelligence/coverage'
*/
coverage.url = (options?: RouteQueryOptions) => {
    return coverage.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::coverage
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:64
* @route '/api/skill-intelligence/coverage'
*/
coverage.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: coverage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::coverage
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:64
* @route '/api/skill-intelligence/coverage'
*/
coverage.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: coverage.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::coverage
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:64
* @route '/api/skill-intelligence/coverage'
*/
const coverageForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: coverage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::coverage
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:64
* @route '/api/skill-intelligence/coverage'
*/
coverageForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: coverage.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\SkillIntelligenceController::coverage
* @see app/Http/Controllers/Api/SkillIntelligenceController.php:64
* @route '/api/skill-intelligence/coverage'
*/
coverageForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: coverage.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

coverage.form = coverageForm

/**
* @see routes/web.php:341
* @route '/skill-intelligence'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/skill-intelligence',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:341
* @route '/skill-intelligence'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/web.php:341
* @route '/skill-intelligence'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:341
* @route '/skill-intelligence'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/web.php:341
* @route '/skill-intelligence'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:341
* @route '/skill-intelligence'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/web.php:341
* @route '/skill-intelligence'
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

const skillIntelligence = {
    heatmap: Object.assign(heatmap, heatmap),
    topGaps: Object.assign(topGaps, topGaps),
    upskilling: Object.assign(upskilling, upskilling),
    coverage: Object.assign(coverage, coverage),
    index: Object.assign(index, index),
}

export default skillIntelligence