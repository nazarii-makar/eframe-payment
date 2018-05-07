<?php

namespace EFrame\Payment\Gateways;

use EFrame\Payment\Contracts\Payment;
use EFrame\Payment\Exceptions\InvalidArgumentException;
use EFrame\Payment\OrderProduct;

/**
 * Class WayForPay
 * @package EFrame\Payment\Gateways
 */
class WayForPay extends Gateway
{
    const PURCHASE_URL     = 'https://secure.wayforpay.com/pay';
    const API_URL          = 'https://api.wayforpay.com/api';
    const FIELDS_DELIMITER = ';';
    const API_VERSION      = 1;
    const DEFAULT_CHARSET  = 'utf8';

    const MODE_PURCHASE         = 'PURCHASE';
    const MODE_SETTLE           = 'SETTLE';
    const MODE_CHARGE           = 'CHARGE';
    const MODE_REFUND           = 'REFUND';
    const MODE_CHECK_STATUS     = 'CHECK_STATUS';
    const MODE_P2P_CREDIT       = 'P2P_CREDIT';
    const MODE_CREATE_INVOICE   = 'CREATE_INVOICE';
    const MODE_P2_PHONE         = 'P2_PHONE';
    const MODE_TRANSACTION_LIST = 'TRANSACTION_LIST';

    private $merchant_account;
    private $merchant_password;
    private $action;
    private $params;
    private $charset;

    /**
     * Bootstraping WayForPay
     */
    public function boot()
    {
        $this->charset           = $this->options->get('charset', self::DEFAULT_CHARSET);
        $this->merchant_account  = $this->options->get('merchantAccount');
        $this->merchant_password = $this->options->get('merchantPassword');
    }

    /**
     * MODE_SETTLE
     *
     * @param $fields
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function settle($fields)
    {
        $this->prepare(self::MODE_SETTLE, $fields);

        return $this->query();
    }

    /**
     * MODE_CHARGE
     *
     * @param $fields
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function charge($fields)
    {
        $this->prepare(self::MODE_CHARGE, $fields);

        return $this->query();
    }

    /**
     * MODE_REFUND
     *
     * @param $fields
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function refund($fields)
    {
        $this->prepare(self::MODE_REFUND, $fields);

        return $this->query();
    }

    /**
     * MODE_PURCHASE
     *
     * @param $fields
     *
     * @return mixed
     */
    public function purchase($fields)
    {
        $this->prepare(self::MODE_PURCHASE, $fields);

        return $this->params;
    }

    /**
     * MODE_CHECK_STATUS
     *
     * @param $fields
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function checkStatus($fields)
    {
        $this->prepare(self::MODE_CHECK_STATUS, $fields);

        return $this->query();
    }

    /**
     * MODE_P2P_CREDIT
     *
     * @param $fields
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function account2card($fields)
    {
        $this->prepare(self::MODE_P2P_CREDIT, $fields);

        return $this->query();
    }

    /**
     * MODE_P2P_CREDIT
     *
     * @param $fields
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createInvoice($fields)
    {
        $this->prepare(self::MODE_CREATE_INVOICE, $fields);

        return $this->query();
    }

    /**
     * MODE_P2P_CREDIT
     *
     * @param $fields
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function account2phone($fields)
    {
        $this->prepare(self::MODE_P2_PHONE, $fields);

        return $this->query();
    }

    /**
     * TRANSACTION_LIST
     *
     * @param $fields
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function transactionList($fields)
    {
        $this->prepare(self::MODE_TRANSACTION_LIST, $fields);

        return $this->query();
    }

    /**
     * Return signature hash
     *
     * @param $action
     * @param $fields
     *
     * @return mixed
     */
    public function createSignature($action, $fields)
    {
        $this->prepare($action, $fields);

        return $this->buildSignature();
    }

    /**
     * @param       $action
     * @param array $params
     *
     * @throws InvalidArgumentException
     */
    private function prepare($action, array $params)
    {
        $this->action = $action;

        throw_if(
            empty($params),
            new InvalidArgumentException('Arguments must be not empty')
        );

        $this->params                      = $params;
        $this->params['transactionType']   = $this->action;
        $this->params['merchantSignature'] = $this->buildSignature();

        if (self::MODE_PURCHASE !== $this->action) {
            $this->params['apiVersion'] = self::API_VERSION;
        }

        $this->checkFields();

    }

