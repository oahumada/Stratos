import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:22
* @route '/api/lms/certificates'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/lms/certificates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:22
* @route '/api/lms/certificates'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:22
* @route '/api/lms/certificates'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:22
* @route '/api/lms/certificates'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:22
* @route '/api/lms/certificates'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:22
* @route '/api/lms/certificates'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::index
* @see app/Http/Controllers/Api/Lms/CertificateController.php:22
* @route '/api/lms/certificates'
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
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:41
* @route '/api/lms/certificates/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/lms/certificates/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:41
* @route '/api/lms/certificates/{id}'
*/
show.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:41
* @route '/api/lms/certificates/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:41
* @route '/api/lms/certificates/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:41
* @route '/api/lms/certificates/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:41
* @route '/api/lms/certificates/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::show
* @see app/Http/Controllers/Api/Lms/CertificateController.php:41
* @route '/api/lms/certificates/{id}'
*/
showForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:52
* @route '/api/lms/certificates/{id}/download'
*/
export const download = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

download.definition = {
    methods: ["get","head"],
    url: '/api/lms/certificates/{id}/download',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:52
* @route '/api/lms/certificates/{id}/download'
*/
download.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return download.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:52
* @route '/api/lms/certificates/{id}/download'
*/
download.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:52
* @route '/api/lms/certificates/{id}/download'
*/
download.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:52
* @route '/api/lms/certificates/{id}/download'
*/
const downloadForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:52
* @route '/api/lms/certificates/{id}/download'
*/
downloadForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::download
* @see app/Http/Controllers/Api/Lms/CertificateController.php:52
* @route '/api/lms/certificates/{id}/download'
*/
downloadForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: download.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

download.form = downloadForm

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verify'
*/
const verify79be5c4a0eef6210fbad746bc97a33be = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify79be5c4a0eef6210fbad746bc97a33be.url(args, options),
    method: 'get',
})

verify79be5c4a0eef6210fbad746bc97a33be.definition = {
    methods: ["get","head"],
    url: '/api/lms/certificates/{id}/verify',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verify'
*/
verify79be5c4a0eef6210fbad746bc97a33be.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return verify79be5c4a0eef6210fbad746bc97a33be.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verify'
*/
verify79be5c4a0eef6210fbad746bc97a33be.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify79be5c4a0eef6210fbad746bc97a33be.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verify'
*/
verify79be5c4a0eef6210fbad746bc97a33be.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verify79be5c4a0eef6210fbad746bc97a33be.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verify'
*/
const verify79be5c4a0eef6210fbad746bc97a33beForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify79be5c4a0eef6210fbad746bc97a33be.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verify'
*/
verify79be5c4a0eef6210fbad746bc97a33beForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify79be5c4a0eef6210fbad746bc97a33be.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verify'
*/
verify79be5c4a0eef6210fbad746bc97a33beForm.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify79be5c4a0eef6210fbad746bc97a33be.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

verify79be5c4a0eef6210fbad746bc97a33be.form = verify79be5c4a0eef6210fbad746bc97a33beForm
/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verification'
*/
const verify7be59a669615c6184abd3868431d12f9 = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify7be59a669615c6184abd3868431d12f9.url(args, options),
    method: 'get',
})

verify7be59a669615c6184abd3868431d12f9.definition = {
    methods: ["get","head"],
    url: '/api/lms/certificates/{id}/verification',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verification'
*/
verify7be59a669615c6184abd3868431d12f9.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return verify7be59a669615c6184abd3868431d12f9.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verification'
*/
verify7be59a669615c6184abd3868431d12f9.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: verify7be59a669615c6184abd3868431d12f9.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verification'
*/
verify7be59a669615c6184abd3868431d12f9.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: verify7be59a669615c6184abd3868431d12f9.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verification'
*/
const verify7be59a669615c6184abd3868431d12f9Form = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify7be59a669615c6184abd3868431d12f9.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verification'
*/
verify7be59a669615c6184abd3868431d12f9Form.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify7be59a669615c6184abd3868431d12f9.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::verify
* @see app/Http/Controllers/Api/Lms/CertificateController.php:63
* @route '/api/lms/certificates/{id}/verification'
*/
verify7be59a669615c6184abd3868431d12f9Form.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: verify7be59a669615c6184abd3868431d12f9.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

verify7be59a669615c6184abd3868431d12f9.form = verify7be59a669615c6184abd3868431d12f9Form

export const verify = {
    '/api/lms/certificates/{id}/verify': verify79be5c4a0eef6210fbad746bc97a33be,
    '/api/lms/certificates/{id}/verification': verify7be59a669615c6184abd3868431d12f9,
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::revoke
* @see app/Http/Controllers/Api/Lms/CertificateController.php:71
* @route '/api/lms/certificates/{id}/revoke'
*/
export const revoke = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: revoke.url(args, options),
    method: 'post',
})

revoke.definition = {
    methods: ["post"],
    url: '/api/lms/certificates/{id}/revoke',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::revoke
* @see app/Http/Controllers/Api/Lms/CertificateController.php:71
* @route '/api/lms/certificates/{id}/revoke'
*/
revoke.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return revoke.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::revoke
* @see app/Http/Controllers/Api/Lms/CertificateController.php:71
* @route '/api/lms/certificates/{id}/revoke'
*/
revoke.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: revoke.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::revoke
* @see app/Http/Controllers/Api/Lms/CertificateController.php:71
* @route '/api/lms/certificates/{id}/revoke'
*/
const revokeForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: revoke.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\CertificateController::revoke
* @see app/Http/Controllers/Api/Lms/CertificateController.php:71
* @route '/api/lms/certificates/{id}/revoke'
*/
revokeForm.post = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: revoke.url(args, options),
    method: 'post',
})

revoke.form = revokeForm

const CertificateController = { index, show, download, verify, revoke }

export default CertificateController