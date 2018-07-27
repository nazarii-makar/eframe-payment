<?php

namespace EFrame\Payment\Observers;

use EFrame\Payment\Models\Order;
use EFrame\Payment\Events\{
    OrderPurchased,
    OrderVerified
};

class OrderObserver
{
    /**
     * Listen to the Order purchased event.
     *
     * @param  Order $order
     *
     * @return void
     */
    public function purchased(Order $order)
    {
        event(new OrderPurchased($order));

        $order->activate();
    }

    /**
     * Listen to the Order verified event.
     *
     * @param  Order $order
     *
     * @return void
     */
    public function verified(Order $order)
    {
        event(new OrderVerified($order));

        $order->activate();
    }
}