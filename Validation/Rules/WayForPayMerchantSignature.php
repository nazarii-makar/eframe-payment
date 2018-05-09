<?php

namespace EFrame\Payment\Validation\Rules;

use Illuminate\Support\Collection;
use Illuminate\Contracts\Validation\Rule;

class WayForPayMerchantSignature implements Rule
{
    /**
     * @var array|Collection
     */
    protected $attributes = [];

    /**
     * @var string
     */
    protected $merchant_password;

    /**
     * @var string
     */
    protected $driver = 'wayforpay';

    /**
     * @var array
     */
    protected $schema = [
        'merchantAccount',
        'orderReference',
        'amount',
        'currency',
        'authCode',
        'cardPan',
        'transactionStatus',
        'reasonCode',
    ];

    /**
     * WayForPayMerchantSignature constructor.
     *
     * @param array $fields
     */
    public function __construct($fields = [])
    {
        $fields = collect($fields);

        $this->merchant_password = $this->searchMerchantPassword($fields->get('merchantAccount'));

        foreach ($this->schema as $key) {
            $this->attributes[$key] = $fields->get($key);
        }
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

    /**
     * Determaxe if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value === hash_hmac('md5', implode(';', $this->attributes), $this->merchant_password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute is invalid.';
    }
}