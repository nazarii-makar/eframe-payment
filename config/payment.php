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
                'merchantAccount'               => env('WAYFORPAY_MERCHANT_ACCOUNT'),
                'merchantPassword'              => env('WAYFORPAY_MERCHANT_PASSWORD'),
                'merchantRegularPassword'       => env('WAYFORPAY_MERCHANT_REGULAR_PASSWORD'),
                'merchantDomainName'            => env('WAYFORPAY_MERCHANT_DOMAIN_NAME'),
                'merchantTransactionType'       => env('WAYFORPAY_MERCHANT_TRANSACTION_TYPE'),
                'merchantTransactionSecureType' => env('WAYFORPAY_MERCHANT_TRANSACTION_SECURE_TYPE'),
                'serviceUrl'                    => env('WAYFORPAY_SERVICE_URL'),
                'charset'                       => env('WAYFORPAY_CHARSET'),
            ],
        ],

    ],
];