import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\GapAnalysisController::analyze
* @see app/Http/Controllers/Api/GapAnalysisController.php:14
* @route '/api/gap-analysis'
*/
export const analyze = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyze.url(options),
    method: 'post',
})

analyze.definition = {
    methods: ["post"],
    url: '/api/gap-analysis',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\GapAnalysisController::analyze
* @see app/Http/Controllers/Api/GapAnalysisController.php:14
* @route '/api/gap-analysis'
*/
analyze.url = (options?: RouteQueryOptions) => {
    return analyze.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\GapAnalysisController::analyze
* @see app/Http/Controllers/Api/GapAnalysisController.php:14
* @route '/api/gap-analysis'
*/
analyze.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyze.url(options),
    method: 'post',
})

const GapAnalysisController = { analyze }

export default GapAnalysisController