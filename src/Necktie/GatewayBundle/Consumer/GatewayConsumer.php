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
 *     maxMessages=100,
 *     maxSeconds=3600.0,
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

            echo "KOKOS JE 1<br>\n";
            sleep(5);
            $this->consumerProxy->handleMessage(self::QUEUE_NAME, $message, $bunnyMessage->deliveryTag);
            $channel->ack($bunnyMessage);
            echo "KOKOS JE 3<br>\n\n";
        }catch (\Exception $ex){
//            $this->errors++;
            echo "KOKOS JE SPATNY<br>\n\n";
            echo $ex->getMessage();

//            if ($this->errors > $this->maxRepeat) {
//                $channel->reject($bunnyMessage);
//                return;
//            }

            $channel->nack($bunnyMessage, false, false);
        }
    }
}