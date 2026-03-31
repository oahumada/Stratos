import {
    applyUrlDefaults,
    queryParams,
    type RouteDefinition,
    type RouteFormDefinition,
    type RouteQueryOptions,
} from './../../../../../wayfinder';
/**
 * @see \App\Http\Controllers\Api\DepartmentController::tree
 * @see app/Http/Controllers/Api/DepartmentController.php:17
 * @route '/api/departments/tree'
 */
export const tree = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tree.url(options),
    method: 'get',
});

tree.definition = {
    methods: ['get', 'head'],
    url: '/api/departments/tree',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\DepartmentController::tree
 * @see app/Http/Controllers/Api/DepartmentController.php:17
 * @route '/api/departments/tree'
 */
tree.url = (options?: RouteQueryOptions) => {
    return tree.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\DepartmentController::tree
 * @see app/Http/Controllers/Api/DepartmentController.php:17
 * @route '/api/departments/tree'
 */
tree.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: tree.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::tree
 * @see app/Http/Controllers/Api/DepartmentController.php:17
 * @route '/api/departments/tree'
 */
tree.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: tree.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::tree
 * @see app/Http/Controllers/Api/DepartmentController.php:17
 * @route '/api/departments/tree'
 */
const treeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tree.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::tree
 * @see app/Http/Controllers/Api/DepartmentController.php:17
 * @route '/api/departments/tree'
 */
treeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tree.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::tree
 * @see app/Http/Controllers/Api/DepartmentController.php:17
 * @route '/api/departments/tree'
 */
treeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: tree.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

tree.form = treeForm;

/**
 * @see \App\Http\Controllers\Api\DepartmentController::updateHierarchy
 * @see app/Http/Controllers/Api/DepartmentController.php:33
 * @route '/api/departments/hierarchy'
 */
export const updateHierarchy = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: updateHierarchy.url(options),
    method: 'post',
});

