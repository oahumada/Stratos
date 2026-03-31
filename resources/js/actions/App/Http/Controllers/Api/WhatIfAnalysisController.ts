import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeHeadcountImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:34
* @route '/api/strategic-planning/what-if/headcount-impact'
*/
export const analyzeHeadcountImpact = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzeHeadcountImpact.url(options),
    method: 'post',
})

analyzeHeadcountImpact.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/what-if/headcount-impact',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeHeadcountImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:34
* @route '/api/strategic-planning/what-if/headcount-impact'
*/
analyzeHeadcountImpact.url = (options?: RouteQueryOptions) => {
    return analyzeHeadcountImpact.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeHeadcountImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:34
* @route '/api/strategic-planning/what-if/headcount-impact'
*/
analyzeHeadcountImpact.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzeHeadcountImpact.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeHeadcountImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:34
* @route '/api/strategic-planning/what-if/headcount-impact'
*/
const analyzeHeadcountImpactForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzeHeadcountImpact.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeHeadcountImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:34
* @route '/api/strategic-planning/what-if/headcount-impact'
*/
analyzeHeadcountImpactForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzeHeadcountImpact.url(options),
    method: 'post',
})

analyzeHeadcountImpact.form = analyzeHeadcountImpactForm

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeFinancialImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:63
* @route '/api/strategic-planning/what-if/financial-impact'
*/
export const analyzeFinancialImpact = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzeFinancialImpact.url(options),
    method: 'post',
})

analyzeFinancialImpact.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/what-if/financial-impact',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeFinancialImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:63
* @route '/api/strategic-planning/what-if/financial-impact'
*/
analyzeFinancialImpact.url = (options?: RouteQueryOptions) => {
    return analyzeFinancialImpact.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeFinancialImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:63
* @route '/api/strategic-planning/what-if/financial-impact'
*/
analyzeFinancialImpact.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzeFinancialImpact.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeFinancialImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:63
* @route '/api/strategic-planning/what-if/financial-impact'
*/
const analyzeFinancialImpactForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzeFinancialImpact.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeFinancialImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:63
* @route '/api/strategic-planning/what-if/financial-impact'
*/
analyzeFinancialImpactForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzeFinancialImpact.url(options),
    method: 'post',
})

analyzeFinancialImpact.form = analyzeFinancialImpactForm

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeRiskImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:81
* @route '/api/strategic-planning/what-if/risk-impact'
*/
export const analyzeRiskImpact = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzeRiskImpact.url(options),
    method: 'post',
})

analyzeRiskImpact.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/what-if/risk-impact',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeRiskImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:81
* @route '/api/strategic-planning/what-if/risk-impact'
*/
analyzeRiskImpact.url = (options?: RouteQueryOptions) => {
    return analyzeRiskImpact.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeRiskImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:81
* @route '/api/strategic-planning/what-if/risk-impact'
*/
analyzeRiskImpact.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: analyzeRiskImpact.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeRiskImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:81
* @route '/api/strategic-planning/what-if/risk-impact'
*/
const analyzeRiskImpactForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzeRiskImpact.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::analyzeRiskImpact
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:81
* @route '/api/strategic-planning/what-if/risk-impact'
*/
analyzeRiskImpactForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: analyzeRiskImpact.url(options),
    method: 'post',
})

analyzeRiskImpact.form = analyzeRiskImpactForm

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::compareWithBaseline
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:109
* @route '/api/strategic-planning/what-if/compare'
*/
export const compareWithBaseline = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compareWithBaseline.url(options),
    method: 'get',
})

compareWithBaseline.definition = {
    methods: ["get","head"],
    url: '/api/strategic-planning/what-if/compare',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::compareWithBaseline
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:109
* @route '/api/strategic-planning/what-if/compare'
*/
compareWithBaseline.url = (options?: RouteQueryOptions) => {
    return compareWithBaseline.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::compareWithBaseline
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:109
* @route '/api/strategic-planning/what-if/compare'
*/
compareWithBaseline.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: compareWithBaseline.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::compareWithBaseline
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:109
* @route '/api/strategic-planning/what-if/compare'
*/
compareWithBaseline.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: compareWithBaseline.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::compareWithBaseline
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:109
* @route '/api/strategic-planning/what-if/compare'
*/
const compareWithBaselineForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compareWithBaseline.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::compareWithBaseline
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:109
* @route '/api/strategic-planning/what-if/compare'
*/
compareWithBaselineForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compareWithBaseline.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::compareWithBaseline
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:109
* @route '/api/strategic-planning/what-if/compare'
*/
compareWithBaselineForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: compareWithBaseline.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

compareWithBaseline.form = compareWithBaselineForm

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::predictOutcomes
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:132
* @route '/api/strategic-planning/what-if/predict-outcomes'
*/
export const predictOutcomes = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: predictOutcomes.url(options),
    method: 'post',
})

