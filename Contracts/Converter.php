<?php

namespace EFrame\Payment\Contracts;

use EFrame\Payment\Models\Order;

interface Converter
{
    /**
     * @param Order $order
     *
     * @return array
     */
    public static function createFromOrder(Order $order);
}