<?php


namespace Necktie\GatewayBundle\Gateway;

use GuzzleHttp\Client;


/**
 * Interface ClientFactoryInterface
 * @package Necktie\GatewayBundle\Gateway
 */
interface ClientFactoryInterface
{

    /**
     * @return Client
     */
    public function createClient();


}