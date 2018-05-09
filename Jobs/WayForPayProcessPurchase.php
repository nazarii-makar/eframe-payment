<?php

namespace EFrame\Payment\Jobs;

use Carbon\Carbon;
use EFrame\Payment\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Business\Comment\Models\Comment;
use Business\Business\Models\Business;
use EFrame\Payment\Events\OrderPurchased;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class WayForPayProcessPurchase extends Job
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $driver = 'wayforpay';

    /**
     * WayForPayProcessPurchase constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     *  Execute the job.
     */
    public function handle()
    {
        /** @var Order $order */
        $order = Order::findOrFail($this->request->get('orderReference'));

        throw_if(
            Order::STATUS_ACTIVE === $order->status && !$order->is_regular,
            ConflictHttpException::class
        );

        $order->activate();

        event(new OrderPurchased($order));

        return $this->response();
    }

    /**
     * @return Collection
     */
    protected function response()
    {
        $fields = [
            'orderReference' => $this->request->get('orderReference'),
            'status'         => 'accept',
            'time'           => intval(Carbon::now()->format('U')),
        ];

        $signature = hash_hmac(
            'md5', implode(';', $fields), $this->searchMerchantPassword($this->request->get('merchantAccount'))
        );

        $fields['signature'] = $signature;

        return collect($fields);
    }

    /**
     * @param $merchant_account
     *
     * @return strin
     */
    protected function searchMerchantPassword($merchant_account)
    {
        if (is_null($merchant_account)) {
            return null;
        }

        /** @var Collection $gateway_config */
        $gateway_config = collect(
            collect(config('payment.gateways'))->first(function ($gateway_config) use ($merchant_account) {
                $gateway_config = collect($gateway_config);
                $gateway_options = collect($gateway_config->get('options'));

                return (
                    $this->driver === $gateway_config->get('driver') &&
                    $merchant_account === $gateway_options->get('merchantAccount')
                );
            })
        );

        $gateway_options = collect($gateway_config->get('options'));

        return $gateway_options->get('merchantPassword');
    }
}