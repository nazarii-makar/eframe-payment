<?php

namespace EFrame\Payment\Events;

use EFrame\Payment\Order;

/**
 * Class OrderPurchased
 * @package EFrame\Payment\Events
 */
class OrderPurchased extends Event
{
    /**
     * @var Order
     */
    public $order;

    /**
     * BookingCreated constructor.
     *
     * @param Booking $booking
     */
    public function __construct(Order $order)
    {
        $order->load(['products']);

        $this->order = $order;
    }
}