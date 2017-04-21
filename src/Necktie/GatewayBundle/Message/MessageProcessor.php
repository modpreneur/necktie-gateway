<?php

namespace Necktie\GatewayBundle\Message;

use Necktie\GatewayBundle\Event\MessageEvent;
use Necktie\GatewayBundle\Gateway\ApiGateway;
use Necktie\GatewayBundle\Gateway\RequestProcessor\BaseProcessor;
use Necktie\GatewayBundle\Logger\LoggerService;
use Necktie\GatewayBundle\Proxy\ProducerProxy;
use Trinity\Bundle\LoggerBundle\Services\ElasticLogService;

/**
 * Class MessageProcessor
 * @package Necktie\GatewayBundle\Message
 */
class MessageProcessor
{

    /** @var  ElasticLogService */
    protected $elasticLog;


    /** @var ProducerProxy */
    private $producer;


    /** @var  BaseProcessor[] */
    protected $processors = [];


    /**
     * MessageProcessor constructor.
     *
     * @param ElasticLogService $elasticLog
     * @param ApiGateway $gateway
     * @param LoggerService $logger
     * @param ProducerProxy $producer
     */
    public function __construct(ElasticLogService $elasticLog, ApiGateway $gateway, LoggerService $logger, ProducerProxy $producer)
    {
        $this->elasticLog = $elasticLog;
        $this->logger   = $logger;
        $this->gateway  = $gateway;
        $this->producer = $producer;
    }


    /**
     * @param BaseProcessor $filter
     */
    public function addProcessor(BaseProcessor $filter)
    {
        $this->processors[$filter->getName()] = $filter;
    }


    /**
     * @param MessageEvent $messageEvent
     */
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

        dump($response);

        $this->producer->publish(serialize($response), 'necktie');
    }
}