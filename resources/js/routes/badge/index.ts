import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Public\BadgeController::svg
* @see app/Http/Controllers/Public/BadgeController.php:12
* @route '/badge/{startup}.svg'
*/
export const svg = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: svg.url(args, options),
    method: 'get',
})

svg.definition = {
    methods: ["get","head"],
    url: '/badge/{startup}.svg',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\BadgeController::svg
* @see app/Http/Controllers/Public/BadgeController.php:12
* @route '/badge/{startup}.svg'
*/
svg.url = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return svg.definition.url
            .replace('{startup}', parsedArgs.startup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\BadgeController::svg
* @see app/Http/Controllers/Public/BadgeController.php:12
* @route '/badge/{startup}.svg'
*/
svg.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: svg.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\BadgeController::svg
* @see app/Http/Controllers/Public/BadgeController.php:12
* @route '/badge/{startup}.svg'
*/
svg.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: svg.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\BadgeController::svg
* @see app/Http/Controllers/Public/BadgeController.php:12
* @route '/badge/{startup}.svg'
*/
const svgForm = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: svg.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\BadgeController::svg
* @see app/Http/Controllers/Public/BadgeController.php:12
* @route '/badge/{startup}.svg'
*/
svgForm.get = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: svg.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\BadgeController::svg
* @see app/Http/Controllers/Public/BadgeController.php:12
* @route '/badge/{startup}.svg'
*/
svgForm.head = (args: { startup: string | { slug: string } } | [startup: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: svg.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

svg.form = svgForm

const badge = {
    svg: Object.assign(svg, svg),
}

export default badge