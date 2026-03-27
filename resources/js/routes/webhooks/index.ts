import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Automation\N8nController::n8n
* @see app/Http/Controllers/Api/Automation/N8nController.php:14
* @route '/api/webhooks/n8n'
*/
export const n8n = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: n8n.url(options),
    method: 'post',
})

n8n.definition = {
    methods: ["post"],
    url: '/api/webhooks/n8n',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Automation\N8nController::n8n
* @see app/Http/Controllers/Api/Automation/N8nController.php:14
* @route '/api/webhooks/n8n'
*/
n8n.url = (options?: RouteQueryOptions) => {
    return n8n.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Automation\N8nController::n8n
* @see app/Http/Controllers/Api/Automation/N8nController.php:14
* @route '/api/webhooks/n8n'
*/
n8n.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: n8n.url(options),
    method: 'post',
})

const webhooks = {
    n8n: Object.assign(n8n, n8n),
}

export default webhooks