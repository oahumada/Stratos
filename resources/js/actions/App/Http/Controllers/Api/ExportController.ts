import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\ExportController::exportPdf
 * @see app/Http/Controllers/Api/ExportController.php:29
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pdf'
 */
export const exportPdf = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: exportPdf.url(args, options),
    method: 'post',
});

exportPdf.definition = {
    methods: ['post'],
    url: '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pdf',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\ExportController::exportPdf
 * @see app/Http/Controllers/Api/ExportController.php:29
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pdf'
 */
exportPdf.url = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args };
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        scenarioId: args.scenarioId,
    };

    return (
        exportPdf.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\ExportController::exportPdf
 * @see app/Http/Controllers/Api/ExportController.php:29
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pdf'
 */
exportPdf.post = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: exportPdf.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::exportPdf
 * @see app/Http/Controllers/Api/ExportController.php:29
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pdf'
 */
const exportPdfForm = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: exportPdf.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::exportPdf
 * @see app/Http/Controllers/Api/ExportController.php:29
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pdf'
 */
exportPdfForm.post = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: exportPdf.url(args, options),
    method: 'post',
});

exportPdf.form = exportPdfForm;

/**
 * @see \App\Http\Controllers\Api\ExportController::exportPptx
 * @see app/Http/Controllers/Api/ExportController.php:52
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pptx'
 */
export const exportPptx = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: exportPptx.url(args, options),
    method: 'post',
});

exportPptx.definition = {
    methods: ['post'],
    url: '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pptx',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\ExportController::exportPptx
 * @see app/Http/Controllers/Api/ExportController.php:52
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pptx'
 */
exportPptx.url = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args };
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        scenarioId: args.scenarioId,
    };

    return (
        exportPptx.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\ExportController::exportPptx
 * @see app/Http/Controllers/Api/ExportController.php:52
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pptx'
 */
exportPptx.post = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: exportPptx.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::exportPptx
 * @see app/Http/Controllers/Api/ExportController.php:52
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pptx'
 */
const exportPptxForm = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: exportPptx.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::exportPptx
 * @see app/Http/Controllers/Api/ExportController.php:52
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/export/pptx'
 */
exportPptxForm.post = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: exportPptx.url(args, options),
    method: 'post',
});

exportPptx.form = exportPptxForm;

/**
 * @see \App\Http\Controllers\Api\ExportController::download
 * @see app/Http/Controllers/Api/ExportController.php:75
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/download'
 */
export const download = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
});

download.definition = {
    methods: ['get', 'head'],
    url: '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/download',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\ExportController::download
 * @see app/Http/Controllers/Api/ExportController.php:75
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/download'
 */
download.url = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { scenarioId: args };
    }

    if (Array.isArray(args)) {
        args = {
            scenarioId: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        scenarioId: args.scenarioId,
    };

    return (
        download.definition.url
            .replace('{scenarioId}', parsedArgs.scenarioId.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\ExportController::download
 * @see app/Http/Controllers/Api/ExportController.php:75
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/download'
 */
download.get = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: download.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::download
 * @see app/Http/Controllers/Api/ExportController.php:75
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/download'
 */
download.head = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: download.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::download
 * @see app/Http/Controllers/Api/ExportController.php:75
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/download'
 */
const downloadForm = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::download
 * @see app/Http/Controllers/Api/ExportController.php:75
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/download'
 */
downloadForm.get = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: download.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::download
 * @see app/Http/Controllers/Api/ExportController.php:75
 * @route '/api/strategic-planning/scenarios/{scenarioId}/executive-summary/download'
 */
downloadForm.head = (
    args:
        | { scenarioId: string | number }
        | [scenarioId: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: download.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

download.form = downloadForm;

/**
 * @see \App\Http\Controllers\Api\ExportController::status
 * @see app/Http/Controllers/Api/ExportController.php:118
 * @route '/api/strategic-planning/strategic-planning/exports/{format}/status'
 */
export const status = (
    args:
        | { format: string | number }
        | [format: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
});

status.definition = {
    methods: ['get', 'head'],
    url: '/api/strategic-planning/strategic-planning/exports/{format}/status',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\ExportController::status
 * @see app/Http/Controllers/Api/ExportController.php:118
 * @route '/api/strategic-planning/strategic-planning/exports/{format}/status'
 */
status.url = (
    args:
        | { format: string | number }
        | [format: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { format: args };
    }

    if (Array.isArray(args)) {
        args = {
            format: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        format: args.format,
    };

    return (
        status.definition.url
            .replace('{format}', parsedArgs.format.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\ExportController::status
 * @see app/Http/Controllers/Api/ExportController.php:118
 * @route '/api/strategic-planning/strategic-planning/exports/{format}/status'
 */
status.get = (
    args:
        | { format: string | number }
        | [format: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::status
 * @see app/Http/Controllers/Api/ExportController.php:118
 * @route '/api/strategic-planning/strategic-planning/exports/{format}/status'
 */
status.head = (
    args:
        | { format: string | number }
        | [format: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteDefinition<'head'> => ({
    url: status.url(args, options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::status
 * @see app/Http/Controllers/Api/ExportController.php:118
 * @route '/api/strategic-planning/strategic-planning/exports/{format}/status'
 */
const statusForm = (
    args:
        | { format: string | number }
        | [format: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: status.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::status
 * @see app/Http/Controllers/Api/ExportController.php:118
 * @route '/api/strategic-planning/strategic-planning/exports/{format}/status'
 */
statusForm.get = (
    args:
        | { format: string | number }
        | [format: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: status.url(args, options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\ExportController::status
 * @see app/Http/Controllers/Api/ExportController.php:118
 * @route '/api/strategic-planning/strategic-planning/exports/{format}/status'
 */
statusForm.head = (
    args:
        | { format: string | number }
        | [format: string | number]
        | string
        | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: status.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

status.form = statusForm;

const ExportController = { exportPdf, exportPptx, download, status };

export default ExportController;
