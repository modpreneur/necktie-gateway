<?php

namespace Necktie\Bundle\GatewayBundle\Proxy;

/**
 * Interface ConsumerInterface
 * @package Necktie\Bundle\GatewayBundle\Proxy
 */
interface ConsumerInterface
{
    public function handleMessage(string $queueName, string $handleMessage, string $deliveryTag);

}