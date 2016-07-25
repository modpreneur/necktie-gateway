<?php

namespace Necktie\GatewayBundle\Proxy;

/**
 * Interface ConsumerInterface
 * @package Necktie\GatewayBundle\Proxy
 */
interface ConsumerInterface
{
    public function handleMessage(string $queueName, string $handleMessage, string $deliveryTag);

}