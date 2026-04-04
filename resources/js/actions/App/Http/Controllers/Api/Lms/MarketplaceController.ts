import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::browse
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:16
* @route '/api/lms/marketplace'
*/
export const browse = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: browse.url(options),
    method: 'get',
})

browse.definition = {
    methods: ["get","head"],
    url: '/api/lms/marketplace',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::browse
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:16
* @route '/api/lms/marketplace'
*/
browse.url = (options?: RouteQueryOptions) => {
    return browse.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::browse
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:16
* @route '/api/lms/marketplace'
*/
browse.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: browse.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::browse
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:16
* @route '/api/lms/marketplace'
*/
browse.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: browse.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::browse
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:16
* @route '/api/lms/marketplace'
*/
const browseForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: browse.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::browse
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:16
* @route '/api/lms/marketplace'
*/
browseForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: browse.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::browse
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:16
* @route '/api/lms/marketplace'
*/
browseForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: browse.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

browse.form = browseForm

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::myListings
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:24
* @route '/api/lms/marketplace/my-listings'
*/
export const myListings = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: myListings.url(options),
    method: 'get',
})

myListings.definition = {
    methods: ["get","head"],
    url: '/api/lms/marketplace/my-listings',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::myListings
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:24
* @route '/api/lms/marketplace/my-listings'
*/
myListings.url = (options?: RouteQueryOptions) => {
    return myListings.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::myListings
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:24
* @route '/api/lms/marketplace/my-listings'
*/
myListings.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: myListings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::myListings
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:24
* @route '/api/lms/marketplace/my-listings'
*/
myListings.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: myListings.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::myListings
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:24
* @route '/api/lms/marketplace/my-listings'
*/
const myListingsForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: myListings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::myListings
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:24
* @route '/api/lms/marketplace/my-listings'
*/
myListingsForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: myListings.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::myListings
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:24
* @route '/api/lms/marketplace/my-listings'
*/
myListingsForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: myListings.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

myListings.form = myListingsForm

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::createListing
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:32
* @route '/api/lms/marketplace'
*/
export const createListing = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createListing.url(options),
    method: 'post',
})

createListing.definition = {
    methods: ["post"],
    url: '/api/lms/marketplace',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::createListing
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:32
* @route '/api/lms/marketplace'
*/
createListing.url = (options?: RouteQueryOptions) => {
    return createListing.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::createListing
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:32
* @route '/api/lms/marketplace'
*/
createListing.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createListing.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::createListing
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:32
* @route '/api/lms/marketplace'
*/
const createListingForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createListing.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::createListing
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:32
* @route '/api/lms/marketplace'
*/
createListingForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: createListing.url(options),
    method: 'post',
})

createListing.form = createListingForm

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::publish
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:51
* @route '/api/lms/marketplace/{listing}/publish'
*/
export const publish = (args: { listing: string | number } | [listing: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: publish.url(args, options),
    method: 'post',
})

publish.definition = {
    methods: ["post"],
    url: '/api/lms/marketplace/{listing}/publish',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::publish
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:51
* @route '/api/lms/marketplace/{listing}/publish'
*/
publish.url = (args: { listing: string | number } | [listing: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { listing: args }
    }

    if (Array.isArray(args)) {
        args = {
            listing: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        listing: args.listing,
    }

    return publish.definition.url
            .replace('{listing}', parsedArgs.listing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::publish
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:51
* @route '/api/lms/marketplace/{listing}/publish'
*/
publish.post = (args: { listing: string | number } | [listing: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: publish.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::publish
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:51
* @route '/api/lms/marketplace/{listing}/publish'
*/
const publishForm = (args: { listing: string | number } | [listing: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: publish.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::publish
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:51
* @route '/api/lms/marketplace/{listing}/publish'
*/
publishForm.post = (args: { listing: string | number } | [listing: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: publish.url(args, options),
    method: 'post',
})

publish.form = publishForm

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchase
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:63
* @route '/api/lms/marketplace/{listing}/purchase'
*/
export const purchase = (args: { listing: string | number } | [listing: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: purchase.url(args, options),
    method: 'post',
})

purchase.definition = {
    methods: ["post"],
    url: '/api/lms/marketplace/{listing}/purchase',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchase
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:63
* @route '/api/lms/marketplace/{listing}/purchase'
*/
purchase.url = (args: { listing: string | number } | [listing: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { listing: args }
    }

    if (Array.isArray(args)) {
        args = {
            listing: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        listing: args.listing,
    }

    return purchase.definition.url
            .replace('{listing}', parsedArgs.listing.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchase
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:63
* @route '/api/lms/marketplace/{listing}/purchase'
*/
purchase.post = (args: { listing: string | number } | [listing: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: purchase.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchase
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:63
* @route '/api/lms/marketplace/{listing}/purchase'
*/
const purchaseForm = (args: { listing: string | number } | [listing: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: purchase.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchase
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:63
* @route '/api/lms/marketplace/{listing}/purchase'
*/
purchaseForm.post = (args: { listing: string | number } | [listing: string | number ] | string | number, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: purchase.url(args, options),
    method: 'post',
})

purchase.form = purchaseForm

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchases
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:71
* @route '/api/lms/marketplace/purchases'
*/
export const purchases = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: purchases.url(options),
    method: 'get',
})

purchases.definition = {
    methods: ["get","head"],
    url: '/api/lms/marketplace/purchases',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchases
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:71
* @route '/api/lms/marketplace/purchases'
*/
purchases.url = (options?: RouteQueryOptions) => {
    return purchases.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchases
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:71
* @route '/api/lms/marketplace/purchases'
*/
purchases.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: purchases.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchases
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:71
* @route '/api/lms/marketplace/purchases'
*/
purchases.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: purchases.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchases
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:71
* @route '/api/lms/marketplace/purchases'
*/
const purchasesForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: purchases.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchases
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:71
* @route '/api/lms/marketplace/purchases'
*/
purchasesForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: purchases.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Api\Lms\MarketplaceController::purchases
* @see app/Http/Controllers/Api/Lms/MarketplaceController.php:71
* @route '/api/lms/marketplace/purchases'
*/
purchasesForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: purchases.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

purchases.form = purchasesForm

const MarketplaceController = { browse, myListings, createListing, publish, purchase, purchases }

export default MarketplaceController