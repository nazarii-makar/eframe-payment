<?php

namespace EFrame\Payment\Events;

use EFrame\Payment\Models\Order;

/**
 * Class OrderVerified
 * @package EFrame\Payment\Events
 */
class OrderVerified extends Event
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