import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../wayfinder'
/**
* @see routes/web.php:114
* @route '/departments/org-chart'
*/
export const orgChart = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orgChart.url(options),
    method: 'get',
})

orgChart.definition = {
    methods: ["get","head"],
    url: '/departments/org-chart',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:114
* @route '/departments/org-chart'
*/
orgChart.url = (options?: RouteQueryOptions) => {
    return orgChart.definition.url + queryParams(options)
}

/**
* @see routes/web.php:114
* @route '/departments/org-chart'
*/
orgChart.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: orgChart.url(options),
    method: 'get',
})

/**
* @see routes/web.php:114
* @route '/departments/org-chart'
*/
orgChart.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: orgChart.url(options),
    method: 'head',
})

/**
* @see routes/web.php:114
* @route '/departments/org-chart'
*/
const orgChartForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orgChart.url(options),
    method: 'get',
})

/**
* @see routes/web.php:114
* @route '/departments/org-chart'
*/
orgChartForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orgChart.url(options),
    method: 'get',
})

/**
* @see routes/web.php:114
* @route '/departments/org-chart'
*/
orgChartForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: orgChart.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

orgChart.form = orgChartForm

const departments = {
    orgChart: Object.assign(orgChart, orgChart),
}

export default departments