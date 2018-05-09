<?php

namespace EFrame\Payment;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use EFrame\Payment\Contracts\Couponable;
use EFrame\Payment\Contracts\Productable;
use EFrame\Payment\Exceptions\LogicException;
use Illuminate\Database\Eloquent\Relations\Relation;
use EFrame\Payment\Exceptions\InvalidArgumentException;

class ShoppingCart
{
    /**
     * @var Collection
     */
    protected $products;

    /**
     * @var Collection
     */
    protected $coupons;

    /**
     * @var Model
     */
    protected $delivery;

    /**
     * @var Model
     */
    protected $client;

    /**
     * ShoppingCart constructor.
     */
    public function __construct()
    {
        $this->products = collect();
        $this->coupons  = collect();
    }

    /**
     * @param Productable $productable
     *
     * @return $this
     */
    public function addProduct(Productable $productable, $count = 1)
    {
        $this->products->push([
            'product' => $productable,
            'count'   => $count,
        ]);

        return $this;
    }

    /**
     * @param Couponable $couponable
     *
     * @return $this
     */
    public function addCoupon(Couponable $couponable)
    {
        $this->coupons->push($couponable);

        return $this;
    }

    /**
     * @param Model $delivery
     *
     * @return $this
     */
    public function to(Model $delivery)
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * @param Model $client
     *
     * @return $this
     */
    public function from(Model $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @param null|string $currency
     *
     * @return Order
     */
    public function buildOrder($currency = null)
    {
        throw_if(
            is_null($this->delivery),
            new InvalidArgumentException('Delivery is required.')
        );

        $order_products = $this->generateOrderProducts($currency);

        $amount = $this->calculate($order_products);

        $order = new Order;

        $order->fill([
            'amount'        => $amount,
            'currency'      => $currency,
            'is_regular'    => false,
            'status'        => Order::STATUS_NOT_ACTIVE,
            'delivery_type' => array_search(get_class($this->delivery), Relation::morphMap(), true),
            'delivery_id'   => $this->delivery->getKey(),
        ]);

        if (!is_null($this->client)) {
            $order->fill([
                'client_type' => array_search(get_class($this->client), Relation::morphMap(), true),
                'client_id'   => $this->client->getKey(),
            ]);
        }

        $order->save();

        $order->products()->saveMany($order_products);

        $this->redemption($order);

        $order->save();

        return $order;
    }

    /**
     * @param Order $order
     */
    protected function redemption(Order $order)
    {
        /** @var Couponable $coupon */
        foreach ($this->coupons as $coupon) {
            $coupon->redemption($order);
        }
    }

    /**
     * @param array $order_products
     *
     * @return float
     */
    protected function calculate($order_products = [])
    {
        $amount = 0.00;

        $order_products = collect($order_products);

        /** @var OrderProduct $order_product */
        foreach ($order_products as $order_product) {
            $amount += floatval($order_product->price);
        }

        return $amount;
    }

    /**
     * @param null|string $currency
     *
     * @return Collection
     */
    protected function generateOrderProducts(&$currency = null)
    {
        $product_currencies = $this->products->pluck('product')->map(function (Productable $productable) {
            return $productable->getCurrency();
        })->unique();

        throw_if(
            is_null($currency) && $product_currencies->count() > 1,
            new LogicException('Multiple product currencies found without default currency.')
        );

        $currency = (is_null($currency)) ? $product_currencies->first() : $currency;

        $order_products = collect();

        /** @var array $product */
        foreach ($this->products as $product) {
            /** @var Productable|Model $productable */
            $productable = $product['product'];

            /** @var int $count */
            $count = $product['count'];

            $price = $productable->getPrice();

            if ($currency !== $productable->getCurrency()) {
                $price = currency($price, $productable->getPrice(), $currency);
            }

            $order_product = new OrderProduct;

            $order_product->fill([
                'name'          => $productable->getName(),
                'price'         => $price,
                'count'         => $count,
                'resource_id'   => $productable->getKey(),
                'resource_type' => array_search(get_class($productable), Relation::morphMap(), true),
            ]);

            $order_products->push($order_product);
        }

        return $order_products;
    }
}