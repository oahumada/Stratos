import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see routes/web.php:179
* @route '/storage/scorm/{orgId}/{packageId}/{path}'
*/
export const content = (args: { orgId: string | number, packageId: string | number, path: string | number } | [orgId: string | number, packageId: string | number, path: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: content.url(args, options),
    method: 'get',
})

content.definition = {
    methods: ["get","head"],
    url: '/storage/scorm/{orgId}/{packageId}/{path}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/web.php:179
* @route '/storage/scorm/{orgId}/{packageId}/{path}'
*/
content.url = (args: { orgId: string | number, packageId: string | number, path: string | number } | [orgId: string | number, packageId: string | number, path: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            orgId: args[0],
            packageId: args[1],
            path: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        orgId: args.orgId,
        packageId: args.packageId,
        path: args.path,
    }

    return content.definition.url
            .replace('{orgId}', parsedArgs.orgId.toString())
            .replace('{packageId}', parsedArgs.packageId.toString())
            .replace('{path}', parsedArgs.path.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/web.php:179
* @route '/storage/scorm/{orgId}/{packageId}/{path}'
*/
content.get = (args: { orgId: string | number, packageId: string | number, path: string | number } | [orgId: string | number, packageId: string | number, path: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: content.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:179
* @route '/storage/scorm/{orgId}/{packageId}/{path}'
*/
content.head = (args: { orgId: string | number, packageId: string | number, path: string | number } | [orgId: string | number, packageId: string | number, path: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: content.url(args, options),
    method: 'head',
})

/**
* @see routes/web.php:179
* @route '/storage/scorm/{orgId}/{packageId}/{path}'
*/
const contentForm = (args: { orgId: string | number, packageId: string | number, path: string | number } | [orgId: string | number, packageId: string | number, path: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: content.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:179
* @route '/storage/scorm/{orgId}/{packageId}/{path}'
*/
contentForm.get = (args: { orgId: string | number, packageId: string | number, path: string | number } | [orgId: string | number, packageId: string | number, path: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: content.url(args, options),
    method: 'get',
})

/**
* @see routes/web.php:179
* @route '/storage/scorm/{orgId}/{packageId}/{path}'
*/
contentForm.head = (args: { orgId: string | number, packageId: string | number, path: string | number } | [orgId: string | number, packageId: string | number, path: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: content.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

content.form = contentForm

const scorm = {
    content: Object.assign(content, content),
}

export default scorm