predictOutcomes.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/what-if/predict-outcomes',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::predictOutcomes
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:132
* @route '/api/strategic-planning/what-if/predict-outcomes'
*/
predictOutcomes.url = (options?: RouteQueryOptions) => {
    return predictOutcomes.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::predictOutcomes
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:132
* @route '/api/strategic-planning/what-if/predict-outcomes'
*/
predictOutcomes.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: predictOutcomes.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::predictOutcomes
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:132
* @route '/api/strategic-planning/what-if/predict-outcomes'
*/
const predictOutcomesForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: predictOutcomes.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::predictOutcomes
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:132
* @route '/api/strategic-planning/what-if/predict-outcomes'
*/
predictOutcomesForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: predictOutcomes.url(options),
    method: 'post',
})

predictOutcomes.form = predictOutcomesForm

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::performSensitivityAnalysis
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:159
* @route '/api/strategic-planning/what-if/sensitivity-analysis'
*/
export const performSensitivityAnalysis = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: performSensitivityAnalysis.url(options),
    method: 'post',
})

performSensitivityAnalysis.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/what-if/sensitivity-analysis',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::performSensitivityAnalysis
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:159
* @route '/api/strategic-planning/what-if/sensitivity-analysis'
*/
performSensitivityAnalysis.url = (options?: RouteQueryOptions) => {
    return performSensitivityAnalysis.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::performSensitivityAnalysis
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:159
* @route '/api/strategic-planning/what-if/sensitivity-analysis'
*/
performSensitivityAnalysis.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: performSensitivityAnalysis.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::performSensitivityAnalysis
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:159
* @route '/api/strategic-planning/what-if/sensitivity-analysis'
*/
const performSensitivityAnalysisForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: performSensitivityAnalysis.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::performSensitivityAnalysis
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:159
* @route '/api/strategic-planning/what-if/sensitivity-analysis'
*/
performSensitivityAnalysisForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: performSensitivityAnalysis.url(options),
    method: 'post',
})

performSensitivityAnalysis.form = performSensitivityAnalysisForm

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::comprehensiveAnalysis
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:177
* @route '/api/strategic-planning/what-if/comprehensive'
*/
export const comprehensiveAnalysis = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: comprehensiveAnalysis.url(options),
    method: 'post',
})

comprehensiveAnalysis.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/what-if/comprehensive',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::comprehensiveAnalysis
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:177
* @route '/api/strategic-planning/what-if/comprehensive'
*/
comprehensiveAnalysis.url = (options?: RouteQueryOptions) => {
    return comprehensiveAnalysis.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::comprehensiveAnalysis
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:177
* @route '/api/strategic-planning/what-if/comprehensive'
*/
comprehensiveAnalysis.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: comprehensiveAnalysis.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::comprehensiveAnalysis
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:177
* @route '/api/strategic-planning/what-if/comprehensive'
*/
const comprehensiveAnalysisForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: comprehensiveAnalysis.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\WhatIfAnalysisController::comprehensiveAnalysis
* @see app/Http/Controllers/Api/WhatIfAnalysisController.php:177
* @route '/api/strategic-planning/what-if/comprehensive'
*/
comprehensiveAnalysisForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: comprehensiveAnalysis.url(options),
    method: 'post',
})

comprehensiveAnalysis.form = comprehensiveAnalysisForm

const WhatIfAnalysisController = { analyzeHeadcountImpact, analyzeFinancialImpact, analyzeRiskImpact, compareWithBaseline, predictOutcomes, performSensitivityAnalysis, comprehensiveAnalysis }

export default WhatIfAnalysisController