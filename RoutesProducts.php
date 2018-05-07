<?php

namespace EFrame\Payment;

use EFrame\Payment\Exceptions\InvalidArgumentException;

trait RoutesProducts
{
    /**
     * @return string
     */
    public function productCurrency()
    {
        return $this->currency;
    }

    /**
     * @return float
     */
    public function productPrice()
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function productName()
    {
        return $this->name;
    }
}
