<?php


namespace Necktie\Bundle\GatewayBundle\Producer;

use Trinity\Bundle\BunnyBundle\Producer\Producer as BaseProducer;
use Trinity\Bundle\BunnyBundle\Setup\BaseRabbitSetup;


/**
 * Class Producer
 * @package Necktie\Bundle\GatewayBundle\Producer
 */
class Producer extends BaseProducer
{

    /**
     * ClientProducer constructor.
     * @param BaseRabbitSetup $clientSetup
     */
    public function __construct(BaseRabbitSetup $clientSetup)
    {
        parent::__construct($clientSetup);
    }


    /**
     * @param string $data
     * @param string $exchangeName
     */
    public function publish(string $data, string $exchangeName)
    {
        echo 'Published '.$exchangeName.PHP_EOL;

        $setup = $this->rabbitSetup;
        $setup->setUp();

        $channel = $setup->getChannel();
        $channel->queueDeclare('queue_necktie');
        $channel->publish($data, [], '', 'queue_necktie');
    }
}