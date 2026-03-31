import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifyRoleCredentialPublic
* @see app/Http/Controllers/Api/ComplianceAuditController.php:401
* @route '/api/compliance/public/credentials/verify'
*/
export const verifyRoleCredentialPublic = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verifyRoleCredentialPublic.url(options),
    method: 'post',
})

verifyRoleCredentialPublic.definition = {
    methods: ["post"],
    url: '/api/compliance/public/credentials/verify',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifyRoleCredentialPublic
* @see app/Http/Controllers/Api/ComplianceAuditController.php:401
* @route '/api/compliance/public/credentials/verify'
*/
verifyRoleCredentialPublic.url = (options?: RouteQueryOptions) => {
    return verifyRoleCredentialPublic.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifyRoleCredentialPublic
* @see app/Http/Controllers/Api/ComplianceAuditController.php:401
* @route '/api/compliance/public/credentials/verify'
*/
verifyRoleCredentialPublic.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verifyRoleCredentialPublic.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifyRoleCredentialPublic
* @see app/Http/Controllers/Api/ComplianceAuditController.php:401
* @route '/api/compliance/public/credentials/verify'
*/
const verifyRoleCredentialPublicForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verifyRoleCredentialPublic.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifyRoleCredentialPublic
* @see app/Http/Controllers/Api/ComplianceAuditController.php:401
* @route '/api/compliance/public/credentials/verify'
*/
verifyRoleCredentialPublicForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verifyRoleCredentialPublic.url(options),
    method: 'post',
})

verifyRoleCredentialPublic.form = verifyRoleCredentialPublicForm

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifierMetadata
* @see app/Http/Controllers/Api/ComplianceAuditController.php:483
* @route '/api/compliance/public/verifier-metadata'
*/
export const verifierMetadata = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verifierMetadata.url(options),
    method: 'get',
})

