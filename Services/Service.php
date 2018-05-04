<?php

namespace EFrame\Payment\Services;

use Illuminate\Support\Collection;
use EFrame\Payment\Contracts\Payment;

/**
 * Class Service
 * @package EFrame\Payment\Services
 */
abstract class Service implements Payment
{
    /**
     * @var Collection
     */
    protected $options;

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options = [])
    {
        $this->options = collect($options);

        return $this;
    }

    /**
     * Bootstraping service
     */
    public function boot()
    {
        //
    }
}