    /**
     * Check required fields
     *
     * @param $fields
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    private function checkFields()
    {
        $required = $this->getRequiredFields();
        $error    = [];

        foreach ($required as $item) {
            if (empty($this->params[$item] ?? $this->options->get($item))) {
                $error[] = $item;
            } else {
                $this->params[$item] = $this->params[$item] ?? $this->options->get($item);
            }
        }

        throw_unless(
            empty($error),
            new InvalidArgumentException('Missed required field(s): ' . implode(', ', $error) . '.')
        );

        return true;
    }

    /**
     * Generate signature hash
     *
     * @param $fields
     *
     * @return string
     * @throws InvalidArgumentException
     */
    private function buildSignature()
    {
        $signFields = $this->getFieldsNameForSignature();
        $data       = [];
        $error      = [];

        foreach ($signFields as $item) {
            if (is_null($this->params[$item] ?? $this->options->get($item))) {
                $error[] = $item;
                continue;
            } else {
                $this->params[$item] = $this->params[$item] ?? $this->options->get($item);
            }

            $value = $this->params[$item];

            $data[] = is_array($value) ? implode(self::FIELDS_DELIMITER, $value) : (string)$value;
        }

        if (self::DEFAULT_CHARSET != $this->charset) {
            foreach ($data as $key => $value) {
                $data[$key] = iconv($this->charset, self::DEFAULT_CHARSET, $data[$key]);
            }
        }

        throw_unless(
            empty($error),
            new InvalidArgumentException('Missed signature field(s): ' . implode(', ', $error) . '.')
        );

        return hash_hmac('md5', implode(self::FIELDS_DELIMITER, $data), $this->merchant_password);
    }

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function query()
    {
        $fields = json_encode($this->params);

        return $this->client->request(
            'POST',
            self::API_URL,
            [
                'headers' => [
                    'Content-Type' => 'application/json;charset=utf-8',
                ],

                'body' => $fields,
            ]
        );
    }

    /**
     * Signature fields
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function getFieldsNameForSignature()
    {
        $purchaseFieldsAlias = [
            'merchantAccount',
            'merchantDomainName',
            'orderReference',
            'orderDate',
            'amount',
            'currency',
            'productName',
            'productCount',
            'productPrice',
        ];

        switch ($this->action) {
            case 'PURCHASE':
                return $purchaseFieldsAlias;
                break;
            case 'REFUND':
                return [
                    'merchantAccount',
                    'orderReference',
                    'amount',
                    'currency',
                ];
            case 'CHECK_STATUS':
                return [
                    'merchantAccount',
                    'orderReference',
                ];
                break;
            case 'CHARGE':
                return $purchaseFieldsAlias;
                break;
            case 'SETTLE':
                return [
                    'merchantAccount',
                    'orderReference',
                    'amount',
                    'currency',
                ];
                break;
            case self::MODE_P2P_CREDIT:
                return [
                    'merchantAccount',
                    'orderReference',
                    'amount',
                    'currency',
                    'cardBeneficiary',
                    'rec2Token',
                ];
                break;
            case self::MODE_CREATE_INVOICE:
                return $purchaseFieldsAlias;
                break;
            case self::MODE_P2_PHONE:
                return [
                    'merchantAccount',
                    'orderReference',
                    'amount',
                    'currency',
                    'phone',
                ];
                break;
            case self::MODE_TRANSACTION_LIST:
                return [
                    'merchantAccount',
                    'dateBegin',
                    'dateEnd',
                ];
                break;
            default:
                throw new InvalidArgumentException('Unknown transaction type: ' . $this->action);
        }
    }

    /**
     * Required fields
     *
     * @return array
     */
    private function getRequiredFields()
    {
        switch ($this->action) {
            case 'PURCHASE':
                return [
                    'merchantAccount',
                    'merchantDomainName',
                    'merchantTransactionSecureType',
                    'orderReference',
                    'orderDate',
                    'amount',
                    'currency',
                    'productName',
                    'productCount',
                    'productPrice',
                ];
            case 'SETTLE':
                return [
                    'transactionType',
                    'merchantAccount',
                    'orderReference',
                    'amount',
                    'currency',
                    'apiVersion',
                ];
            case 'CHARGE':
                $required = [
                    'transactionType',
                    'merchantAccount',
                    'merchantDomainName',
                    'orderReference',
                    'apiVersion',
                    'orderDate',
                    'amount',
                    'currency',
                    'productName',
                    'productCount',
                    'productPrice',
                    'clientFirstName',
                    'clientLastName',
                    'clientEmail',
                    'clientPhone',
                    'clientCountry',
                    'clientIpAddress',
                ];

                $additional = !empty($this->params['recToken']) ?
                    ['recToken'] :
                    ['card', 'expMonth', 'expYear', 'cardCvv', 'cardHolder'];

                return array_merge($required, $additional);
            case 'REFUND':
                return [
                    'transactionType',
                    'merchantAccount',
                    'orderReference',
                    'amount',
                    'currency',
                    'comment',
                    'apiVersion',
                ];
            case 'CHECK_STATUS':
                return [
                    'transactionType',
                    'merchantAccount',
                    'orderReference',
                    'apiVersion',
                ];
            case self::MODE_P2P_CREDIT:
                return [
                    'transactionType',
                    'merchantAccount',
                    'orderReference',
                    'amount',
                    'currency',
                    'cardBeneficiary',
                    'merchantSignature',
                ];
            case self::MODE_CREATE_INVOICE:
                return [
                    'transactionType',
                    'merchantAccount',
                    'merchantDomainName',
                    'orderReference',
                    'amount',
                    'currency',
                    'productName',
                    'productCount',
                    'productPrice',
                ];
            case self::MODE_P2_PHONE:
                return [
                    'merchantAccount',
                    'orderReference',
                    'orderDate',
                    'currency',
                    'amount',
                    'phone',
                ];
                break;
            case self::MODE_TRANSACTION_LIST:
                return [
                    'merchantAccount',
                    'dateBegin',
                    'dateEnd',
                ];
                break;
            default:
                throw new InvalidArgumentException('Unknown transaction type');
        }
    }
}