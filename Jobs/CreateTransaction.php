<?php

namespace EFrame\Payment\Jobs;

use EFrame\Payment\Models\Order;

class CreateTransaction extends Job
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @var \Illuminate\Support\Collection
     */
    protected $attributes;

    /**
     * PurchaseOrder constructor.
     *
     * @param Order $order
     * @param array $attributes
     */
    public function __construct(Order $order, $attributes = [])
    {
        $this->order      = $order;
        $this->attributes = collect($attributes);
    }

    /**
     * @return Order
     */
    public function handle()
    {
        return $this->order->transactions()->create(
            $this->attributes->toArray()
        );
    }
}