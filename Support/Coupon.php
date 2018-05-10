<?php

namespace EFrame\Payment\Support;

use EFrame\Payment\Models\Order;
use EFrame\Payment\Contracts\Couponable;

abstract class Coupon implements Couponable
{
    /**
     * @param Order $order
     *
     * @return Order
     */
    abstract public function redemption(Order $order);
}