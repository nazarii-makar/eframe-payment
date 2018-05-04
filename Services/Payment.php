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
     * Payment constructor.
     */
    public function __construct()
    {
        $this->service = $this->buildService($this->config('default'));
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
        throw_if(
            is_null($this->config("services.{$service}")),
            new Exception("Payment service as {$service} does not specified.")
        );

        $service_config = collect($this->config("services.{$service}"));

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

    /**
     * @param null $key
     * @param null $default
     *
     * @return mixed
     */
    protected function config($key = null, $default = null)
    {
        if (is_null($key)) {
            return config('payment');
        }

        return config("payment.{$key}", $default);
    }
}