import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition } from './../wayfinder'
/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
const loginForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
loginForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::login
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:47
* @route '/login'
*/
loginForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: login.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

login.form = loginForm

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::logout
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:100
* @route '/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::logout
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:100
* @route '/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::logout
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:100
* @route '/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::logout
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:100
* @route '/logout'
*/
const logoutForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

/**
* @see \Laravel\Fortify\Http\Controllers\AuthenticatedSessionController::logout
* @see vendor/laravel/fortify/src/Http/Controllers/AuthenticatedSessionController.php:100
* @route '/logout'
*/
logoutForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: logout.url(options),
    method: 'post',
})

logout.form = logoutForm

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
export const register = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

register.definition = {
    methods: ["get","head"],
    url: '/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
register.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
register.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: register.url(options),
    method: 'head',
})

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
const registerForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: register.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
registerForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: register.url(options),
    method: 'get',
})

/**
* @see \Laravel\Fortify\Http\Controllers\RegisteredUserController::register
* @see vendor/laravel/fortify/src/Http/Controllers/RegisteredUserController.php:41
* @route '/register'
*/
registerForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: register.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

register.form = registerForm

/**
* @see \App\Http\Controllers\HomeController::home
* @see app/Http/Controllers/HomeController.php:13
* @route '/'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\HomeController::home
* @see app/Http/Controllers/HomeController.php:13
* @route '/'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\HomeController::home
* @see app/Http/Controllers/HomeController.php:13
* @route '/'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\HomeController::home
* @see app/Http/Controllers/HomeController.php:13
* @route '/'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\HomeController::home
* @see app/Http/Controllers/HomeController.php:13
* @route '/'
*/
const homeForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\HomeController::home
* @see app/Http/Controllers/HomeController.php:13
* @route '/'
*/
homeForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\HomeController::home
* @see app/Http/Controllers/HomeController.php:13
* @route '/'
*/
homeForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: home.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

home.form = homeForm

/**
* @see \App\Http\Controllers\Public\LeaderboardController::leaderboard
* @see app/Http/Controllers/Public/LeaderboardController.php:14
* @route '/leaderboard'
*/
export const leaderboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: leaderboard.url(options),
    method: 'get',
})

leaderboard.definition = {
    methods: ["get","head"],
    url: '/leaderboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\LeaderboardController::leaderboard
* @see app/Http/Controllers/Public/LeaderboardController.php:14
* @route '/leaderboard'
*/
leaderboard.url = (options?: RouteQueryOptions) => {
    return leaderboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\LeaderboardController::leaderboard
* @see app/Http/Controllers/Public/LeaderboardController.php:14
* @route '/leaderboard'
*/
leaderboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: leaderboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\LeaderboardController::leaderboard
* @see app/Http/Controllers/Public/LeaderboardController.php:14
* @route '/leaderboard'
*/
leaderboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: leaderboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\LeaderboardController::leaderboard
* @see app/Http/Controllers/Public/LeaderboardController.php:14
* @route '/leaderboard'
*/
const leaderboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: leaderboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\LeaderboardController::leaderboard
* @see app/Http/Controllers/Public/LeaderboardController.php:14
* @route '/leaderboard'
*/
leaderboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: leaderboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\LeaderboardController::leaderboard
* @see app/Http/Controllers/Public/LeaderboardController.php:14
* @route '/leaderboard'
*/
leaderboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: leaderboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

leaderboard.form = leaderboardForm

/**
* @see \App\Http\Controllers\Public\MarketplaceController::marketplace
* @see app/Http/Controllers/Public/MarketplaceController.php:14
* @route '/marketplace'
*/
export const marketplace = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: marketplace.url(options),
    method: 'get',
})

marketplace.definition = {
    methods: ["get","head"],
    url: '/marketplace',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Public\MarketplaceController::marketplace
* @see app/Http/Controllers/Public/MarketplaceController.php:14
* @route '/marketplace'
*/
marketplace.url = (options?: RouteQueryOptions) => {
    return marketplace.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Public\MarketplaceController::marketplace
* @see app/Http/Controllers/Public/MarketplaceController.php:14
* @route '/marketplace'
*/
marketplace.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: marketplace.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\MarketplaceController::marketplace
* @see app/Http/Controllers/Public/MarketplaceController.php:14
* @route '/marketplace'
*/
marketplace.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: marketplace.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Public\MarketplaceController::marketplace
* @see app/Http/Controllers/Public/MarketplaceController.php:14
* @route '/marketplace'
*/
const marketplaceForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: marketplace.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\MarketplaceController::marketplace
* @see app/Http/Controllers/Public/MarketplaceController.php:14
* @route '/marketplace'
*/
marketplaceForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: marketplace.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Public\MarketplaceController::marketplace
* @see app/Http/Controllers/Public/MarketplaceController.php:14
* @route '/marketplace'
*/
marketplaceForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: marketplace.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

marketplace.form = marketplaceForm

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

/**
* @see \App\Http\Controllers\DashboardController::dashboard
* @see app/Http/Controllers/DashboardController.php:13
* @route '/dashboard'
*/
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\DashboardController::dashboard
* @see app/Http/Controllers/DashboardController.php:13
* @route '/dashboard'
*/
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\DashboardController::dashboard
* @see app/Http/Controllers/DashboardController.php:13
* @route '/dashboard'
*/
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::dashboard
* @see app/Http/Controllers/DashboardController.php:13
* @route '/dashboard'
*/
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\DashboardController::dashboard
* @see app/Http/Controllers/DashboardController.php:13
* @route '/dashboard'
*/
const dashboardForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::dashboard
* @see app/Http/Controllers/DashboardController.php:13
* @route '/dashboard'
*/
dashboardForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\DashboardController::dashboard
* @see app/Http/Controllers/DashboardController.php:13
* @route '/dashboard'
*/
dashboardForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: dashboard.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

dashboard.form = dashboardForm
