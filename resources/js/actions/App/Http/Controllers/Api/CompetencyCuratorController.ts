import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::curate
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:32
* @route '/api/strategic-planning/assessments/curator/skills/{id}/curate'
*/
export const curate = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: curate.url(args, options),
    method: 'post',
})

curate.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/assessments/curator/skills/{id}/curate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::curate
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:32
* @route '/api/strategic-planning/assessments/curator/skills/{id}/curate'
*/
curate.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return curate.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::curate
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:32
* @route '/api/strategic-planning/assessments/curator/skills/{id}/curate'
*/
curate.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: curate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::curate
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:32
* @route '/api/strategic-planning/assessments/curator/skills/{id}/curate'
*/
const curateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: curate.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::curate
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:32
* @route '/api/strategic-planning/assessments/curator/skills/{id}/curate'
*/
curateForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: curate.url(args, options),
    method: 'post',
})

curate.form = curateForm

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::generateQuestions
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:46
* @route '/api/strategic-planning/assessments/curator/skills/{id}/generate-questions'
*/
export const generateQuestions = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateQuestions.url(args, options),
    method: 'post',
})

generateQuestions.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/assessments/curator/skills/{id}/generate-questions',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::generateQuestions
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:46
* @route '/api/strategic-planning/assessments/curator/skills/{id}/generate-questions'
*/
generateQuestions.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return generateQuestions.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::generateQuestions
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:46
* @route '/api/strategic-planning/assessments/curator/skills/{id}/generate-questions'
*/
generateQuestions.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateQuestions.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::generateQuestions
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:46
* @route '/api/strategic-planning/assessments/curator/skills/{id}/generate-questions'
*/
const generateQuestionsForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateQuestions.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::generateQuestions
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:46
* @route '/api/strategic-planning/assessments/curator/skills/{id}/generate-questions'
*/
generateQuestionsForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: generateQuestions.url(args, options),
    method: 'post',
})

generateQuestions.form = generateQuestionsForm

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::curateCompetency
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:18
* @route '/api/strategic-planning/assessments/curator/competencies/{id}/curate'
*/
export const curateCompetency = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: curateCompetency.url(args, options),
    method: 'post',
})

curateCompetency.definition = {
    methods: ["post"],
    url: '/api/strategic-planning/assessments/curator/competencies/{id}/curate',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::curateCompetency
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:18
* @route '/api/strategic-planning/assessments/curator/competencies/{id}/curate'
*/
curateCompetency.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return curateCompetency.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::curateCompetency
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:18
* @route '/api/strategic-planning/assessments/curator/competencies/{id}/curate'
*/
curateCompetency.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: curateCompetency.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::curateCompetency
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:18
* @route '/api/strategic-planning/assessments/curator/competencies/{id}/curate'
*/
const curateCompetencyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: curateCompetency.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\CompetencyCuratorController::curateCompetency
* @see app/Http/Controllers/Api/CompetencyCuratorController.php:18
* @route '/api/strategic-planning/assessments/curator/competencies/{id}/curate'
*/
curateCompetencyForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: curateCompetency.url(args, options),
    method: 'post',
})

curateCompetency.form = curateCompetencyForm

const CompetencyCuratorController = { curate, generateQuestions, curateCompetency }

export default CompetencyCuratorController