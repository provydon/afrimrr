import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
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

const connections = {
    store: Object.assign(store, store),
    destroy: Object.assign(destroy, destroy),
}

export default connections