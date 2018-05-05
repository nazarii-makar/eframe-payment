<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway Name
    |--------------------------------------------------------------------------
    |
    */

    'default' => env('PAYMENT_GATEWAY', 'wayforpay'),

    /*
    |--------------------------------------------------------------------------
    | Gateways
    |--------------------------------------------------------------------------
    |
    */

    'gateways' => [

        'wayforpay' => [
            'driver'  => 'wayforpay',
            'options' => [
                'account'          => env('WAYFORPAY_MERCHANT_ACCOUNT'),
                'password'         => env('WAYFORPAY_MERCHANT_PASSWORD'),
                'regular_password' => env('WAYFORPAY_MERCHANT_REGULAR_PASSWORD'),
                'domain_name'      => env('WAYFORPAY_MERCHANT_DOMAIN_NAME'),
            ],
        ],

    ],
];