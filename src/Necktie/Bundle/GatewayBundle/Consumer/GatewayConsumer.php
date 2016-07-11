<?php

namespace Necktie\Bundle\GatewayBundle\Consumer;

use Bunny\Client;
use Bunny\Channel;
use Bunny\Message as BunnyMessage;
use Necktie\Bundle\GatewayBundle\Proxy\ConsumerProxy;
use Trinity\Bundle\BunnyBundle\Annotation\Consumer;

/**
 * @Consumer(
 *     queue="queue_gateway",
 *     maxMessages=100,
 *     maxSeconds=3600.0,
 *     prefetchCount=1
 * )
 */
class GatewayConsumer
{

    /**
     * @var ConsumerProxy
     */
    protected $consumerProxy;


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
        // @todo(@jancar) -> max repeat for nack

        try{
            $this->consumerProxy->handleMessage('queue_gateway', $message, $bunnyMessage->deliveryTag);
            $channel->ack($bunnyMessage);
        }catch (\Exception $ex){
            echo $ex->getMessage();
            // @todo - log exception

            echo $ex->getMessage();
            $channel->nack($bunnyMessage);
        }

    }

}