<?php

namespace EFrame\Payment\Converters;

use EFrame\Payment\Order;
use EFrame\Payment\OrderProduct;
use Illuminate\Support\Collection;
use EFrame\Payment\Contracts\Converter;

class WayForPayConverter extends Collection implements Converter
{
    /**
     * @param Order $order
     *
     * @return array
     */
    public static function createFromOrder(Order $order)
    {
        $products = $order->products;

        $data = [
            'orderReference' => $order->id,
            'orderDate'      => $order->created_at->format('U'),
            'amount'         => $order->amount,
            'currency'       => $order->currency,
            'productName'    => $products->pluck('name'),
            'productPrice'   => $products->pluck('price'),
            'productCount'   => $products->pluck('count'),
        ];

        return new static($data);
    }
}