verifierMetadata.definition = {
    methods: ["get","head"],
    url: '/api/compliance/public/verifier-metadata',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifierMetadata
* @see app/Http/Controllers/Api/ComplianceAuditController.php:483
* @route '/api/compliance/public/verifier-metadata'
*/
verifierMetadata.url = (options?: RouteQueryOptions) => {
    return verifierMetadata.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifierMetadata
* @see app/Http/Controllers/Api/ComplianceAuditController.php:483
* @route '/api/compliance/public/verifier-metadata'
*/
verifierMetadata.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verifierMetadata.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifierMetadata
* @see app/Http/Controllers/Api/ComplianceAuditController.php:483
* @route '/api/compliance/public/verifier-metadata'
*/
verifierMetadata.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verifierMetadata.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifierMetadata
* @see app/Http/Controllers/Api/ComplianceAuditController.php:483
* @route '/api/compliance/public/verifier-metadata'
*/
const verifierMetadataForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verifierMetadata.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifierMetadata
* @see app/Http/Controllers/Api/ComplianceAuditController.php:483
* @route '/api/compliance/public/verifier-metadata'
*/
verifierMetadataForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verifierMetadata.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifierMetadata
* @see app/Http/Controllers/Api/ComplianceAuditController.php:483
* @route '/api/compliance/public/verifier-metadata'
*/
verifierMetadataForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verifierMetadata.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

verifierMetadata.form = verifierMetadataForm

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::index
* @see app/Http/Controllers/Api/ComplianceAuditController.php:23
* @route '/api/compliance/audit-events'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/compliance/audit-events',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::index
* @see app/Http/Controllers/Api/ComplianceAuditController.php:23
* @route '/api/compliance/audit-events'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::index
* @see app/Http/Controllers/Api/ComplianceAuditController.php:23
* @route '/api/compliance/audit-events'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::index
* @see app/Http/Controllers/Api/ComplianceAuditController.php:23
* @route '/api/compliance/audit-events'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::index
* @see app/Http/Controllers/Api/ComplianceAuditController.php:23
* @route '/api/compliance/audit-events'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::index
* @see app/Http/Controllers/Api/ComplianceAuditController.php:23
* @route '/api/compliance/audit-events'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::index
* @see app/Http/Controllers/Api/ComplianceAuditController.php:23
* @route '/api/compliance/audit-events'
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

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:52
* @route '/api/compliance/audit-events/summary'
*/
export const summary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

summary.definition = {
    methods: ["get","head"],
    url: '/api/compliance/audit-events/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:52
* @route '/api/compliance/audit-events/summary'
*/
summary.url = (options?: RouteQueryOptions) => {
    return summary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:52
* @route '/api/compliance/audit-events/summary'
*/
summary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:52
* @route '/api/compliance/audit-events/summary'
*/
summary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: summary.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:52
* @route '/api/compliance/audit-events/summary'
*/
const summaryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:52
* @route '/api/compliance/audit-events/summary'
*/
summaryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:52
* @route '/api/compliance/audit-events/summary'
*/
summaryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: summary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

summary.form = summaryForm

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::iso30414Summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:79
* @route '/api/compliance/iso30414/summary'
*/
export const iso30414Summary = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: iso30414Summary.url(options),
    method: 'get',
})

iso30414Summary.definition = {
    methods: ["get","head"],
    url: '/api/compliance/iso30414/summary',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::iso30414Summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:79
* @route '/api/compliance/iso30414/summary'
*/
iso30414Summary.url = (options?: RouteQueryOptions) => {
    return iso30414Summary.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::iso30414Summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:79
* @route '/api/compliance/iso30414/summary'
*/
iso30414Summary.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: iso30414Summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::iso30414Summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:79
* @route '/api/compliance/iso30414/summary'
*/
iso30414Summary.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: iso30414Summary.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::iso30414Summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:79
* @route '/api/compliance/iso30414/summary'
*/
const iso30414SummaryForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: iso30414Summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::iso30414Summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:79
* @route '/api/compliance/iso30414/summary'
*/
iso30414SummaryForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: iso30414Summary.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::iso30414Summary
* @see app/Http/Controllers/Api/ComplianceAuditController.php:79
* @route '/api/compliance/iso30414/summary'
*/
iso30414SummaryForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: iso30414Summary.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

iso30414Summary.form = iso30414SummaryForm

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::recordAiConsent
* @see app/Http/Controllers/Api/ComplianceAuditController.php:198
* @route '/api/compliance/consents/ai-processing'
*/
export const recordAiConsent = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordAiConsent.url(options),
    method: 'post',
})

recordAiConsent.definition = {
    methods: ["post"],
    url: '/api/compliance/consents/ai-processing',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::recordAiConsent
* @see app/Http/Controllers/Api/ComplianceAuditController.php:198
* @route '/api/compliance/consents/ai-processing'
*/
recordAiConsent.url = (options?: RouteQueryOptions) => {
    return recordAiConsent.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::recordAiConsent
* @see app/Http/Controllers/Api/ComplianceAuditController.php:198
* @route '/api/compliance/consents/ai-processing'
*/
recordAiConsent.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordAiConsent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::recordAiConsent
* @see app/Http/Controllers/Api/ComplianceAuditController.php:198
* @route '/api/compliance/consents/ai-processing'
*/
const recordAiConsentForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: recordAiConsent.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::recordAiConsent
* @see app/Http/Controllers/Api/ComplianceAuditController.php:198
* @route '/api/compliance/consents/ai-processing'
*/
recordAiConsentForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: recordAiConsent.url(options),
    method: 'post',
})

recordAiConsent.form = recordAiConsentForm

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::executeGdprPurge
* @see app/Http/Controllers/Api/ComplianceAuditController.php:261
* @route '/api/compliance/gdpr/purge'
*/
export const executeGdprPurge = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: executeGdprPurge.url(options),
    method: 'post',
})

executeGdprPurge.definition = {
    methods: ["post"],
    url: '/api/compliance/gdpr/purge',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::executeGdprPurge
* @see app/Http/Controllers/Api/ComplianceAuditController.php:261
* @route '/api/compliance/gdpr/purge'
*/
executeGdprPurge.url = (options?: RouteQueryOptions) => {
    return executeGdprPurge.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::executeGdprPurge
* @see app/Http/Controllers/Api/ComplianceAuditController.php:261
* @route '/api/compliance/gdpr/purge'
*/
executeGdprPurge.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: executeGdprPurge.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::executeGdprPurge
* @see app/Http/Controllers/Api/ComplianceAuditController.php:261
* @route '/api/compliance/gdpr/purge'
*/
const executeGdprPurgeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: executeGdprPurge.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::executeGdprPurge
* @see app/Http/Controllers/Api/ComplianceAuditController.php:261
* @route '/api/compliance/gdpr/purge'
*/
executeGdprPurgeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: executeGdprPurge.url(options),
    method: 'post',
})

executeGdprPurge.form = executeGdprPurgeForm

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::exportRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:354
* @route '/api/compliance/credentials/roles/{roleId}'
*/
export const exportRoleCredential = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportRoleCredential.url(args, options),
    method: 'get',
})

