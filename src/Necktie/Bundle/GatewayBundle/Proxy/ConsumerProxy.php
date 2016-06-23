<?php

namespace Necktie\Bundle\GatewayBundle\Proxy;

use Necktie\Bundle\GatewayBundle\Event\MessageEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class ConsumerProxy
 * @package Necktie\Bundle\GatewayBundle\Proxy
 */
class ConsumerProxy implements ConsumerInterface
{

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;


    /**
     * GatewayConsumer constructor.
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }


    /**
     * @param string $queueName
     * @param string $message
     * @param string $deliveryTag
     */
    public function handleMessage(string $queueName, string $message, string $deliveryTag)
    {
        $this->eventDispatcher->dispatch(MessageEvent::EVENT, new MessageEvent($message, $queueName, $deliveryTag));
    }
}