<?php

namespace Necktie\GatewayBundle\Message;

use Necktie\GatewayBundle\Event\MessageEvent;
use Necktie\GatewayBundle\Logger\Entity\Message;
use Trinity\Bundle\LoggerBundle\Services\ElasticLogService;

/**
 * Class MessagesLogger
 * @package Necktie\GatewayBundle\Message
 */
class MessagesLogger
{

    /** @var  ElasticLogService */
    protected $elasticLog;


    /**
     * MessagesListener constructor.
     * @param ElasticLogService $elasticLog
     */
    public function __construct(ElasticLogService $elasticLog)
    {
        $this->elasticLog = $elasticLog;
    }


    /**
     * @param MessageEvent $messageEvent
     *
     * @throws \Exception
     */
    public function saveMessage(MessageEvent $messageEvent): void
    {
        try {
            $elasticLog = $this->elasticLog;
            $message = new Message();
            $message->setMessage(\json_encode($messageEvent->getContent()));
            $message->setCreatedAt((new \DateTime())->getTimestamp());
            $message->setTag($messageEvent->getDeliveryTag());
            $elasticLog->writeInto('Message', $message);
            echo 'Logged message from '.$messageEvent->getQueue().PHP_EOL;

        } catch (\Exception $ex) {
            echo 'Error' . $ex->getMessage();
            throw $ex;
        }
    }
}
