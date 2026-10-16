import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\StartupApiController::index
* @see app/Http/Controllers/Api/StartupApiController.php:13
* @route '/api/startups'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/startups',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\StartupApiController::index
* @see app/Http/Controllers/Api/StartupApiController.php:13
* @route '/api/startups'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\StartupApiController::index
* @see app/Http/Controllers/Api/StartupApiController.php:13
* @route '/api/startups'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\StartupApiController::index
* @see app/Http/Controllers/Api/StartupApiController.php:13
* @route '/api/startups'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\StartupApiController::index
* @see app/Http/Controllers/Api/StartupApiController.php:13
* @route '/api/startups'
*/
const indexForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\StartupApiController::index
* @see app/Http/Controllers/Api/StartupApiController.php:13
* @route '/api/startups'
*/
indexForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\StartupApiController::index
* @see app/Http/Controllers/Api/StartupApiController.php:13
* @route '/api/startups'
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
* @see \App\Http\Controllers\Api\StartupApiController::show
* @see app/Http/Controllers/Api/StartupApiController.php:31
* @route '/api/startups/{startup}'
*/
export const show = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/startups/{startup}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\StartupApiController::show
* @see app/Http/Controllers/Api/StartupApiController.php:31
* @route '/api/startups/{startup}'
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
* @see \App\Http\Controllers\Api\StartupApiController::show
* @see app/Http/Controllers/Api/StartupApiController.php:31
* @route '/api/startups/{startup}'
*/
show.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\StartupApiController::show
* @see app/Http/Controllers/Api/StartupApiController.php:31
* @route '/api/startups/{startup}'
*/
show.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\StartupApiController::show
* @see app/Http/Controllers/Api/StartupApiController.php:31
* @route '/api/startups/{startup}'
*/
const showForm = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\StartupApiController::show
* @see app/Http/Controllers/Api/StartupApiController.php:31
* @route '/api/startups/{startup}'
*/
showForm.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\StartupApiController::show
* @see app/Http/Controllers/Api/StartupApiController.php:31
* @route '/api/startups/{startup}'
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

const StartupApiController = { index, show }

export default StartupApiController