<?php

namespace Necktie\GatewayBundle\Proxy;

/**
 * Interface ConsumerInterface
 * @package Necktie\GatewayBundle\Proxy
 */
interface ConsumerInterface
{
    /**
     * @param string $queueName
     * @param string $handleMessage
     * @param string $deliveryTag
     *
     * @return mixed
     */
    public function handleMessage(string $queueName, string $handleMessage, string $deliveryTag);
}
