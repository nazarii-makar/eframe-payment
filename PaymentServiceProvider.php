<?php

namespace EFrame\Payment;

use EFrame\Payment\Payment;
use EFrame\Payment\Models\Order;
use EFrame\Payment\Gateways\WayForPay;
use Illuminate\Support\ServiceProvider;
use EFrame\Payment\Observers\OrderObserver;
use EFrame\Payment\Console\OrderTableCommand;
use EFrame\Payment\Console\TransactionTableCommand;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $observers = [
        Order::class => OrderObserver::class,
    ];

    /**
     * @var array
     */
    protected $commands = [
        OrderTableCommand::class,
        TransactionTableCommand::class,
    ];

    /**
     * Register the application services
     */
    public function register()
    {
        $this->registerGateways();
        $this->registerPayment();
        $this->commands($this->commands);
    }

    /**
     * Register Payment service
     */
    protected function registerPayment()
    {
        $this->app->singleton('payment', function () {
            return new Payment(
                new GatewayFactory($this->config('gateways')),
                $this->config('default')
            );
        });
    }

    /**
     * Register payment services
     */
    protected function registerGateways()
    {
        $this->registerWayForPay();
    }

    /**
     * Register WayForPay service
     */
    protected function registerWayForPay()
    {
        $this->app->bind('wayforpay', WayForPay::class);
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

    /**
     * Register observers
     *
     * @return void
     */
    protected function registerObservers()
    {
        /**
         * @var Model  $model
         * @var string $observer
         */
        foreach ($this->observers as $model => $observer) {
            $model::observe($observer);
        }
    }

    /**
     * Bootstrap the application services
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/config/payment.php');
        $this->mergeConfigFrom($config, 'payment');

        $this->registerObservers();
    }
}