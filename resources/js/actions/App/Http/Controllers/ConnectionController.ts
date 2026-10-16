import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\ConnectionController::index
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
export const index = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/dashboard/startups/{startup}/connections',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\ConnectionController::index
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
index.url = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return index.definition.url
            .replace('{startup}', parsedArgs.startup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConnectionController::index
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
index.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConnectionController::index
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
index.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\ConnectionController::index
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
const indexForm = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConnectionController::index
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
indexForm.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\ConnectionController::index
* @see app/Http/Controllers/ConnectionController.php:16
* @route '/dashboard/startups/{startup}/connections'
*/
indexForm.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

index.form = indexForm

/**
* @see \App\Http\Controllers\ConnectionController::store
* @see app/Http/Controllers/ConnectionController.php:45
* @route '/dashboard/startups/{startup}/connections'
*/
export const store = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/dashboard/startups/{startup}/connections',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\ConnectionController::store
* @see app/Http/Controllers/ConnectionController.php:45
* @route '/dashboard/startups/{startup}/connections'
*/
store.url = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return store.definition.url
            .replace('{startup}', parsedArgs.startup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConnectionController::store
* @see app/Http/Controllers/ConnectionController.php:45
* @route '/dashboard/startups/{startup}/connections'
*/
store.post = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ConnectionController::store
* @see app/Http/Controllers/ConnectionController.php:45
* @route '/dashboard/startups/{startup}/connections'
*/
const storeForm = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ConnectionController::store
* @see app/Http/Controllers/ConnectionController.php:45
* @route '/dashboard/startups/{startup}/connections'
*/
storeForm.post = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

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

/**
* @see \App\Http\Controllers\ConnectionController::destroy
* @see app/Http/Controllers/ConnectionController.php:83
* @route '/dashboard/startups/{startup}/connections/{provider}'
*/
export const destroy = (args: { startup: string | { slug: string }, provider: string | number } | [startup: string | { slug: string }, provider: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/dashboard/startups/{startup}/connections/{provider}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\ConnectionController::destroy
* @see app/Http/Controllers/ConnectionController.php:83
* @route '/dashboard/startups/{startup}/connections/{provider}'
*/
destroy.url = (args: { startup: string | { slug: string }, provider: string | number } | [startup: string | { slug: string }, provider: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            startup: args[0],
            provider: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        startup: typeof args.startup === 'object'
        ? args.startup.slug
        : args.startup,
        provider: args.provider,
    }

    return destroy.definition.url
            .replace('{startup}', parsedArgs.startup.toString())
            .replace('{provider}', parsedArgs.provider.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\ConnectionController::destroy
* @see app/Http/Controllers/ConnectionController.php:83
* @route '/dashboard/startups/{startup}/connections/{provider}'
*/
destroy.delete = (args: { startup: string | { slug: string }, provider: string | number } | [startup: string | { slug: string }, provider: string | number ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\ConnectionController::destroy
* @see app/Http/Controllers/ConnectionController.php:83
* @route '/dashboard/startups/{startup}/connections/{provider}'
*/
const destroyForm = (args: { startup: string | { slug: string }, provider: string | number } | [startup: string | { slug: string }, provider: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\ConnectionController::destroy
* @see app/Http/Controllers/ConnectionController.php:83
* @route '/dashboard/startups/{startup}/connections/{provider}'
*/
destroyForm.delete = (args: { startup: string | { slug: string }, provider: string | number } | [startup: string | { slug: string }, provider: string | number ], options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: destroy.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'DELETE',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

destroy.form = destroyForm

const ConnectionController = { index, store, sync, destroy }

export default ConnectionController