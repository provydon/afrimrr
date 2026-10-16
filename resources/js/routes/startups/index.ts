import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Public\StartupDirectoryController::index
* @see app/Http/Controllers/Public/StartupDirectoryController.php:14
* @route '/startups'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/startups',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\StartupDirectoryController::index
* @see app/Http/Controllers/Public/StartupDirectoryController.php:14
* @route '/startups'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\StartupDirectoryController::index
* @see app/Http/Controllers/Public/StartupDirectoryController.php:14
* @route '/startups'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\StartupDirectoryController::index
* @see app/Http/Controllers/Public/StartupDirectoryController.php:14
* @route '/startups'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\StartupDirectoryController::index
* @see app/Http/Controllers/Public/StartupDirectoryController.php:14
* @route '/startups'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\StartupDirectoryController::index
* @see app/Http/Controllers/Public/StartupDirectoryController.php:14
* @route '/startups'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\StartupDirectoryController::index
* @see app/Http/Controllers/Public/StartupDirectoryController.php:14
* @route '/startups'
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
* @see \App\Http\Controllers\Public\StartupProfileController::show
* @see app/Http/Controllers/Public/StartupProfileController.php:13
* @route '/startups/{startup}'
*/
export const show = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/startups/{startup}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\StartupProfileController::show
* @see app/Http/Controllers/Public/StartupProfileController.php:13
* @route '/startups/{startup}'
*/
show.url = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { startup: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { startup: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            startup: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        startup: typeof args.startup === 'object'
        ? args.startup.slug
        : args.startup,
    }

    return show.definition.url
            .replace('{startup}', parsedArgs.startup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\StartupProfileController::show
* @see app/Http/Controllers/Public/StartupProfileController.php:13
* @route '/startups/{startup}'
*/
show.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\StartupProfileController::show
* @see app/Http/Controllers/Public/StartupProfileController.php:13
* @route '/startups/{startup}'
*/
show.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\StartupProfileController::show
* @see app/Http/Controllers/Public/StartupProfileController.php:13
* @route '/startups/{startup}'
*/
const showForm = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\StartupProfileController::show
* @see app/Http/Controllers/Public/StartupProfileController.php:13
* @route '/startups/{startup}'
*/
showForm.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\StartupProfileController::show
* @see app/Http/Controllers/Public/StartupProfileController.php:13
* @route '/startups/{startup}'
*/
showForm.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
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
* @see \App\Http\Controllers\StartupController::create
* @see app/Http/Controllers/StartupController.php:15
* @route '/dashboard/startups/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/dashboard/startups/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StartupController::create
* @see app/Http/Controllers/StartupController.php:15
* @route '/dashboard/startups/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StartupController::create
* @see app/Http/Controllers/StartupController.php:15
* @route '/dashboard/startups/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StartupController::create
* @see app/Http/Controllers/StartupController.php:15
* @route '/dashboard/startups/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StartupController::create
* @see app/Http/Controllers/StartupController.php:15
* @route '/dashboard/startups/create'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StartupController::create
* @see app/Http/Controllers/StartupController.php:15
* @route '/dashboard/startups/create'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StartupController::create
* @see app/Http/Controllers/StartupController.php:15
* @route '/dashboard/startups/create'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\StartupController::store
* @see app/Http/Controllers/StartupController.php:20
* @route '/dashboard/startups'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/startups',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StartupController::store
* @see app/Http/Controllers/StartupController.php:20
* @route '/dashboard/startups'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StartupController::store
* @see app/Http/Controllers/StartupController.php:20
* @route '/dashboard/startups'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StartupController::store
* @see app/Http/Controllers/StartupController.php:20
* @route '/dashboard/startups'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StartupController::store
* @see app/Http/Controllers/StartupController.php:20
* @route '/dashboard/startups'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\StartupController::edit
* @see app/Http/Controllers/StartupController.php:35
* @route '/dashboard/startups/{startup}/edit'
*/
export const edit = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/dashboard/startups/{startup}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\StartupController::edit
* @see app/Http/Controllers/StartupController.php:35
* @route '/dashboard/startups/{startup}/edit'
*/
edit.url = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { startup: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { startup: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            startup: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        startup: typeof args.startup === 'object'
        ? args.startup.slug
        : args.startup,
    }

    return edit.definition.url
            .replace('{startup}', parsedArgs.startup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StartupController::edit
* @see app/Http/Controllers/StartupController.php:35
* @route '/dashboard/startups/{startup}/edit'
*/
edit.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StartupController::edit
* @see app/Http/Controllers/StartupController.php:35
* @route '/dashboard/startups/{startup}/edit'
*/
edit.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\StartupController::edit
* @see app/Http/Controllers/StartupController.php:35
* @route '/dashboard/startups/{startup}/edit'
*/
const editForm = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StartupController::edit
* @see app/Http/Controllers/StartupController.php:35
* @route '/dashboard/startups/{startup}/edit'
*/
editForm.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\StartupController::edit
* @see app/Http/Controllers/StartupController.php:35
* @route '/dashboard/startups/{startup}/edit'
*/
editForm.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: edit.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

edit.form = editForm

/**
* @see \App\Http\Controllers\StartupController::update
* @see app/Http/Controllers/StartupController.php:60
* @route '/dashboard/startups/{startup}'
*/
export const update = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/dashboard/startups/{startup}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\StartupController::update
* @see app/Http/Controllers/StartupController.php:60
* @route '/dashboard/startups/{startup}'
*/
update.url = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { startup: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { startup: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            startup: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        startup: typeof args.startup === 'object'
        ? args.startup.slug
        : args.startup,
    }

    return update.definition.url
            .replace('{startup}', parsedArgs.startup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StartupController::update
* @see app/Http/Controllers/StartupController.php:60
* @route '/dashboard/startups/{startup}'
*/
update.put = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\StartupController::update
* @see app/Http/Controllers/StartupController.php:60
* @route '/dashboard/startups/{startup}'
*/
const updateForm = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StartupController::update
* @see app/Http/Controllers/StartupController.php:60
* @route '/dashboard/startups/{startup}'
*/
updateForm.put = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\StartupController::destroy
* @see app/Http/Controllers/StartupController.php:71
* @route '/dashboard/startups/{startup}'
*/
export const destroy = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/startups/{startup}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\StartupController::destroy
* @see app/Http/Controllers/StartupController.php:71
* @route '/dashboard/startups/{startup}'
*/
destroy.url = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { startup: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { startup: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            startup: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        startup: typeof args.startup === 'object'
        ? args.startup.slug
        : args.startup,
    }

    return destroy.definition.url
            .replace('{startup}', parsedArgs.startup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\StartupController::destroy
* @see app/Http/Controllers/StartupController.php:71
* @route '/dashboard/startups/{startup}'
*/
destroy.delete = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\StartupController::destroy
* @see app/Http/Controllers/StartupController.php:71
* @route '/dashboard/startups/{startup}'
*/
const destroyForm = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\StartupController::destroy
* @see app/Http/Controllers/StartupController.php:71
* @route '/dashboard/startups/{startup}'
*/
destroyForm.delete = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
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
* @see \App\Http\Controllers\ConnectionController::connections
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
export const connections = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: connections.url(args, options),
    method: 'get',
})

connections.definition = {
    methods: ["get","head"],
    url: '/dashboard/startups/{startup}/connections',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ConnectionController::connections
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
connections.url = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { startup: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { startup: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            startup: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        startup: typeof args.startup === 'object'
        ? args.startup.slug
        : args.startup,
    }

    return connections.definition.url
            .replace('{startup}', parsedArgs.startup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConnectionController::connections
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
connections.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: connections.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConnectionController::connections
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
connections.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: connections.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ConnectionController::connections
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
const connectionsForm = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: connections.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConnectionController::connections
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
connectionsForm.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: connections.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConnectionController::connections
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
connectionsForm.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: connections.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

connections.form = connectionsForm

/**
* @see \App\Http\Controllers\ConnectionController::sync
* @see app/Http/Controllers/ConnectionController.php:70
* @route '/dashboard/startups/{startup}/sync'
*/
export const sync = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sync.url(args, options),
    method: 'post',
})

sync.definition = {
    methods: ["post"],
    url: '/dashboard/startups/{startup}/sync',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ConnectionController::sync
* @see app/Http/Controllers/ConnectionController.php:70
* @route '/dashboard/startups/{startup}/sync'
*/
sync.url = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { startup: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { startup: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            startup: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        startup: typeof args.startup === 'object'
        ? args.startup.slug
        : args.startup,
    }

    return sync.definition.url
            .replace('{startup}', parsedArgs.startup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConnectionController::sync
* @see app/Http/Controllers/ConnectionController.php:70
* @route '/dashboard/startups/{startup}/sync'
*/
sync.post = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: sync.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ConnectionController::sync
* @see app/Http/Controllers/ConnectionController.php:70
* @route '/dashboard/startups/{startup}/sync'
*/
const syncForm = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sync.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ConnectionController::sync
* @see app/Http/Controllers/ConnectionController.php:70
* @route '/dashboard/startups/{startup}/sync'
*/
syncForm.post = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: sync.url(args, options),
    method: 'post',
})

sync.form = syncForm

const startups = {
    index: Object.assign(index, index),
    show: Object.assign(show, show),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    connections: Object.assign(connections, connections),
    sync: Object.assign(sync, sync),
}

export default startups