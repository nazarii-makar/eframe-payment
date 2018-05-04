<?php

namespace EFrame\Payment;

use EFrame\Payment\Services\Payment;
use EFrame\Payment\Services\WayForPay;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $aliases = [
        'wayforpay' => WayForPay::class,
    ];

    /**
     * Register the application services
     */
    public function register()
    {
        $this->registerAliases();
        $this->registerPayment();
    }

    /**
     * Register Payment service
     */
    protected function registerPayment()
    {
        $this->app->singleton('payment', function () {
            return new Payment();
        });
    }

    /**
     * Register payment services
     */
    protected function registerAliases()
    {
        foreach ($this->aliases as $alias => $service) {
            $this->app->alias($alias, $service);
        }
    }

    /**
     * Bootstrap the application services
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/config/payment.php');
        $this->mergeConfigFrom($config, 'payment');
    }
}