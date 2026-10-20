<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported payment providers
    |--------------------------------------------------------------------------
    |
    | Providers we pull verified revenue from. For each we sum successful
    | transaction volume over the trailing 30 days (via their transactions /
    | charges APIs) and normalize to monthly USD. Monnify is intentionally not
    | here yet.
    |
    */
    'providers' => [
        'paystack' => [
            'label' => 'Paystack',
            'base' => 'https://api.paystack.co',
            'key_hint' => 'Secret key (sk_live_... / sk_test_...). A read-only key is enough.',
        ],
        'flutterwave' => [
            'label' => 'Flutterwave',
            'base' => 'https://api.flutterwave.com/v3',
            'key_hint' => 'Secret key (FLWSECK-...).',
        ],
        'stripe' => [
            'label' => 'Stripe',
            'base' => 'https://api.stripe.com/v1',
            'key_hint' => 'Restricted key with read access to Charges (rk_live_... / sk_live_...).',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | FX rates — USD per 1 major unit of each currency
    |--------------------------------------------------------------------------
    |
    | PLACEHOLDER rates (approx. Q4 2026). MRR is normalized to USD for ranking.
    | Replace with a live FX feed (e.g. a daily cron hitting an FX API) in prod.
    |
    */
    'fx' => [
        'USD' => 1.0,
        'EUR' => 1.08,
        'GBP' => 1.27,
        'NGN' => 0.00063,
        'GHS' => 0.064,
        'KES' => 0.0077,
        'ZAR' => 0.055,
        'EGP' => 0.020,
        'XOF' => 0.0017,
        'XAF' => 0.0017,
        'RWF' => 0.00072,
        'UGX' => 0.00027,
        'TZS' => 0.00037,
        'ZMW' => 0.037,
        'MAD' => 0.10,
        'ETB' => 0.0080,
    ],

    'categories' => [
        'ai' => 'AI',
        'saas' => 'SaaS',
        'fintech' => 'Fintech',
        'devtools' => 'Developer Tools',
        'marketing' => 'Marketing',
        'ecommerce' => 'E-commerce',
        'healthtech' => 'HealthTech',
        'edtech' => 'EdTech',
        'agritech' => 'AgriTech',
        'logistics' => 'Logistics',
        'productivity' => 'Productivity',
        'media' => 'Media & Creator',
        'other' => 'Other',
    ],

    // ISO-2 => display name. African-first, with a few global billing hubs.
    'countries' => [
        'NG' => 'Nigeria',
        'GH' => 'Ghana',
        'KE' => 'Kenya',
        'ZA' => 'South Africa',
        'EG' => 'Egypt',
        'RW' => 'Rwanda',
        'UG' => 'Uganda',
        'TZ' => 'Tanzania',
        'SN' => 'Senegal',
        'CI' => "Côte d'Ivoire",
        'MA' => 'Morocco',
        'ET' => 'Ethiopia',
        'ZM' => 'Zambia',
        'CM' => 'Cameroon',
        'OTHER' => 'Other',
    ],
];
