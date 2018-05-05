<?php

namespace EFrame\Payment\Facades;

use Illuminate\Support\Facades\Facade;
use EFrame\Payment\Contracts\Gateway as GatewayContract;

/**
 * Class Payment
 * @package EFrame\Payment\Facades
 *
 * @method static GatewayContract gateway($gateway_name)
 */
class Payment extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'payment';
    }
}