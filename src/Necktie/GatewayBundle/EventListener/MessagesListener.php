<?php

namespace Necktie\GatewayBundle\EventListener;

use Necktie\GatewayBundle\Event\MessageEvent;
use Necktie\GatewayBundle\Message\MessageProcessor;
use Necktie\GatewayBundle\Message\MessagesLogger;

/**
 * Class MessagesListener
 * @package Necktie\GatewayBundle\EventListener
 */
class MessagesListener
{
    /**
     * @var MessagesLogger
     */
    private $logger;

    /**
     * @var MessageProcessor
     */
    private $messageProcessor;


    /**
     * MessagesListener constructor.
     * @param MessagesLogger $logger
     * @param MessageProcessor $messageProcessor
     */
    public function __construct(MessagesLogger $logger, MessageProcessor $messageProcessor)
    {
        $this->logger           = $logger;
        $this->messageProcessor = $messageProcessor;
    }


    /**
     * @param MessageEvent $consumedEvent
     * @throws \Exception
     */
    public function onMessageConsume(MessageEvent $consumedEvent)
    {
        echo 'Consume message from ' . $consumedEvent->getQueue() . PHP_EOL;

        $this->logger->saveMessage($consumedEvent);
        $this->messageProcessor->process($consumedEvent);

    }
}