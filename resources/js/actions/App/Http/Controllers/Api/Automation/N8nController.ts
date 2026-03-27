import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Automation\N8nController::handleWebhook
* @see app/Http/Controllers/Api/Automation/N8nController.php:14
* @route '/api/webhooks/n8n'
*/
export const handleWebhook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handleWebhook.url(options),
    method: 'post',
})

handleWebhook.definition = {
    methods: ["post"],
    url: '/api/webhooks/n8n',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Automation\N8nController::handleWebhook
* @see app/Http/Controllers/Api/Automation/N8nController.php:14
* @route '/api/webhooks/n8n'
*/
handleWebhook.url = (options?: RouteQueryOptions) => {
    return handleWebhook.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Automation\N8nController::handleWebhook
* @see app/Http/Controllers/Api/Automation/N8nController.php:14
* @route '/api/webhooks/n8n'
*/
handleWebhook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: handleWebhook.url(options),
    method: 'post',
})

const N8nController = { handleWebhook }

export default N8nController