<?php

namespace Necktie\GatewayBundle\Message;

use Doctrine\ORM\EntityManager;
use Necktie\GatewayBundle\Event\MessageEvent;
use Necktie\GatewayBundle\Gateway\ApiGateway;
use Necktie\GatewayBundle\Gateway\RequestProcessor\BaseProcessor;
use Necktie\GatewayBundle\Logger\Logger;
use Necktie\GatewayBundle\Proxy\ProducerProxy;

/**
 * Class MessageProcessor
 * @package Necktie\GatewayBundle\Message
 */
class MessageProcessor
{

    /** @var  EntityManager */
    protected $em;


    /**
     * @var ProducerProxy
     */
    private $producer;


    /** @var  BaseProcessor[] */
    protected $processors = [];


    /**
     * MessageProcessor constructor.
     * @param EntityManager $em
     * @param ApiGateway $gateway
     * @param Logger $logger
     * @param ProducerProxy $producer
     */
    public function __construct(EntityManager $em, ApiGateway $gateway, Logger $logger, ProducerProxy $producer)
    {
        $this->em = $em;
        $this->logger   = $logger;
        $this->gateway  = $gateway;
        $this->producer = $producer;
    }


    public function addProcessor(BaseProcessor $filter)
    {
        $this->processors[$filter->getName()] = $filter;
    }


    public function process(MessageEvent $messageEvent)
    {
        $this->execute($messageEvent->getContent());
    }


    /**
     * @param array $message
     */
    protected function execute(array $message)
    {
        $processor = null;

        if(array_key_exists('processorName', $message)){
            /** @var BaseProcessor $processor */
            $processor = $this->processors[$message['processorName']];
        }else{
            $processor = $this->processors['HTTPProcessor'];
        }

        $response = $processor->process($message);
        $this->producer->publish(serialize($response), 'necktie');
    }

}