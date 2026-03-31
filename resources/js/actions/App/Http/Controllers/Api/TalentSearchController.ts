import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\TalentSearchController::search
* @see app/Http/Controllers/Api/TalentSearchController.php:18
* @route '/api/search'
*/
export const search = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(options),
    method: 'get',
})

search.definition = {
    methods: ["get","head"],
    url: '/api/search',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentSearchController::search
* @see app/Http/Controllers/Api/TalentSearchController.php:18
* @route '/api/search'
*/
search.url = (options?: RouteQueryOptions) => {
    return search.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentSearchController::search
* @see app/Http/Controllers/Api/TalentSearchController.php:18
* @route '/api/search'
*/
search.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: search.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::search
* @see app/Http/Controllers/Api/TalentSearchController.php:18
* @route '/api/search'
*/
search.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: search.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::search
* @see app/Http/Controllers/Api/TalentSearchController.php:18
* @route '/api/search'
*/
const searchForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: search.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::search
* @see app/Http/Controllers/Api/TalentSearchController.php:18
* @route '/api/search'
*/
searchForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: search.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::search
* @see app/Http/Controllers/Api/TalentSearchController.php:18
* @route '/api/search'
*/
searchForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: search.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

search.form = searchForm

/**
* @see \App\Http\Controllers\Api\TalentSearchController::searchBySkills
* @see app/Http/Controllers/Api/TalentSearchController.php:49
* @route '/api/search/skills'
*/
export const searchBySkills = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: searchBySkills.url(options),
    method: 'get',
})

searchBySkills.definition = {
    methods: ["get","head"],
    url: '/api/search/skills',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentSearchController::searchBySkills
* @see app/Http/Controllers/Api/TalentSearchController.php:49
* @route '/api/search/skills'
*/
searchBySkills.url = (options?: RouteQueryOptions) => {
    return searchBySkills.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentSearchController::searchBySkills
* @see app/Http/Controllers/Api/TalentSearchController.php:49
* @route '/api/search/skills'
*/
searchBySkills.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: searchBySkills.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::searchBySkills
* @see app/Http/Controllers/Api/TalentSearchController.php:49
* @route '/api/search/skills'
*/
searchBySkills.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: searchBySkills.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::searchBySkills
* @see app/Http/Controllers/Api/TalentSearchController.php:49
* @route '/api/search/skills'
*/
const searchBySkillsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: searchBySkills.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::searchBySkills
* @see app/Http/Controllers/Api/TalentSearchController.php:49
* @route '/api/search/skills'
*/
searchBySkillsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: searchBySkills.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::searchBySkills
* @see app/Http/Controllers/Api/TalentSearchController.php:49
* @route '/api/search/skills'
*/
searchBySkillsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: searchBySkills.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

searchBySkills.form = searchBySkillsForm

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findBySkillLevel
* @see app/Http/Controllers/Api/TalentSearchController.php:148
* @route '/api/search/skill-level'
*/
export const findBySkillLevel = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: findBySkillLevel.url(options),
    method: 'get',
})

findBySkillLevel.definition = {
    methods: ["get","head"],
    url: '/api/search/skill-level',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findBySkillLevel
* @see app/Http/Controllers/Api/TalentSearchController.php:148
* @route '/api/search/skill-level'
*/
findBySkillLevel.url = (options?: RouteQueryOptions) => {
    return findBySkillLevel.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findBySkillLevel
* @see app/Http/Controllers/Api/TalentSearchController.php:148
* @route '/api/search/skill-level'
*/
findBySkillLevel.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: findBySkillLevel.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findBySkillLevel
* @see app/Http/Controllers/Api/TalentSearchController.php:148
* @route '/api/search/skill-level'
*/
findBySkillLevel.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: findBySkillLevel.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findBySkillLevel
* @see app/Http/Controllers/Api/TalentSearchController.php:148
* @route '/api/search/skill-level'
*/
const findBySkillLevelForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: findBySkillLevel.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findBySkillLevel
* @see app/Http/Controllers/Api/TalentSearchController.php:148
* @route '/api/search/skill-level'
*/
findBySkillLevelForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: findBySkillLevel.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findBySkillLevel
* @see app/Http/Controllers/Api/TalentSearchController.php:148
* @route '/api/search/skill-level'
*/
findBySkillLevelForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: findBySkillLevel.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

findBySkillLevel.form = findBySkillLevelForm

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByExperience
* @see app/Http/Controllers/Api/TalentSearchController.php:179
* @route '/api/search/experience'
*/
export const findByExperience = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: findByExperience.url(options),
    method: 'get',
})

findByExperience.definition = {
    methods: ["get","head"],
    url: '/api/search/experience',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByExperience
* @see app/Http/Controllers/Api/TalentSearchController.php:179
* @route '/api/search/experience'
*/
findByExperience.url = (options?: RouteQueryOptions) => {
    return findByExperience.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByExperience
* @see app/Http/Controllers/Api/TalentSearchController.php:179
* @route '/api/search/experience'
*/
findByExperience.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: findByExperience.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByExperience
* @see app/Http/Controllers/Api/TalentSearchController.php:179
* @route '/api/search/experience'
*/
findByExperience.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: findByExperience.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByExperience
* @see app/Http/Controllers/Api/TalentSearchController.php:179
* @route '/api/search/experience'
*/
const findByExperienceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: findByExperience.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByExperience
* @see app/Http/Controllers/Api/TalentSearchController.php:179
* @route '/api/search/experience'
*/
findByExperienceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: findByExperience.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByExperience
* @see app/Http/Controllers/Api/TalentSearchController.php:179
* @route '/api/search/experience'
*/
findByExperienceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: findByExperience.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

findByExperience.form = findByExperienceForm

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByCredential
* @see app/Http/Controllers/Api/TalentSearchController.php:205
* @route '/api/search/credential'
*/
export const findByCredential = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: findByCredential.url(options),
    method: 'get',
})

