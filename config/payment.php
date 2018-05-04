<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Service Name
    |--------------------------------------------------------------------------
    |
    */

    'default' => env('PAYMENT_SERVICE', 'wayforpay'),

    /*
    |--------------------------------------------------------------------------
    | Services
    |--------------------------------------------------------------------------
    |
    */

    'services' => [

        'wayforpay' => [
            'driver'           => 'wayforpay',
            'account'          => env('WAYFORPAY_MERCHANT_ACCOUNT'),
            'password'         => env('WAYFORPAY_MERCHANT_PASSWORD'),
            'regular_password' => env('WAYFORPAY_MERCHANT_REGULAR_PASSWORD'),
            'domain_name'      => env('WAYFORPAY_MERCHANT_DOMAIN_NAME'),
        ],

    ],
];