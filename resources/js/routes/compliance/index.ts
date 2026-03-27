import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::didDocument
* @see app/Http/Controllers/Api/ComplianceAuditController.php:447
* @route '/.well-known/did.json'
*/
export const didDocument = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: didDocument.url(options),
    method: 'get',
})

didDocument.definition = {
    methods: ["get","head"],
    url: '/.well-known/did.json',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::didDocument
* @see app/Http/Controllers/Api/ComplianceAuditController.php:447
* @route '/.well-known/did.json'
*/
didDocument.url = (options?: RouteQueryOptions) => {
    return didDocument.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::didDocument
* @see app/Http/Controllers/Api/ComplianceAuditController.php:447
* @route '/.well-known/did.json'
*/
didDocument.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: didDocument.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::didDocument
* @see app/Http/Controllers/Api/ComplianceAuditController.php:447
* @route '/.well-known/did.json'
*/
didDocument.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: didDocument.url(options),
    method: 'head',
})

const compliance = {
    didDocument: Object.assign(didDocument, didDocument),
}

export default compliance