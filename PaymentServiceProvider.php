<?php

namespace EFrame\Payment;

use EFrame\Payment\Services\Payment;
use EFrame\Payment\Services\WayForPay;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register the application services
     */
    public function register()
    {
        $this->registerServices();
        $this->registerPayment();
    }

    /**
     * Register Payment service
     */
    protected function registerPayment()
    {
        $this->app->singleton('payment', Payment::class);
    }

    /**
     * Register payment services
     */
    protected function registerServices()
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
     * Bootstrap the application services
     */
    public function boot()
    {
        $config = realpath(__DIR__ . '/config/payment.php');
        $this->mergeConfigFrom($config, 'payment');
    }
}