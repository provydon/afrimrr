import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../wayfinder'
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

const StartupProfileController = { show }

export default StartupProfileController