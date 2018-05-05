<?php

namespace EFrame\Payment\Gateways;

use EFrame\Payment\Contracts\Payment;
use EFrame\Payment\Exceptions\InvalidArgumentException;

/**
 * Class WayForPay
 * @package EFrame\Payment\Gateways
 */
class WayForPay extends Gateway
{
    const PURCHASE_URL     = 'https://secure.wayforpay.com/pay';
    const API_URL          = 'https://api.wayforpay.com/api';
    const WIDGET_URL       = 'https://secure.wayforpay.com/server/pay-widget.js';
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

    private $_merchant_account;
    private $_merchant_password;
    private $_action;
    private $_params;
    private $_charset;

    /**
     * Init
     *
     * @param        $merchant_account
     * @param        $merchant_password
     * @param string $charset
     *
     * @throws InvalidArgumentException
     */
    public function __construct($charset = self::DEFAULT_CHARSET)
    {
        $this->_charset = $charset;
    }

    /**
     * Bootstraping WayForPay
     */
    public function boot()
    {
        $this->_merchant_account  = $this->options->get('account');
        $this->_merchant_password = $this->options->get('password');
    }

    /**
     * MODE_SETTLE
     *
     * @param $fields
     *
     * @return mixed
     */
    public function settle($fields)
    {
        $this->_prepare(self::MODE_SETTLE, $fields);

        return $this->_query();
    }

    /**
     * MODE_CHARGE
     *
     * @param $fields
     *
     * @return mixed
     */
    public function charge($fields)
    {
        $this->_prepare(self::MODE_CHARGE, $fields);

        return $this->_query();
    }

    /**
     * MODE_REFUND
     *
     * @param $fields
     *
     * @return mixed
     */
    public function refund($fields)
    {
        $this->_prepare(self::MODE_REFUND, $fields);

        return $this->_query();
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
        $this->_prepare(self::MODE_PURCHASE, $fields);

        return $this->_params;
    }

    /**
     * MODE_CHECK_STATUS
     *
     * @param $fields
     *
     * @return mixed
     */
    public function checkStatus($fields)
    {
        $this->_prepare(self::MODE_CHECK_STATUS, $fields);

        return $this->_query();
    }

    /**
     * MODE_P2P_CREDIT
     *
     * @param $fields
     *
     * @return mixed
     */
    public function account2card($fields)
    {
        $this->_prepare(self::MODE_P2P_CREDIT, $fields);

        return $this->_query();
    }

    /**
     * MODE_P2P_CREDIT
     *
     * @param $fields
     *
     * @return mixed
     */
    public function createInvoice($fields)
    {
        $this->_prepare(self::MODE_CREATE_INVOICE, $fields);

        return $this->_query();
    }

    /**
     * MODE_P2P_CREDIT
     *
     * @param $fields
     *
     * @return mixed
     */
    public function account2phone($fields)
    {
        $this->_prepare(self::MODE_P2_PHONE, $fields);

        return $this->_query();
    }

    /**
     * TRANSACTION_LIST
     *
     * @param $fields
     *
     * @return mixed
     */
    public function transactionList($fields)
    {
        $this->_prepare(self::MODE_TRANSACTION_LIST, $fields);

        return $this->_query();
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
        $this->_prepare($action, $fields);

        return $this->_buildSignature();
    }

    /**
     * @param       $action
     * @param array $params
     *
     * @throws InvalidArgumentException
     */
    private function _prepare($action, array $params)
    {
        $this->_action = $action;

        throw_if(
            empty($params),
            new InvalidArgumentException('Arguments must be not empty')
        );

        $this->_params                      = $params;
        $this->_params['transactionType']   = $this->_action;
        $this->_params['merchantAccount']   = $this->_merchant_account;
        $this->_params['merchantSignature'] = $this->_buildSignature();

        if (self::MODE_PURCHASE !== $this->_action) {
            $this->_params['apiVersion'] = self::API_VERSION;
        }

        $this->_checkFields();

    }

    /**
     * Check required fields
     *
     * @param $fields
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    private function _checkFields()
    {
        $required = $this->_getRequiredFields();
        $error    = [];

        foreach ($required as $item) {
            if (array_key_exists($item, $this->_params)) {
                if (empty($this->_params[$item])) {
                    $error[] = $item;
                }
            } else {
                $error[] = $item;
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
    private function _buildSignature()
    {
        $signFields = $this->_getFieldsNameForSignature();
        $data       = [];
        $error      = [];

        foreach ($signFields as $item) {
            if (array_key_exists($item, $this->_params)) {
                $value = $this->_params[$item];
                if (is_array($value)) {
                    $data[] = implode(self::FIELDS_DELIMITER, $value);
                } else {
                    $data[] = (string)$value;
                }
            } else {
                $error[] = $item;
            }
        }

        if ($this->_charset != self::DEFAULT_CHARSET) {
            foreach ($data as $key => $value) {
                $data[$key] = iconv($this->_charset, self::DEFAULT_CHARSET, $data[$key]);
            }
        }

        throw_unless(
            empty($error),
            new InvalidArgumentException('Missed signature field(s): ' . implode(', ', $error) . '.')
        );

        return hash_hmac('md5', implode(self::FIELDS_DELIMITER, $data), $this->_merchant_password);
    }

    /**
     * Request method
     * @return mixed
     */
    private function _query()
    {
        $fields = json_encode($this->_params);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::API_URL);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json;charset=utf-8']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }


    /**
     * Signature fields
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function _getFieldsNameForSignature()
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

        switch ($this->_action) {
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
                throw new InvalidArgumentException('Unknown transaction type: ' . $this->_action);
        }
    }

    /**
     * Required fields
     *
     * @return array
     */
    private function _getRequiredFields()
    {
        switch ($this->_action) {
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

                $additional = !empty($this->_params['recToken']) ?
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