<?php

namespace Necktie\Bundle\GatewayBundle\Gateway;

use GuzzleHttp\Client;

/**
 * Class ClientFactory
 * @package Necktie\Bundle\GatewayBundle\Gateway
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