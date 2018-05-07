<?php

namespace EFrame\Payment;

trait HasProducts
{
    /**
     * Get the entity's products.
     */
    public function products()
    {
        return $this->morphMany(OrderProduct::class, 'resource')->orderBy('created_at', 'desc');
    }
}
