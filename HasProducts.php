<?php

namespace EFrame\Payment;

use EFrame\Payment\Models\OrderProduct;

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
