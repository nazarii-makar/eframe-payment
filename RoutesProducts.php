<?php

namespace EFrame\Payment;

trait RoutesProducts
{
    /**
     * @return string
     */
    public function getCurrencyKey()
    {
        return 'currency';
    }

    /**
     * @return float
     */
    public function getPriceKey()
    {
        return 'price';
    }

    /**
     * @return string
     */
    public function getNameKey()
    {
        return 'name';
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->getAttribute($this->getCurrencyKey());
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->getAttribute($this->getPriceKey());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute($this->getNameKey());
    }
}
