<?php

namespace Necktie\GatewayBundle\Consumer;

use Bunny\Client;
use Bunny\Channel;
use Bunny\Message as BunnyMessage;
use Necktie\GatewayBundle\Proxy\ConsumerProxy;
use Trinity\Bundle\BunnyBundle\Annotation\Consumer;

/**
 * @Consumer(
 *     queue="queue_gateway",
 *     maxMessages=30,
 *     maxSeconds=60.0,
 *     prefetchCount=1
 * )
 */
class GatewayConsumer
{

    const QUEUE_NAME = 'queue_gateway';

    /**
     * @var ConsumerProxy
     */
    protected $consumerProxy;

    /**
     * @var int
     */
    protected $errors = 0;

    /**
     * @var int
     */
    protected $maxRepeat = 20;


    /**
     * GatewayConsumer constructor.
     * @param ConsumerProxy $consumerProxy
     */
    public function __construct(ConsumerProxy $consumerProxy)
    {
        $this->consumerProxy = $consumerProxy;
    }


    /**
     * @param $message
     * @param BunnyMessage $bunnyMessage
     * @param Channel $channel
     * @param Client $client
     */
    public function handleMessage($message, BunnyMessage $bunnyMessage, Channel $channel, Client $client)
    {
        try{
            $this->errors = 0;

            $channel->ack($bunnyMessage);
            $this->consumerProxy->handleMessage(self::QUEUE_NAME, $message, $bunnyMessage->deliveryTag);
        }catch (\Exception $ex){
            $this->errors++;

            echo $ex->getMessage();

            if ($this->errors > $this->maxRepeat) {
                $channel->reject($bunnyMessage);
                return;
            }

            $channel->nack($bunnyMessage);
        }
    }
}