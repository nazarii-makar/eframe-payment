<?php

namespace EFrame\Payment\Jobs;

use EFrame\Payment\Models\Order;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class VerifyOrder extends Job
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
     * VerifyOrder constructor.
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
        throw_if(
            Order::STATUS_ACTIVE === $this->order->status && !$this->order->is_regular,
            ConflictHttpException::class
        );

        $this->order->fill(
            $this->attributes->toArray()
        )->verify();

        return $this->order;
    }
}