<?php

namespace EFrame\Payment\Contracts;

interface Productable
{
    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @return string
     */
    public function getName();
}