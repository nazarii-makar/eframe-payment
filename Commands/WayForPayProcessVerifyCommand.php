<?php

namespace EFrame\Payment\Commands;

use EFrame\Payment\Models\{
    Order,
    Transaction
};
use EFrame\Payment\Jobs\{
    VerifyOrder,
    CreateTransaction
};
use Carbon\Carbon;
use Illuminate\Support\Collection;
use EFrame\Foundation\Bus\Dispatchable;
use EFrame\Payment\Http\Requests\WayForPayVerifyRequest;

/**
 * Class WayForPayProcessVerifyCommand
 * @package EFrame\Payment\Commands
 */
class WayForPayProcessVerifyCommand
{
    use Dispatchable;

    /**
     * @var WayForPayVerifyRequest
     */
    protected $request;

    /**
     * @var string
     */
    protected $driver = 'wayforpay';

    /**
     * @var array
     */
    protected $details_schema = [
        'auth_code'           => 'authCode',
        'card_pan'            => 'cardPan',
        'card_type'           => 'cardType',
        'fee'                 => 'fee',
        'payment_system'      => 'paymentSystem',
        'issuer_bank_country' => 'issuerBankCountry',
        'issuer_bank_name'    => 'issuerBankName',
    ];

    /**
     * WayForPayProcessVerifyCommand constructor.
     *
     * @param WayForPayVerifyRequest $request
     */
    public function __construct(WayForPayVerifyRequest $request)
    {
        $this->request = $request;
    }

    /**
     *  Execute the command.
     */
    public function handle()
    {
        /** @var Order $order */
        $order = Order::findOrFail(
            $this->request->get('orderReference')
        );

        switch ($this->request->get('transactionStatus')) {
            case 'WaitingAuthComplete':
                $transaction_status = Transaction::STATUS_SUCCESS;
                break;
            case 'Created':
            case 'InProcessing':
            case 'Pending':
                $transaction_status = Transaction::STATUS_PENDING;
                break;
            case 'Expired':
            case 'Refunded':
            case 'Declined':
            case 'RefundInProcessing':
                $transaction_status = Transaction::STATUS_FAILURE;
                break;
            case 'Voided':
                return $this->response();
            default:
                $transaction_status = Transaction::STATUS_ERROR;
        }

        if (Transaction::STATUS_SUCCESS === $transaction_status) {
            $order = VerifyOrder::dispatchNow($order, [
                'rec_token' => $this->request->get('recToken'),
            ]);
        }

        CreateTransaction::dispatchNow($order, [
            'gateway'         => $this->driver,
            'amount'          => $order->amount,
            'currency'        => $order->currency,
            'details'         => $this->extractDetails(),
            'rec_token'       => $order->rec_token,
            'status'          => $transaction_status,
            'created_at'      => Carbon::createFromTimestamp($this->request->get('createdDate')),
            'updated_at'      => Carbon::createFromTimestamp($this->request->get('createdDate')),
            'processing_date' => Carbon::createFromTimestamp($this->request->get('processingDate')),
        ]);

        return $this->response();
    }


    /**
     * @return Collection
     */
    protected function extractDetails()
    {
        return collect(
            array_keys($this->details_schema)
        )->combine(array_values(
            $this->request->only(
                array_values($this->details_schema)
            )
        ));
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
                $gateway_config  = collect($gateway_config);
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