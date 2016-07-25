<?php

namespace Necktie\GatewayBundle\Gateway;

use GuzzleHttp\Client;

/**
 * Class ClientFactory
 * @package Necktie\GatewayBundle\Gateway
 */
class ClientFactory implements ClientFactoryInterface
{

    /**
     * @return Client
     */
    public function createClient()
    {
        return new Client();

    }

}