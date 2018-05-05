<?php

namespace EFrame\Payment;

use Illuminate\Support\Collection;
use EFrame\Payment\Exceptions\RuntimeException;
use EFrame\Payment\Exceptions\InvalidArgumentException;
use EFrame\Payment\Contracts\Gateway as GatewayContract;

class GatewayFactory
{
    /**
     * @var Collection
     */
    protected $gateways;

    /**
     * GatewayFactory constructor.
     *
     * @param array $gateways
     */
    public function __construct($gateways = [])
    {
        $this->replace($gateways);
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->gateways;
    }

    /**
     * @param array $gateways
     *
     * @return $this
     */
    public function replace($gateways = [])
    {
        $this->gateways = collect($gateways);

        return $this;
    }

    /**
     * @param $gateway_name
     *
     * @return $this
     */
    public function register($gateway_name, $options = [])
    {
        if (!$this->gateways->has($gateway_name)) {
            $this->gateways->offsetSet($gateway_name, collect($options));
        }

        return $this;
    }

    /**
     * @param string $gateway_name
     *
     * @return Gateway
     */
    public function create($gateway_name)
    {
        throw_unless(
            $this->gateways->has($gateway_name),
            new RuntimeException("Gateway '{$gateway_name}' not found.")
        );

        $gateway_config = collect($this->gateways->get($gateway_name));

        $gateway_options = collect($gateway_config->get('options'));

        $gateway_driver_name = $gateway_config->get('driver');

        throw_unless(
            app()->has($gateway_driver_name),
            new RuntimeException("Gateway driver '{$gateway_driver_name}' not found.")
        );

        /** @var GatewayContract $gateway_driver */
        $gateway_driver = app($gateway_driver_name);

        throw_unless(
            $gateway_driver instanceof GatewayContract,
            new InvalidArgumentException("Gateway driver '{$gateway_driver_name}' must implement '" . GatewayContract::class . "'.'")
        );

        $gateway_driver->setOptions(
            $gateway_options
        )->boot();

        return $gateway_driver;
    }
}