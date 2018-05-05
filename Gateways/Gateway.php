<?php

namespace EFrame\Payment\Gateways;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Illuminate\Support\Collection;
use EFrame\Payment\Contracts\Gateway as GatewayContract;

/**
 * Class Service
 * @package EFrame\Payment\Gateways
 */
abstract class Gateway implements GatewayContract
{
    /**
     * @var Collection
     */
    protected $options;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * Gateway constructor.
     *
     * @param ClientInterface|null $client
     */
    public function __construct(ClientInterface $client = null)
    {
        $this->client = (is_null($client)) ? $this->getDefaultHttpClient() : $client;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options = [])
    {
        $this->options = collect($options);

        return $this;
    }

    /**
     * @return ClientInterface
     */
    protected function getDefaultHttpClient()
    {
        return new Client;
    }

    /**
     * Bootstraping service
     */
    public function boot()
    {
        //
    }
}