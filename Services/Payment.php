<?php

namespace EFrame\Payment\Services;

use Illuminate\Support\Collection;
use EFrame\Payment\Exceptions\Exception;
use EFrame\Payment\Contracts\Payment as PaymentContract;

/**
 * Class Payment
 * @package EFrame\Payment\Services
 */
class Payment
{
    /**
     * @var PaymentContract
     */
    protected $service;

    /**
     * @var Collection
     */
    protected $config;

    /**
     * Payment constructor.
     */
    public function __construct()
    {
        $this->config = collect(config('payment'));

        $this->service = $this->buildService($service);
    }

    /**
     * @param PaymentContract $service
     */
    public function on($service)
    {
        return $this->buildService($service);
    }

    /**
     * @param $service
     *
     * @return PaymentContract
     */
    protected function buildService($service)
    {
        throw_unless(
            $this->config->has("services.{$service}"),
            new Exception("Payment service as {$service} does not specified.")
        );

        $service_config = collect($this->config->get("services.{$service}"));

        /** @var PaymentContract $driver */
        $driver = app($service_config->get('driver'));

        $driver->setOptions(
            $service_config
        )->boot();

        return $driver;
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
        return call_user_func_array([$this->service, $name], $arguments);
    }
}