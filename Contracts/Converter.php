<?php

namespace EFrame\Payment\Contracts;

use EFrame\Payment\Order;

interface Converter
{
    /**
     * @param Order $order
     *
     * @return array
     */
    public static function createFromOrder(Order $order);
}