updateHierarchy.definition = {
    methods: ['post'],
    url: '/api/departments/hierarchy',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\DepartmentController::updateHierarchy
 * @see app/Http/Controllers/Api/DepartmentController.php:33
 * @route '/api/departments/hierarchy'
 */
updateHierarchy.url = (options?: RouteQueryOptions) => {
    return updateHierarchy.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\DepartmentController::updateHierarchy
 * @see app/Http/Controllers/Api/DepartmentController.php:33
 * @route '/api/departments/hierarchy'
 */
updateHierarchy.post = (
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: updateHierarchy.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::updateHierarchy
 * @see app/Http/Controllers/Api/DepartmentController.php:33
 * @route '/api/departments/hierarchy'
 */
const updateHierarchyForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: updateHierarchy.url(options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::updateHierarchy
 * @see app/Http/Controllers/Api/DepartmentController.php:33
 * @route '/api/departments/hierarchy'
 */
updateHierarchyForm.post = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: updateHierarchy.url(options),
    method: 'post',
});

updateHierarchy.form = updateHierarchyForm;

/**
 * @see \App\Http\Controllers\Api\DepartmentController::updateDepartmentParent
 * @see app/Http/Controllers/Api/DepartmentController.php:55
 * @route '/api/departments/{department}/hierarchy'
 */
export const updateDepartmentParent = (
    args:
        | { department: number | { id: number } }
        | [department: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: updateDepartmentParent.url(args, options),
    method: 'put',
});

updateDepartmentParent.definition = {
    methods: ['put'],
    url: '/api/departments/{department}/hierarchy',
} satisfies RouteDefinition<['put']>;

/**
 * @see \App\Http\Controllers\Api\DepartmentController::updateDepartmentParent
 * @see app/Http/Controllers/Api/DepartmentController.php:55
 * @route '/api/departments/{department}/hierarchy'
 */
updateDepartmentParent.url = (
    args:
        | { department: number | { id: number } }
        | [department: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { department: args };
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { department: args.id };
    }

    if (Array.isArray(args)) {
        args = {
            department: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        department:
            typeof args.department === 'object'
                ? args.department.id
                : args.department,
    };

    return (
        updateDepartmentParent.definition.url
            .replace('{department}', parsedArgs.department.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\DepartmentController::updateDepartmentParent
 * @see app/Http/Controllers/Api/DepartmentController.php:55
 * @route '/api/departments/{department}/hierarchy'
 */
updateDepartmentParent.put = (
    args:
        | { department: number | { id: number } }
        | [department: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteDefinition<'put'> => ({
    url: updateDepartmentParent.url(args, options),
    method: 'put',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::updateDepartmentParent
 * @see app/Http/Controllers/Api/DepartmentController.php:55
 * @route '/api/departments/{department}/hierarchy'
 */
const updateDepartmentParentForm = (
    args:
        | { department: number | { id: number } }
        | [department: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: updateDepartmentParent.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::updateDepartmentParent
 * @see app/Http/Controllers/Api/DepartmentController.php:55
 * @route '/api/departments/{department}/hierarchy'
 */
updateDepartmentParentForm.put = (
    args:
        | { department: number | { id: number } }
        | [department: number | { id: number }]
        | number
        | { id: number },
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: updateDepartmentParent.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'post',
});

updateDepartmentParent.form = updateDepartmentParentForm;

/**
 * @see \App\Http\Controllers\Api\DepartmentController::setManager
 * @see app/Http/Controllers/Api/DepartmentController.php:112
 * @route '/api/departments/{id}/manager'
 */
export const setManager = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: setManager.url(args, options),
    method: 'post',
});

setManager.definition = {
    methods: ['post'],
    url: '/api/departments/{id}/manager',
} satisfies RouteDefinition<['post']>;

/**
 * @see \App\Http\Controllers\Api\DepartmentController::setManager
 * @see app/Http/Controllers/Api/DepartmentController.php:112
 * @route '/api/departments/{id}/manager'
 */
setManager.url = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { id: args };
    }

    if (Array.isArray(args)) {
        args = {
            id: args[0],
        };
    }

    args = applyUrlDefaults(args);

    const parsedArgs = {
        id: args.id,
    };

    return (
        setManager.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
    );
};

/**
 * @see \App\Http\Controllers\Api\DepartmentController::setManager
 * @see app/Http/Controllers/Api/DepartmentController.php:112
 * @route '/api/departments/{id}/manager'
 */
setManager.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteDefinition<'post'> => ({
    url: setManager.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::setManager
 * @see app/Http/Controllers/Api/DepartmentController.php:112
 * @route '/api/departments/{id}/manager'
 */
const setManagerForm = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: setManager.url(args, options),
    method: 'post',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::setManager
 * @see app/Http/Controllers/Api/DepartmentController.php:112
 * @route '/api/departments/{id}/manager'
 */
setManagerForm.post = (
    args: { id: string | number } | [id: string | number] | string | number,
    options?: RouteQueryOptions,
): RouteFormDefinition<'post'> => ({
    action: setManager.url(args, options),
    method: 'post',
});

setManager.form = setManagerForm;

/**
 * @see \App\Http\Controllers\Api\DepartmentController::heatmapData
 * @see app/Http/Controllers/Api/DepartmentController.php:129
 * @route '/api/departments/heatmap'
 */
export const heatmapData = (
    options?: RouteQueryOptions,
): RouteDefinition<'get'> => ({
    url: heatmapData.url(options),
    method: 'get',
});

heatmapData.definition = {
    methods: ['get', 'head'],
    url: '/api/departments/heatmap',
} satisfies RouteDefinition<['get', 'head']>;

/**
 * @see \App\Http\Controllers\Api\DepartmentController::heatmapData
 * @see app/Http/Controllers/Api/DepartmentController.php:129
 * @route '/api/departments/heatmap'
 */
heatmapData.url = (options?: RouteQueryOptions) => {
    return heatmapData.definition.url + queryParams(options);
};

/**
 * @see \App\Http\Controllers\Api\DepartmentController::heatmapData
 * @see app/Http/Controllers/Api/DepartmentController.php:129
 * @route '/api/departments/heatmap'
 */
heatmapData.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: heatmapData.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::heatmapData
 * @see app/Http/Controllers/Api/DepartmentController.php:129
 * @route '/api/departments/heatmap'
 */
heatmapData.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: heatmapData.url(options),
    method: 'head',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::heatmapData
 * @see app/Http/Controllers/Api/DepartmentController.php:129
 * @route '/api/departments/heatmap'
 */
const heatmapDataForm = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: heatmapData.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::heatmapData
 * @see app/Http/Controllers/Api/DepartmentController.php:129
 * @route '/api/departments/heatmap'
 */
heatmapDataForm.get = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: heatmapData.url(options),
    method: 'get',
});

/**
 * @see \App\Http\Controllers\Api\DepartmentController::heatmapData
 * @see app/Http/Controllers/Api/DepartmentController.php:129
 * @route '/api/departments/heatmap'
 */
heatmapDataForm.head = (
    options?: RouteQueryOptions,
): RouteFormDefinition<'get'> => ({
    action: heatmapData.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        },
    }),
    method: 'get',
});

heatmapData.form = heatmapDataForm;

const DepartmentController = {
    tree,
    updateHierarchy,
    updateDepartmentParent,
    setManager,
    heatmapData,
};

export default DepartmentController;