exportRoleCredential.definition = {
    methods: ["get","head"],
    url: '/api/compliance/credentials/roles/{roleId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::exportRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:354
* @route '/api/compliance/credentials/roles/{roleId}'
*/
exportRoleCredential.url = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { roleId: args }
    }

    if (Array.isArray(args)) {
        args = {
            roleId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        roleId: args.roleId,
    }

    return exportRoleCredential.definition.url
            .replace('{roleId}', parsedArgs.roleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::exportRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:354
* @route '/api/compliance/credentials/roles/{roleId}'
*/
exportRoleCredential.get = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: exportRoleCredential.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::exportRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:354
* @route '/api/compliance/credentials/roles/{roleId}'
*/
exportRoleCredential.head = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: exportRoleCredential.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::exportRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:354
* @route '/api/compliance/credentials/roles/{roleId}'
*/
const exportRoleCredentialForm = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportRoleCredential.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::exportRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:354
* @route '/api/compliance/credentials/roles/{roleId}'
*/
exportRoleCredentialForm.get = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportRoleCredential.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::exportRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:354
* @route '/api/compliance/credentials/roles/{roleId}'
*/
exportRoleCredentialForm.head = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: exportRoleCredential.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

exportRoleCredential.form = exportRoleCredentialForm

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifyRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:375
* @route '/api/compliance/credentials/roles/{roleId}/verify'
*/
export const verifyRoleCredential = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verifyRoleCredential.url(args, options),
    method: 'post',
})

verifyRoleCredential.definition = {
    methods: ["post"],
    url: '/api/compliance/credentials/roles/{roleId}/verify',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifyRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:375
* @route '/api/compliance/credentials/roles/{roleId}/verify'
*/
verifyRoleCredential.url = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { roleId: args }
    }

    if (Array.isArray(args)) {
        args = {
            roleId: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        roleId: args.roleId,
    }

    return verifyRoleCredential.definition.url
            .replace('{roleId}', parsedArgs.roleId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifyRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:375
* @route '/api/compliance/credentials/roles/{roleId}/verify'
*/
verifyRoleCredential.post = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: verifyRoleCredential.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifyRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:375
* @route '/api/compliance/credentials/roles/{roleId}/verify'
*/
const verifyRoleCredentialForm = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verifyRoleCredential.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::verifyRoleCredential
* @see app/Http/Controllers/Api/ComplianceAuditController.php:375
* @route '/api/compliance/credentials/roles/{roleId}/verify'
*/
verifyRoleCredentialForm.post = (args: { roleId: string | number } | [roleId: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: verifyRoleCredential.url(args, options),
    method: 'post',
})

verifyRoleCredential.form = verifyRoleCredentialForm

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::internalAuditWizard
* @see app/Http/Controllers/Api/ComplianceAuditController.php:611
* @route '/api/compliance/internal-audit-wizard'
*/
export const internalAuditWizard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: internalAuditWizard.url(options),
    method: 'get',
})

internalAuditWizard.definition = {
    methods: ["get","head"],
    url: '/api/compliance/internal-audit-wizard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::internalAuditWizard
* @see app/Http/Controllers/Api/ComplianceAuditController.php:611
* @route '/api/compliance/internal-audit-wizard'
*/
internalAuditWizard.url = (options?: RouteQueryOptions) => {
    return internalAuditWizard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::internalAuditWizard
* @see app/Http/Controllers/Api/ComplianceAuditController.php:611
* @route '/api/compliance/internal-audit-wizard'
*/
internalAuditWizard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: internalAuditWizard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::internalAuditWizard
* @see app/Http/Controllers/Api/ComplianceAuditController.php:611
* @route '/api/compliance/internal-audit-wizard'
*/
internalAuditWizard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: internalAuditWizard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::internalAuditWizard
* @see app/Http/Controllers/Api/ComplianceAuditController.php:611
* @route '/api/compliance/internal-audit-wizard'
*/
const internalAuditWizardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: internalAuditWizard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::internalAuditWizard
* @see app/Http/Controllers/Api/ComplianceAuditController.php:611
* @route '/api/compliance/internal-audit-wizard'
*/
internalAuditWizardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: internalAuditWizard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::internalAuditWizard
* @see app/Http/Controllers/Api/ComplianceAuditController.php:611
* @route '/api/compliance/internal-audit-wizard'
*/
internalAuditWizardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: internalAuditWizard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

internalAuditWizard.form = internalAuditWizardForm

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

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::didDocument
* @see app/Http/Controllers/Api/ComplianceAuditController.php:447
* @route '/.well-known/did.json'
*/
const didDocumentForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: didDocument.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::didDocument
* @see app/Http/Controllers/Api/ComplianceAuditController.php:447
* @route '/.well-known/did.json'
*/
didDocumentForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: didDocument.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\ComplianceAuditController::didDocument
* @see app/Http/Controllers/Api/ComplianceAuditController.php:447
* @route '/.well-known/did.json'
*/
didDocumentForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: didDocument.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

didDocument.form = didDocumentForm

const ComplianceAuditController = { verifyRoleCredentialPublic, verifierMetadata, index, summary, iso30414Summary, recordAiConsent, executeGdprPurge, exportRoleCredential, verifyRoleCredential, internalAuditWizard, didDocument }

export default ComplianceAuditController