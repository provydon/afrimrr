import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Public\PageController::faq
* @see app/Http/Controllers/Public/PageController.php:11
* @route '/faq'
*/
export const faq = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: faq.url(options),
    method: 'get',
})

faq.definition = {
    methods: ["get","head"],
    url: '/faq',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\PageController::faq
* @see app/Http/Controllers/Public/PageController.php:11
* @route '/faq'
*/
faq.url = (options?: RouteQueryOptions) => {
    return faq.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\PageController::faq
* @see app/Http/Controllers/Public/PageController.php:11
* @route '/faq'
*/
faq.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: faq.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::faq
* @see app/Http/Controllers/Public/PageController.php:11
* @route '/faq'
*/
faq.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: faq.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\PageController::faq
* @see app/Http/Controllers/Public/PageController.php:11
* @route '/faq'
*/
const faqForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: faq.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::faq
* @see app/Http/Controllers/Public/PageController.php:11
* @route '/faq'
*/
faqForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: faq.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::faq
* @see app/Http/Controllers/Public/PageController.php:11
* @route '/faq'
*/
faqForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: faq.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

faq.form = faqForm

/**
* @see \App\Http\Controllers\Public\PageController::about
* @see app/Http/Controllers/Public/PageController.php:16
* @route '/about'
*/
export const about = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: about.url(options),
    method: 'get',
})

about.definition = {
    methods: ["get","head"],
    url: '/about',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\PageController::about
* @see app/Http/Controllers/Public/PageController.php:16
* @route '/about'
*/
about.url = (options?: RouteQueryOptions) => {
    return about.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\PageController::about
* @see app/Http/Controllers/Public/PageController.php:16
* @route '/about'
*/
about.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: about.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::about
* @see app/Http/Controllers/Public/PageController.php:16
* @route '/about'
*/
about.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: about.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\PageController::about
* @see app/Http/Controllers/Public/PageController.php:16
* @route '/about'
*/
const aboutForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: about.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::about
* @see app/Http/Controllers/Public/PageController.php:16
* @route '/about'
*/
aboutForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: about.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::about
* @see app/Http/Controllers/Public/PageController.php:16
* @route '/about'
*/
aboutForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: about.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

about.form = aboutForm

/**
* @see \App\Http\Controllers\Public\PageController::terms
* @see app/Http/Controllers/Public/PageController.php:21
* @route '/terms'
*/
export const terms = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: terms.url(options),
    method: 'get',
})

terms.definition = {
    methods: ["get","head"],
    url: '/terms',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\PageController::terms
* @see app/Http/Controllers/Public/PageController.php:21
* @route '/terms'
*/
terms.url = (options?: RouteQueryOptions) => {
    return terms.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\PageController::terms
* @see app/Http/Controllers/Public/PageController.php:21
* @route '/terms'
*/
terms.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: terms.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::terms
* @see app/Http/Controllers/Public/PageController.php:21
* @route '/terms'
*/
terms.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: terms.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\PageController::terms
* @see app/Http/Controllers/Public/PageController.php:21
* @route '/terms'
*/
const termsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: terms.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::terms
* @see app/Http/Controllers/Public/PageController.php:21
* @route '/terms'
*/
termsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: terms.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::terms
* @see app/Http/Controllers/Public/PageController.php:21
* @route '/terms'
*/
termsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: terms.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

terms.form = termsForm

/**
* @see \App\Http\Controllers\Public\PageController::privacy
* @see app/Http/Controllers/Public/PageController.php:26
* @route '/privacy'
*/
export const privacy = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: privacy.url(options),
    method: 'get',
})

privacy.definition = {
    methods: ["get","head"],
    url: '/privacy',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\PageController::privacy
* @see app/Http/Controllers/Public/PageController.php:26
* @route '/privacy'
*/
privacy.url = (options?: RouteQueryOptions) => {
    return privacy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\PageController::privacy
* @see app/Http/Controllers/Public/PageController.php:26
* @route '/privacy'
*/
privacy.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: privacy.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::privacy
* @see app/Http/Controllers/Public/PageController.php:26
* @route '/privacy'
*/
privacy.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: privacy.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\PageController::privacy
* @see app/Http/Controllers/Public/PageController.php:26
* @route '/privacy'
*/
const privacyForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: privacy.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::privacy
* @see app/Http/Controllers/Public/PageController.php:26
* @route '/privacy'
*/
privacyForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: privacy.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::privacy
* @see app/Http/Controllers/Public/PageController.php:26
* @route '/privacy'
*/
privacyForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: privacy.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

privacy.form = privacyForm

/**
* @see \App\Http\Controllers\Public\PageController::badgeStore
* @see app/Http/Controllers/Public/PageController.php:31
* @route '/badge-store'
*/
export const badgeStore = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: badgeStore.url(options),
    method: 'get',
})

badgeStore.definition = {
    methods: ["get","head"],
    url: '/badge-store',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\PageController::badgeStore
* @see app/Http/Controllers/Public/PageController.php:31
* @route '/badge-store'
*/
badgeStore.url = (options?: RouteQueryOptions) => {
    return badgeStore.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\PageController::badgeStore
* @see app/Http/Controllers/Public/PageController.php:31
* @route '/badge-store'
*/
badgeStore.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: badgeStore.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::badgeStore
* @see app/Http/Controllers/Public/PageController.php:31
* @route '/badge-store'
*/
badgeStore.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: badgeStore.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\PageController::badgeStore
* @see app/Http/Controllers/Public/PageController.php:31
* @route '/badge-store'
*/
const badgeStoreForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: badgeStore.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::badgeStore
* @see app/Http/Controllers/Public/PageController.php:31
* @route '/badge-store'
*/
badgeStoreForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: badgeStore.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\PageController::badgeStore
* @see app/Http/Controllers/Public/PageController.php:31
* @route '/badge-store'
*/
badgeStoreForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: badgeStore.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

badgeStore.form = badgeStoreForm

const PageController = { faq, about, terms, privacy, badgeStore }

export default PageController