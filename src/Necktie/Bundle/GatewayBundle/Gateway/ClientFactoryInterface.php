<?php


namespace Necktie\Bundle\GatewayBundle\Gateway;

use GuzzleHttp\Client;


/**
 * Interface ClientFactoryInterface
 * @package Necktie\Bundle\GatewayBundle\Gateway
 */
interface ClientFactoryInterface
{

    /**
     * @return Client
     */
    function createClient();


}