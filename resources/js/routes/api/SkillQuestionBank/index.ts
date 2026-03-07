import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see routes/form-schema-complete.php:38
* @route '/api/SkillQuestionBank'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/SkillQuestionBank',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/form-schema-complete.php:38
* @route '/api/SkillQuestionBank'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see routes/form-schema-complete.php:38
* @route '/api/SkillQuestionBank'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see routes/form-schema-complete.php:38
* @route '/api/SkillQuestionBank'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see routes/form-schema-complete.php:38
* @route '/api/SkillQuestionBank'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/form-schema-complete.php:38
* @route '/api/SkillQuestionBank'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see routes/form-schema-complete.php:38
* @route '/api/SkillQuestionBank'
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
* @see routes/form-schema-complete.php:45
* @route '/api/SkillQuestionBank'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/SkillQuestionBank',
} satisfies RouteDefinition<["post"]>

/**
* @see routes/form-schema-complete.php:45
* @route '/api/SkillQuestionBank'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see routes/form-schema-complete.php:45
* @route '/api/SkillQuestionBank'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see routes/form-schema-complete.php:45
* @route '/api/SkillQuestionBank'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see routes/form-schema-complete.php:45
* @route '/api/SkillQuestionBank'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see routes/form-schema-complete.php:52
* @route '/api/SkillQuestionBank/{id}'
*/
export const show = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/SkillQuestionBank/{id}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see routes/form-schema-complete.php:52
* @route '/api/SkillQuestionBank/{id}'
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
* @see routes/form-schema-complete.php:52
* @route '/api/SkillQuestionBank/{id}'
*/
show.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see routes/form-schema-complete.php:52
* @route '/api/SkillQuestionBank/{id}'
*/
show.head = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see routes/form-schema-complete.php:52
* @route '/api/SkillQuestionBank/{id}'
*/
const showForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see routes/form-schema-complete.php:52
* @route '/api/SkillQuestionBank/{id}'
*/
showForm.get = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see routes/form-schema-complete.php:52
* @route '/api/SkillQuestionBank/{id}'
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
* @see routes/form-schema-complete.php:59
* @route '/api/SkillQuestionBank/{id}'
*/
export const update = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/api/SkillQuestionBank/{id}',
} satisfies RouteDefinition<["put"]>

/**
* @see routes/form-schema-complete.php:59
* @route '/api/SkillQuestionBank/{id}'
*/
update.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/form-schema-complete.php:59
* @route '/api/SkillQuestionBank/{id}'
*/
update.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see routes/form-schema-complete.php:59
* @route '/api/SkillQuestionBank/{id}'
*/
const updateForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see routes/form-schema-complete.php:59
* @route '/api/SkillQuestionBank/{id}'
*/
updateForm.put = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

/**
* @see routes/form-schema-complete.php:66
* @route '/api/SkillQuestionBank/{id}'
*/
export const patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: patch.url(args, options),
    method: 'patch',
})

patch.definition = {
    methods: ["patch"],
    url: '/api/SkillQuestionBank/{id}',
} satisfies RouteDefinition<["patch"]>

/**
* @see routes/form-schema-complete.php:66
* @route '/api/SkillQuestionBank/{id}'
*/
patch.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return patch.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/form-schema-complete.php:66
* @route '/api/SkillQuestionBank/{id}'
*/
patch.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: patch.url(args, options),
    method: 'patch',
})

/**
* @see routes/form-schema-complete.php:66
* @route '/api/SkillQuestionBank/{id}'
*/
const patchForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: patch.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see routes/form-schema-complete.php:66
* @route '/api/SkillQuestionBank/{id}'
*/
patchForm.patch = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: patch.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

patch.form = patchForm

/**
* @see routes/form-schema-complete.php:73
* @route '/api/SkillQuestionBank/{id}'
*/
export const destroy = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/SkillQuestionBank/{id}',
} satisfies RouteDefinition<["delete"]>

/**
* @see routes/form-schema-complete.php:73
* @route '/api/SkillQuestionBank/{id}'
*/
destroy.url = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{id}', parsedArgs.id.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see routes/form-schema-complete.php:73
* @route '/api/SkillQuestionBank/{id}'
*/
destroy.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see routes/form-schema-complete.php:73
* @route '/api/SkillQuestionBank/{id}'
*/
const destroyForm = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see routes/form-schema-complete.php:73
* @route '/api/SkillQuestionBank/{id}'
*/
destroyForm.delete = (args: { id: string | number } | [id: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

/**
* @see routes/form-schema-complete.php:80
* @route '/api/SkillQuestionBank/search'
*/
export const search = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: search.url(options),
    method: 'post',
})

search.definition = {
    methods: ["post"],
    url: '/api/SkillQuestionBank/search',
} satisfies RouteDefinition<["post"]>

/**
* @see routes/form-schema-complete.php:80
* @route '/api/SkillQuestionBank/search'
*/
search.url = (options?: RouteQueryOptions) => {
    return search.definition.url + queryParams(options)
}

/**
* @see routes/form-schema-complete.php:80
* @route '/api/SkillQuestionBank/search'
*/
search.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: search.url(options),
    method: 'post',
})

/**
* @see routes/form-schema-complete.php:80
* @route '/api/SkillQuestionBank/search'
*/
const searchForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: search.url(options),
    method: 'post',
})

/**
* @see routes/form-schema-complete.php:80
* @route '/api/SkillQuestionBank/search'
*/
searchForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: search.url(options),
    method: 'post',
})

search.form = searchForm

/**
* @see routes/form-schema-complete.php:87
* @route '/api/SkillQuestionBank/search-with-paciente'
*/
export const searchWithPaciente = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: searchWithPaciente.url(options),
    method: 'post',
})

searchWithPaciente.definition = {
    methods: ["post"],
    url: '/api/SkillQuestionBank/search-with-paciente',
} satisfies RouteDefinition<["post"]>

/**
* @see routes/form-schema-complete.php:87
* @route '/api/SkillQuestionBank/search-with-paciente'
*/
searchWithPaciente.url = (options?: RouteQueryOptions) => {
    return searchWithPaciente.definition.url + queryParams(options)
}

/**
* @see routes/form-schema-complete.php:87
* @route '/api/SkillQuestionBank/search-with-paciente'
*/
searchWithPaciente.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: searchWithPaciente.url(options),
    method: 'post',
})

/**
* @see routes/form-schema-complete.php:87
* @route '/api/SkillQuestionBank/search-with-paciente'
*/
const searchWithPacienteForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: searchWithPaciente.url(options),
    method: 'post',
})

/**
* @see routes/form-schema-complete.php:87
* @route '/api/SkillQuestionBank/search-with-paciente'
*/
searchWithPacienteForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: searchWithPaciente.url(options),
    method: 'post',
})

searchWithPaciente.form = searchWithPacienteForm

const SkillQuestionBank = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    show: Object.assign(show, show),
    update: Object.assign(update, update),
    patch: Object.assign(patch, patch),
    destroy: Object.assign(destroy, destroy),
    search: Object.assign(search, search),
    searchWithPaciente: Object.assign(searchWithPaciente, searchWithPaciente),
}

export default SkillQuestionBank