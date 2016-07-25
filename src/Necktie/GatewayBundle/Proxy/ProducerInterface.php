<?php

namespace Necktie\GatewayBundle\Proxy;

/**
 * Interface ProducerInterface
 * @package Necktie\GatewayBundle\Proxy
 */
interface ProducerInterface
{

    /**
     * @param string $data
     * @param string $queueName
     * @return void
     */
    public function publish(string $data, string $queueName = '');

}