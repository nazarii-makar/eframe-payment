<?php

namespace EFrame\Payment\Gateways;

use Illuminate\Support\Collection;
use EFrame\Payment\Contracts\Gateway as GatewayContract;

/**
 * Class Service
 * @package EFrame\Payment\Gateways
 */
abstract class Gateway implements GatewayContract
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