findByCredential.definition = {
    methods: ["get","head"],
    url: '/api/search/credential',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByCredential
* @see app/Http/Controllers/Api/TalentSearchController.php:205
* @route '/api/search/credential'
*/
findByCredential.url = (options?: RouteQueryOptions) => {
    return findByCredential.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByCredential
* @see app/Http/Controllers/Api/TalentSearchController.php:205
* @route '/api/search/credential'
*/
findByCredential.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: findByCredential.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByCredential
* @see app/Http/Controllers/Api/TalentSearchController.php:205
* @route '/api/search/credential'
*/
findByCredential.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: findByCredential.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByCredential
* @see app/Http/Controllers/Api/TalentSearchController.php:205
* @route '/api/search/credential'
*/
const findByCredentialForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: findByCredential.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByCredential
* @see app/Http/Controllers/Api/TalentSearchController.php:205
* @route '/api/search/credential'
*/
findByCredentialForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: findByCredential.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::findByCredential
* @see app/Http/Controllers/Api/TalentSearchController.php:205
* @route '/api/search/credential'
*/
findByCredentialForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: findByCredential.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

findByCredential.form = findByCredentialForm

/**
* @see \App\Http\Controllers\Api\TalentSearchController::similar
* @see app/Http/Controllers/Api/TalentSearchController.php:113
* @route '/api/search/similar'
*/
export const similar = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: similar.url(options),
    method: 'get',
})

similar.definition = {
    methods: ["get","head"],
    url: '/api/search/similar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentSearchController::similar
* @see app/Http/Controllers/Api/TalentSearchController.php:113
* @route '/api/search/similar'
*/
similar.url = (options?: RouteQueryOptions) => {
    return similar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentSearchController::similar
* @see app/Http/Controllers/Api/TalentSearchController.php:113
* @route '/api/search/similar'
*/
similar.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: similar.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::similar
* @see app/Http/Controllers/Api/TalentSearchController.php:113
* @route '/api/search/similar'
*/
similar.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: similar.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::similar
* @see app/Http/Controllers/Api/TalentSearchController.php:113
* @route '/api/search/similar'
*/
const similarForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: similar.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::similar
* @see app/Http/Controllers/Api/TalentSearchController.php:113
* @route '/api/search/similar'
*/
similarForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: similar.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::similar
* @see app/Http/Controllers/Api/TalentSearchController.php:113
* @route '/api/search/similar'
*/
similarForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: similar.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

similar.form = similarForm

/**
* @see \App\Http\Controllers\Api\TalentSearchController::getTrending
* @see app/Http/Controllers/Api/TalentSearchController.php:77
* @route '/api/analytics/trending'
*/
export const getTrending = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getTrending.url(options),
    method: 'get',
})

getTrending.definition = {
    methods: ["get","head"],
    url: '/api/analytics/trending',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\TalentSearchController::getTrending
* @see app/Http/Controllers/Api/TalentSearchController.php:77
* @route '/api/analytics/trending'
*/
getTrending.url = (options?: RouteQueryOptions) => {
    return getTrending.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentSearchController::getTrending
* @see app/Http/Controllers/Api/TalentSearchController.php:77
* @route '/api/analytics/trending'
*/
getTrending.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: getTrending.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::getTrending
* @see app/Http/Controllers/Api/TalentSearchController.php:77
* @route '/api/analytics/trending'
*/
getTrending.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: getTrending.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::getTrending
* @see app/Http/Controllers/Api/TalentSearchController.php:77
* @route '/api/analytics/trending'
*/
const getTrendingForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getTrending.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::getTrending
* @see app/Http/Controllers/Api/TalentSearchController.php:77
* @route '/api/analytics/trending'
*/
getTrendingForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getTrending.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::getTrending
* @see app/Http/Controllers/Api/TalentSearchController.php:77
* @route '/api/analytics/trending'
*/
getTrendingForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: getTrending.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

getTrending.form = getTrendingForm

/**
* @see \App\Http\Controllers\Api\TalentSearchController::gaps
* @see app/Http/Controllers/Api/TalentSearchController.php:93
* @route '/api/analytics/gaps'
*/
export const gaps = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: gaps.url(options),
    method: 'post',
})

gaps.definition = {
    methods: ["post"],
    url: '/api/analytics/gaps',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\TalentSearchController::gaps
* @see app/Http/Controllers/Api/TalentSearchController.php:93
* @route '/api/analytics/gaps'
*/
gaps.url = (options?: RouteQueryOptions) => {
    return gaps.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\TalentSearchController::gaps
* @see app/Http/Controllers/Api/TalentSearchController.php:93
* @route '/api/analytics/gaps'
*/
gaps.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: gaps.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::gaps
* @see app/Http/Controllers/Api/TalentSearchController.php:93
* @route '/api/analytics/gaps'
*/
const gapsForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: gaps.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\TalentSearchController::gaps
* @see app/Http/Controllers/Api/TalentSearchController.php:93
* @route '/api/analytics/gaps'
*/
gapsForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: gaps.url(options),
    method: 'post',
})

gaps.form = gapsForm

const TalentSearchController = { search, searchBySkills, findBySkillLevel, findByExperience, findByCredential, similar, getTrending, gaps }

export default TalentSearchController