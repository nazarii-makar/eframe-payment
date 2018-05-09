<?php

namespace EFrame\Payment;

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