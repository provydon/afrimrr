import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Public\FounderProfileController::show
* @see app/Http/Controllers/Public/FounderProfileController.php:15
* @route '/founders/{username}'
*/
export const show = (args: { username: string | number } | [username: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/founders/{username}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\FounderProfileController::show
* @see app/Http/Controllers/Public/FounderProfileController.php:15
* @route '/founders/{username}'
*/
show.url = (args: { username: string | number } | [username: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { username: args }
    }

    if (Array.isArray(args)) {
        args = {
            username: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        username: args.username,
    }

    return show.definition.url
            .replace('{username}', parsedArgs.username.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\FounderProfileController::show
* @see app/Http/Controllers/Public/FounderProfileController.php:15
* @route '/founders/{username}'
*/
show.get = (args: { username: string | number } | [username: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\FounderProfileController::show
* @see app/Http/Controllers/Public/FounderProfileController.php:15
* @route '/founders/{username}'
*/
show.head = (args: { username: string | number } | [username: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\FounderProfileController::show
* @see app/Http/Controllers/Public/FounderProfileController.php:15
* @route '/founders/{username}'
*/
const showForm = (args: { username: string | number } | [username: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\FounderProfileController::show
* @see app/Http/Controllers/Public/FounderProfileController.php:15
* @route '/founders/{username}'
*/
showForm.get = (args: { username: string | number } | [username: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\FounderProfileController::show
* @see app/Http/Controllers/Public/FounderProfileController.php:15
* @route '/founders/{username}'
*/
showForm.head = (args: { username: string | number } | [username: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

const founders = {
    show: Object.assign(show, show),
}

export default founders