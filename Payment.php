<?php

namespace EFrame\Payment;

use Illuminate\Support\Collection;
use EFrame\Payment\Exceptions\Exception;
use EFrame\Payment\Exceptions\BadMethodCallException;
use EFrame\Payment\Contracts\Gateway as GatewayContract;

/**
 * Class Payment
 * @package EFrame\Payment
 */
class Payment
{
    /**
     * @var GatewayContract
     */
    protected $gateway;

    /**
     * @var GatewayFactory
     */
    protected $factory;

    /**
     * Payment constructor.
     */
    public function __construct(GatewayFactory $factory, $gateway_name)
    {
        $this->factory = $factory;
        $this->gateway = $this->factory->create($gateway_name);
    }

    /**
     * @param $gateway_name
     *
     * @return GatewayContract
     */
    public function gateway($gateway_name)
    {
        return $this->factory->create($gateway_name);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->resolve($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    protected function resolve($name, $arguments)
    {
        throw_unless(
            method_exists($this->gateway, $name),
            BadMethodCallException::class
        );

        return call_user_func_array([$this->gateway, $name], $arguments);
    }
}