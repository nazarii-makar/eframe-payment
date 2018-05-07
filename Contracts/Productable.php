<?php

namespace EFrame\Payment\Contracts;

interface Productable
{
    /**
     * @return string
     */
    public function productCurrency();

    /**
     * @return float
     */
    public function productPrice();

    /**
     * @return string
     */
    public function productName();
}