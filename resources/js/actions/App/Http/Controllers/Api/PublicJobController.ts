import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\PublicJobController::index
* @see app/Http/Controllers/Api/PublicJobController.php:16
* @route '/api/career/{tenantSlug}'
*/
export const index = (args: { tenantSlug: string | number } | [tenantSlug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/career/{tenantSlug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PublicJobController::index
* @see app/Http/Controllers/Api/PublicJobController.php:16
* @route '/api/career/{tenantSlug}'
*/
index.url = (args: { tenantSlug: string | number } | [tenantSlug: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { tenantSlug: args }
    }

    if (Array.isArray(args)) {
        args = {
            tenantSlug: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        tenantSlug: args.tenantSlug,
    }

    return index.definition.url
            .replace('{tenantSlug}', parsedArgs.tenantSlug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PublicJobController::index
* @see app/Http/Controllers/Api/PublicJobController.php:16
* @route '/api/career/{tenantSlug}'
*/
index.get = (args: { tenantSlug: string | number } | [tenantSlug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PublicJobController::index
* @see app/Http/Controllers/Api/PublicJobController.php:16
* @route '/api/career/{tenantSlug}'
*/
index.head = (args: { tenantSlug: string | number } | [tenantSlug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PublicJobController::show
* @see app/Http/Controllers/Api/PublicJobController.php:39
* @route '/api/career/{tenantSlug}/jobs/{jobSlug}'
*/
export const show = (args: { tenantSlug: string | number, jobSlug: string | number } | [tenantSlug: string | number, jobSlug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/career/{tenantSlug}/jobs/{jobSlug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\PublicJobController::show
* @see app/Http/Controllers/Api/PublicJobController.php:39
* @route '/api/career/{tenantSlug}/jobs/{jobSlug}'
*/
show.url = (args: { tenantSlug: string | number, jobSlug: string | number } | [tenantSlug: string | number, jobSlug: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            tenantSlug: args[0],
            jobSlug: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        tenantSlug: args.tenantSlug,
        jobSlug: args.jobSlug,
    }

    return show.definition.url
            .replace('{tenantSlug}', parsedArgs.tenantSlug.toString())
            .replace('{jobSlug}', parsedArgs.jobSlug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PublicJobController::show
* @see app/Http/Controllers/Api/PublicJobController.php:39
* @route '/api/career/{tenantSlug}/jobs/{jobSlug}'
*/
show.get = (args: { tenantSlug: string | number, jobSlug: string | number } | [tenantSlug: string | number, jobSlug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\PublicJobController::show
* @see app/Http/Controllers/Api/PublicJobController.php:39
* @route '/api/career/{tenantSlug}/jobs/{jobSlug}'
*/
show.head = (args: { tenantSlug: string | number, jobSlug: string | number } | [tenantSlug: string | number, jobSlug: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\PublicJobController::apply
* @see app/Http/Controllers/Api/PublicJobController.php:58
* @route '/api/career/{tenantSlug}/jobs/{jobSlug}/apply'
*/
export const apply = (args: { tenantSlug: string | number, jobSlug: string | number } | [tenantSlug: string | number, jobSlug: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(args, options),
    method: 'post',
})

apply.definition = {
    methods: ["post"],
    url: '/api/career/{tenantSlug}/jobs/{jobSlug}/apply',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\PublicJobController::apply
* @see app/Http/Controllers/Api/PublicJobController.php:58
* @route '/api/career/{tenantSlug}/jobs/{jobSlug}/apply'
*/
apply.url = (args: { tenantSlug: string | number, jobSlug: string | number } | [tenantSlug: string | number, jobSlug: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            tenantSlug: args[0],
            jobSlug: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        tenantSlug: args.tenantSlug,
        jobSlug: args.jobSlug,
    }

    return apply.definition.url
            .replace('{tenantSlug}', parsedArgs.tenantSlug.toString())
            .replace('{jobSlug}', parsedArgs.jobSlug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\PublicJobController::apply
* @see app/Http/Controllers/Api/PublicJobController.php:58
* @route '/api/career/{tenantSlug}/jobs/{jobSlug}/apply'
*/
apply.post = (args: { tenantSlug: string | number, jobSlug: string | number } | [tenantSlug: string | number, jobSlug: string | number ], options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(args, options),
    method: 'post',
})

const PublicJobController = { index, show, apply }

export default PublicJobController