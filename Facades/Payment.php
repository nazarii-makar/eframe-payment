<?php

namespace EFrame\Payment\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Payment
 * @package EFrame\Payment\Facades
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