<?php


namespace Necktie\Bundle\GatewayBundle\EventListener;


use Necktie\Bundle\GatewayBundle\Event\MessageEvent;
use Necktie\Bundle\GatewayBundle\Message\MessageProcessor;
use Necktie\Bundle\GatewayBundle\Message\MessagesLogger;


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

        $this->logger = $logger;
        $this->messageProcessor = $messageProcessor;
    }


    /**
     * @param MessageEvent $consumedEvent
     * @throws \Exception
     */
    public function onMessageConsume(MessageEvent $consumedEvent)
    {
        echo 'Consume message from '.$consumedEvent->getQueue().PHP_EOL;

        $this->logger->saveMessage($consumedEvent);
        $this->messageProcessor->process($consumedEvent);

    }

}